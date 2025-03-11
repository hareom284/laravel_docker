<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Mappers\QuotationTemplateItemsMapper;
use Src\Company\Document\Application\Policies\ProjectQuotationPolicy;
use Src\Company\Document\Application\UseCases\Commands\CreateQuotationTemplateCommand;
use Src\Company\Document\Application\UseCases\Commands\DeleteQuotationTemplateCommand;
use Src\Company\Document\Application\UseCases\Commands\DeleteQuotationTemplateItemsCommand;
use Src\Company\Document\Application\UseCases\Commands\duplicateTemplateCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreQuotationTemplateCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreQuotationTemplateItemsCommand;
use Src\Company\Document\Application\UseCases\Commands\StoreSalepersonQuotationTemplateCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateQuotationTemplateCommand;
use Src\Company\Document\Application\UseCases\Commands\UpdateQuotationTemplateItemsCommand;
use Src\Company\Document\Application\UseCases\Queries\FindQuotationAllTemplateQuery;
use Src\Company\Document\Application\UseCases\Queries\FindQuotationTemplateQuery;
use Src\Company\Document\Domain\Imports\MultipleTemplateImport;
use Src\Company\Document\Domain\Imports\TemplateImport;
use Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class QuotationTemplateItemsController extends Controller
{
    private $quotationItemsInterface;

    public function __construct(QuotationTemplateItemsRepositoryInterface $quotationItemsRepository)
    {
        $this->quotationItemsInterface = $quotationItemsRepository;
    }

    public function index(Request $request): JsonResponse
    {
        // check if user's has permission
        // abort_if(authorize('view_template', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cannotViewTemplate = authorize('view_template', ProjectQuotationPolicy::class);

        $cannotViewPersonalTemplate = authorize('view_personal_template', ProjectQuotationPolicy::class);

        if ($cannotViewTemplate && $cannotViewPersonalTemplate) {
            abort(Response::HTTP_FORBIDDEN, 'Need view_template and view_personal_template permissions for Quotation Template Item!');
        }
        try {
            $items = $this->quotationItemsInterface->getQuotationItems($request->template_id);

            return response()->success($items, 'success', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $cannotStoreTemplate = authorize('store_template', ProjectQuotationPolicy::class);

        $cannotStorePersonalTemplate = authorize('update_personal_template', ProjectQuotationPolicy::class);

        if ($cannotStoreTemplate && $cannotStorePersonalTemplate) {
            abort(Response::HTTP_FORBIDDEN, 'Need store_template and update_personal_template permissions for Quotation Template Item!');
        }

        try {
            $quotationTemplateItems = QuotationTemplateItemsMapper::fromRequest($request);

            $quotationTemplateItemsData = (new StoreQuotationTemplateItemsCommand($quotationTemplateItems))->execute();

            return response()->success($quotationTemplateItemsData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function templateStore(Request $request)
    {
        $cannotStoreTemplate = authorize('store_template', ProjectQuotationPolicy::class);

        $cannotStorePersonalTemplate = authorize('update_personal_template', ProjectQuotationPolicy::class);

        if ($cannotStoreTemplate && $cannotStorePersonalTemplate) {
            abort(Response::HTTP_FORBIDDEN, 'Need store_template and update_personal_template permissions for Quotation Template Item!');
        }

        $array = $request->all();

        $data = (new StoreQuotationTemplateCommand($array))->execute();

        return response()->success($data, 'success', Response::HTTP_CREATED);
    }

    public function salepersonTemplateStore(Request $request)
    {
        $array = $request->all();

        $data = (new StoreSalepersonQuotationTemplateCommand($array))->execute();

        return response()->success($data, 'success', Response::HTTP_CREATED);
    }

    public function retrieveAllTemplate(Request $request)
    {
        $quotationTemplate = (new FindQuotationAllTemplateQuery($request->salesperson_id, $request->contract))->handle();

        return response()->success($quotationTemplate, 'success', Response::HTTP_CREATED);
    }

    public function retrieveTemplate(Request $request)
    {
        $templates = (new FindQuotationTemplateQuery($request->template_id))->handle();

        return response()->success($templates, 'success', Response::HTTP_CREATED);
    }

    public function duplicateTemplate(Request $request)
    {
        $data = (new duplicateTemplateCommand($request))->execute();

        return response()->success($data, 'success', Response::HTTP_CREATED);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        // check if user's has permission
        abort_if(authorize('update_template', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need update_template permission for Quotation Template Item!');

        try {
            $quotationItems = QuotationTemplateItemsMapper::fromRequest($request, $id);

            (new UpdateQuotationTemplateItemsCommand($quotationItems))->execute();

            return response()->success($quotationItems, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy($id): JsonResponse
    {
        abort_if(authorize('destroy_template', ProjectQuotationPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy_template permission for Quotation Template Item!');

        try {
            (new DeleteQuotationTemplateItemsCommand($id))->execute();

            return response()->success($id, 'Successfully Deleted', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function excelImport(Request $request)
    {
        try {
            $uploadFile = $request->file('excel_file');

            $filePath = $uploadFile->getRealPath();

            // Load the spreadsheet
            $spreadsheet = IOFactory::load($filePath);

            // Get sheet names
            $sheetNames = $spreadsheet->getSheetNames();

            // Convert spreadsheet to array
            $sheetsData = Excel::toArray([], $uploadFile);

            foreach ($sheetsData as $index => $sheet) {
                $sheetName = $sheetNames[$index] ?? 'Sheet ' . ($index + 1);
                $import = new TemplateImport($sheetName);
                $import->collection(collect($sheet));
            }
            return response()->success(null, 'Successfully Excel Imported', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function createTemplate(Request $request)
    {
        $cannotStoreTemplate = authorize('store_template', ProjectQuotationPolicy::class);

        $cannotStorePersonalTemplate = authorize('update_personal_template', ProjectQuotationPolicy::class);

        if ($cannotStoreTemplate && $cannotStorePersonalTemplate) {
            abort(Response::HTTP_FORBIDDEN, 'Need store_template and update_personal_template permissions for Quotation Template Item!');
        }

        $array = $request->all();

        $data = (new CreateQuotationTemplateCommand($array))->execute();

        return response()->success($data, 'success', Response::HTTP_CREATED);
    }

    public function updateTemplate(Request $request)
    {
        $cannotStoreTemplate = authorize('update_template', ProjectQuotationPolicy::class);

        $cannotStorePersonalTemplate = authorize('update_personal_template', ProjectQuotationPolicy::class);

        if ($cannotStoreTemplate && $cannotStorePersonalTemplate) {
            abort(Response::HTTP_FORBIDDEN, 'Need store_template and update_personal_template permissions for Quotation Template Item!');
        }

        $array = $request->all();

        $data = (new UpdateQuotationTemplateCommand($array))->execute();

        return response()->success($data, 'success', Response::HTTP_CREATED);
    }

    public function deleteTemplate($id)
    {
        $cannotStoreTemplate = authorize('destroy_template', ProjectQuotationPolicy::class);

        $cannotStorePersonalTemplate = authorize('update_personal_template', ProjectQuotationPolicy::class);

        if ($cannotStoreTemplate && $cannotStorePersonalTemplate) {
            abort(Response::HTTP_FORBIDDEN, 'Need store_template and update_personal_template permissions for Quotation Template Item!');
        }
        try {
            (new DeleteQuotationTemplateCommand($id))->execute();

            return response()->success($id, 'Successfully Deleted', Response::HTTP_OK);
        } catch (UnauthorizedUserException $e) {
            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function suggestionAreaOfWork()
    {
        $response_json = collect(config('area_of_works.rooms'));

        $rooms = $response_json->map(function ($room) {
            return $room['name'];
        })->toArray();

        return response()->success($rooms, 'success', Response::HTTP_OK);
    }
}
