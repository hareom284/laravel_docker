<?php

namespace Src\Company\System\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\System\Application\Mappers\CompanyKpiMapper;
use Src\Company\System\Application\Policies\CompanyKpiPolicy;
use Src\Company\System\Application\Requests\CreateCompanyKpiRequest;
use Src\Company\System\Application\UseCases\Commands\StoreCompanyKpiCommand;
use Src\Company\System\Application\UseCases\Queries\FindCompanyKpiByYearQuery;
use Src\Company\System\Application\UseCases\Queries\FindCompanyKpiQuery;

class CompanyKpiController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', CompanyKpiPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Company Kpi!');

        try {

            return response()->success((new FindCompanyKpiQuery($request->company_id))->handle(), "Company Kpi List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function kpiRecordByYear(Request $request)
    {
        abort_if(authorize('view_kpi_by_year', CompanyKpiPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_kpi_by_year permission for Company Kpi!');
        try {

            return response()->success((new FindCompanyKpiByYearQuery($request->company_id, $request->year))->handle(), "Company Kpi List", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(CreateCompanyKpiRequest $request): JsonResponse
    {
        abort_if(authorize('store', CompanyKpiPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Company Kpi!');

        try {
            $companyKpi = CompanyKpiMapper::fromRequest($request, $request->id);

            $companyKpiData = (new StoreCompanyKpiCommand($companyKpi))->execute();

            return response()->success($companyKpiData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
