<?php

namespace Src\Company\StaffManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\StaffManagement\Application\Mappers\StaffMapper;
use Src\Company\StaffManagement\Application\UseCases\Commands\StoreStaffCommand;
use Src\Company\StaffManagement\Application\UseCases\Commands\UpdateStaffCommand;
use Maatwebsite\Excel\Facades\Excel;
use Src\Company\StaffManagement\Domain\Imports\StaffExcelImport;

class StaffController extends Controller
{

    public function store(Request $request): JsonResponse
    {
        try {

            $staff = StaffMapper::fromRequest($request,null,$request->user_id);

            $staffData = (new StoreStaffCommand($staff))->execute();

            return response()->success($staffData, 'success', Response::HTTP_CREATED);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        try {

            $staff = StaffMapper::fromRequest($request,$id,$request->user_id);

            $staffData = (new UpdateStaffCommand($staff))->execute();

            return response()->success($staffData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function staffExcelImport(Request $request)
    {
        try {

            $uploadFile = $request->file('staff_excel_file');

            // Convert spreadsheet to array
            $sheetsData = Excel::toArray([], $uploadFile);

            foreach ($sheetsData as $sheet) {
                $import = new StaffExcelImport();
                $import->collection(collect($sheet));
            }

            return response()->success(null, "Successfully Staff Excel Imported", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
