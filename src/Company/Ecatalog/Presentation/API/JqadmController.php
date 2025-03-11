<?php

namespace Src\Company\Ecatalog\Presentation\API;

use Illuminate\Support\Facades\Request;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Ecatalog\Presentation\API;
use Nyholm\Psr7\Factory\Psr17Factory;
use Illuminate\Validation\UnauthorizedException;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;
use Psr\Http\Message\ServerRequestInterface;

use Aimeos\Shop\Controller\AdminController;


class JqadmController extends AdminController
{

    public function saveAction()
    {

        logger('message',[request()->all()]);
        try {

            if (config('shop.authorize', true)) {
                $this->authorize('admin', [JqadmController::class, config('shop.roles', ['admin', 'editor'])]);
            }

            $cntl = $this->createAdmin();


            logger('message',[$cntl]);

            if (($html = $cntl->save()) == '') {
                return response()->json([
                    'data' => "success"
                ], 200);
            }

            return response()->json([
                'data' => "success"
            ], 200);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), $e->getLine(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $error) {
            return response()->error($error->getMessage(), $error->getLine(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Returns the resource controller
     *
     * @return \Aimeos\Admin\JQAdm\Iface JQAdm client
     */
    protected function createAdmin(): \Aimeos\Admin\JQAdm\Iface
    {

        logger('message request all',[request()->all()]);
        $site = 'default';
        $lang = Request::get('locale', config('app.locale', 'en'));
        $resource = Route::input('resource');

        $aimeos = app('aimeos')->get();




        $context = app('aimeos.context')->get(false, 'backend');
        $context->setI18n(app('aimeos.i18n')->get(array($lang, 'en')));
        $context->setLocale(app('aimeos.locale')->getBackend($context, $site));

        $siteManager = \Aimeos\MShop::create($context, 'locale/site');
        $context->config()->apply($siteManager->find($site)->getConfig());

        $paths = $aimeos->getTemplatePaths('admin/jqadm/templates', $context->locale()->getSiteItem()->getTheme());
        $view = app('aimeos.view')->create($context, $paths, $lang);


        $view->aimeosType = 'Laravel';
        $view->aimeosVersion = app('aimeos')->getVersion();
        $view->aimeosExtensions = implode(',', $aimeos->getExtensions());

        $context->setView($view);
        return \Aimeos\Admin\JQAdm::create($context, $aimeos, $resource);
    }
}
