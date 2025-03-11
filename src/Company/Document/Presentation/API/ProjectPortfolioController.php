<?php

namespace Src\Company\Document\Presentation\API;


use Src\Company\Document\Application\Policies\ProjectPortfolioPolicy;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Requests\StoreProjectPortfolioRequest;
use Src\Company\Document\Infrastructure\EloquentModels\ProjectPorfolioEloquentModel;
use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\Requests\UpdateProjectPortfolioRequest;
use Src\Company\Document\Application\UseCases\Queries\GetProjectPortfolioquery;
use Src\Company\Document\Application\UseCases\Queries\GetProjectByProjectPortfolioId;

class ProjectPortfolioController extends Controller
{


    public function getProjectBySalePerson(int $sale_person_id)
    {
        try {


            $projectPortfolio = ((new GetProjectByProjectPortfolioId($sale_person_id))->handle());

            return response()->success($projectPortfolio, 'success', Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getByProjectId(int $projectId)
    {
        // abort_if(authorize('view', ProjectPortfolioPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project Protfolio!');
        try {


            $projectPortfolio = ((new GetProjectPortfolioquery($projectId))->handle());

            return response()->success($projectPortfolio, 'success', Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show(ProjectPorfolioEloquentModel $project_portfolios)
    {
        // abort_if(authorize('view', ProjectPortfolioPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Project Protfolio!');

        try {


            $project_portfolios->project_url = $project_portfolios->getFirstMediaUrl('document_file');

            return response()->success($project_portfolios, 'success', Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreProjectPortfolioRequest $request)
    {
        // abort_if(authorize('store', ProjectPortfolioPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Project Portfolio!');

        try {


            DB::beginTransaction();
            // return $request->all();
            $projectPortfolio = ProjectPorfolioEloquentModel::create($request->all());

            if (request()->hasFile('document_file') && request()->file('document_file')->isValid()) {

                $projectPortfolio->clearMediaCollection('document_file');
                $projectPortfolio->addMediaFromRequest('document_file')->toMediaCollection('document_file', 'media_project_portfolio');
            }


            DB::commit();
            return $projectPortfolio;

            return response()->success($designData, 'success', Response::HTTP_CREATED);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->error(null, $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update(ProjectPorfolioEloquentModel $project_portfolios, UpdateProjectPortfolioRequest $request)
    {
        // abort_if(authorize('update', ProjectPortfolioPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Project Protfolio!');

        try {
            DB::beginTransaction();

            $projectPortfolio = $project_portfolios->update($request->all());

            if (request()->hasFile('document_file') && request()->file('document_file')->isValid()) {

                $project_portfolios->clearMediaCollection('document_file');
                $project_portfolios->addMediaFromRequest('document_file')->toMediaCollection('document_file', 'media_project_portfolio');
            }

            DB::commit();

            return response()->success($projectPortfolio, 'success', Response::HTTP_CREATED);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function delete(ProjectPorfolioEloquentModel $project_portfolios)
    {
        // abort_if(authorize('destroy', ProjectPortfolioPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Project Protfolio!');

        try {

            $project_portfolios->delete();

            return response()->success($project_portfolios, "Successfully Deleted", Response::HTTP_OK);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
