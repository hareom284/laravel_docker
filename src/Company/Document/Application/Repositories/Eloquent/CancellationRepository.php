<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Log;
use Src\Company\Document\Domain\Repositories\CancellationRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use stdClass;
// use Illuminate\Support\Facades\Log;
use Src\Company\Document\Application\DTO\RenovationDocumentData;
use Src\Company\Document\Application\Mappers\RenovationDocumentsMapper;
use Src\Company\Document\Infrastructure\EloquentModels\AOWIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ItemsIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsIndexEloquentModel;

class CancellationRepository implements CancellationRepositoryInterface
{
    public function getCountLists($projectId)
    {
        $countResult = RenovationDocumentsEloquentModel::where([
            ['project_id', $projectId],
            ['type', 'CANCELLATION']
        ])->count();

        return $countResult;
    }

    public function OldgetCancellationItems($projectId)
    {
        $authId = auth('sanctum')->user()->id;

        //===============================To check master or salesperson template=============================
        //Get renovation document based on project id
        $renovation_document = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->whereNotNull('signed_date')
            ->first();

        //Getting renovation_section
        if ($renovation_document) {
            $renovation_section = RenovationSectionsEloquentModel::where('document_id', $renovation_document->id)
                ->first();
        } else {
            return response()->json(['error' => 'Renovation document not found'], 404);
        }

        //Getting section id
        if ($renovation_section->section_id) {
            $section = SectionsEloquentModel::where('id', $renovation_section->section_id)
                ->first();
        } else {
            return response()->json(['error' => 'Section ID in renovation section not found'], 404);
        }

        $templateItems = QuotationTemplateItemsEloquentModel::with('sections', 'areaOfWorks')->where('salesperson_id', $section->salesperson_id)->get();


        $latestItemsId = collect($templateItems)->max('id');

        $signed_quotation_document = RenovationDocumentsEloquentModel::where('project_id', $projectId)->where('type', 'QUOTATION')->whereNotNull('signed_date')->first(['id']);

        $signed_quotation_items = RenovationItemsEloquentModel::with('quotation_items', 'renovation_sections', 'renovation_area_of_work')->where('renovation_document_id', $signed_quotation_document->id)->get()->sortBy('quotation_items.index');

        $quotation_sections = RenovationSectionsEloquentModel::with('sections')->where('document_id', $signed_quotation_document->id)->get();

        $totalAmountForLump = [];
        $totalSectionDescriptions = [];

        foreach ($quotation_sections as $section) {

            if ($section->sections->calculation_type == "LUMP_SUM") {
                $obj = new stdClass;
                $obj->section_id = $section->sections->id;
                $obj->section_name = $section->sections->name;
                $obj->calculation_type = $section->sections->calculation_type;
                $obj->section_index = $section->sections->index;
                $obj->total_amount = $section->total_price;
                $obj->section_description = $section->description;

                array_push($totalAmountForLump, $obj);
            }

            $obj1 = new stdClass;
            $obj1->section_id = $section->sections->id;
            $obj1->section_description = $section->description;
            array_push($totalSectionDescriptions, $obj1);
        }

        $cancellation_items = [];

        foreach ($signed_quotation_items as $item) {
            if (isset($item->renovation_area_of_work) && isset($item->renovation_area_of_work->name))
                $areaOfWorkName = $item->renovation_area_of_work->name;
            else
                $areaOfWorkName = $item->renovation_item_area_of_work_id ? $item->renovation_area_of_work->areaOfWork->name : "";

            $obj = new stdClass;
            $obj->id = $item->quotation_template_item_id ?? null;
            $obj->document_item_id = $item->id;
            $obj->name = $item->name;
            $obj->price = $item->price;
            $obj->cost_price = $item->cost_price;
            $obj->profit_margin = $item->profit_margin;
            $obj->measurement = $item->unit_of_measurement;
            $obj->is_fixed_measurement = $item->is_fixed_measurement;
            $obj->quantity = $item->quantity;
            $obj->section = $item->renovation_sections->sections->index;
            $obj->sectionId = $item->renovation_sections->sections->id;
            $obj->sectionName = $item->renovation_sections->sections->name;
            $obj->section_description = $item->renovation_sections->sections->description;
            $obj->calculation_type = $item->renovation_sections->sections->calculation_type;
            $obj->area_of_work = $item->renovation_item_area_of_work_id ? $item->renovation_area_of_work->areaOfWork->index : null;
            $obj->area_of_work_id = $item->renovation_item_area_of_work_id ? $item->renovation_area_of_work->areaOfWork->id : null;
            $obj->area_of_work_name = $areaOfWorkName;
            $obj->isChecked = false;
            $obj->isCancel = false;
            $obj->sub_description = $item->sub_description;


            array_push($cancellation_items, $obj);
        }

        //Getting sign variation documents
        $signed_variation_documents = RenovationDocumentsEloquentModel::where('project_id', $projectId)->where('type', 'VARIATIONORDER')->whereNotNull('signed_date')->get(['id']);

        if (!$signed_variation_documents->isEmpty()) {
            foreach ($signed_variation_documents as $voId) {

                $variation_sections = RenovationSectionsEloquentModel::with('sections')->where('document_id', $voId->id)->get();

                foreach ($variation_sections as $section) {

                    if ($section->sections->calculation_type == "LUMP_SUM") {
                        $section_check_status = in_array($section->section_id, array_column($totalAmountForLump, 'section_id'));

                        if ($section_check_status) {
                            $search_index_in_totalAmountForLump = array_search($section->section_id, array_column($totalAmountForLump, 'section_id'));

                            $search_section_result = $totalAmountForLump[$search_index_in_totalAmountForLump];

                            $search_section_result->total_amount += $section->total_price;
                        } else {

                            $obj = new stdClass;
                            $obj->section_id = $section->sections->id;
                            $obj->section_name = $section->sections->name;
                            $obj->section_description = $section->sections->description;

                            $obj->calculation_type = $section->sections->calculation_type;
                            $obj->section_index = $section->sections->index;
                            $obj->total_amount = $section->total_price;

                            array_push($totalAmountForLump, $obj);
                        }
                    }
                }

                //Getting signed variation items basec on doument id
                $signed_variation_items = RenovationItemsEloquentModel::with('quotation_items', 'renovation_sections', 'renovation_area_of_work')->where('renovation_document_id', $voId->id)->get();

                foreach ($signed_variation_items as $variation_item) {

                    if ($variation_item->prev_item_id) {
                        $search_index_in_cancellation_items = array_search($variation_item->prev_item_id, array_column($cancellation_items, 'document_item_id'));

                        $search_result = $cancellation_items[$search_index_in_cancellation_items];

                        $search_result->document_item_id = $variation_item->id;
                        $search_result->index = $variation_item->quotation_items->index ?? 0;
                        $search_result->name = $variation_item->name;
                        $search_result->price = $variation_item->price;
                        $search_result->cost_price = $variation_item->cost_price;
                        $search_result->profit_margin = $variation_item->profit_margin;
                        $search_result->measurement = $variation_item->unit_of_measurement;
                        $search_result->quantity = $variation_item->quantity;
                        $search_result->sub_description = $variation_item->sub_description;

                    } else {
                        $obj = new stdClass;
                        $obj->id = $variation_item->quotation_template_item_id ?? null;
                        $obj->document_item_id = $variation_item->id;
                        $obj->index = $variation_item->quotation_items->index ?? 0;
                        $obj->name = $variation_item->name;
                        $obj->price = $variation_item->price;
                        $obj->cost_price = $variation_item->cost_price;
                        $obj->profit_margin = $variation_item->profit_margin;
                        $obj->measurement = $variation_item->unit_of_measurement;
                        $obj->quantity = $variation_item->quantity;
                        $obj->section = $variation_item->renovation_sections->sections->index;
                        $obj->sectionId = $variation_item->renovation_sections->sections->id;
                        $obj->sectionName = $variation_item->renovation_sections->sections->name;
                        $obj->section_description = $variation_item->renovation_sections->sections->description;

                        $obj->calculation_type = $variation_item->renovation_sections->sections->calculation_type;
                        $obj->area_of_work = $variation_item->renovation_item_area_of_work_id ? $variation_item->renovation_area_of_work->areaOfWork->index : null;
                        $obj->area_of_work_id = $variation_item->renovation_item_area_of_work_id ? $variation_item->renovation_area_of_work->areaOfWork->id : null;
                        $obj->area_of_work_name = $variation_item->renovation_item_area_of_work_id ? $variation_item->renovation_area_of_work->areaOfWork->name : "";
                        $obj->isChecked = false;
                        $obj->isCancel = false;
                        $obj->sub_description = $variation_item->sub_description;

                        array_push($cancellation_items, $obj);
                    }
                }

                // Sort cancellation_items based on quotation_tenmplate_item's index
                usort($cancellation_items, function ($a, $b) {
                    // Setting a default value to prevent error
                    $indexA = property_exists($a, 'index') ? $a->index : PHP_INT_MAX;
                    $indexB = property_exists($b, 'index') ? $b->index : PHP_INT_MAX;

                    return $indexA <=> $indexB;
                });
            }
        }

        $signed_cancellation_items = RenovationDocumentsEloquentModel::with('renovation_items')->where('project_id', $projectId)->where('type', 'CANCELLATION')->whereNotNull('signed_date')->get(['id']);

        if (!$signed_cancellation_items->isEmpty()) {
            foreach ($signed_cancellation_items as $signed_cancellation_item) {

                foreach ($signed_cancellation_item->renovation_items as $cancellation_item) {

                    $search_items_by_sign_cancellation = array_search($cancellation_item->prev_item_id, array_column($cancellation_items, 'document_item_id'));

                    $search_result = $cancellation_items[$search_items_by_sign_cancellation];

                    $search_result->isCancel = $search_result->isChecked = true;
                }
            }
        }

        $groupBySection = collect($cancellation_items)->groupBy('section');
        // Log::info([$groupBySection]);
        $company = ProjectEloquentModel::select('company_id')->find($projectId);

        $document_standard = DocumentStandardEloquentModel::where('name', 'cancellation')->where('company_id', $company->company_id)->first(['id', 'header_text', 'footer_text']);

        $final_result = new stdClass;
        $final_result->document_standard_id = $document_standard ? $document_standard->id : '';
        $final_result->header_text = $document_standard ? $document_standard->header_text : '';
        $final_result->footer_text = $document_standard ? $document_standard->footer_text : '';
        $final_result->section_total_amount = $totalAmountForLump;
        $final_result->items = $groupBySection;
        $final_result->totalSectionDescriptions = $totalSectionDescriptions;

        return $final_result;
    }

