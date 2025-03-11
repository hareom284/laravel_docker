<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Src\Company\Document\Domain\Repositories\EvoRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\EvoEloquentModel;
use Src\Company\Document\Domain\Model\Entities\Evo;
use Src\Company\Document\Application\DTO\EvoData;
use Src\Company\Document\Application\Mappers\EvoMapper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Domain\Resources\EVODetailResource;
use Src\Company\Document\Domain\Resources\EVOEditResource;
use Src\Company\Document\Domain\Resources\EvoVersionResource;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateItemEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateRoomEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;

class EvoRepository implements EvoRepositoryInterface
{
    public function index() 
    {
        $template = [];

        $templateRooms = EvoTemplateRoomEloquentModel::get();

        $templateItems = EvoTemplateItemEloquentModel::get();

        $template = [
            'items' => $templateItems->toArray(),
            'rooms' => $templateRooms->toArray()
        ];

        return $template;
    }

    public function getEvoAmt($projectId)
    {
        $evo = EvoEloquentModel::where('project_id', $projectId)->whereNotNull('signed_date')->get();

        return $evo;
    }

    public function findEvoByProjectId($projectId)
    {
        $evoEloquent = EvoEloquentModel::where('project_id', $projectId)->get();

        $evos = EvoVersionResource::collection($evoEloquent);

        return $evos;
    }

    public function findEvoById($id)
    {
        $evoEloquent = EvoEloquentModel::with('evo_items', 'projects.customer.customers', 'salesperson.user', 'salesperson.rank', 'customer_signatures.customer.customers')->find($id);

        $evos = new EVODetailResource($evoEloquent);

        return $evos;
    }

    public function findEvoByIdForUpdate($id)
    {
        $evoEloquent = EvoEloquentModel::with('evo_items', 'projects.customer.customers', 'salesperson.user', 'salesperson.rank', 'customer_signatures.customer.customers')->find($id);

        if(!isset($evoEloquent))
            return [];

        $evo = new EVOEditResource($evoEloquent);

        return $evo;

    }

    public function store(Evo $evo): EvoData
    {
        return DB::transaction(function () use ($evo) {

            $evoEloquent = EvoMapper::toEloquent($evo);

            $evoEloquent->save();

            return EvoData::fromEloquent($evoEloquent);
        });
    }

    public function customerSign($request)
    {
        // if ($request->file('customer_signature')) {
        //     $customerFileName =  time() . '.' . $request->file('customer_signature')->extension();

        //     $customerFilePath = 'EVO/customer_signature_file/' . $customerFileName;

        //     Storage::disk('public')->put($customerFilePath, file_get_contents($request->file('customer_signature')));

        //     $customerSignatureFile = $customerFileName;
        // }
        if ($request->customer_signature) {
            $customer_signature_array = $request->customer_signature;
            if (count($customer_signature_array) > 0) {
                $initialFile = null;
                foreach ($customer_signature_array as $customer_sign) {
                    $timestamp = time();
                    $uniqueId = uniqid();
                    $extension = $customer_sign['customer_signature']->extension();
                    $customerFileName = "{$timestamp}_{$uniqueId}.{$extension}";
                    $customerFilePath = 'EVO/customer_signature_file/' . $customerFileName;

                    Storage::disk('public')->put($customerFilePath, file_get_contents($customer_sign['customer_signature']));

                    $customerSignatureFile = $customerFileName;



                    $evo = EvoEloquentModel::find($request->id);

                    $evo->customer_signatures()->create([
                        'evo_id' => $request->id,
                        'customer_id' => $customer_sign['customer_id'],
                        'customer_signature' => $customerSignatureFile
                    ]);
                    $initialFile = $customerSignatureFile;
                }
                $evo->update([
                    'customer_signature' => $initialFile,
                    'signed_date' => Carbon::now()
                ]);
            }
        }


        $evoItems = $evo->evo_items;

        foreach ($evoItems as $evoItem) {
            
            $rooms = $evoItem->rooms;

            // Update pivot data for each room
            foreach ($rooms as $room) {
                // Update the pivot table for the 'rooms' relationship
                $evoItem->rooms()->updateExistingPivot($room->id, ['start_date' => Carbon::now(),'end_date' => Carbon::now()->addWeek()]);
            }
        }

        $evo->update([
            'customer_signature' => $customerSignatureFile,
            'signed_date' => Carbon::now()
        ]);

        SaleReportEloquentModel::where('project_id', $evo->project_id)->increment('total_sales', $evo->grand_total);
        SaleReportEloquentModel::where('project_id', $evo->project_id)->increment('remaining', $evo->grand_total);

        return $evo;
    }

    public function getDocumentStandard($project_id)
    {
        $project = ProjectEloquentModel::find($project_id);

        $documentStandard = DocumentStandardEloquentModel::where('company_id', $project->company_id)->where('name', "electrical variation order")->orderBy('id', 'desc')->first(['header_text', 'footer_text', 'disclaimer']);
        return [
            'header_text' => $documentStandard ? $documentStandard->header_text : '',
            'footer_text' => $documentStandard ? $documentStandard->footer_text : '',
            'disclaimer' => $documentStandard ? $documentStandard->disclaimer : '',
        ];
    }
}
