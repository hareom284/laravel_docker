<?php

namespace Src\Company\Ecatalog\Presentation\API;


use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Ecatalog\Application\Requests\UploadImageRequest;

class ImageController extends Controller
{



    public function upload(UploadImageRequest $request)
    {

        $images = [];
        if ($request->images) {
            foreach ($request->images as $key => $image) {
                $filename = rand(100,100000).time() . '.' . $image->getClientOriginalExtension();

                $path = $image->storeAs('images', $filename, 'public');

                $fullUrl = asset('storage/'.$path);


                $images[]['url'] = $fullUrl;
            }
        }

        return response()->json(['data' => $images]);
    }
}
