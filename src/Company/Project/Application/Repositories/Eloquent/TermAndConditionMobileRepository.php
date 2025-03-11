<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Src\Company\Project\Domain\Repositories\TermAndConditionMobileRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\TermAndConditionSignatureEloquentModel;

class TermAndConditionMobileRepository implements TermAndConditionMobileRepositoryInterface
{
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
        }
    }
}