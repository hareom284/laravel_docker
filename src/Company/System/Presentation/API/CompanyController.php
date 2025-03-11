<?php

namespace Src\Company\System\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\System\Application\Mappers\CompanyMapper;
use Src\Company\System\Application\Policies\CompanyPolicy;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\System\Application\Requests\StoreCompanyRequest;
use Src\Company\System\Application\Requests\UpdateCompanyRequest;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;
use Src\Company\System\Application\UseCases\Commands\StoreCompanyCommand;
use Src\Company\System\Application\UseCases\Queries\FindCompanyByIdQuery;
use Src\Company\System\Application\UseCases\Commands\DeleteCompanyCommand;
use Src\Company\System\Application\UseCases\Commands\UpdateCompanyCommand;
use Src\Company\System\Application\UseCases\Queries\GetDefaultCompanyQuery;
use Src\Company\System\Application\UseCases\Commands\UpdateDefaultCompanyCommand;
use Src\Company\System\Application\UseCases\Commands\updateAccountingSoftwareCompanyIdsCommand;

class CompanyController extends Controller
{
    private $companyInterFace;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyInterFace = $companyRepository;
    }

    public function index(Request $request): JsonResponse
    {
        //check if user's has permission
        abort_if(authorize('view', CompanyPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Company!');

        try {

            $filters = $request;

            $companies = $this->companyInterFace->getCompanies($filters);

            return response()->success($companies, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function all(): JsonResponse
    {
        abort_if(authorize('view', CompanyPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Company!');
        try {

            $companies = $this->companyInterFace->getAll();

            return response()->success($companies, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getDefaultCompany()
    {
        try {

            return response()->success((new GetDefaultCompanyQuery())->handle(), 'success', Response::HTTP_OK);
            
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(int $id): JsonResponse
    {
        abort_if(authorize('view', CompanyPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Company!');

        try {
            return response()->json((new FindCompanyByIdQuery($id))->handle());
        } catch (UnauthorizedUserException $e) {

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        abort_if(authorize('store', CompanyPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Company!');

        try {
            $company = CompanyMapper::fromRequest($request);

            $companyData = (new StoreCompanyCommand($company))->execute();

            return response()->success($companyData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, UpdateCompanyRequest $request): JsonResponse
    {
        abort_if(authorize('update', CompanyPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Company!');

        try {
            $company = CompanyMapper::fromRequest($request, $id);

            (new UpdateCompanyCommand($company))->execute();

            return response()->success($company, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateDefaultCompany(int $id): JsonResponse
    {
        abort_if(authorize('update', CompanyPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Company!');
        try {

            (new UpdateDefaultCompanyCommand($id))->execute();

            return response()->success(null, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function updateAccountingSoftwareCompanyIds(Request $request)
    {
        try {

            (new updateAccountingSoftwareCompanyIdsCommand(json_decode($request->companies)))->execute();

            return response()->success(null, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        abort_if(authorize('destroy', CompanyPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Company!');

        try {
            (new DeleteCompanyCommand($id))->execute();

            return response()->success($id, "Successfully Deleted", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
