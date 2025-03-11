<?php

namespace Src\Company\StaffManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\StaffManagement\Application\Mappers\SalepersonYearlyKpiMapper;
use Src\Company\StaffManagement\Application\Policies\SalespersonKpiPolicy;
use Src\Company\StaffManagement\Application\Requests\CreateSalepersonYearlyKpiRequest;
use Src\Company\StaffManagement\Application\UseCases\Commands\StoreSalepersonYearlyKpiCommand;
use Src\Company\StaffManagement\Application\UseCases\Queries\FindSalepersonYearlyKpiByYearQuery;
use Src\Company\StaffManagement\Application\UseCases\Queries\FindSalepersonYearlyKpiQuery;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class SalepersonYearlyKpiController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view_salesperson_yearly_kpi', SalespersonKpiPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_salesperson_yearly_kpi permission for View Saleperson Yearly Kpi!');

        try {

            $saleperson = StaffEloquentModel::query()->where('user_id', $request->saleperson_id)->first();

            $salepersonId = $saleperson->id;

            return response()->success((new FindSalepersonYearlyKpiQuery($salepersonId))->handle(), "Saleperson Yearly Kpi List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function kpiRecordByYear(Request $request)
    {
        abort_if(authorize('view_salesperson_yearly_kpi', SalespersonKpiPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_salesperson_yearly_kpi permission for View Saleperson Yearly Kpi!');

        try {

            $saleperson = StaffEloquentModel::query()->where('user_id', $request->saleperson_id)->first();

            $salepersonId = $saleperson->id;

            return response()->success((new FindSalepersonYearlyKpiByYearQuery($salepersonId, $request->year))->handle(), "Saleperson Kpi List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(CreateSalepersonYearlyKpiRequest $request): JsonResponse
    {
        abort_if(authorize('store_salesperson_yearly_kpi', SalespersonKpiPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_salesperson_yearly_kpi permission for View Saleperson Yearly Kpi!');

        try {

            $saleperson = StaffEloquentModel::query()->where('user_id', $request->saleperson_id)->first();

            $salepersonId = $saleperson->id;

            $salepersonKpi = SalepersonYearlyKpiMapper::fromRequest($request, $request->id, $salepersonId);

            $salepersonKpiData = (new StoreSalepersonYearlyKpiCommand($salepersonKpi))->execute();

            return response()->success($salepersonKpiData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