    public function getCancellationItems($projectId)
    {
        $project = ProjectEloquentModel::with('company')->find($projectId);

        $gst = $project->company->gst;

        $final_result = new stdClass;

        $countCNDocs = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                                                        ->where('type', 'CANCELLATION')
                                                        ->whereNotNull('signed_date')
                                                        ->count();

        //firstly get unsigned cancellation docs for getUpdateItems
        $documentId = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                                                        ->where('type', 'CANCELLATION')
                                                        ->whereNull('signed_date')
                                                        ->pluck('id')
                                                        ->first();

        //check unsigned cancellation docs exists
        if(!$documentId)
        {
            //get signed cancellation latest docs for create
            $documentId = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                                                            ->where('type', 'CANCELLATION')
                                                            ->whereNotNull('signed_date')
                                                            ->pluck('id')
                                                            ->last();
        }

        //get signed QO docs
        $sign_quotation_id = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                                                    ->where('type', 'QUOTATION')
                                                    ->whereNotNull('signed_date')
                                                    ->pluck('id')
                                                    ->first();

        //if signed or unsigned FOC docs exists insert that id, or not insert signed QO id for sorting index
        $documentId = $documentId ? $documentId : $sign_quotation_id;

        $sectionIndexArray = SectionsIndexEloquentModel::where('document_id', $documentId)
                                                      ->pluck('section_sequence')
                                                      ->first();

        //Convert back into array
        $sectionIndexArray = json_decode($sectionIndexArray);

        //Initalize sections data from quotation
        $sectionsData = $this->initializeSectionsDataFromQuotation($sign_quotation_id, $documentId, $sectionIndexArray);

        // return $sectionsData;

        //Get all documents , earliest to latest
        $renovationDocuments = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                                                                ->whereNotNull('signed_date')
                                                                ->where('type', '!=', 'QUOTATION')
                                                               ->orderBy('signed_date', 'asc')
                                                                ->get();


        $signedItemArray = [];
        if($renovationDocuments)
        {
            foreach($renovationDocuments as $renovationDocument)
            {
                $documentId = $renovationDocument->id;

                switch ($renovationDocument->type) {

                    case 'VARIATIONORDER':

                        $renoItems = RenovationItemsEloquentModel::where('renovation_document_id', $documentId)
                                                                 ->get();

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

                            //get all items id from section data
                            // $item = collect($sectionsData)
                            //         ->flatMap(function ($section) {
                            //             return $section->area_of_works;
                            //         })
                            //         ->flatMap(function ($aow) {
                            //             return $aow->items;
                            //         })
                            //         ->firstWhere('id', $renoItem->quotation_template_item_id);

                            $item = null;
                            foreach ($sectionsData as $section) {
                                foreach ($section->area_of_works as $aow) {
                                    $item = $this->findItemById($aow->items, $renoItem->quotation_template_item_id);
                                    if ($item) {
                                        break 2; // break both foreach loops
                                    }
                                }
                            }

                            //if exists update existing items
                            if($item)
                            {

                                $signedItemArray[] = $renoItem;
                                $item->name = $renoItem->name;
                                $item->quantity = $renoItem->quantity;
                                $item->lengthmm = $renoItem->length;
                                $item->breadthmm = $renoItem->breadth;
                                $item->heightmm = $renoItem->height;
                                $item->price = $renoItem->price;
                                $item->cost_price = $renoItem->cost_price;
                                $item->profit_margin = $renoItem->profit_margin;
                                $item->is_FOC = $renoItem->is_FOC;
                                $item->measurement = $renoItem->unit_of_measurement;
                                $item->is_fixed_measurement = $renoItem->is_fixed_measurement;
                                $item->sub_description = $renoItem->sub_description;

                                $item->is_signed_in_QO_or_VO = true;
                            }else{

                                //or not add new items function private
                                $this->initializeNewVariationData($sectionsData, $renoItem);

                            }
                        }
                    break;

                    case 'CANCELLATION' :
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

                            foreach ($section->area_of_works as $aow) {
                                foreach ($renoItems as $renoItem) {
                                    $this->updateStatus($aow->items, $renoItem);
                                }
                            }
                        }

                        // foreach($sectionsData as $section)
                        // {
                        //     foreach($section->area_of_works as $aow)
                        //     {
                        //         foreach($renoItems as $renoItem)
                        //         {

                        //             foreach($aow->items as $item)
                        //             {
                        //                 //If found, means update the item
                        //                 if($item->id == $renoItem->quotation_template_item_id)
                        //                 {
                        //                     $item->quantity -= $renoItem->quantity;
                        //                     $signedItemArray[] = $renoItem;

                        //                     $item->isChecked = true;
                        //                 }
                        //             }
                        //         }
                        //     }
                        // }
                    break;
                }
            }
        }

        $unsignedCNDocuments = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                                                                ->where('type', 'CANCELLATION')
                                                                ->whereNull('signed_date')
                                                                ->first();

        if($unsignedCNDocuments)
        {
            $unsigned_renoItems = RenovationItemsEloquentModel::with('renovation_sections')->where('renovation_document_id', $unsignedCNDocuments->id)->get();

            foreach ($unsigned_renoItems as $renoItem) {

                collect($sectionsData)->each(function ($sectionData) use ($renoItem) {
                    $sectionData->total_section_price = $sectionData->id === $renoItem->renovation_sections->section_id
                        ? $renoItem->renovation_sections->total_price
                        : $sectionData->total_section_price;
        
                    $sectionData->total_section_cost_price = $sectionData->id === $renoItem->renovation_sections->section_id
                        ? $renoItem->renovation_sections->total_cost_price
                        : $sectionData->total_section_cost_price;

                    $sectionData->is_page_break = $sectionData->id === $renoItem->renovation_sections->section_id
                        ? $renoItem->renovation_sections->is_page_break
                        : $sectionData->is_page_break ?? false;
                });

                $item = collect($sectionsData)
                ->flatMap(function ($section) {
                    return $section->area_of_works;
                })
                ->flatMap(function ($aow) {
                    return $aow->items;
                })
                ->firstWhere('id', $renoItem->quotation_template_item_id);

                // Update items in the hierarchy recursively
                $itemFound = false;
                foreach ($sectionsData as $section) {
                    foreach ($section->area_of_works as $aow) {
                        $itemFound = $this->updateItemRecursive($aow->items, $renoItem);
                        if ($itemFound) break; // Stop if item is found and updated
                    }
                    if ($itemFound) break; // Stop if item is found and updated
                }
        
                // If item is not found, initialize new item data
                if (!$itemFound) {
                    $this->initializeNewVariationData($sectionsData, $renoItem);
                }

                // $item = $this->updateItemRecursive($item,$renoItem);

            }
        }

        //remove unnessary section, area_of_works and items in array based on is_sigined_in_QO_or_VO (refactor code)
        $sectionsData = collect($sectionsData)->map(function ($sectionData) {
            $sectionData->area_of_works = collect($sectionData->area_of_works)->map(function ($aow) {
                // dd($aow->items);
                $aow->items = collect($aow->items)->filter->is_signed_in_QO_or_VO->values()->all();
                return $aow;
            })->reject(function ($aow) {
                return empty($aow->items);
            })->values()->all();

        return $sectionData;

        })->reject(function ($sectionData) {
            return empty($sectionData->area_of_works);
        })->values()->all();

        // adding shouldShowInSummary in Section & Area Of Work
        foreach ($sectionsData as $section) {
            foreach ($section->area_of_works as $area_of_work) {
                foreach ($area_of_work->items as $item) {
                    if(isset($item->shouldShowInSummary)){
                        $section->shouldShowInSummary = $item->shouldShowInSummary;
                        $area_of_work->shouldShowInSummary = $item->shouldShowInSummary;
                        break;
                    }
                }
            }
        }

        $final_result->reno_document_id = $unsignedCNDocuments ? $unsignedCNDocuments->id : '';
        $final_result->version = "/CN" . ($countCNDocs + 1);
        $final_result->gst = $gst;
        $final_result->singed_items =  $signedItemArray ?? [];
        $final_result->sectionsData = $sectionsData;
        $final_result->special_discount_percentage = $unsignedCNDocuments ? $unsignedCNDocuments->special_discount_percentage : 0;
        $final_result->remark = $unsignedCNDocuments ? $unsignedCNDocuments->remark : '';

        return $final_result;
    }

    public function updateItemRecursive($items, $renoItem) {

        foreach ($items as $item) {
            if ($item->id === $renoItem->quotation_template_item_id) {
                // Update properties for the current item
                $item->name = $renoItem->name;
                $item->quantity = $renoItem->is_excluded == 1 ? $item->quantity : $item->quantity - $renoItem->quantity;
                $item->lengthmm = isset($renoItem->length) ? $renoItem->length : null;
                $item->breadthmm = isset($renoItem->breadth) ? $renoItem->breadth : null;
                $item->heightmm = isset($renoItem->height) ? $renoItem->height : null;
                $item->price = $renoItem->price;
                $item->cost_price = $renoItem->cost_price;
                $item->profit_margin = $renoItem->profit_margin;
                $item->is_FOC = $renoItem->is_FOC;
                $item->measurement = $renoItem->unit_of_measurement;
                $item->is_fixed_measurement = $renoItem->is_fixed_measurement;
                $item->showInSummary = isset($renoItem->is_excluded) ? true : false; // to show in Summary Page for item that was not checked but its sub-item is checked
                $item->already_selected_quantity = isset($renoItem->quantity) && $renoItem->is_excluded == 0 ? $renoItem->quantity : 0;
                $item->is_excluded = $renoItem->is_excluded;
                $item->original_quantity = $item->quantity + $renoItem->quantity;
                $item->isChecked = true;
                $item->already_stored = isset($renoItem->is_excluded) ? true : false;
                $item->sub_description = $renoItem->sub_description;
        
                // $item->already_selected_in_cancellation = false;
                // $item->canRestore = false;
                // if ((int)$item->quantity === 0) {
                    $item->already_selected_in_cancellation = true;
                    $item->canRestore = true;
                // }
                return true; // Return true if the item is found and updated
            }
            
            // Recursively update child items
            if (!empty($item->items)) {
                $found = $this->updateItemRecursive($item->items, $renoItem);
                if ($found) return true; // If found in children, stop further searching
            }
        }
    
        return false; // Return false if the item is not found
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
                $sectionData->is_page_break = $section->is_page_break ?? false;
                $sectionData->area_of_works = [];

                $aowIndexArray = json_decode($aowIndexes->get($sectionId, "[]"));


                foreach ($aowIndexArray as $aowId) {
                    $area_of_works = $aowsArray->get($aowId) ?: $aowFallbacks->get($aowId);

                    if ($area_of_works) {
                        $aowData = new stdClass;
                        $aowData->id = $aowId;
                        $aowData->name = $area_of_works->name;
                        $aowData->isChecked = false;
                        $aowData->items = [];

                        $itemIndexArray = json_decode($itemIndexes->get($aowId, "[]"));

                        // -- Removing unselected sub-items -- //
                        //-- Start -- //

                        // Convert the JSON string to an associative array
                        $firstArray = json_decode($renoItems, true);

                        // Get the keys from the first array
                        $firstArrayKeys = array_keys($firstArray);

                        // Use array_intersect to keep only the values in $secondValue that exist in $firstArrayKeys
                        $filteredArray = array_intersect($itemIndexArray, $firstArrayKeys);

                        // Optionally re-index the array if needed
                        $itemIndexArray = array_values($filteredArray);

                        //-- End -- //

                        foreach ($itemIndexArray as $itemId) {


                            $itemData = new stdClass;
                            $itemData->id = $itemId;
                            $itemData->from_quotation = true;

                            $item = $renoItems->get($itemId) ?: $quotationItems->get($itemId);
                          
                            if ($item) {
                                $itemData->is_signed_in_QO_or_VO = isset($renoItems[$itemId]);
                                $itemData->document_item_id = isset($renoItems[$itemId]) ? $item->id : null;
                                $itemData->name = $item->name ?: $item->description;
                                $itemData->quantity = $item->quantity;
                                $itemData->lengthmm = $item->length;
                                $itemData->breadthmm = $item->breadth;
                                $itemData->heightmm = $item->height;
                                $itemData->price = $item->price ?: $item->price_with_gst;
                                $itemData->cost_price = $item->cost_price;
                                $itemData->profit_margin = $item->profit_margin;
                                $itemData->measurement = $item->unit_of_measurement;
                                $itemData->is_fixed_measurement = $item->is_fixed_measurement;
                                $itemData->is_FOC = $item->is_FOC ?: false;
                                $itemData->parent_id = $item->parent_id;
                                $itemData->sub_description = $item->sub_description;

                            } else {
                                $itemData->is_signed_in_QO_or_VO = false;
                                $itemData->document_item_id = null;
                            }

                            $itemData->already_selected_in_cancellation = false;
                            $itemData->is_selected_in_cancellation = false;

                            $aowData->items[] = $itemData;
                        }

                        $aowData->items = $this->buildItemHierarchy($aowData->items);
                        $sectionData->area_of_works[] = $aowData;
                    }
                }
                $sectionsData[] = $sectionData;
            }
            
        }
        
        return $sectionsData;
    }

    // initilize new data from previous signed VO
    private function initializeNewVariationData (&$sectionsData, $renoItem)
    {
        $sectionId = $renoItem->renovation_sections->section_id; //get section id from new items

        $aowId = $renoItem->renovation_area_of_work->section_area_of_work_id; //get aow id from new items

        $filta_section_data = collect($sectionsData)->firstWhere('id', $sectionId); //filte original section data by section id

        if(!$filta_section_data)
        {
            $documentId = $renoItem->renovation_document_id;

            //Check if section got any selected item based on if reno section got data
            $section = RenovationSectionsEloquentModel::where('section_id', $sectionId)
                                                    ->where('document_id', $documentId)
                                                    ->first();

            //If don't have, find from section table
            if(!$section){
                $section = SectionsEloquentModel::where('id', $sectionId)
                                                ->first();
            }

            $sectionData = new stdClass;

            $sectionData->id = $sectionId;
            $sectionData->name = $section->name;
            $sectionData->total_section_price =  0;
            $sectionData->total_section_cost_price = 0;
            $sectionData->calculation_type = $section->calculation_type;
            $sectionData->description = $section->description;
            $sectionData->area_of_works = [];

            $aowIndexArray = AOWIndexEloquentModel::where('document_id', $documentId)
                                                ->where('section_id', $sectionId)
                                                ->pluck('aow_sequence')
                                                ->first();

            $aowIndexArray = json_decode($aowIndexArray);

            foreach($aowIndexArray as $aowId)
            {
                //Check if aow got any selected item
                $area_of_works = RenovationAreaOfWorkEloquentModel::where('document_id', $documentId)
                                                        ->where('section_area_of_work_id', $aowId)
                                                        ->first();

                if(!$area_of_works){
                    $area_of_works = SectionAreaOfWorkEloquentModel::where('id', $aowId)
                                                        ->first();
                }


                $aowData = new stdClass;

                $aowData->id = $aowId;
                $aowData->name = $area_of_works->name;
                $aowData->items = [];

                $itemIndexArray = ItemsIndexEloquentModel::where('document_id', $documentId)
                                                        ->where('aow_id', $aowId)
                                                        ->pluck('items_sequence')
                                                        ->first();

                $itemIndexArray = json_decode($itemIndexArray);

                foreach($itemIndexArray as $itemId)
                {

                    $itemData = new stdClass;

                    $itemData->id = $itemId;
                    $itemData->from_quotation = true;  // (1/7)

                    //Check if item is selected
                    $item = RenovationItemsEloquentModel::where('renovation_document_id', $documentId)
                                                        ->where('quotation_template_item_id', $itemId)
                                                        ->first();

                    //If no item found, means not checked
                    if(!$item){
                        $item = QuotationTemplateItemsEloquentModel::where('id', $itemId)
                                                                    ->first();

                        $itemData->is_signed_in_QO_or_VO = false; // (2/7)
                        $itemData->document_item_id = null;
                    }
                    else{
                        $itemData->is_signed_in_QO_or_VO = true; // (2/7)
                        $itemData->document_item_id = $item->id;
                    }

                    $itemData->name = isset($item->name) ? $item->name : $item->description;
                    $itemData->quantity = $item->quantity;
                    $itemData->lengthmm = isset($item->length) ? $item->length : null;
                    $itemData->breadthmm = isset($item->breadth) ? $item->breadth : null;
                    $itemData->heightmm = isset($item->height) ? $item->heigth : null;
                    $itemData->price = isset($item->price) ? $item->price : $item->price_with_gst;
                    $itemData->cost_price = $item->cost_price;
                    $itemData->profit_margin = $item->profit_margin;
                    $itemData->measurement = $item->unit_of_measurement;
                    $itemData->is_fixed_measurement = $item->is_fixed_measurement;
                    $itemData->sub_description = $item->sub_description;

                    $itemData->already_selected_in_cancellation = false;
                    $itemData->is_selected_in_cancellation = false;

                    $itemData->is_FOC = $item->is_FOC ?: false;
                    $itemData->parent_id = $item->parent_id;

                    $aowData->items[] = $itemData;
                }

                // $aowData->items = $this->buildItemHierarchy($aowData->items);
                $sectionData->area_of_works[] = $aowData;
            }

            $sectionsData[] = $sectionData;

        }

        // $filta_section = collect($sectionsData)->firstWhere('id', $sectionId); //filte original section data by section id

        // foreach ($filta_section->area_of_works as $aow) {

        //     //find aow by aowId and insert new items
        //     if($aow->id == $aowId)
        //     {
        //         $itemData = new stdClass;

        //         $itemData->id = $renoItem->quotation_template_item_id;
        //         $itemData->name = $renoItem->name;
        //         $itemData->quantity = $renoItem->quantity;
        //         $itemData->price = $renoItem->price;
        //         $itemData->cost_price = $renoItem->cost_price;
        //         $itemData->profit_margin = $renoItem->profit_margin;
        //         $itemData->measurement = $renoItem->unit_of_measurement;
        //         $itemData->is_fixed_measurement = $renoItem->is_fixed_measurement;

        //         $itemData->is_signed_in_QO_or_VO = true;
        //         $itemData->already_selected_in_cancellation = false;
        //         $itemData->is_selected_in_cancellation = false;

        //         $aow->items[] = $itemData;
        //     }
        // }

        $filta_section = collect($sectionsData)->firstWhere('id', $sectionId);

        foreach ($filta_section->area_of_works as $aow) {
            if ($aow->id == $aowId) {
                if (!$renoItem->parent_id) {
                    // Add top-level item
                    $itemData = $this->createNewItemData($renoItem);
                    $aow->items[] = $itemData;
                } else {
                    // Add nested item
                    $this->addNestedItem($aow->items, $renoItem);
                }
            }
        }

    }

    public function store($renovationDocumentData , $data)
    {
        //save document
        $documentEloquent = RenovationDocumentsMapper::toEloquent($renovationDocumentData);

        $documentEloquent->save();
        $documentEloquent->approvers()->detach();

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

            foreach($section->area_of_works as $aow)
            {
                $aow->document_id = $documentEloquent->id;

                $aow->section_id = $sectionId;

                // Validate area of work and get AOW ID
                $aowId = $this->validateAreaOfWork($aow);

                //push section id into section index array
                $aowSequence[] = $aowId;

                //Reset Item sequence every new AOW
                $itemSequence = [];

                foreach($aow->items as $item){

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
            $documentEloquent->remark = $documentEloquent->remark ?? ''
        );

        return $renovationDocumentData;
    }

    private function createRenovationItem($item, $documentEloquent, $sectionId, $section, $renoSectionIndex, $aowId, $aow, $renoAOWIndex, $itemCheckedCount, &$itemSequence, $parentItem = null)
    {
        $item->document_id = $documentEloquent->id;

        $item->section_id = $sectionId;
        $item->area_of_work_id = $aowId;

        $itemId = $this->validateItem($item, $parentItem);

        //Assign itemId to item id (Only use for newly created item)
        $item->id = $itemId;

        if(isset($itemId))
        {
            $itemSequence[] = $itemId;
        }

        //If item checked, create a reno section, aow and item
        if ((isset($item->isChecked) && $item->isChecked) || (isset($item->showInSummary) && $item->showInSummary) || (isset($item->already_stored))) {
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
            $prevItemId = null;

            $renoItemData = $this->storeRenovationItem($documentEloquent, $item, $itemId, $renoSectionData, $renoAOWData, $cancellationId, $prevItemId, false, false, isset($parentItem) ? $parentItem->quotation_template_item_id : null);
        }

        if (!empty($item->items)) {
            foreach ($item->items as $subItem) {
                if(!empty($subItem->name))
                    $this->createRenovationItem($subItem, $documentEloquent, $sectionId, $section, $renoSectionIndex, $aowId, $aow, $renoAOWIndex, $itemCheckedCount, $itemSequence, isset($renoItemData) ? $renoItemData : null);
            }
        }
    }

    private function storeRenovationItem($documentEloquent, $item, $itemId, $renoSectionData, $renoAOWData, $cancellationId, $prevItemId,$completed, $active, $parentId = null)
    {

        $data = [];

        $existingItem = RenovationItemsEloquentModel::where([
            'renovation_document_id' => $documentEloquent->id,
            'quotation_template_item_id' => $itemId,
        ])->first();

        if(isset($item->canRestore) && !$item->canRestore){

            $existingItem->delete();

            // will run this if the user checked the restored item again
            if((isset($item->isChecked) && $item->isChecked) && (isset($item->showInSummary) && $item->showInSummary)) {
                // Newly Checked Data
                $quantity = 0;

                if(isset($item->quantity))
                {
                    // Add the item's quantity to the existing quantity
                    $quantity += $item->quantity;
                }

                $measurement = isset($item->measurement) ? $item->measurement : null;

                $excluded = 0;

                $data = $this->renovationItemUpdateOrCreateQuery($documentEloquent,$itemId,$parentId,$cancellationId,$prevItemId,$item,$renoSectionData,$renoAOWData,$quantity,$measurement,$completed,$active,$excluded);
            } else {
                $data = $existingItem;
            }

        } else {

            // New Parent Item or data that saved without changing
            if((isset($item->isChecked) && !$item->isChecked) && (isset($item->showInSummary) && $item->showInSummary))
            {

                // New Parent Item
                if(!isset($item->already_stored)){
                    $quantity = 0;

                    if(isset($item->quantity))
                    {
                        // If an existing item is found, use its quantity otherwise, use 0
                        $quantity = $existingItem ? $existingItem->quantity : 0;
                        // Add the item's quantity to the existing quantity
                        $quantity += $item->quantity;
                    }

                    $measurement = isset($item->measurement) ? $item->measurement : null;

                    if((isset($item->isChecked) && $item->isChecked) && (isset($item->showInSummary) && $item->showInSummary)){
                        $excluded = 0;
                    } else {
                        $excluded = 1;
                    }

                    $data = $this->renovationItemUpdateOrCreateQuery($documentEloquent,$itemId,$parentId,$cancellationId,$prevItemId,$item,$renoSectionData,$renoAOWData,$quantity,$measurement,$completed,$active,$excluded);

                } else {
                    // already checked data that changes were not made
                    $data = $existingItem;
                }

            } else if((isset($item->isChecked) && $item->isChecked) && (isset($item->showInSummary) && $item->showInSummary)) {
                // Newly Checked Data
                $quantity = 0;

                if(isset($item->quantity))
                {
                    // If an existing item is found, use its quantity otherwise, use 0
                    // $quantity = $existingItem ? $existingItem->quantity : 0;
                    // Add the item's quantity to the existing quantity
                    $quantity += $item->quantity;
                }

                $measurement = isset($item->measurement) ? $item->measurement : null;

                if((isset($item->isChecked) && $item->isChecked) && (isset($item->showInSummary) && $item->showInSummary)){
                    $excluded = 0;
                } else {
                    $excluded = 1;
                }

                $data = $this->renovationItemUpdateOrCreateQuery($documentEloquent,$itemId,$parentId,$cancellationId,$prevItemId,$item,$renoSectionData,$renoAOWData,$quantity,$measurement,$completed,$active,$excluded);

            }        
        }

        return $data;
    }

    private function renovationItemUpdateOrCreateQuery($documentEloquent,$itemId,$parentId,$cancellationId,$prevItemId,$item,$renoSectionData,$renoAOWData,$quantity,$measurement,$completed,$active,$excluded){

        $data = RenovationItemsEloquentModel::updateOrCreate(
            [
                'renovation_document_id' => $documentEloquent->id,
                'quotation_template_item_id' => $itemId,
            ],
            [
            'renovation_document_id' => $documentEloquent->id,
            'parent_id' => $parentId,
            'cancellation_id' => $cancellationId,
            'prev_item_id' => $prevItemId,
            'project_id' => $documentEloquent->project_id,
            'name' => $item->name,
            'quotation_template_item_id' => isset($itemId) ? $itemId : null,
            'renovation_item_section_id' => isset($renoSectionData->id) ? $renoSectionData->id : null,
            'renovation_item_area_of_work_id' => isset($renoAOWData->id) ? $renoAOWData->id : null,
            'quantity' => $quantity,
            'length' => isset($item->lengthmm) ? $item->lengthmm : null,
            'breadth' => isset($item->breadthmm) ? $item->breadthmm : null,
            'height' => isset($item->lengthmm) ? $item->lengthmm : null,
            'price' => isset($item->price) ? $item->price : null,
            'cost_price' => isset($item->cost_price) ? $item->cost_price : null,
            'profit_margin' => isset($item->profit_margin) ? $item->profit_margin : 0,
            'is_FOC' => isset($item->isFOC) ? $item->isFOC : 0,
            'unit_of_measurement' => $measurement,
            'is_fixed_measurement' => isset($item->is_fixed_measurement) ? $item->is_fixed_measurement : 0,
            'completed' => $completed,
            'active' => $active,
            'is_excluded' => $excluded,
            'sub_description' => $item->sub_description
        ]);

        return $data;

    }

    //Check if section exist check if section exist, but different calc type
    private function validateSection($section)
    {
        //Also check if calc type same incase user change calc type
        $sectionData = SectionsEloquentModel::where('id', isset($section->id) ? $section->id : null)
                                            // ->where('calculation_type', $section->calculation_type)
                                            ->first();

        if (!$sectionData) {
            $sectionData = SectionsEloquentModel::create([
                'name' => $section->name,
                'calculation_type' => $section->calculation_type,
                'is_active' => 0,
                'document_id' => $section->document_id,
                'index' => 0,
                'is_misc' => 0,
                'is_page_break' => $section->is_page_break ?? false
            ]);
        }

        return $sectionData->id;
    }

    private function validateAreaOfWork($aow)
    {
        //If no ID means newly created AOW
        $aowData = SectionAreaOfWorkEloquentModel::where('id', isset($aow->id) ? $aow->id : null)
                                                ->first();

        return $aowData->id;
    }

    private function validateItem($item)
    {
        //No ID means newly created item
        $itemData =  QuotationTemplateItemsEloquentModel::find($item->id,['id']);


        if (!$itemData) {
            $itemData = QuotationTemplateItemsEloquentModel::create([
                'document_id' => $item->document_id,
                'description' => $item->name,
                'index' => 0,
                'quantity' => $item->quantity,
                'unit_of_measurement' => $item->measurement,
                'is_fixed_measurement' => $item->is_fixed_measurement,
                'section_id' => $item->section_id,
                'area_of_work_id' => $item->area_of_work_id,
                'price_without_gst' => isset($item->price) ?  $item->price : 0,
                'price_with_gst' => $item->price,
                'cost_price' =>  isset($item->cost_price) ? $item->cost_price : 0,
                'profit_margin' => isset($item->profit_margin) ? $item->profit_margin : 0,
                'sub_description' => $item->sub_description
            ]);
        }

        return $itemData->id;

    }

    private function countItemChecked($section)
    {
        $checkedCount = 0;

        foreach($section->area_of_works as $aow) {
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

        // if($renoSectionData) {

        //     if($sectionCalculated){
        //         if($method != 'put'){
        //             $renoSectionData->total_price =  $section->total_section_price;
        //             $renoSectionData->total_cost_price = $section->total_section_cost_price;
        //             $renoSectionData->update();
        //         }
        //     } else {
        //         $renoSectionData->total_price +=  $section->total_section_price;
        //         $renoSectionData->total_cost_price += $section->total_section_cost_price;
        //         $renoSectionData->update();
        //     }
        
        // }

        if ($renoSectionData) {
            $renoSectionData->total_price =  $section->total_section_price;
            $renoSectionData->total_cost_price = $section->total_section_cost_price;
            $renoSectionData->is_page_break = $section->is_page_break ?? false;
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
                'is_page_break' => $section->is_page_break ?? false

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

    // private function createRenovationItem($documentEloquent, $item, $itemId, $renoSectionData, $renoAOWData, $cancellationId ,$prevItemId, $completed, $active, $parentItem = null)
    // {

    //     /**
    //      * if exist qty already reduce from that qty
    //      */
    //     $existingItem = RenovationItemsEloquentModel::where([
    //         'renovation_document_id' => $documentEloquent->id,
    //         'quotation_template_item_id' => $itemId,
    //     ])->first();

    //     $quantity = 0;

    //     if(isset($item->quantity))
    //     {
    //         // If an existing item is found, use its quantity otherwise, use 0
    //         $quantity = $existingItem ? $existingItem->quantity : 0;
    //         // Add the item's quantity to the existing quantity
    //         $quantity += $item->quantity;
    //     }

    //     if(isset($item->isChecked) && $item->isChecked){
    //         $data = RenovationItemsEloquentModel::updateOrCreate(
    //             [
    //                 'renovation_document_id' => $documentEloquent->id,
    //                 'quotation_template_item_id' => $itemId,
    //                 'name' => $item->name, // temporary fix for updating the the value with sub-item in cancellation create
    //             ],
    //             [
    //             'renovation_document_id' => $documentEloquent->id,
    //             'cancellation_id' => $cancellationId,
    //             'prev_item_id' => $prevItemId,
    //             'project_id' => $documentEloquent->project_id,
    //             'name' => $item->name,
    //             'quotation_template_item_id' => isset($itemId) ? $itemId : null,
    //             'renovation_item_section_id' => isset($renoSectionData->id) ? $renoSectionData->id : null,
    //             'renovation_item_area_of_work_id' => isset($renoAOWData->id) ? $renoAOWData->id : null,
    //             'quantity' => $quantity,
    //             'price' => isset($item->price) ? $item->price : null,
    //             'cost_price' => isset($item->cost_price) ? $item->cost_price : null,
    //             'profit_margin' => isset($item->profit_margin) ? $item->profit_margin : 0,
    //             'is_FOC' => isset($item->isFOC) ? $item->isFOC : 0,
    //             'unit_of_measurement' => isset($item->measurement) ? $item->measurement : null,
    //             'is_fixed_measurement' => isset($item->is_fixed_measurement) ? $item->is_fixed_measurement : 0,
    //             'completed' => $completed,
    //             'active' => $active,
    //             'parent_id' => isset($parentItem) ? $parentItem->quotation_template_item_id : null
    //         ]);
    //     } else {
    //         $data = $parentItem;
    //     }

    //     if (!empty($item->items)) {
            
    //         foreach ($item->items as $subItem) {
    //             $this->createRenovationItem($documentEloquent, $subItem, $itemId, $renoSectionData, $renoAOWData, $cancellationId ,$prevItemId, $completed, $active, isset($data) ? $data : null);
    //         }
    //     }

    //     return $data;
    // }

    private function buildItemHierarchy($items)
    {

        $itemMap = [];
        $rootItems = [];
        // First pass: create item map
        foreach ($items as $item) {            
            $itemData = (object) [
                'id' => $item->id,
                'parent_id' => $item->parent_id,
                'quantity' => $item->quantity,
                'lengthmm' => $item->lengthmm,
                'breadthmm' => $item->breadthmm,
                'heightmm' => $item->heightmm,
                'name' => $item->name,
                'price' => $item->price,
                'cost_price' => $item->cost_price,
                'profit_margin' => $item->profit_margin,
                'measurement' => $item->measurement,
                'is_fixed_measurement' => $item->is_fixed_measurement,
                // 'isChecked' => $item->isChecked,
                // 'isNew' => $item->isNew,
                'is_FOC' => $item->is_FOC,
                'is_signed_in_QO_or_VO' => $item->is_signed_in_QO_or_VO,
                'document_item_id' => $item->document_item_id,
                'items' => [],
                'isChecked' => false,
                'sub_description' => $item->sub_description
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

    function updateStatus($items, $renoItem) {
        foreach ($items as $item) {
            // If found, means edited item, update the item
            if ($item->id == $renoItem->quotation_template_item_id) {

                $item->quantity -= $renoItem->quantity;
                $signedItemArray[] = $renoItem;

                $item->isChecked = false;
                $item->isCancel = $item->quantity == 0 ? true : false;
                // $item->is_FOC = true;
            }
            if (!empty($item->items)) {
                $this->updateStatus($item->items, $renoItem);
            }
        }
    }

    private function createNewItemData($renoItem){
        $itemData = new stdClass;
    
        $itemData->id = $renoItem->quotation_template_item_id;
        $itemData->name = $renoItem->name;
        $itemData->quantity = $renoItem->quantity;
        $itemData->lengthmm = isset($renoItem->length) ? $renoItem->length : null;
        $itemData->breadthmm = isset($renoItem->breadth) ? $renoItem->breadth : null;
        $itemData->heightmm = isset($renoItem->height) ? $renoItem->height : null;
        $itemData->price = $renoItem->price;
        $itemData->cost_price = $renoItem->cost_price;
        $itemData->profit_margin = $renoItem->profit_margin;
        $itemData->measurement = $renoItem->unit_of_measurement;
        $itemData->is_fixed_measurement = $renoItem->is_fixed_measurement;
    
        // $itemData->is_edited = false;
        // $itemData->from_quotation = false;
        $itemData->is_FOC = $renoItem->is_FOC;
        // $itemData->is_Cancellation = false;
        // $itemData->is_selected_in_quotation = false;
        // $itemData->is_selected_in_foc = false;
        // $itemData->is_signed_in_VO = true;
        // $itemData->is_show_in_summary = $renoItem->is_excluded ? true : false;
        // $itemData->is_signed_in_QO_or_VO = $renoItem->is_signed_in_QO_or_VO;
        $itemData->is_signed_in_QO_or_VO = true;
        $itemData->document_item_id = $renoItem->document_item_id;
        $itemData->isChecked = false;
        $itemData->sub_description = $renoItem->sub_description;
    
        $itemData->items = [];
    
        return $itemData;
    }

    private function addNestedItem(&$items, $renoItem){
        foreach ($items as &$item) {
            if ($item->id == $renoItem->parent_id) {
                $itemData = $this->createNewItemData($renoItem);
                $item->items[] = $itemData;
                return true;
            }
    
            if (!empty($item->items)) {
                // Recursively search in child items
                if ($this->addNestedItem($item->items, $renoItem)) {
                    return true;
                }
            }
        }
    
        return false;
    }
}
