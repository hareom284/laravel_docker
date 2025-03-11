<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

// use Illuminate\Support\Facades\Log;
use stdClass;
use Src\Company\Document\Application\DTO\RenovationDocumentData;
use Src\Company\Document\Application\Mappers\RenovationDocumentsMapper;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\AOWIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;

use Src\Company\Document\Infrastructure\EloquentModels\ItemsIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationItemsEloquentModel;

use Src\Company\Document\Domain\Repositories\VariationOrderMobileRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionAreaOfWorkEloquentModel;


use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;

class VariationOrderMobileRepository implements VariationOrderMobileRepositoryInterface
{

    public function getVariationItems($projectId, $saleperson_id)
    {

        $project = ProjectEloquentModel::with('company')->find($projectId);

        $gst = $project->company->gst;

        $final_result = new stdClass;

        $countVODocs = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'VARIATIONORDER')
            ->whereNotNull('signed_date')
            ->count();

        //firstly get unsigned VO docs for getUpdateItems
        $unsignedVO = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'VARIATIONORDER')
            ->whereNull('signed_date')
            ->pluck('id')
            ->first();

        //check unsigned VO docs exists
        // if(!$unsignedVO)
        // {
        //get signed VO latest docs for create
        $lastSignedVO = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'VARIATIONORDER')
            ->whereNotNull('signed_date')
            ->pluck('id')
            ->last();


        //get signed QO docs
        $sign_quotation_id = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'QUOTATION')
            ->whereNotNull('signed_date')
            ->pluck('id')
            ->first();

        $sign_quotation_discount_percentage = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'QUOTATION')
            ->whereNotNull('signed_date')
            ->pluck('special_discount_percentage')
            ->first();

        //if signed or unsigned VO docs exists insert that id, or not insert signed QO id for sorting index
        // $documentId = $documentId ? $documentId : $sign_quotation_id; //old one

        $documentId = $unsignedVO || $lastSignedVO ? ($unsignedVO ? $unsignedVO : $lastSignedVO) : $sign_quotation_id;

        $sectionIndexArray = SectionsIndexEloquentModel::where('document_id', $documentId)
            ->pluck('section_sequence')
            ->first();

        //Convert back into array
        $sectionIndexArray = json_decode($sectionIndexArray);


        //Initalize sections data from quotation
        $sectionsData = $this->initializeSectionsDataFromQuotation($sign_quotation_id, $documentId, $sectionIndexArray);

        //Get all documents , earliest to latest
        $renovationDocuments = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->whereNotNull('signed_date')
            ->where('type', '!=', 'QUOTATION')
            ->orderBy('signed_date', 'asc')
            ->get();
        if ($renovationDocuments) {


            $signedItemArray = [];
            foreach ($renovationDocuments as $renovationDocument) {


                $documentId = $renovationDocument->id;

                switch ($renovationDocument->type) {
                    case 'VARIATIONORDER':
                        $renoItems = RenovationItemsEloquentModel::where('renovation_document_id', $documentId)->get();

                        logger('renoItems',[$renoItems]);

                        $renoSections = RenovationSectionsEloquentModel::where('document_id', $documentId)->get();

                        foreach ($sectionsData as $section) {

                            if($section->calculation_type == 'LUMP_SUM')
                            {
                                foreach ($renoSections as $renoSection) {

                                    if($renoSection->calculation_type == 'LUMP_SUM' && $section->id == $renoSection->section_id)
                                    {
                                        $section->original_total_section_price += $renoSection->total_price;

                                    }

                                }
                            }

                        }

                        foreach ($renoItems as $renoItem) {
                            // Flatten the sections data and find the item
                            $item = null;
                            foreach ($sectionsData as $section) {
                                foreach ($section->area_of_works as $aow) {
                                    $item = $this->findItemById($aow->items, $renoItem->quotation_template_item_id);
                                    if ($item) {
                                        break 2; // break both foreach loops
                                    }
                                }
                            }

                            // If the item exists, update existing items
                            if ($item) {
                                $item->name = $renoItem->name;
                                $item->quantity = $renoItem->quantity;
                                $item->price = $renoItem->price;
                                $item->cost_price = $renoItem->cost_price;
                                $item->profit_margin = $renoItem->profit_margin;
                                $item->is_FOC = $renoItem->is_FOC;
                                $item->is_CN = $renoItem->is_CN;
                                $item->measurement = $renoItem->unit_of_measurement;
                                $item->is_fixed_measurement = $renoItem->is_fixed_measurement;

                                //add l b h var
                                $item->lengthmm = $renoItem->length;
                                $item->breadthmm = $renoItem->breadth;
                                $item->heightmm = $renoItem->height;

                                $item->is_edited = false;
                                $item->is_selected_in_variation_order = false;
                                $item->is_signed_in_VO = true;
                            } else {
                                // If not, add new items
                                $this->initializeNewVariationData($sectionsData, $renoItem);
                            }
                        }
                        break;

                    case 'FOC':
                        $renoItems = RenovationItemsEloquentModel::with('renovation_area_of_work')->where('renovation_document_id', $documentId)->get();

                        foreach ($renoItems as $renoItem) {
                            $itemFound = false;
                            foreach ($sectionsData as $section) {
                                foreach ($section->area_of_works as $aow) {

                                    if ($aow->id == $renoItem->renovation_area_of_work->section_area_of_work_id) { // Assuming `aow_id` exists in $renoItem
                                        $this->updateFOCStatus($aow->items, $renoItem, $itemFound);
                                        if (!$itemFound && !$renoItem->is_excluded) {
                                            $this->addNewItem($aow->items, $renoItem,'FOC');
                                        }
                                        break 2; // Exit the loop once the correct AOW is found and processed
                                    }
                                }
                            }
                        }
                        break;

                        case 'CANCELLATION':
                            $renoItems = RenovationItemsEloquentModel::where('renovation_document_id', $documentId)->get();

                            $renoSections = RenovationSectionsEloquentModel::where('document_id', $documentId)->get();

                            foreach ($sectionsData as $section) {

                                if($section->calculation_type == 'LUMP_SUM')
                                {
                                    foreach ($renoSections as $renoSection) {

                                        if($renoSection->calculation_type == 'LUMP_SUM' && $section->id == $renoSection->section_id)
                                        {
                                            $section->original_total_section_price -= $renoSection->total_price;

                                        }

                                    }
                                }

                            }

                            foreach ($renoItems as $renoItem) {
                                $itemFound = false;
                                foreach ($sectionsData as $section) {
                                    foreach ($section->area_of_works as $aow) {
                                        if ($aow->id == $renoItem->renovation_area_of_work->section_area_of_work_id) { // Assuming `aow_id` exists in $renoItem
                                            $this->updateCancellationStatus($aow->items, $renoItem, $itemFound);
                                            if (!$itemFound && !$renoItem->is_excluded) {
                                                $this->addNewItem($aow->items, $renoItem,'CANCELLATION');
                                            }
                                            break 2; // Exit the loop once the correct AOW is found and processed
                                        }
                                    }
                                }
                            }
                            break;
                }
            }
        }

        $unsignedVODocuments = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'VARIATIONORDER')
            ->whereNull('signed_date')
            ->first();


        /***
         *
         * get singedInQuoation during first time VO
         *
         */

         $quotationSectionsData = $this->initializeSectionsDataFromQuotation($sign_quotation_id, $documentId, $sectionIndexArray);


        //   //remove unnessary section, area_of_works and items in array based on is_sigined_in_QO_or_VO
        //   foreach ($quotationSectionsData as $sectionData) {

        //     foreach ($sectionData->area_of_works as $aow) {

        //         $aow->items = $this->cleanUpItems($aow->items);
        //     }

        //     $sectionData->area_of_works = collect($sectionData->area_of_works)->reject(function ($aow) {
        //         return count($aow->items) == 0;
        //     })->values()->all();
        // }

        // $quotationSectionsData = collect($quotationSectionsData)->reject(function ($sectionData) {
        //     return count($sectionData->area_of_works) == 0;
        // })->values()->all();





        if ($unsignedVODocuments) {
            $unsigned_renoItems = RenovationItemsEloquentModel::with('renovation_sections')->where('renovation_document_id', $unsignedVODocuments->id)->get();
            $itemsMap = RenovationItemsEloquentModel::where('renovation_document_id', $sign_quotation_id)
            ->pluck('name', 'quotation_template_item_id')
            ->toArray();
            foreach ($unsigned_renoItems as $renoItem) {

                collect($sectionsData)->each(function ($sectionData) use ($renoItem) {
                    $sectionData->total_section_price = $sectionData->id === $renoItem->renovation_sections->section_id
                        ? $renoItem->renovation_sections->total_price
                        : $sectionData->total_section_price;

                    $sectionData->total_section_cost_price = $sectionData->id === $renoItem->renovation_sections->section_id
                        ? $renoItem->renovation_sections->total_cost_price
                        : $sectionData->total_section_cost_price;
                });

                // Update items in the hierarchy recursively
                $itemFound = false;
                foreach ($sectionsData as $section) {
                    foreach ($section->area_of_works as $aow) {
                        $itemFound = $this->updateItemInHierarchy($aow->items, $renoItem, $itemsMap);
                        if ($itemFound) break; // Stop if item is found and updated
                    }
                    if ($itemFound) break; // Stop if item is found and updated
                }

                // If item is not found, initialize new item data
                if (!$itemFound) {
                    $this->initializeNewVariationData($sectionsData, $renoItem);
                }
            }

            //get original signed quotation data for unsigned Vo (update api)
            $quotationSectionsData = $this->initializeSectionsDataFromQuotation($sign_quotation_id, $documentId, $sectionIndexArray);


            foreach ($renovationDocuments as $renovationDocument) {

                if($renovationDocument->type == 'CANCELLATION')
                {
                    $renoItems = RenovationItemsEloquentModel::where('renovation_document_id', $documentId = $renovationDocument->id)->get();

                    foreach ($renoItems as $renoItem) {

                    $item = null;
                    foreach ($quotationSectionsData as $section) {
                        foreach ($section->area_of_works as $aow) {
                            $item = $this->findItemById($aow->items, $renoItem->quotation_template_item_id);
                            if ($item) {
                                break 2; // break both foreach loops
                            }
                        }
                    }

                    if($item)
                    {

                        $item->quantity = $item->quantity == $renoItem->quantity ? $item->quantity : $item->quantity - $renoItem->quantity;

                    }

                }
                }

            }
        }



          //check there has last signed VO
          if (isset($lastSignedVO)) {

            //if has get reno items for that VO
            $renoItems = RenovationItemsEloquentModel::where('renovation_document_id', $lastSignedVO)
                ->get();

            //update existing items in signed quotation data
            foreach ($renoItems as $renoItem) {


                $item = null;
                foreach ($quotationSectionsData as $section) {
                    foreach ($section->area_of_works as $aow) {



                        $item = $this->findItemById($aow->items, $renoItem->quotation_template_item_id);
                        if ($item) {
                            break 2; // break both foreach loops
                        }
                    }
                }

                //if exists update existing items
                if ($item) {
                    $item->name = $renoItem->name;
                    $item->quantity = $renoItem->quantity;
                    $item->price = $renoItem->price;
                    $item->cost_price = $renoItem->cost_price;
                    $item->profit_margin = $renoItem->profit_margin;
                    $item->is_FOC = $renoItem->is_FOC;
                    $item->measurement = $renoItem->unit_of_measurement;
                    $item->is_fixed_measurement = $renoItem->is_fixed_measurement;

                    //add l b h
                    $item->lengthmm = $renoItem->length;
                    $item->breadthmm = $renoItem->breadth;
                    $item->heightmm = $renoItem->height;

                    $item->is_edited = false;
                    $item->isChecked = false;
                    $item->is_selected_in_variation_order = false;
                    $item->is_signed_in_VO = true;
                } else {

                    //or not add new items function private
                    $this->initializeNewVariationData($quotationSectionsData, $renoItem);
                }
            }
        }





        foreach($sectionsData as $section)
        {
            foreach($section->area_of_works as $area_of_work)
            {
                /**
                 * add isChecked on section
                */
                $SectionsCollection = collect($area_of_work->items);
                $area_of_work->isChecked = $SectionsCollection->contains(function ($itemisChecked){
                    return ($itemisChecked->isChecked || $itemisChecked->is_edited  || ( ($itemisChecked->isChecked || $itemisChecked->is_edited) && $itemisChecked->is_selected_in_quotation))?? false;
                });


                foreach($area_of_work->items as $item)
                {
                    $item->section_id = $section->id;
                    if($item->is_edited)
                    {
                        $item->isChecked = $item->is_edited;
                    }
                    $item->area_of_work_id = $area_of_work->id;
                }
            }


            $itemsCollection = collect($section->area_of_works);
            // add isChecked
            $section->isChecked = $itemsCollection->contains(function ($itemisChecked){
                return $itemisChecked->isChecked ?? false;
            });

        }


        //remove unnessary section, area_of_works and items in array based on is_sigined_in_QO_or_VO
        foreach ($quotationSectionsData as $sectionData) {

            foreach ($sectionData->area_of_works as $aow) {

                $aow->items = $this->cleanUpItems($aow->items);
            }

            $sectionData->area_of_works = collect($sectionData->area_of_works)->reject(function ($aow) {
                return count($aow->items) == 0;
            })->values()->all();
        }

        $quotationSectionsData = collect($quotationSectionsData)->reject(function ($sectionData) {
            return count($sectionData->area_of_works) == 0;
        })->values()->all();



        $final_result->reno_document_id = $unsignedVODocuments ? $unsignedVODocuments->id : '';
        $final_result->version = "/VO" . ($countVODocs + 1);
        $final_result->gst = $gst;
        $final_result->special_discount_percentage = $unsignedVODocuments ? $unsignedVODocuments->special_discount_percentage : $sign_quotation_discount_percentage;
        $final_result->singed_items =  $signedItemArray ?? [];
        $final_result->data = $sectionsData;

        if (isset($quotationSectionsData)) {
            $final_result->quotationSectionData = $quotationSectionsData;
        }

        return $final_result;
    }

    public function getUpdateVariationItems($documentId, $saleperson_id)
    {
        $documentId = RenovationDocumentsEloquentModel::where('id', $documentId)
            ->pluck('id')
            ->last();
    }

    public function store($renovationDocumentData, $data)
    {

        //save document
        $documentEloquent = RenovationDocumentsMapper::toEloquent($renovationDocumentData);


        //these function will deleted old record
        if(isset($documentEloquent->id))
        {
            deleteOldRecordData($documentEloquent->id);
        }

        $documentEloquent->save();

        //Array to store index of sections
        $sectionSequence = [];

        $renoSectionIndex = 1;

        foreach ($data as $section) {
            $section->document_id = $documentEloquent->id;

            // Validate section and get section ID
            $sectionId = $this->validateSection($section);

            //push section id into section index array
            $sectionSequence[] = $sectionId;

            $renoAOWIndex = 1;

            //Reset AOW sequence every new section
            $aowSequence = [];

            $itemCheckedCount = $this->countItemChecked($section);

            foreach ($section->area_of_works as $aow) {
                $aow->document_id = $documentEloquent->id;

                $aow->section_id = $sectionId;
                // Validate area of work and get AOW ID
                $aowId = $this->validateAreaOfWork($aow);

                //push section id into section index array
                $aowSequence[] = $aowId;

                //Check how many item is checked

                //Reset Item sequence every new AOW
                $itemSequence = [];

                foreach ($aow->items as $item) {
                    $this->createRenovationItem($item, $documentEloquent, $sectionId, $section, $renoSectionIndex, $aowId, $aow, $renoAOWIndex, $itemCheckedCount, $itemSequence);
                }
                ItemsIndexEloquentModel::updateOrCreate(
                    ['document_id' => $documentEloquent->id, 'aow_id' => $aowId],
                    ['items_sequence' => json_encode($itemSequence)]
                );
            }

            AOWIndexEloquentModel::updateOrCreate(
                ['document_id' => $documentEloquent->id, 'section_id' => $sectionId],
                ['aow_sequence' => json_encode($aowSequence)]
            );
        }
        SectionsIndexEloquentModel::updateOrCreate(
            ['document_id' => $documentEloquent->id],
            ['section_sequence' => json_encode($sectionSequence)]
        );

        $renovationDocumentData = new RenovationDocumentData(
            $documentEloquent->id,
            $documentEloquent->type,
            $documentEloquent->version_number = '',
            $documentEloquent->disclaimer,
            $documentEloquent->special_discount_percentage,
            $documentEloquent->total_amount,
            $documentEloquent->salesperson_signature,
            $documentEloquent->signed_by_salesperson_id,
            $documentEloquent->customer_signature = $documentEloquent->customer_signature ?? '',
            $documentEloquent->additional_notes = $documentEloquent->additional_notes ?? '',
            $documentEloquent->project_id,
            $documentEloquent->document_standard_id = $documentEloquent->document_standard_id ?? 1,
            $documentEloquent->payment_terms,
            $documentEloquent->remark
        );

        return $renovationDocumentData;
    }


    //Initliaze the full sections data from quotation
    public function initializeSectionsDataFromQuotation($sign_quotation_id, $documentId, $sectionIndexArray)
    {
        $sectionsData = [];

        // Retrieve all required data in batch queries
        $sectionsArray = RenovationSectionsEloquentModel::whereIn('section_id', $sectionIndexArray)
            ->where('document_id', $sign_quotation_id)
            ->get()
            ->keyBy('section_id');

        $sectionFallbacks = SectionsEloquentModel::whereIn('id', $sectionIndexArray)
            ->get()
            ->keyBy('id');

        $aowIndexes = AOWIndexEloquentModel::where('document_id', $documentId)
            ->whereIn('section_id', $sectionIndexArray)
            ->pluck('aow_sequence', 'section_id');

        $aowsArray = RenovationAreaOfWorkEloquentModel::where('document_id', $sign_quotation_id)
            ->get()
            ->keyBy('section_area_of_work_id');

        $aowFallbacks = SectionAreaOfWorkEloquentModel::get()->keyBy('id');

        $itemIndexes = ItemsIndexEloquentModel::where('document_id', $documentId)
            ->get()
            ->pluck('items_sequence', 'aow_id');

        $renoItems = RenovationItemsEloquentModel::where('renovation_document_id', $sign_quotation_id)
            ->get()
            ->keyBy('quotation_template_item_id');

        $quotationItems = QuotationTemplateItemsEloquentModel::get()
            ->keyBy('id');


        foreach ($sectionIndexArray as $sectionId) {
            $section = $sectionsArray->get($sectionId) ?: $sectionFallbacks->get($sectionId);

            if ($section) {
                $sectionData = new stdClass;
                $sectionData->id = $sectionId;
                $sectionData->name = $section->name;
                $sectionData->total_section_price = 0;
                $sectionData->total_section_cost_price = 0;
                $sectionData->original_total_section_price = $section->total_price ? $section->total_price : 0;
                $sectionData->calculation_type = $section->calculation_type;
                $sectionData->description = $section->description;
                $sectionData->area_of_works = [];

                $aowIndexArray = json_decode($aowIndexes->get($sectionId, "[]"));

                foreach ($aowIndexArray as $aowId) {
                    $area_of_works = $aowsArray->get($aowId) ?: $aowFallbacks->get($aowId);

                    if ($area_of_works) {
                        $aowData = new stdClass;
                        $aowData->id = $aowId;
                        $aowData->name = $area_of_works->name;
                        $aowData->items = [];

                        $itemIndexArray = json_decode($itemIndexes->get($aowId, "[]"));

                        $items = [];

                        foreach ($itemIndexArray as $itemId) {
                            $itemData = new stdClass;
                            $itemData->id = $itemId;
                            $itemData->from_quotation = true;

                            $item = $renoItems->get($itemId) ?: $quotationItems->get($itemId);

                            if ($item) {
                                $itemData->is_selected_in_quotation = isset($renoItems[$itemId]);
                                $itemData->document_item_id = isset($renoItems[$itemId]) ? $item->id : null;
                                $itemData->name = $item->name ?: $item->description;
                                $itemData->quantity = $item->quantity;
                                $itemData->price = $item->price ?: $item->price_with_gst;
                                $itemData->cost_price = $item->cost_price;
                                $itemData->profit_margin = $item->profit_margin;
                                $itemData->measurement = $item->unit_of_measurement;
                                $itemData->is_fixed_measurement = $item->is_fixed_measurement;
                                $itemData->is_FOC = $item->is_FOC ?: false;
                                $itemData->is_CN = $item->is_CN ?: false;
                                $itemData->parent_id = $item->parent_id;


                                //add l b h var
                                $itemData->lengthmm = $item->length;
                                $itemData->breadthmm = $item->breadth;
                                $itemData->heightmm = $item->height;
                            } else {
                                $itemData->is_selected_in_quotation = false;
                                $itemData->document_item_id = null;
                                $itemData->parent_id = $itemData->quotation_items->parent_id;
                            }
                            $itemData->is_edited = false;
                            $itemData->isChecked = false;
                            $itemData->is_selected_in_variation_order = false;
                            $itemData->is_Cancellation = false;
                            $itemData->is_signed_in_VO = false;
                            $itemData->is_signed_in_FOC = false;
                            $itemData->is_signed_in_CN = false;
                            $itemData->is_show_in_summary = false;


                            $items[] = $itemData;
                        }

                        $items = $this->buildItemHierarchy($items);
                        $aowData->items = $items;


                        $sectionData->area_of_works[] = $aowData;
                    }
                }

                $sectionsData[] = $sectionData;
            }
        }


        return $sectionsData;
    }

    private function buildItemHierarchy($items)
    {

        $itemMap = [];
        $rootItems = [];
        // First pass: create item map
        foreach ($items as $item) {
            $itemData = (object) [
                'id' => $item->id,
                'from_quotation' => $item->from_quotation,
                'parent_id' => $item->parent_id,
                'is_selected_in_quotation' => $item->is_selected_in_quotation,
                'document_item_id' => $item->document_item_id,
                'description' => $item->name,
                'quantity' => $item->quantity,
                'lengthmm' => $item->lengthmm ?? 1,
                'breadthmm' => $item->breadthmm ?? 1,
                'heightmm' => $item->heightmm ?? 1,
                'price' => $item->price,
                'cost_price' => $item->cost_price,
                'profit_margin' => $item->profit_margin,
                'measurement' => $item->measurement,
                'unit_of_measurement' => $item->measurement,
                'is_fixed_measurement' => $item->is_fixed_measurement,
                'is_FOC' => $item->is_FOC,
                'is_CN' => $item->is_CN,
                'is_edited' => $item->is_edited,
                'isChecked' => $item->is_selected_in_variation_order,
                'is_selected_in_variation_order' => $item->is_selected_in_variation_order,
                'is_Cancellation' => $item->is_Cancellation,
                'is_signed_in_VO' => $item->is_signed_in_VO,
                'is_signed_in_FOC' => $item->is_signed_in_FOC,
                'is_signed_in_CN' => $item->is_signed_in_CN,
                'is_show_in_summary' => $item->is_show_in_summary,
                'items' => []
            ];
            $itemMap[$item->id] = $itemData;
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

    private function updateItemInHierarchy($items, $renoItem,$itemsMap)
    {
         foreach ($items as $item) {
             if ($item->id === $renoItem->quotation_template_item_id) {
                 // Update item properties
                 $item->name = $renoItem->name;
                 $item->quantity = $renoItem->quantity;
                 $item->lengthmm = $renoItem->length;
                 $item->breadthmm = $renoItem->breadth;
                 $item->heightmm = $renoItem->height;
                 $item->price = $renoItem->price;
                 $item->cost_price = $renoItem->cost_price;
                 $item->profit_margin = $renoItem->profit_margin;
                 $item->is_FOC = $renoItem->is_FOC;
                 $item->is_CN = $renoItem->is_CN;
                 $item->measurement = $renoItem->unit_of_measurement;
                 $item->is_fixed_measurement = $renoItem->is_fixed_measurement;
                 $item->is_edited = isset($itemsMap[$item->id]);
                 $item->is_selected_in_variation_order = $item->is_selected_in_quotation ? false : true;
                 $item->isChecked = $item->is_selected_in_quotation ? false : true;
                 $item->is_signed_in_VO = false;
                 $item->is_show_in_summary = true;
                 return true; // Return true if the item is found and updated
             }

             // Recursively update child items
             if (!empty($item->items)) {
                 $found = $this->updateItemInHierarchy($item->items, $renoItem, $itemsMap);
                 if ($found) return true; // If found in children, stop further searching
             }
         }

         return false; // Return false if the item is not found
    }

    // Recursive function to find item by id
function findItemById($items, $id) {
    foreach ($items as $item) {
        if ($item->id == $id) {
            return $item;
        }
        if (!empty($item->items)) {
            $foundItem = $this->findItemById($item->items, $id);
            if ($foundItem) {
                return $foundItem;
            }
        }
    }
    return null;
}

// Recursive function to update FOC status
function updateFOCStatus($items, $renoItem, &$itemFound) {
    foreach ($items as $item) {
        // If found, means edited item, update the item
        if ($item->id == $renoItem->quotation_template_item_id && !$renoItem->is_excluded) {
            $item->is_FOC = true;
            $item->is_signed_in_FOC = true;
            $itemFound = true;

        }
        if (!empty($item->items)) {
            $this->updateFOCStatus($item->items, $renoItem, $itemFound);
        }
    }
}

// Recursive function to update Cancellation status
function updateCancellationStatus($items, $renoItem, &$itemFound) {
    foreach ($items as $item) {
        // If found, means update the item
        if ($item->id == $renoItem->quotation_template_item_id && !$renoItem->is_excluded) {

            if($item->quantity == $renoItem->quantity)
            {
                $item->is_Cancellation = true;
            }else{
                $item->is_Cancellation = false;
                $item->quantity = $item->quantity == $renoItem->quantity ? $item->quantity : $item->quantity - $renoItem->quantity;
            }

            $item->is_signed_in_CN = true;
            $itemFound = true;
        }
        if (!empty($item->items)) {
            $this->updateCancellationStatus($item->items, $renoItem, $itemFound);
        }
    }
}




   private function cleanUpItems($items)
   {
    // Recursively clean up items
    return collect($items)->map(function ($item) {
        if (!empty($item->items)) {
            $item->items = $this->cleanUpItems($item->items);
        }
        return $item;
    })->reject(function ($item) {
        return !$item->is_selected_in_quotation && !$item->is_signed_in_VO && empty($item->items);
    })->values()->all();
   }

    //Initialize only one section == TODO, for retrieve for update
    public function initializeSectionData($documentId, $renoSection)
    {
        $sectionData = new stdClass;

        $sectionData->id = $renoSection->section_id;
        $sectionData->name = $renoSection->name;
        $sectionData->total_section_price = isset($renoSection->total_price) ? $renoSection->total_price : 0;
        $sectionData->total_section_cost_price = 0;
        $sectionData->calculation_type = $renoSection->calculation_type;
        $sectionData->description = $renoSection->description;
        $sectionData->aow = [];

        $aowIndexArray = AOWIndexEloquentModel::where('document_id', $documentId)
            ->where('section_id', $renoSection->section_id)
            ->pluck('aow_sequence')
            ->first();

        $aowIndexArray = json_decode($aowIndexArray);

        foreach ($aowIndexArray as $aowId) {
            //Check if aow got any selected item
            $aow = RenovationAreaOfWorkEloquentModel::where('document_id', $documentId)
                ->where('section_area_of_work_id', $aowId)
                ->first();
            $aowData = new stdClass;

            $aowData->id = $aowId;
            $aowData->name = $aow->name;
            $aowData->items = [];

            $itemIndexArray = ItemsIndexEloquentModel::where('document_id', $documentId)
                ->where('aow_id', $aowId)
                ->pluck('items_sequence')
                ->first();

            $itemIndexArray = json_decode($itemIndexArray);

            foreach ($itemIndexArray as $itemId) {

                $itemData = new stdClass;

                $itemData->id = $itemId;
                $itemData->from_quotation   = true;

                //Check if item is selected
                $item = RenovationItemsEloquentModel::where('renovation_document_id', $documentId)
                    ->where('quotation_template_item_id', $itemId)
                    ->first();

                //If no item found, means not checked
                if (!$item) {
                    $item = QuotationTemplateItemsEloquentModel::where('id', $itemId)
                        ->first();

                    $itemData->is_selected_in_quotation = false;
                } else {
                    $itemData->is_selected_in_quotation = true;
                }

                $itemData->name = isset($item->name) ? $item->name : $item->description;
                $itemData->quantity = $item->quantity;
                $itemData->price = isset($item->price) ? $item->price : $item->price_with_gst;
                $itemData->cost_price = $item->cost_price;
                $itemData->profit_margin = $item->profit_margin;
                $itemData->is_FOC = isset($item->is_FOC) ? $item->is_FOC : false;
                $itemData->measurement = $item->unit_of_measurement;
                $itemData->is_fixed_measurement = $item->is_fixed_measurement;
                $itemData->is_show_in_summary = false;

                $itemData->is_edited = false;
                $itemData->is_selected_in_variation_order = false;

                $aowData->items[] = $itemData;
            }

            $sectionData->aow[] = $aowData;
        }

        return $sectionData;
    }

    //Check if section exist, if not create one and tag to doc id OR check if section exist, but different calc type
    private function validateSection($section)
    {
        //No ID means newly created section
        //Also check if calc type same incase user change calc type
        $sectionData = SectionsEloquentModel::where('id', isset($section->id) ? $section->id : null)
            ->where('calculation_type', $section->calculation_type)
            // ->where('document_id', null)  //(wai yan)unnecessary checking i don't know why you this
            ->first();

        if (!$sectionData) {
            $sectionData = SectionsEloquentModel::create([
                'name' => $section->name,
                'calculation_type' => $section->calculation_type,
                'is_active' => 0,
                'document_id' => $section->document_id,
                'index' => 0,
                'is_misc' => 0,
            ]);
        }

        return $sectionData->id;
    }

    private function validateAreaOfWork($aow)
    {
        //If no ID means newly created AOW
        $aowData = SectionAreaOfWorkEloquentModel::where('id', isset($aow->id) ? $aow->id : null)
            // ->where('document_id', null) //(wai yan)unnecessary checking i don't know why you this
            ->first();
        //commented out to prevent creating unnecesary SectionAreaOfWork
        if (!$aowData) {
            $aowData = SectionAreaOfWorkEloquentModel::create([
                'name' => $aow->name,
                'is_active' => 0,
                'document_id' => $aow->document_id,
                'index' => 0,
                'section_id' => $aow->section_id,
            ]);
        }

        return $aowData->id;
    }

    private function validateItem($item, $parentItem)
    {
        //No ID means newly created item
        $itemData = QuotationTemplateItemsEloquentModel::where('id', isset($item->id) ? $item->id : null)
            // ->where('document_id', null)    //(wai yan)unnecessary checking i don't know why you this
            ->first();

        if (!$itemData) {
            $itemData = QuotationTemplateItemsEloquentModel::create([
                'document_id' => $item->document_id,
                'parent_id' => isset($parentItem) ? $parentItem->quotation_template_item_id : null,
                'description' => $item?->name ?? $item->description,
                'index' => 0,
                'quantity' => $item->quantity,
                'unit_of_measurement' => $item?->measurement ?? $item->unit_of_measurement,
                'is_fixed_measurement' => $item?->is_fixed_measurement ?? true,
                'section_id' => $item->section_id,
                'area_of_work_id' => $item->area_of_work_id,
                'price_without_gst' => isset($item->price) ?  $item->price : 0,
                'price_with_gst' => $item->price,
                'cost_price' =>  isset($item->cost_price) ? $item->cost_price : 0,
                'profit_margin' => isset($item->profit_margin) ? $item->profit_margin : 0
            ]);
        }

        return $itemData->id;
    }
    private function countItemChecked($section)
    {
        $checkedCount = 0;



        foreach ($section->area_of_works as $aow) {
            foreach ($aow->items as $item) {
                if (isset($item->isChecked) && $item->isChecked) {
                    $checkedCount++;
                }
            }
        }

        return $checkedCount;
    }

    private function validateRenovationSections($sectionId, $section, $documentEloquent, $itemCheckedCount, &$renoSectionIndex)
    {

        $renoSectionData = RenovationSectionsEloquentModel::where('section_id', $sectionId)
            ->where('name', $section->name)
            ->where('calculation_type', $section->calculation_type)
            ->where('document_id', $documentEloquent->id)
            ->first();

        if ($renoSectionData) {
            $renoSectionData->total_price =  $section->total_section_price;
            $renoSectionData->total_cost_price = $section->total_section_cost_price;
            $renoSectionData->update();
        }

        if (!$renoSectionData) {
            $renoSectionData = RenovationSectionsEloquentModel::create([
                'section_id' => $sectionId,
                'name' => $section->name,
                'calculation_type' => $section->calculation_type,
                'document_id' => $documentEloquent->id,
                'total_price' => $section->total_section_price,
                'total_cost_price' => $section->total_section_cost_price,
                'description' => $section->description ?? null,
                'total_items_count' => $itemCheckedCount,
                'index' => $renoSectionIndex,
            ]);

            $renoSectionIndex++;
        }

        return ['index' => $renoSectionIndex, 'data' => $renoSectionData];
    }

    // Validate Reno Area of Work and get AOW ID
    private function validateRenoAreaOfWork($aow, $aowId, $documentId, &$renoAOWIndex)
    {
        $renoAOWData = RenovationAreaOfWorkEloquentModel::where('section_area_of_work_id', $aowId)
            ->where('name', $aow->name)
            ->where('document_id', $documentId)
            ->first();

        if (!$renoAOWData) {
            $renoAOWData = RenovationAreaOfWorkEloquentModel::create([
                'section_area_of_work_id' => $aowId,
                'name' => $aow->name,
                'document_id' => $documentId,
                'index' => $renoAOWIndex,
            ]);

            $renoAOWIndex++;
        }

        return ['index' => $renoAOWIndex, 'data' => $renoAOWData];
    }

    private function createRenovationItem($item, $documentEloquent, $sectionId, $section, $renoSectionIndex, $aowId, $aow, $renoAOWIndex, $itemCheckedCount, &$itemSequence, $parentItem = null)
    {

        $item->document_id = $documentEloquent->id;

        $item->section_id = $sectionId;
        $item->area_of_work_id = $aowId;

        $itemId = $this->validateItem($item, $parentItem);

        //Assign itemId to item id (Only use for newly created item)
        $item->id = $itemId;

        $itemSequence[] = $itemId;

        //changes
        if ($item->is_edited || $item->is_selected_in_variation_order || $item->is_show_in_summary) {
            //Return both index and data
            $renoSection = $this->validateRenovationSections($sectionId, $section, $documentEloquent, $itemCheckedCount, $renoSectionIndex);

            //Reassign the index and data section
            $renoSectionIndex  = $renoSection['index'];
            $renoSectionData  = $renoSection['data'];

            //Return both index and data
            $renoAOW = $this->validateRenoAreaOfWork($aow, $aowId, $documentEloquent->id, $renoAOWIndex);

            //Reassign the index and data reno aow
            $renoAOWIndex  = $renoAOW['index'];
            $renoAOWData  = $renoAOW['data'];

            $cancellationId = null;
            $prevItemId = $item->is_edited ? $item?->document_item_id ?? null : null;

            $renoItemData = $this->storeRenovationItem($documentEloquent, $item, $itemId, $renoSectionData, $renoAOWData, $cancellationId, $prevItemId, false, false,isset($parentItem) ? $parentItem->quotation_template_item_id : null);
        }

        if (!empty($item->items)) {
            foreach ($item->items as $subItem) {
                if(!empty($subItem->name))
                    $this->createRenovationItem($subItem, $documentEloquent, $sectionId, $section, $renoSectionIndex, $aowId, $aow, $renoAOWIndex, $itemCheckedCount, $itemSequence, isset($renoItemData) ? $renoItemData : null);
            }
        }
    }

    function storeRenovationItem($documentEloquent, $item, $itemId, $renoSectionData, $renoAOWData, $cancellationId, $prevItemId, $completed, $active, $parentId = null)
    {

        $is_excluded = (isset($item->is_FOC) ? $item->is_FOC : false) || (isset($item->is_Cancellation) ? $item->is_Cancellation : false) || (!$item->is_edited && !$item->is_selected_in_variation_order && $item->is_show_in_summary);

        return RenovationItemsEloquentModel::create(
            [
                'renovation_document_id' => $documentEloquent->id,
                'parent_id' => $parentId,
                'cancellation_id' => $cancellationId,
                'prev_item_id' => $prevItemId,
                'project_id' => $documentEloquent->project_id,
                'name' => $item->description,
                'quotation_template_item_id' => isset($itemId) ? $itemId : null,
                'renovation_item_section_id' => isset($renoSectionData->id) ? $renoSectionData->id : null,
                'renovation_item_area_of_work_id' => isset($renoAOWData->id) ? $renoAOWData->id : null,
                'quantity' => isset($item->quantity) ? $item->quantity : null,
                'length' => isset($item->lengthmm) ? $item->lengthmm : 1,
                'breadth' => isset($item->breadthmm) ? $item->breadthmm : 1,
                'height' => isset($item->lengthmm) ? $item->lengthmm : 1,
                'price' => isset($item->price) ? $item->price : null,
                'cost_price' => isset($item->cost_price) ? $item->cost_price : null,
                'profit_margin' => isset($item->profit_margin) ? $item->profit_margin : 0,
                'is_FOC' => isset($item->is_FOC) ? $item->is_FOC : 0,
                'is_CN' => isset($item->is_CN) ? $item->is_CN : 0,
                'is_excluded' => $is_excluded,
                'unit_of_measurement' => isset($item->measurement) ? $item->measurement : null,
                'is_fixed_measurement' => isset($item->is_fixed_measurement) ? $item->is_fixed_measurement : 0,
                'completed' => $completed,
                'active' => $active,

            ]
        );
    }

    // initilize new data from previous signed VO
    private function initializeNewVariationData($sectionData, $renoItem)
    {
        $sectionId = $renoItem->renovation_sections->section_id; //get section id from new items

        $aowId = $renoItem->renovation_area_of_work->section_area_of_work_id; //get aow id from new items

        $filta_section_data = collect($sectionData)->firstWhere('id', $sectionId); //filte original section data by section id

        if(isset($filta_section_data->area_of_works))
        {
            foreach ($filta_section_data->area_of_works as $aow) {

                //find aow by aowId and insert new items
                if ($aow->id == $aowId && !$renoItem->parent_id) {
                    $itemData = new stdClass;

                    $itemData->id = $renoItem->quotation_template_item_id;
                    $itemData->name = $renoItem->name;
                    $itemData->quantity = $renoItem->quantity;
                    $itemData->price = $renoItem->price;
                    $itemData->cost_price = $renoItem->cost_price;
                    $itemData->profit_margin = $renoItem->profit_margin;
                    $itemData->measurement = $renoItem->unit_of_measurement;
                    $itemData->is_fixed_measurement = $renoItem->is_fixed_measurement;

                    //add l b h
                    $itemData->lengthmm = $renoItem->length;
                    $itemData->breadthmm = $renoItem->breadth;
                    $itemData->heightmm = $renoItem->height;

                    $itemData->is_edited = false;
                    $itemData->from_quotation = false;
                    $itemData->is_FOC = $renoItem->is_FOC;
                    $itemData->is_CN = $renoItem->is_CN;
                    $itemData->is_Cancellation = false;
                    $itemData->is_selected_in_quotation = false;
                    $itemData->is_selected_in_variation_order = false;
                    $itemData->isChecked = false;
                    $itemData->is_signed_in_VO = true;
                    $itemData->is_signed_in_FOC = false;
                    $itemData->is_signed_in_CN = false;
                    $itemData->is_show_in_summary = false;


                    $aow->items[] = $itemData;
                }
            }
        }

    }

    function addNewItem(&$items, $renoItem, $document_type) {

        $newItem = new stdClass(); // Assuming $items contains objects of a class
        $newItem->id = $renoItem->quotation_template_item_id;
        $newItem->parent_id = $renoItem->parent_id;
        if($document_type=='FOC'){
            $newItem->is_FOC = true;
            $newItem->is_signed_in_FOC = true;
        }else{
            $newItem->is_Cancellation = true;
            $newItem->is_signed_in_CN = true;
        }
        // Add other properties from $renoItem as needed
        $newItem->name = $renoItem->name;
        $newItem->quantity = $renoItem->quantity;
        $newItem->price = $renoItem->price;
        $newItem->cost_price = $renoItem->cost_price;
        $newItem->profit_margin = $renoItem->profit_margin;
        $newItem->unit_of_measurement = $renoItem->unit_of_measurement;
        $newItem->measurement = $renoItem->unit_of_measurement;
        $newItem->is_fixed_measurement = $renoItem->is_fixed_measurement;
        $newItem->is_edited = false;
        $newItem->is_selected_in_variation_order = false;
        $newItem->is_signed_in_VO = false;
        $newItem->is_show_in_summary = false;

        // If parent_id is null, add item at root level
        if ($renoItem->parent_id === null) {
            $items[] = $newItem;
        } else {
            // Find parent item and add new item as its child
            foreach ($items as &$item) {
                if ($item->id == $renoItem->parent_id) {
                    if (!isset($item->items)) {
                        $item->items = [];
                    }
                    $item->items[] = $newItem;
                    break;
                }
                if (!empty($item->items)) {
                    $this->addNewItem($item->items, $renoItem,$document_type);
                }
            }
        }
    }
}
