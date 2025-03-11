<?php

use Illuminate\Support\Facades\Route;
use Aimeos\Shop\Controller\JsonapiController;
use Src\Company\Ecatalog\Presentation\API\ImageController;
use Src\Company\Ecatalog\Presentation\API\ProductController;
use Src\Company\Ecatalog\Presentation\API\JqadmController;






Route::post('/ecatalog/image_upload', [ImageController::class, 'upload']);
Route::group([
    'prefix' => 'admin/{site}/jqadm'
], function () {




    // Route::match( array( 'POST' ), 'save/{resource}', array(
    //     'as' => 'aimeos_shop_jqadm_save',
    //     'uses' => 'Src\Company\Ecatalog\Presentation\API\JqadmController@saveAction'
    // ) )->where( ['locale' => '[a-z]{2}(\_[A-Z]{2})?', 'site' => '[A-Za-z0-9\.\-]+', 'resource' => '[a-z\/]+'] );
});
