<?php

namespace Src\Company\CompanyManagement\Presentation\API;

use Illuminate\Http\JsonResponse;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\CompanyManagement\Application\Mappers\FAQItemMapper;
use Src\Company\CompanyManagement\Application\Requests\StoreFAQItemRequest;
use Src\Company\CompanyManagement\Application\UseCases\Commands\DeleteFAQItemCommand;
use Src\Company\CompanyManagement\Application\UseCases\Commands\ReplyCustomerFAQCommand;
use Src\Company\CompanyManagement\Application\UseCases\Commands\StoreFAQItemCommand;
use Src\Company\CompanyManagement\Application\UseCases\Commands\UpdateFAQItemCommand;
use Src\Company\CompanyManagement\Application\UseCases\Queries\FindAllFAQItemQuery;
use Src\Company\CompanyManagement\Application\UseCases\Queries\FindCustomerFAQItemQuery;
use Src\Company\CompanyManagement\Application\UseCases\Queries\FindFAQItemByIdQuery;
use Src\Company\CompanyManagement\Application\Policies\FAQPolicy;

class FAQItemController extends Controller
{

    public function index(Request $request)
    {
        abort_if(authorize('view', FAQPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for FAQ Item!');

        try {

            $filters = $request->all();

            $FAQLists = (new FindAllFAQItemQuery($filters))->handle();

            return response()->success($FAQLists, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function findCustomerQuestions()
    {
        abort_if(authorize('view_customer_FAQ', FAQPolicy::class), Response::HTTP_FORBIDDEN, 'Need view_customer_FAQ permission for FAQ Item!');

        try {

            $FAQLists = (new FindCustomerFAQItemQuery())->handle();

            return response()->success($FAQLists, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function sendReply(int $faqId, Request $request)
    {
        abort_if(authorize('reply_customer_FAQ', FAQPolicy::class), Response::HTTP_FORBIDDEN, 'Need reply_customer_FAQ permission for FAQ Item!');

        try {

            $faq = (new ReplyCustomerFAQCommand($faqId, $request->answer))->execute();

            return response()->success($faq, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function show($faq_id)
    {
        abort_if(authorize('view', FAQPolicy::class), Response::HTTP_FORBIDDEN, 'Need view permission for FAQ Item!');

        try {

            $FAQItem = (new FindFAQItemByIdQuery($faq_id))->handle();

            return response()->success($FAQItem, 'success', Response::HTTP_OK);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function store(StoreFAQItemRequest $request)
    {
        abort_if(authorize('store', FAQPolicy::class), Response::HTTP_FORBIDDEN, 'Need store permission for FAQ Item!');

        try {

            $FAQItem = FAQItemMapper::fromRequest($request);

            (new StoreFAQItemCommand($FAQItem))->execute();

            return response()->success(null, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(StoreFAQItemRequest $request, $id)
    {
        abort_if(authorize('update', FAQPolicy::class), Response::HTTP_FORBIDDEN, 'Need update permission for FAQ Item!');

        try {

            $FAQItem = FAQItemMapper::fromRequest($request, $id);

            $FAQItemData = (new UpdateFAQItemCommand($FAQItem))->execute();

            return response()->success($FAQItemData, 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function destroy($id)
    {
        abort_if(authorize('destroy', FAQPolicy::class), Response::HTTP_FORBIDDEN, 'Need destroy permission for FAQ Item!');

        try {

            (new DeleteFAQItemCommand($id))->execute();

            return response()->success("Deleted Successfully", 'success', Response::HTTP_CREATED);
        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
