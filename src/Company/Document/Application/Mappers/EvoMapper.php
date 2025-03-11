<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Domain\Model\Entities\Evo;
use Src\Company\Document\Infrastructure\EloquentModels\EvoEloquentModel;

class EvoMapper
{
    public static function fromRequest(Request $request, ?int $evo_id = null): Evo
    {
         $salespersonSignatureFile = '';

        if($request->file('salesperson_signature'))
        {
            $salesFileName =  time().'.'.$request->file('salesperson_signature')->extension();

            $salesFilePath = 'EVO/salesperson_signature_file/' . $salesFileName;

            Storage::disk('public')->put($salesFilePath, file_get_contents($request->file('salesperson_signature')));

            $salespersonSignatureFile = $salesFileName;
        }

        //get authenticated salesperson id
        $salespersonId = auth('sanctum')->user()->staffs->id;

        return new Evo(
            id: $evo_id,
            salesperson_signature: $salespersonSignatureFile,
            // version_number: $request->string('version_number'),
            total_amount: $request->float('total_amount'),
            grand_total: $request->float('grand_total'),
            // signed_date: $request->string('signed_date'),
            additional_notes: $request->string('additional_notes'),
            project_id: $request->integer('project_id'),
            signed_by_salesperson_id: $salespersonId,
        );
    }

    public static function fromEloquent(EvoEloquentModel $evoEloquent): Evo
    {
        return new Evo(
            id: $evoEloquent->id,
            salesperson_signature: $evoEloquent->salesperson_signature,
            // version_number: $evoEloquent->version_number,
            total_amount: $evoEloquent->total_amount,
            grand_total: $evoEloquent->grand_total,
            // signed_date: $evoEloquent->signed_date,
            additional_notes: $evoEloquent->additional_notes,
            project_id: $evoEloquent->project_id,
            signed_by_salesperson_id: $evoEloquent->signed_by_salesperson_id,
        );
    }

    public static function toEloquent(Evo $evo): EvoEloquentModel
    {
        $countProject = EvoEloquentModel::where( 'project_id', $evo->project_id )->count();

        $version_number = $countProject + 1;

        // if ($evo->id) {
        //     $evoEloquent = EvoEloquentModel::query()->findOrFail($evo->id);

        //     logger('salesperson_signature', [$evoEloquent->salesperson_signature]);

        //     if (!empty($evoEloquent->salesperson_signature)) {
        //         $evoEloquent = new EvoEloquentModel();

        //         $versionNumber = EvoEloquentModel::where([
        //             ['project_id', $evo->project_id]
        //         ])
        //         ->orderByRaw("CAST(version_number AS SIGNED) DESC")
        //         ->pluck('version_number')
        //         ->first();

        //         $version_number = (int) $versionNumber + 1;

        //         logger('version number if case',[$version_number]);

        //     } else {

        //         $version_number = EvoEloquentModel::find($evo->id)->version_number;

        //         logger('version number else else case',[$version_number]);
        //     }
        // } else {
        //     $evoEloquent = new EvoEloquentModel();
        //     logger('version number else case',[$version_number]);


        // }

        $evoEloquent = new EvoEloquentModel();
        // if ($evo->id) {

        //     $evoEloquent = EvoEloquentModel::query()->findOrFail($evo->id);

        // }

        $evoEloquent->version_number = $version_number;
        $evoEloquent->total_amount = $evo->total_amount;
        $evoEloquent->grand_total = $evo->grand_total;
        $evoEloquent->salesperson_signature = $evo->salesperson_signature;
        $evoEloquent->signed_date = null;
        $evoEloquent->additional_notes = $evo->additional_notes;
        $evoEloquent->project_id = $evo->project_id;
        $evoEloquent->signed_by_salesperson_id = $evo->signed_by_salesperson_id;

        return $evoEloquent;
    }
}