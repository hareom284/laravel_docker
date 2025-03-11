<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Document\Domain\Repositories\SupplierInvoiceRepositoryInterface;

class SupplierInvoiceRepository implements SupplierInvoiceRepositoryInterface
{

    public function getSupplierInvoices(int $projectId)
    {
        $project = ProjectEloquentModel::find($projectId);
        return $project->getMedia('supplier_invoice_document');
    }

    public function show($id)
    {
        return Media::where('uuid', $id)->firstOrFail();
    }

    public function store($projectId, $documentFiles)
    {
        $project = ProjectEloquentModel::find($projectId);
        $uploadedFiles = [];
        foreach ($documentFiles as $documentFile) {
            $media = $project->addMedia($documentFile)
                             ->usingName($documentFile->getClientOriginalName())
                             ->toMediaCollection('supplier_invoice_document');
    
            $uploadedFiles[] = $media->getUrl();
        }
        return $uploadedFiles;
    }

    public function delete($id)
    {
        $media = Media::where('uuid', $id)->firstOrFail();
        $media->delete();
    }
}