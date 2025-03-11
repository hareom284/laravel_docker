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



class ProductController extends Controller
{

    public function index(ServerRequestInterface $request)
    {


        try {

            $productLists =  $this->createClient()->get($request, (new Psr17Factory)->createResponse());

            return $productLists;

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $error) {
            return response()->error($error->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    /**
	 * Returns the JsonAdm client
	 *
	 * @return \Aimeos\Client\JsonApi\Iface JsonApi client
	 */
	protected function createClient() : \Aimeos\Client\JsonApi\Iface
	{
		$resource = Route::input( 'resource' );
		$related = Route::input( 'related', Request::get( 'related' ) );

		$aimeos = app( 'aimeos' )->get();
		$context = app( 'aimeos.context' )->get();
		$tmplPaths = $aimeos->getTemplatePaths( 'client/jsonapi/templates', $context->locale()->getSiteItem()->getTheme() );

		$langid = $context->locale()->getLanguageId();

		$context->setView( app( 'aimeos.view' )->create( $context, $tmplPaths, $langid ) );

		return \Aimeos\Client\JsonApi::create( $context, $resource . '/' . $related );
	}
}
