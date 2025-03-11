<?php

namespace Src\Company\Document\Presentation\API;

use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Common\Infrastructure\Laravel\Controller;
use Symfony\Component\HttpFoundation\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Company\Document\Application\UseCases\Queries\FindQuotationAllTemplateMobileQuery;
use Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryInterface;

class QuotationTemplateItemsMobileController extends Controller
{
    private $quotationItemsInterface;

    public function __construct(QuotationTemplateItemsRepositoryInterface $quotationItemsRepository)
    {
        $this->quotationItemsInterface = $quotationItemsRepository;
    }

    public function retrieveAllTemplate(Request $request)
    {

        try {
            $quotationTemplate = (new FindQuotationAllTemplateMobileQuery($request->salesperson_id))->handle();

            return response()->success($quotationTemplate, 'success', Response::HTTP_OK);
        } catch (\Exception $error) {


            logger('error', [$error->getMessage(), $error->getTrace()]);
        }
    }


    public function getTemplate(Request $request): JsonResponse
    {
        try {
            $items = $this->quotationItemsInterface->getQuotationItems($request->template_id);

            return response()->success($items, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
