<?php

namespace Src\Company\Document\Presentation\API;

use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\VendorMapper;
use Src\Company\Document\Application\Requests\StoreVendorRequrest;
use Src\Company\Document\Application\UseCases\Commands\DeleteVendorCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreVendorCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateVendorCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllVendorQuery;
use Src\Company\Document\Application\UseCases\Queries\FindVendorByIdQuery;
use Src\Company\Document\Application\Policies\VendorPolicy;
use Src\Company\Document\Application\Requests\UpdateVendorRequest;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Src\Company\Document\Application\UseCases\Queries\FindVendorByUserIdQuery;
use Src\Company\Document\Domain\Imports\VendorExcelUpdateImport;
use Src\Company\Document\Domain\Imports\VendorImport;
use Src\Company\Document\Domain\Imports\VendorUpdateImport;

class VendorController extends Controller
{

    public function index(Request $request)
    {
        abort_if(authorize('view', VendorPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Vendor!');

        try {

            $filters = $request->all();

            $vendorLists = (new FindAllVendorQuery($filters))->handle();

            return response()->success($vendorLists, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($vendor_id)
    {
        abort_if(authorize('view', VendorPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Vendor!');

        try {

            $vendor = (new FindVendorByIdQuery($vendor_id))->handle();

            return response()->success($vendor, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getVendorByUserId($userId)
    {
        try {

            $vendor = (new FindVendorByUserIdQuery($userId))->handle();

            return response()->success($vendor, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreVendorRequrest $request)
    {
        abort_if(authorize('store', VendorPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for Vendor!');

        try {
            $vendor = VendorMapper::fromRequest($request);

            (new StoreVendorCommand($vendor))->execute();

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(UpdateVendorRequest $request, $vendor_id)
    {
        abort_if(authorize('update', VendorPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for Vendor!');

        try {

            $vendor = VendorMapper::fromRequest($request, $vendor_id);

            $vendorData = (new UpdateVendorCommand($vendor))->execute();

            return response()->success($vendorData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy($vendor_id)
    {
        abort_if(authorize('destroy', VendorPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for Vendor!');

        try {

            (new DeleteVendorCommand($vendor_id))->execute();

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function vendorExcelImport(Request $request)
    {
        try {

            $uploadFile = $request->file('vendor_excel');
           
            $filePath = $uploadFile->getRealPath();

            // Load the spreadsheet
            $spreadsheet = IOFactory::load($filePath);

            // Get sheet names
            $sheetNames = $spreadsheet->getSheetNames();

            // Convert spreadsheet to array
            $sheetsData = Excel::toArray([], $uploadFile);

            foreach ($sheetsData as $index => $sheet) {
                $sheetName = $sheetNames[$index] ?? "Sheet " . ($index + 1);
                $import = new VendorImport($sheetName);
                $import->collection(collect($sheet));
            }
            return response()->success(null, "Successfully Excel Imported", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function vendorExcelUpdateImport(Request $request)
    {
        try {

            Excel::import(new VendorUpdateImport, request()->file('vendor_excel'));

            return response()->success(null, "Successfully Excel Imported", Response::HTTP_OK);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
