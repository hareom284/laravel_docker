<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Infrastructure\EloquentModels\ContractEloquentModel;
use Src\Company\Project\Application\DTO\TermAndConditionData;
use Src\Company\Project\Application\Mappers\TermAndConditionMapper;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\Project\Domain\Model\Entities\TermAndCondition;
use Src\Company\Project\Domain\Repositories\TermAndConditionRepositoryInterface;
use Src\Company\Project\Domain\Resources\TermAndConditionResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\TermAndConditionPageEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\TermAndConditionEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\TermAndConditionParagraphEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\TermAndConditionSignatureEloquentModel;

class TermAndConditionRepository implements TermAndConditionRepositoryInterface
{
    public function index($filters = [])
    {
        //roles list
        $perPage = $filters['perPage'] ?? 10;

        $termAndConditionEloquent = TermAndConditionEloquentModel::query()->with('pages.paragraphs')->filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $termAndCondition = TermAndConditionResource::collection($termAndConditionEloquent);

        $links = [
            'first' => $termAndCondition->url(1),
            'last' => $termAndCondition->url($termAndCondition->lastPage()),
            'prev' => $termAndCondition->previousPageUrl(),
            'next' => $termAndCondition->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $termAndCondition->currentPage(),
            'from' => $termAndCondition->firstItem(),
            'last_page' => $termAndCondition->lastPage(),
            'path' => $termAndCondition->url($termAndCondition->currentPage()),
            'per_page' => $perPage,
            'to' => $termAndCondition->lastItem(),
            'total' => $termAndCondition->total(),
        ];
        $responseData['data'] = $termAndCondition;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function findTermAndConditionById(int $id)
    {
        $termAndConditionEloquent = TermAndConditionEloquentModel::query()->with('pages.paragraphs')->findOrFail($id);

        $termAndCondition = new TermAndConditionResource($termAndConditionEloquent);

        return $termAndCondition;
    }

    public function store(TermAndCondition $termAndCondition, array $termAndConditionData): TermAndConditionData
    {
        DB::beginTransaction();
        try {

            $termAndConditionEloquent = TermAndConditionMapper::toEloquent($termAndCondition);
            $termAndConditionEloquent->save();

            $contents = json_decode($termAndConditionData['pages']);
            foreach ($contents as $index => $contentData) {
                $termAndConditionContent = TermAndConditionPageEloquentModel::create([
                    'term_and_condition_id' => $termAndConditionEloquent->id,
                    'page_number' => $contentData->page_number,
                ]);
                foreach ($contentData->paragraphs as $p_index => $paragraph) {
                    $termAndConditionParagraph = TermAndConditionParagraphEloquentModel::create([
                        'term_and_condition_page_id' => $termAndConditionContent->id,
                        'content' => $paragraph->content,
                        'is_need_signature' => $paragraph->is_need_signature,
                        'signature_position' => $paragraph->signature_position
                    ]);
                    $fileKey = "file{$index}{$p_index}";
                    if (isset($termAndConditionData[$fileKey]) && $termAndConditionData[$fileKey] instanceof \Illuminate\Http\UploadedFile) {
                        $termAndConditionParagraph
                            ->addMedia($termAndConditionData[$fileKey])
                            ->toMediaCollection('termAndCondition', 'media_termAndCondition');
                    }
                }
            }
            DB::commit();
            return TermAndConditionData::fromEloquent($termAndConditionEloquent);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex; // Handle exception as needed
        }
    }

    public function update(TermAndCondition $termAndCondition, array $termAndConditionData): TermAndConditionData
    {
        DB::beginTransaction();
        try {
            $termAndConditionEloquent = TermAndConditionMapper::toEloquent($termAndCondition);

            $termAndConditionEloquent->save();
            $contents = json_decode($termAndConditionData['pages']);
            foreach ($contents as $index => $contentData) {
                if (isset($contentData->id)) {
                    // Update existing content
                    $termAndConditionContent = TermAndConditionPageEloquentModel::find($contentData->id);
                    if ($termAndConditionContent) {
                        $termAndConditionContent->update([
                            'term_and_condition_id' => $termAndConditionEloquent->id,
                            'page_number' => $contentData->page_number,
                        ]);
                        foreach ($contentData->paragraphs as $p_index => $paragraph) {
                            if (isset($paragraph->id)) {
                                $termAndConditionParagraph = TermAndConditionParagraphEloquentModel::find($paragraph->id);
                                if ($termAndConditionParagraph) {
                                    $termAndConditionParagraph->update([
                                        'term_and_condition_page_id' => $termAndConditionContent->id,
                                        'content' => $paragraph->content,
                                        'is_need_signature' => $paragraph->is_need_signature,
                                        'signature_position' => $paragraph->signature_position
                                    ]);
                                    // Check if a new file is provided for the content
                                    $fileKey = "file{$index}{$p_index}";
                                    if (isset($termAndConditionData[$fileKey]) && $termAndConditionData[$fileKey] instanceof \Illuminate\Http\UploadedFile) {
                                        $termAndConditionParagraph
                                            ->clearMediaCollection('termAndCondition'); // Remove old file
                                        $termAndConditionParagraph
                                            ->addMedia($termAndConditionData[$fileKey])
                                            ->toMediaCollection('termAndCondition', 'media_termAndCondition');
                                    }
                                }
                            } else {
                                $termAndConditionParagraph = TermAndConditionParagraphEloquentModel::create([
                                    'term_and_condition_page_id' => $termAndConditionContent->id,
                                    'content' => $paragraph->content,
                                    'is_need_signature' => $paragraph->is_need_signature,
                                    'signature_position' => $paragraph->signature_position
                                ]);
                                $fileKey = "file{$index}{$p_index}";
                                if (isset($termAndConditionData[$fileKey]) && $termAndConditionData[$fileKey] instanceof \Illuminate\Http\UploadedFile) {
                                    $termAndConditionParagraph
                                        ->addMedia($termAndConditionData[$fileKey])
                                        ->toMediaCollection('termAndCondition', 'media_termAndCondition');
                                }
                            }
                        }
                    }
                } else {

                    $termAndConditionContent = TermAndConditionPageEloquentModel::create([
                        'term_and_condition_id' => $termAndCondition->id,
                        'page_number' => $contentData->page_number,
                    ]);
                    foreach ($contentData->paragraphs as $p_index => $paragraph) {
                        $termAndConditionParagraph = TermAndConditionParagraphEloquentModel::create([
                            'term_and_condition_page_id' => $termAndConditionContent->id,
                            'content' => $paragraph->content,
                            'is_need_signature' => $paragraph->is_need_signature,
                            'signature_position' => $paragraph->signature_position
                        ]);
                        $fileKey = "file{$index}{$p_index}";
                        if (isset($termAndConditionData[$fileKey]) && $termAndConditionData[$fileKey] instanceof \Illuminate\Http\UploadedFile) {
                            $termAndConditionParagraph
                                ->addMedia($termAndConditionData[$fileKey])
                                ->toMediaCollection('termAndCondition', 'media_termAndCondition');
                        }
                    }
                }
            }

            // Handle deleted contents
            $deletedContents = json_decode($termAndConditionData['deletedContents']);
            $deletedParagraphs = json_decode($termAndConditionData['deletedParagraphs']);
            if (!empty($deletedContents)) {
                $deletedContents = TermAndConditionPageEloquentModel::whereIn('id', $deletedContents)->get();
                foreach ($deletedContents as $content) {
                    $content->delete();
                }
            }

            if (!empty($deletedParagraphs)) {
                $deletedParagraphs = TermAndConditionParagraphEloquentModel::whereIn('id', $deletedParagraphs)->get();
                foreach ($deletedParagraphs as $paragraph) {
                    $paragraph->clearMediaCollection('termAndCondition');
                    $paragraph->delete();
                }
            }
            DB::commit();

            return TermAndConditionData::fromEloquent($termAndConditionEloquent);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex; // Handle exception as needed
        }
    }

    public function delete(int $termAndConditionId): void
    {
        $termAndConditionEloquent = TermAndConditionEloquentModel::query()->findOrFail($termAndConditionId);
        $termAndConditionEloquent->delete();
    }

    public function getAll()
    {
        $termAndConditionEloquent = TermAndConditionEloquentModel::select('id', 'title')->orderBy('id', 'desc')->get();
        return $termAndConditionEloquent;
    }

    public function storeTermAndConditionSignatures($contract)
    {
        $project = ProjectEloquentModel::with(['customersPivot', 'termAndCondition.pages.paragraphs'])->find($contract->project_id);

        if (!$project) {
            throw new \Exception('project not found');
            return;
        }
        if (!$project->termAndCondition) {
            throw new \Exception('Need To Assign Term And Condition To Project!');
            return;
        }

        $customers = $project->customersPivot;
        $pages = $project->termAndCondition->pages;
        if (!$customers || !$pages) {
            return;
        }

        $signaturesData = [];

        foreach ($pages as $page) {
            foreach ($page->paragraphs as $paragraph) {
                $customerSignatures = [];

                foreach ($customers as $customer) {
                    $customerSignatures[] = [
                        'user_id' => $customer->id,
                        'signature' => null,
                    ];
                }
                $signaturesData[] = [
                    'contract_id' => $contract->id,
                    'customer_signatures' => json_encode($customerSignatures),
                    'term_and_condition_paragraph_id' => $paragraph->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($signaturesData)) {
            TermAndConditionSignatureEloquentModel::insert($signaturesData);
            logger('inserted');
        }
    }

    public function updateCustomerSignatures($contract_id, $signaturesData, $files)
    {
        DB::beginTransaction();
        try {
            foreach ($signaturesData as $index => $signature) {
                $contract_id = $signature['contract_id'];
                $user_id = $signature['user_id'];
                $paragraph_id = $signature['term_and_condition_paragraph_id'];

                $contract = ContractEloquentModel::find($contract_id);
                if ($contract) {
                    $contract->update([
                        'is_already_signed' => true,
                    ]);

                    $current_paragraph = TermAndConditionSignatureEloquentModel::where('contract_id', $contract_id)
                        ->where('term_and_condition_paragraph_id', $paragraph_id)
                        ->first();

                    if ($current_paragraph) {
                        $paragraph_signatures = json_decode($current_paragraph->customer_signatures, true) ?? [];

                        $signatureEntry = collect($paragraph_signatures)->firstWhere('user_id', $user_id);

                        if (!$signatureEntry) {
                            $signatureEntry = [
                                'user_id' => $user_id,
                                'signature' => null,
                            ];
                            $paragraph_signatures[] = $signatureEntry;
                        }

                        if (isset($files[$index]['signature'])) {
                            $uploadedFile = $files[$index]['signature'];
                            $media = $current_paragraph->addMedia($uploadedFile)
                                ->toMediaCollection('termAndConditionSignature', 'media_termAndCondition_signatures');

                            foreach ($paragraph_signatures as &$entry) {
                                if ($entry['user_id'] == $user_id) {
                                    $entry['signature'] = $media->getUrl();
                                    break;
                                }
                            }
                        }
                        $current_paragraph->update([
                            'customer_signatures' => json_encode($paragraph_signatures),
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Signatures updated successfully.'], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
