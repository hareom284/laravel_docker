<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Domain\Model\Entities\HDBForms;
use Src\Company\Document\Infrastructure\EloquentModels\HDBFormsEloquentModel;

class HDBFormsMapper
{
    public static function fromRequest(Request $request, ?int $hdb_forms_id = null): HDBForms
    {
        if($request->hasFile('document_file'))
        {
            $fileName =  time().'.'.$request->document_file->getClientOriginalExtension();

            $filePath = 'hdb_forms_file/' . $fileName;
        
            Storage::disk('public')->put($filePath, file_get_contents($request->document_file));

        }else{

            $fileName = '';

        }

        return new HDBForms(
            id: $hdb_forms_id,
            name: $request->string('name'),
            document_file: $fileName,
            project_id: $request->integer('project_id')
        );
    }

    public static function fromEloquent(HDBFormsEloquentModel $hdbForms): HDBForms
    {
        return new HDBForms(
            id: $hdbForms->id,
            name: $hdbForms->name,
            document_file: $hdbForms->document_file,
            project_id: $hdbForms->project_id
        );
    }

    public static function toEloquent(HDBForms $hdbForms): HDBFormsEloquentModel
    {
        $date_uploaded = Carbon::now();

        $document_file = $hdbForms->document_file;

        $hdbFormsEloquent = new HDBFormsEloquentModel();
        if ($hdbForms->id) {
            $hdbFormsEloquent = HDBFormsEloquentModel::query()->findOrFail($hdbForms->id);

            if($hdbForms->document_file)
            {
                 if(Storage::disk('public')->exists('hdb_forms_file/' . $hdbFormsEloquent->document_file)){

                    Storage::disk('public')->delete('hdb_forms_file/' . $hdbFormsEloquent->document_file);

                }
            }

            $document_file = $hdbForms->document_file == null ? $hdbFormsEloquent->document_file : $document_file;
        }
        $hdbFormsEloquent->name = $hdbForms->name;
        $hdbFormsEloquent->date_uploaded = $date_uploaded;
        $hdbFormsEloquent->document_file = $document_file;
        $hdbFormsEloquent->project_id = $hdbForms->project_id;
        
        return $hdbFormsEloquent;
    }
}