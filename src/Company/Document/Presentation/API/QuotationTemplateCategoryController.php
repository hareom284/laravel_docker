<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\QuotationTemplateCategoryMapper;
use Src\Company\Document\Application\UseCases\Commands\DeleteQuotationTemplateCategoryCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreQuotationTemplateCategoryCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateQuotationTemplateCategoryCommand;
use Src\Company\Document\Application\UseCases\Commands\MoveQuotationTemplateCategoryCommand;
use Src\Company\Document\Application\UseCases\Queries\FindAllQuotationTemplateCategories;
use Src\Company\Document\Application\UseCases\Queries\FindQuotationTemplateCategory;
use Src\Company\Document\Application\UseCases\Queries\FindSalespersonQuotationTemplateCategories;

class QuotationTemplateCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        try {

            return response()->success((new FindAllQuotationTemplateCategories())->handle(), "Quotation Template Categorie List", Response::HTTP_OK);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        try {

            return response()->success((new FindQuotationTemplateCategory($id))->handle(), "Quotation Template Categorie Detail", Response::HTTP_OK);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function store(Request $request): JsonResponse
    {
        try {

            $quotationTemplateCategory = QuotationTemplateCategoryMapper::fromRequest($request);

            $quotationTemplateCategoryData = (new StoreQuotationTemplateCategoryCommand($quotationTemplateCategory))->execute();

            return response()->success($quotationTemplateCategoryData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $quotationTemplateCategory = QuotationTemplateCategoryMapper::fromRequest($request, $id);

            $quotationTemplateCategoryData = (new UpdateQuotationTemplateCategoryCommand($quotationTemplateCategory))->execute();

            return response()->success($quotationTemplateCategoryData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {

            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            (new DeleteQuotationTemplateCategoryCommand($id))->execute();

            return response()->success($id, "Deleted Successfully", Response::HTTP_OK);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getSalespersonQuotationTemplateCategory($user_id): JsonResponse
    {
        try {

            return response()->success((new FindSalespersonQuotationTemplateCategories($user_id))->handle(), "Quotation Template Categorie List", Response::HTTP_OK);
        } catch (\Exception $e) {

            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function moveTemplate(Request $request, $id)
    {
        try {
            $data = $request->all();

            $quotationTemplateCategoryData = (new MoveQuotationTemplateCategoryCommand($id, $data))->execute();

            return response()->success($quotationTemplateCategoryData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {

            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
