<?php

namespace Src\Company\Project\Presentation\API;

use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Project\Application\UseCases\Queries\FindProjectReportQuery;
use Src\Company\Project\Application\Policies\ProjectPolicy;

class ProjectReportController extends Controller
{
    public function getProjectReport($projectId)
    {
        abort_if(authorize('view_project_report', ProjectPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_project_report permission for Project Report!');

        try {

            $data = (new FindProjectReportQuery($projectId))->handle();

            return response()->success($data, "success", Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
