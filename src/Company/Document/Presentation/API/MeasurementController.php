<?php

namespace Src\Company\Document\Presentation\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Response;
use Src\Common\Domain\Exceptions\UnauthorizedUserException;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\Company\Document\Application\Requests\StorePurchaseOrderRequest;
use Src\Company\Document\Application\UseCases\Queries\FindAllMeasurementQuery;
use Src\Company\Document\Application\Mappers\MeasurementMapper;
use Src\Company\Document\Application\UseCases\Commands\StoreMeasurementCommand;
use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;
use Illuminate\Support\Facades\Log;

class MeasurementController extends Controller
{
    public function index(Request $request)
	{
		try {

            $measurements = (new FindAllMeasurementQuery())->handle();

            return response()->success($measurements,'success', Response::HTTP_CREATED);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
	}

	public function store(Request $request)
	{
		try {

            $measurementRequests = $request->measurements;

            $measurementData = MeasurementMapper::fromRequest($measurementRequests);

            $measurement = (new StoreMeasurementCommand($measurementData))->execute();

            $items = QuotationTemplateItemsEloquentModel::select('id', 'unit_of_measurement', 'is_fixed_measurement')->get();

            foreach ($measurement as $m) {
                foreach ($items as $item) {
                    if ($m->name === $item->unit_of_measurement) {
                        if ($m->fixed != $item->is_fixed_measurement) {
                            // Update the is_fixed_measurement of the item
                            $item->is_fixed_measurement = $m->fixed;
                            $item->save();

                        }
                    }
                }
            }

            return response()->success($measurement,'success', Response::HTTP_CREATED);

        } catch (\DomainException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (UnauthorizedUserException $e) {

            return response()->error($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
	}
}