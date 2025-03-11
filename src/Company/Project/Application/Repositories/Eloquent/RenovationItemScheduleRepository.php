<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Src\Company\Project\Application\DTO\RenovationItemScheduleData;
use Src\Company\Project\Application\Mappers\RenovationItemScheduleMapper;
use Src\Company\Project\Domain\Model\Entities\RenovationItemSchedule;
use Src\Company\Project\Domain\Repositories\RenovationItemScheduleInterface;
use Src\Company\Project\Infrastructure\EloquentModels\RenovationItemScheduleEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Carbon\Carbon;
use Src\Company\Document\Domain\Resources\EVODetailResource;
use Src\Company\Document\Infrastructure\EloquentModels\EvoEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\EvoItemEloquentModel;
use Src\Company\Project\Domain\Resources\EvoItemScheduleResource;
use Src\Company\Project\Domain\Resources\RenovationItemScheduleResource;

class RenovationItemScheduleRepository implements RenovationItemScheduleInterface
{

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

    private function buildItemHierarchy($items)
    {
        $itemMap = [];
        $rootItems = [];
        
        // First pass: create item map
        foreach ($items as $item) {
            $itemName = $item->renovationItem->name;

            $areaOfWork = $item->renovationItem->renovation_area_of_work;
            $area = $areaOfWork ? $areaOfWork->areaOfWork->name : "";
            $areaId = $areaOfWork ? $areaOfWork->areaOfWork->id : "";

            $id_to_map = $item->renovationItem->quotation_template_item_id ?? $item->renovationItem->id;

            $itemData = (object) [
                'schedule_id' => $item->id,
                'previous_item_id' => $item->renovationItem->prev_item_id,
                'sectionId' => $item->renovationItem->renovation_sections->sections->id,
                'sectionName' => $item->renovationItem->renovation_sections->sections->name,
                'item_id' => $item->renovationItem->id,
                'id_to_map' => $id_to_map,
                'item_name' => $itemName,
                'parent_id' => $item->renovationItem->parent_id,
                'quotation_template_item_id' => $item->renovationItem->quotation_template_item_id,
                'areaId' => $areaId,
                'areaName' => $area,
                'item_type' => $item->renovationItem->renovation_documents->type,
                'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'isChecked' => $item->is_checked,
                'template_item_id' => $item->renovationItem->quotation_template_item_id,
                'isCancel' => $item->isCancel,
                'items' => [] // Initialize items array for children
            ];
            $itemMap[$id_to_map] = $itemData; // temporary fix for overwriting the quotation if variation also have the same $id_to_map
        }

        // Second pass: assign children to their parents
        foreach ($itemMap as $item) {
            if ($item->parent_id && isset($itemMap[$item->parent_id])) {
                $itemMap[$item->parent_id]->items[] = $item;
            } else {
                $rootItems[] = $item;  // Top-level items
            }
        }

        return $rootItems;
    }

    public function getEvoItemSchedule(int $projectId)
    {
        $evos = EvoEloquentModel::where('project_id',$projectId)
                ->whereNotNull('signed_date')
                ->with('evo_items')
                ->get();

        $result = EvoItemScheduleResource::collection($evos);

        return $result;
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

    public function updateEvoItemsStatus($id,$roomId,$status)
    {

        $evoItem = EvoItemEloquentModel::query()->findOrFail($id);

        $evoItem->rooms()->updateExistingPivot($roomId, ['is_checked' => $status]);

        return true;
    }

    public function updateAllEvoItemsStatus($evoId,$status)
    {
        $evo = EvoEloquentModel::query()->findOrFail($evoId);

        $evoItems = $evo->evo_items;

        foreach ($evoItems as $evoItem) {
            
            $rooms = $evoItem->rooms;

            // Update pivot data for each room
            foreach ($rooms as $room) {
                // Update the pivot table for the 'rooms' relationship
                $evoItem->rooms()->updateExistingPivot($room->id, ['is_checked' => $status]);
            }
        }

        return true;
    }

    public function updateEvoSchedule($scheduleArray)
    {
        foreach($scheduleArray['scheduleIds'] as $id)
        {
            $evoItem = EvoItemEloquentModel::query()->findOrFail($id);

            $rooms = $evoItem->rooms;

            // Update pivot data for each room
            foreach ($rooms as $room) {
                // Update the pivot table for the 'rooms' relationship
                $evoItem->rooms()->updateExistingPivot($room->id, [
                    'start_date' => $scheduleArray['start_date'],
                    'end_date' => $scheduleArray['end_date']
                ]);
            }

        }

        return true;
    }

}