<?php

namespace Src\Company\StaffManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\StaffManagement\Application\Mappers\SalepersonMonthlyKpiMapper;
use Src\Company\StaffManagement\Application\Policies\SalespersonKpiPolicy;
use Src\Company\StaffManagement\Application\Requests\CreateSalepersonMonthlyKpiRequest;
use Src\Company\StaffManagement\Application\UseCases\Commands\StoreSalepersonMonthlyKpiCommand;
use Src\Company\StaffManagement\Application\UseCases\Queries\FindSalepersonMonthlyKpiByMonthQuery;
use Src\Company\StaffManagement\Application\UseCases\Queries\FindSalepersonMonthlyKpiQuery;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class SalepersonMonthlyKpiController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view_salesperson_monthly_kpi', SalespersonKpiPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_salesperson_monthly_kpi permission for Saleperson Monthly Kpi!');

        try {

            $saleperson = StaffEloquentModel::query()->where('user_id', $request->saleperson_id)->first();

            $salepersonId = $saleperson->id;

            return response()->success((new FindSalepersonMonthlyKpiQuery($salepersonId))->handle(), "Saleperson Yearly Kpi List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function kpiRecordByMonth(Request $request)
    {
        abort_if(authorize('view_salesperson_monthly_kpi', SalespersonKpiPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_salesperson_monthly_kpi permission for Saleperson Monthly Kpi!');

        try {

            $saleperson = StaffEloquentModel::query()->where('user_id', $request->saleperson_id)->first();

            $salepersonId = $saleperson->id;

            return response()->success((new FindSalepersonMonthlyKpiByMonthQuery($salepersonId, $request->year, $request->month))->handle(), "Saleperson Kpi List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(CreateSalepersonMonthlyKpiRequest $request): JsonResponse
    {
        abort_if(authorize('store_salesperson_monthly_kpi', SalespersonKpiPolicy::class), Response::HTTP_FORBIDDEN, 'Need store_salesperson_monthly_kpi permission for Saleperson Monthly Kpi!');

        try {

            $saleperson = StaffEloquentModel::query()->where('user_id', $request->saleperson_id)->first();

            $salepersonId = $saleperson->id;

            $salepersonKpi = SalepersonMonthlyKpiMapper::fromRequest($request, $request->id, $salepersonId);

            $salepersonKpiData = (new StoreSalepersonMonthlyKpiCommand($salepersonKpi))->execute();

            return response()->success($salepersonKpiData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
