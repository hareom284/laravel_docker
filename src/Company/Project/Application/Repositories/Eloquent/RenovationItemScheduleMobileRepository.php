<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Src\Company\Project\Domain\Resources\RenovationItemScheduleResource;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleMobileInterface;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\RenovationItemScheduleEloquentModel;

class RenovationItemScheduleMobileRepository implements RenovationItemScheduleMobileInterface
{
    public function store(int $documentId)
    {
        $renoDocumentEloquent = RenovationDocumentsEloquentModel::where('id',$documentId)->first();

        $signedDate = $renoDocumentEloquent->signed_date;

        $projectId = $renoDocumentEloquent->project_id;

        $signDateCargon = Carbon::parse($signedDate);

        $tomorrow = $signDateCargon->addDay(); // get tomorrow date

        $startDate = $tomorrow->format('Y-m-d');  //start date

        $tomorrowAsCarbon = Carbon::parse($startDate);

        $oneWeekLater = $tomorrowAsCarbon->copy()->addWeek(); // Get the end date (one week later)

        $endDate = $oneWeekLater->format('Y-m-d'); // end date

        $renoItemIds = $renoDocumentEloquent->renovation_items->pluck('id');

        foreach($renoItemIds as $itemId)
        {
            RenovationItemScheduleEloquentModel::create([
                'renovation_item_id' => $itemId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'project_id' => $projectId,
                'show_in_timeline' => false,
                'is_checked' => false,
            ]);
        }

        return true;
    }

    public function index(int $projectId)
    {

        $data = RenovationItemScheduleEloquentModel::where('project_id',$projectId)
                ->with('renovationItem','renovationItem.renovation_sections.sections','renovationItem.renovation_documents')
                ->get();

        $cancellations = RenovationItemScheduleEloquentModel::where('project_id', $projectId)
            ->whereHas('renovationItem.renovation_documents', function ($query) {
                $query->where('type', 'CANCELLATION');
            })
            ->whereHas('renovationItem', function ($query){
                $query->where('is_excluded', 0);
            })
            ->with('renovationItem', 'renovationItem.renovation_sections.sections', 'renovationItem.renovation_documents')
            ->get();

        // Convert cancellations to an array of quotation_template_item_id for easy comparison
        $cancellationIds = $cancellations->pluck('renovationItem.quotation_template_item_id')->toArray();

        // Add isCancel property to the items in $data
        $data->each(function ($item) use ($cancellationIds) {
            // Check if the renovationItem exists to avoid errors
            if (isset($item->renovationItem)) {
                $item->isCancel = in_array($item->renovationItem->quotation_template_item_id, $cancellationIds);
            }
            else {
                $item->isCancel = false;
            }
        });

        $result = RenovationItemScheduleResource::collection($data);

        // $final_result = $this->buildItemHierarchy($data);
        
        return $result;
    }

    public function getSectionsByDate(int $projectId, string $date)
    {
        $data = RenovationItemScheduleEloquentModel::where('project_id', $projectId)
            ->with([
                'renovationItem',
                'renovationItem.renovation_sections.sections',
                'renovationItem.renovation_documents'
            ])
            ->get();

        $highlightedDates = [];
        $sectionsForDate = [];

        foreach ($data as $item) {
            $start = new \DateTime($item->start_date);
            $end = new \DateTime($item->end_date);

            while ($start <= $end) {
                $highlightedDates[] = $start->format('Y-m-d');
                $start->modify('+1 day');
            }

            if ($item->start_date <= $date && $item->end_date >= $date) {
                $sectionsForDate[] = [
                    'sectionId' => $item->renovationItem->renovation_sections->sections->id ?? null,
                    'sectionName' => $item->renovationItem->renovation_sections->sections->name ?? null,
                    'start_date' => $item->start_date,
                    'end_date' => $item->end_date,
                ];
            }
        }

        return [
            'highlighted_dates' => array_values(array_unique($highlightedDates)),
            'sections' => array_values($sectionsForDate)
        ];
    }


    public function getDates(int $projectId)
    {

        $getNewDates = RenovationItemScheduleEloquentModel::selectRaw('MIN(start_date) as min_start_date, MAX(end_date) as max_end_date')
            ->where('project_id', $projectId)
            ->first();

        $minStartDate = $getNewDates->min_start_date;

        $maxEndDate = Carbon::parse($getNewDates->max_end_date);

        $returnData['startDate'] = Carbon::parse($minStartDate)->subDay()->format('Y-m-d');

        $returnData['endDate'] = $maxEndDate->addMonths(3)->format('Y-m-d');
        
        return $returnData;
    }

    public function getRenoItemCount(int $projectId)
    {
        $renoSection = RenovationSectionsEloquentModel::with('renovation_document','sections')->whereHas('renovation_document', function ($query) use ($projectId) {
            $query->where('project_id', $projectId)
                    ->whereIn('type', ['QUOTATION', 'VARIATIONORDER','FOC','CANCELLATION'])
                    ->whereNotNull('signed_date');
        })
        ->get();

        $itemCount = $renoSection->groupBy('section_id')->map(function ($items) {

            $totalCount = 0;
            $section = null;
        
            foreach ($items as $item) {
                $section = $item->sections;

                if ($item->renovation_document->type === 'QUOTATION' || $item->renovation_document->type === 'VARIATIONORDER' || $item->renovation_document->type === 'FOC') {
                    $totalCount += $item->total_items_count;
                } elseif ($item->renovation_document->type === 'CANCELLATION') {
                    $totalCount -= $item->total_items_count;
                }
            }

            if(isset($section))
                return [
                    'section_id' => $section->id,
                    'section_name' => $section->name,
                    'total_item_count' => $totalCount,
                ];
            else
                return [];
        
        })->values()->toArray();

        return $itemCount;
    }

    public function updateSchedule($scheduleArray)
    {
        foreach($scheduleArray['scheduleIds'] as $id)
        {
            $scheduleEloquent = RenovationItemScheduleEloquentModel::query()->findOrFail($id);

            $scheduleEloquent->update([
                'start_date' => $scheduleArray['start_date'],
                'end_date' => $scheduleArray['end_date']
            ]);
        }

        return true;
    }

    public function updateStatus($scheduleArray,$id)
    {

        $scheduleEloquent = RenovationItemScheduleEloquentModel::query()->findOrFail($id);

        $scheduleEloquent->update([
            'is_checked' => $scheduleArray['is_checked']
        ]);

        return true;
    }

    public function updateAllStatus(array $itemIds,$isChecked)
    {
        RenovationItemScheduleEloquentModel::whereIn('id', $itemIds)->update(['is_checked' => $isChecked]);

        return true;
    }

}