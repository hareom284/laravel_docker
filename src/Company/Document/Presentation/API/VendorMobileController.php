<?php

namespace Src\Company\Document\Presentation\API;

use Exception;
use Illuminate\Http\JsonResponse;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\UseCases\Queries\FindAllVendorMobileQuery;

class VendorMobileController extends Controller
{

    public function index(Request $request)
    {
        // abort_if(authorize('view', VendorPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for Vendor!');

        try {

            $filters = $request->all();

            $vendorLists = (new FindAllVendorMobileQuery($filters))->handle();

            return response()->success($vendorLists, 'success', Response::HTTP_CREATED);
        } catch (Exception $ex) {

            return response()->error($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
