<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

// use Illuminate\Support\Facades\Log;
use Src\Company\Document\Application\DTO\RenovationDocumentData;
use Src\Company\Document\Application\Mappers\RenovationDocumentsMapper;
use Src\Company\Document\Domain\Repositories\VariationRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationItemsEloquentModel;

use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;


use Src\Company\Document\Infrastructure\EloquentModels\SectionsIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\AOWIndexEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ItemsIndexEloquentModel;
use stdClass;

class VariationRepository implements VariationRepositoryInterface
{
    public function index($projectId)
    {
        $final_result = [];

        $data = RenovationDocumentsEloquentModel::where([
            ['project_id', $projectId],
            ['type', 'VARIATIONORDER']
        ])->get(['id', 'version_number', 'signed_date']);

        foreach ($data as $item) {

            $countForItems = RenovationItemsEloquentModel::where('renovation_document_id', $item->id)->count();

            $obj = new stdClass;
            $obj->id = $item->id;
            $obj->version_number = $item->version_number;
            $obj->signed_date = $item->signed_date;
            $obj->total_items = $countForItems;

            array_push($final_result, $obj);
        }

        return $final_result;
    }

    public function getCountLists($projectId)
    {
        $countResult = RenovationDocumentsEloquentModel::where([
            ['project_id', $projectId],
            ['type', 'VARIATIONORDER']
        ])->count();

        return $countResult;
    }

    public function oldgetVariationItems($projectId, $saleperson_id)
    {
        $authId = auth('sanctum')->user()->id;

        $variation_items = [];

        $project = ProjectEloquentModel::with('company')->find($projectId);

        $statusCompanyGstNo = $project->company->gst_reg_no;

        //===============================To check master or salesperson template=============================
        //Get renovation document based on project id
        $renovation_document = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->whereNotNull('signed_date')
            ->first();

        // $all_renovation_sections = RenovationSectionsEloquentModel::where('document_id', $renovation_document -> id)->get();

        // $totalAmountForLump = [];
        // foreach ($all_renovation_sections as $value) {

        //     $obj = new stdClass;
        //     $obj->section_id = $value->id;
        //     $obj->section_name = $value->sections->name;
        //     $obj->total_cost_price = $value->total_cost_price;

        //     array_push($totalAmountForLump, $obj);
        // }

        $renovationSections = RenovationSectionsEloquentModel::with('sections')->where('document_id', $renovation_document->id)->get();

        $sections = SectionsEloquentModel::get();

        $totalAmountForLump = [];

        //create new arr for total amount of lump sum

        $totalAmountForLump = $sections->flatMap(function ($section) use ($renovationSections) {
            $sameSectionsInRenovation = $renovationSections->where('section_id', $section->id)->first();

            if ($sameSectionsInRenovation) {
                return [
                    (object)[
                        'section_id' => $section->id,
                        'section_name' => $section->name,
                        'calculation_type' => $sameSectionsInRenovation->calculation_type,
                        'section_index' => $section->index,
                        'total_amount' => 0,
                        'total_cost_price' => 0,
                        'section_description' => $sameSectionsInRenovation->description
                    ]
                ];
            } elseif ($section->calculation_type === 'LUMP_SUM') {
                return [
                    (object)[
                        'section_id' => $section->id,
                        'section_name' => $section->name,
                        'calculation_type' => $section->calculation_type,
                        'section_index' => $section->index,
                        'total_amount' => 0,
                        'total_cost_price' => 0,
                        'section_description' => $section->description

                    ]
                ];
            }

            return [];
        });

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

        // $templateItems = QuotationTemplateItemsEloquentModel::with('sections', 'areaOfWorks')->where('salesperson_id', $section->salesperson_id)->get()->sortBy('index');

        $templateItems = QuotationTemplateItemsEloquentModel::whereHas('sections', function ($query) use ($section) {
            $query->where('is_active', 1)
                ->where('quotation_template_id', $section->quotation_template_id);
        })
            ->with('sections')
            ->with('areaOfWorks')
            ->get()
            ->sortBy('index');



        $latestItemsId = collect($templateItems)->max('id');

        foreach ($templateItems as $templateItem) {
            if ($templateItem->sections) {
                $obj = new stdClass;

                $obj->id = $templateItem->id;
                $obj->document_item_id = '';
                $obj->index = $templateItem->index ?? 0;
                $obj->name = $templateItem->description;
                $obj->price = $statusCompanyGstNo ? $templateItem->price_with_gst : $templateItem->price_without_gst;
                $obj->cost_price = $templateItem->cost_price;
                $obj->profit_margin = $templateItem->profit_margin;
                $obj->measurement = $templateItem->unit_of_measurement;;
                $obj->quantity = $templateItem->quantity;
                $obj->section = $templateItem->sections->index;
                $obj->is_misc = $templateItem->sections->is_misc;
                $obj->sectionId = $templateItem->sections->id;
                $obj->sectionName = $templateItem->sections->name;
                $obj->section_description = $templateItem->sections->description;
                $obj->is_fixed_measurement = $templateItem->is_fixed_measurement ?? 0;
                $obj->calculation_type = $templateItem->sections->calculation_type;
                $obj->area_of_work = $templateItem->areaOfWorks->index ?? null;
                $obj->area_of_work_id = $templateItem->areaOfWorks->id ?? null;
                $obj->area_of_work_name = $templateItem->areaOfWorks->name ?? "";
                $obj->isFOC = false;
                $obj->isEdit = true;
                $obj->isOrigin = true;
                $obj->isCancel = false;
                $obj->isChecked = false;
                $obj->sub_description = $templateItem->sub_description;


                array_push($variation_items, $obj);
            }
        }

        //Getting sign quotation document items
        $signed_quotation_document = RenovationDocumentsEloquentModel::where('project_id', $projectId)->where('type', 'QUOTATION')->whereNotNull('signed_date')->first(['id']);

        $signed_quotation_items = RenovationItemsEloquentModel::with('quotation_items', 'renovation_sections', 'renovation_area_of_work')->where('renovation_document_id', $signed_quotation_document->id)->get();

        foreach ($signed_quotation_items as $quotaiton_item) {
            $check_items_by_sign_quotation = in_array($quotaiton_item->quotation_template_item_id, array_column($variation_items, 'id'));
            // check if id of item in template matches id of quotation item and must be non null as many items can have null template id
            if ($check_items_by_sign_quotation && $quotaiton_item->quotation_template_item_id) {
                // if a match is found, store the id
                $search_items_by_sign_quotation = array_search($quotaiton_item->quotation_template_item_id, array_column($variation_items, 'id'));
                // find for the object that has the matching id
                $search_result = $variation_items[$search_items_by_sign_quotation];
                // check if area of work is set; if yes, populate $search_result's area_of_work_name with that value
                if (isset($quotaiton_item->renovation_area_of_work) && isset($quotaiton_item->renovation_area_of_work->name)) {
                    $areaOfWorkName = $quotaiton_item->renovation_area_of_work->name;
                    $search_result->area_of_work_name = $areaOfWorkName;
                }

                $search_result->document_item_id = $quotaiton_item->id;
                $search_result->price = $quotaiton_item->price;
                $search_result->cost_price = $quotaiton_item->cost_price;
                $search_result->profit_margin = $quotaiton_item->profit_margin;
                $search_result->measurement = $quotaiton_item->unit_of_measurement;
                $search_result->is_fixed_measurement = $quotaiton_item->is_fixed_measurement ?? 0;
                $search_result->quantity = $quotaiton_item->quantity;
                $search_result->isFOC = $quotaiton_item->is_FOC == 1 ? true : false;
                $search_result->isEdit = true;
                $search_result->isOrigin = false;
                $search_result->isCancel = false;
                $search_result->isChecked = false;
                $search_result->sub_description = $quotaiton_item->sub_description;

            } else {

                $obj = new stdClass;
                $obj->id = $quotaiton_item->quotation_template_item_id ?? null;
                $obj->document_item_id = $quotaiton_item->id;
                $obj->name = $quotaiton_item->name;
                $obj->price = $quotaiton_item->price;
                $obj->cost_price = $quotaiton_item->cost_price;
                $obj->profit_margin = $quotaiton_item->profit_margin;
                $obj->measurement = $quotaiton_item->unit_of_measurement;
                $obj->quantity = $quotaiton_item->quantity;
                $obj->section = $quotaiton_item->renovation_sections->sections->index;
                $obj->is_misc = $quotaiton_item->renovation_sections->sections->is_misc;
                $obj->sectionId = $quotaiton_item->renovation_sections->sections->id;
                $obj->sectionName = $quotaiton_item->renovation_sections->sections->name;
                $obj->section_description = $quotaiton_item->renovation_sections->sections->description;
                $obj->is_fixed_measurement = $quotaiton_item->is_fixed_measurement ?? 0;
                $obj->calculation_type = $quotaiton_item->renovation_sections->sections->calculation_type;
                $obj->area_of_work = $quotaiton_item->renovation_item_area_of_work_id ? $quotaiton_item->renovation_area_of_work->areaOfWork->index : null;
                $obj->area_of_work_id = $quotaiton_item->renovation_item_area_of_work_id ? $quotaiton_item->renovation_area_of_work->areaOfWork->id : null;
                $obj->area_of_work_name = $quotaiton_item->renovation_item_area_of_work_id ? $quotaiton_item->renovation_area_of_work->name : "";
                $obj->isFOC = $quotaiton_item->is_FOC == 1 ? true : false;
                $obj->isEdit = true;
                $obj->isOrigin = false;
                $obj->isCancel = false;
                $obj->isChecked = false;
                $obj->sub_description = $quotaiton_item->sub_description;
                array_push($variation_items, $obj);
            }
        }

        //Getting sign variation documents items
        $signed_variation_items = RenovationDocumentsEloquentModel::with('renovation_items')->where('project_id', $projectId)->where('type', 'VARIATIONORDER')->whereNotNull('signed_date')->get(['id']);

        if (!$signed_variation_items->isEmpty()) {
            foreach ($signed_variation_items as $signed_variation_item) {

                foreach ($signed_variation_item->renovation_items as $variation_item) {

                    //check if that items has prev item id
                    if ($variation_item->prev_item_id) {

                        //check this items includes in variation items array and store
                        $check_items_by_sign_variation = in_array($variation_item->prev_item_id, array_column($variation_items, 'document_item_id'));

                        if ($check_items_by_sign_variation) {
                            $search_items_by_sign_variation = array_search($variation_item->prev_item_id, array_column($variation_items, 'document_item_id'));

                            $search_result = $variation_items[$search_items_by_sign_variation];

                            $search_result->document_item_id = $variation_item->id;
                            $search_result->name = $variation_item->name;
                            $search_result->price = $variation_item->price;
                            $search_result->cost_price = $variation_item->cost_price;
                            $search_result->profit_margin = $variation_item->profit_margin;
                            $search_result->measurement = $variation_item->unit_of_measurement;
                            $search_result->is_fixed_measurement = $variation_item->is_fixed_measurement ?? 0;
                            $search_result->quantity = $variation_item->quantity;
                            $search_result->isFOC = $variation_item->is_FOC == 1 ? true : false;
                            $search_result->isEdit = true;
                            $search_result->isOrigin = false;
                            $search_result->isCancel = false;
                            $search_result->isChecked = false;
                            $search_result->sub_description = $variation_item->sub_description;
                        } else {

                            $obj = new stdClass;
                            $obj->id = $variation_item->quotation_template_item_id ?? null;
                            $obj->document_item_id = $variation_item->id;
                            $obj->name = $variation_item->name;
                            $obj->price = $variation_item->price;
                            $obj->cost_price = $variation_item->cost_price;
                            $obj->profit_margin = $variation_item->profit_margin;
                            $obj->measurement = $variation_item->unit_of_measurement;
                            $obj->is_fixed_measurement = $variation_item->is_fixed_measurement ?? 0;
                            $obj->quantity = $variation_item->quantity;
                            $obj->section = $variation_item->renovation_sections->sections->index;
                            $obj->sectionId = $variation_item->renovation_sections->sections->id;
                            $obj->sectionName = $variation_item->renovation_sections->sections->name;
                            $obj->section_description = $variation_item->renovation_sections->sections->description;

                            $obj->calculation_type = $variation_item->renovation_sections->sections->calculation_type;
                            $obj->area_of_work = $variation_item->renovation_item_area_of_work_id ? $variation_item->renovation_area_of_work->areaOfWork->index : null;
                            $obj->area_of_work_id = $variation_item->renovation_item_area_of_work_id ? $variation_item->renovation_area_of_work->areaOfWork->id : null;
                            $obj->area_of_work_name = $variation_item->renovation_item_area_of_work_id ? $variation_item->renovation_area_of_work->areaOfWork->name : "";
                            $obj->isFOC = $variation_item->is_FOC == 1 ? true : false;
                            $obj->isEdit = true;
                            $obj->isOrigin = false;
                            $obj->isCancel = false;
                            $obj->isChecked = false;
                            $obj->sub_description = $variation_item->sub_description;

                            array_push($variation_items, $obj);
                        }
                    } else {

                        if ($variation_item->quotation_template_item_id) {
                            $search_items_by_sign_variation = array_search($variation_item->quotation_template_item_id, array_column($variation_items, 'id'));

                            $search_result = $variation_items[$search_items_by_sign_variation];

                            $search_result->document_item_id = $variation_item->id;
                            $search_result->name = $variation_item->name;
                            $search_result->price = $variation_item->price;
                            $search_result->cost_price = $variation_item->cost_price;
                            $search_result->profit_margin = $variation_item->profit_margin;
                            $search_result->measurement = $variation_item->unit_of_measurement;
                            $search_result->is_fixed_measurement = $variation_item->is_fixed_measurement ?? 0;
                            $search_result->quantity = $variation_item->quantity;
                            $search_result->isFOC = $variation_item->is_FOC == 1 ? true : false;
                            $search_result->isEdit = true;
                            $search_result->isOrigin = false;
                            $search_result->isCancel = false;
                            $search_result->isChecked = false;
                            $search_result->sub_description = $variation_item->sub_description;

                        } else {

                            $obj = new stdClass;
                            $obj->id = null;
                            $obj->document_item_id = $variation_item->id;
                            $obj->name = $variation_item->name;
                            $obj->price = $variation_item->price;
                            $obj->cost_price = $variation_item->cost_price;
                            $obj->profit_margin = $variation_item->profit_margin;
                            $obj->measurement = $variation_item->unit_of_measurement;
                            $obj->is_fixed_measurement = $variation_item->is_fixed_measurement ?? 0;
                            $obj->quantity = $variation_item->quantity;
                            $obj->section = $variation_item->renovation_sections->sections->index;
                            $obj->sectionId = $variation_item->renovation_sections->sections->id;
                            $obj->sectionName = $variation_item->renovation_sections->sections->name;
                            $obj->section_description = $variation_item->renovation_sections->sections->description;
                            $obj->calculation_type = $variation_item->renovation_sections->sections->calculation_type;
                            $obj->area_of_work = $variation_item->renovation_item_area_of_work_id ? $variation_item->renovation_area_of_work->areaOfWork->index : null;
                            $obj->area_of_work_id = $variation_item->renovation_item_area_of_work_id ? $variation_item->renovation_area_of_work->areaOfWork->id : null;
                            $obj->area_of_work_name = $variation_item->renovation_item_area_of_work_id ? $variation_item->renovation_area_of_work->areaOfWork->name : "";
                            $obj->isFOC = $variation_item->is_FOC == 1 ? true : false;
                            $obj->isEdit = true;
                            $obj->isOrigin = false;
                            $obj->isCancel = false;
                            $obj->isChecked = false;
                            $obj->sub_description = $variation_item->sub_description;

                            array_push($variation_items, $obj);
                        }
                    }
                }
            }
        }

        $signed_foc_items = RenovationDocumentsEloquentModel::with('renovation_items')->where('project_id', $projectId)->where('type', 'FOC')->whereNotNull('signed_date')->get(['id']);

        if (!$signed_foc_items->isEmpty()) {
            foreach ($signed_foc_items as $signed_foc_item) {
                foreach ($signed_foc_item->renovation_items as $foc_item) {

                    $search_items_by_sign_foc = array_search($foc_item->quotation_template_item_id, array_column($variation_items, 'id'));

                    $search_result = $variation_items[$search_items_by_sign_foc];

                    $search_result->isFOC = true;
                }
            }
        }

        //Getting sign cancellation document items
        $signed_cancellation_items = RenovationDocumentsEloquentModel::with('renovation_items')->where('project_id', $projectId)->where('type', 'CANCELLATION')->whereNotNull('signed_date')->get(['id']);

        if (!$signed_cancellation_items->isEmpty()) {
            foreach ($signed_cancellation_items as $signed_cancellation_item) {

                foreach ($signed_cancellation_item->renovation_items as $cancellation_item) {

                    $search_items_by_sign_cancellation = array_search($cancellation_item->prev_item_id, array_column($variation_items, 'document_item_id'));

                    $search_result = $variation_items[$search_items_by_sign_cancellation];

                    $search_result->isCancel = true;
                }
            }
        }

        $groupBySection = collect($variation_items)->groupBy('sectionId');

        $companyId = $project->company_id;

        $document_standard = DocumentStandardEloquentModel::where('name', 'variation order')->where('company_id', $companyId)->first(['id', 'header_text', 'footer_text', 'disclaimer']);

        $final_result = new stdClass;
        $final_result->document_standard_id = $document_standard ? $document_standard->id : '';
        $final_result->header_text = $document_standard ? $document_standard->header_text : '';
        $final_result->footer_text = $document_standard ? $document_standard->footer_text : '';
        $final_result->disclaimer = $document_standard ? $document_standard->disclaimer : '';
        $final_result->totalAmountForLump = $totalAmountForLump;
        $final_result->items = $groupBySection;
        return $final_result;
    }

    public function tempgetVariationItems($projectId, $saleperson_id)
    {
        $project = ProjectEloquentModel::with('company')->find($projectId);

        $gst = $project->company->gst;

        $final_result = new stdClass;

        //Get renovation document based on project id
        $document = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            ->where('type', 'VARIATIONORDER')
            ->whereNotNull('signed_date')
            ->latest()
            ->first();


        //If can't find signed VO, find quotation document
        if (!$document) {
            $document = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                ->where('type', 'QUOTATION')
                ->whereNotNull('signed_date')
                ->first();
        }

        $documentId = $document->id;

        $sectionIndexArray = SectionsIndexEloquentModel::where('document_id', $documentId)
            ->pluck('section_sequence')
            ->first();

        //Convert back into array
        $sectionIndexArray = json_decode($sectionIndexArray);

        foreach ($sectionIndexArray as $sectionId) {
            //Check if section got any selected item based on if reno section got data
            $section = RenovationSectionsEloquentModel::where('section_id', $sectionId)
                ->where('document_id', $documentId)
                ->first();

            if ($section && $section->calculation_type == 'LUMP_SUM') {
                $section->total_amount = $section->total_price;
                $section->total_cost_amount = $section->total_cost_price;
                $section->id = $section->section_id;
            }

            //If don't have, find from section table
            if (!$section) {
                $section = SectionsEloquentModel::where('id', $sectionId)
                    ->first();

                $section->total_items_count = 0;
            }
            $section->total_price = 0;

            $aows = [];

            //Get the AOW index based on section and document id
            $aowIndexArray = AOWIndexEloquentModel::where('document_id', $documentId)
                ->where('section_id', $sectionId)
                ->pluck('aow_sequence')
                ->first();


            if ($aowIndexArray) {
                $aowIndexArray = json_decode($aowIndexArray);

                foreach ($aowIndexArray as $aowId) {
                    //Check if aow got any selected item
                    $aow = RenovationAreaOfWorkEloquentModel::where('document_id', $documentId)
                        ->where('section_area_of_work_id', $aowId)
                        ->first();

                    if ($aow) {
                        $aow->id = $aow->section_area_of_work_id;
                    }

                    if (!$aow) {
                        $aow = SectionAreaOfWorkEloquentModel::where('id', $aowId)
                            ->first();
                    }

                    $items = [];

                    $itemIndexArray = ItemsIndexEloquentModel::where('document_id', $documentId)
                        ->where('aow_id', $aowId)
                        ->pluck('items_sequence')
                        ->first();


                    if ($itemIndexArray) {
                        $itemIndexArray = json_decode($itemIndexArray);

                        foreach ($itemIndexArray as $itemId) {
                            //Check if item is selected
                            $item = RenovationItemsEloquentModel::where('renovation_document_id', $documentId)
                                ->where('quotation_template_item_id', $itemId)
                                ->first();
                            if ($item) {
                                $item->price_with_gst = $item->price;
                                $item->id = $item->quotation_template_item_id;
                            }

                            //If no item found, means not checked
                            if (!$item) {
                                $item = QuotationTemplateItemsEloquentModel::where('id', $itemId)
                                    ->first();
                                $item->isChecked = false;
                                $item->isNew = false;
                                $item->isFOC = false;
                            } else {
                                $item->isChecked = true;
                                $item->isOriginalItem = true;
                                $item->description = $item->name;
                                $item->sub_description = $item->sub_description;

                            }

                            //Add item to items array
                            $items[] = $item;
                        }
                    }

                    //Pass items array to aow in data
                    $aow->items = $items;

                    //assign aow to aows array
                    $aows[] = $aow;
                }
            }
            //Assign aows to section
            $section->area_of_works = $aows;

            $final_result->items[] = $section;
        }

        $document_standard = DocumentStandardEloquentModel::where('id', $document->document_standard_id)
            ->first();

        $final_result->document_standard_id = $document_standard ? $document_standard->id : '';
        $final_result->header_text = $document_standard ? $document_standard->header_text : '';
        $final_result->footer_text = $document_standard ? $document_standard->footer_text : '';
        $final_result->disclaimer = $document_standard ? $document_standard->disclaimer : '';
        $final_result->gst = $gst;

        return $final_result;
    }


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
        // }

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

        // logger("item index",[$sectionIndexArray]);

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
                                $item->is_page_break = false;
                                $item->sub_description = $renoItem->sub_description;

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

                    $sectionData->is_page_break = $sectionData->id === $renoItem->renovation_sections->section_id
                        ? $renoItem->renovation_sections->is_page_break
                        : $sectionData->is_page_break;
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


            //check there has last signed VO
            if (isset($lastSignedVO)) {
                
                //if has get reno items for that VO
                $renoItems = RenovationItemsEloquentModel::where('renovation_document_id', $lastSignedVO)
                    ->get();

                //update existing items in signed quotation data
                foreach ($renoItems as $renoItem) {

                    //get all items id from section data
                    // $item = collect($quotationSectionsData)
                    //     ->flatMap(function ($section) {
                    //         return $section->area_of_works;
                    //     })
                    //     ->flatMap(function ($aow) {
                    //         return $aow->items;
                    //     })
                    //     ->firstWhere('id', $renoItem->quotation_template_item_id);

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
                        $item->is_selected_in_variation_order = false;
                        $item->is_signed_in_VO = true;
                        $item->sub_description = $renoItem->sub_description;

                    } else {

                        //or not add new items function private
                        $this->initializeNewVariationData($quotationSectionsData, $renoItem);
                    }
                }
            }

            // $lastSignedCN = RenovationDocumentsEloquentModel::where('project_id', $projectId)
            // ->where('type', 'CANCELLATION')
            // ->whereNotNull('signed_date')
            // ->pluck('id')
            // ->last();

            // if (isset($lastSignedCN)) {

            //     $renoItems = RenovationItemsEloquentModel::where('renovation_document_id', $lastSignedCN)->get();

            //     foreach ($renoItems as $renoItem) {
                    
            //         $item = null;
            //         foreach ($quotationSectionsData as $section) {
            //             foreach ($section->area_of_works as $aow) {
            //                 $item = $this->findItemById($aow->items, $renoItem->quotation_template_item_id);
            //                 if ($item) {
            //                     break 2; // break both foreach loops
            //                 }
            //             }
            //         }

            //         if($item)
            //         {
                       
            //             $item->quantity = $item->quantity == $renoItem->quantity ? $item->quantity : $item->quantity - $renoItem->quantity;
                        
            //         }

            //     }

            // }

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
            

            //remove unnessary section, area_of_works and items in array based on is_sigined_in_QO_or_VO
            foreach ($quotationSectionsData as $sectionData) {

                foreach ($sectionData->area_of_works as $aow) {

                    // $aow->items = collect($aow->items)->reject(function ($item) {
                    //     return !$item->is_selected_in_quotation && !$item->is_signed_in_VO;
                    // })->values()->all();
                    $aow->items = $this->cleanUpItems($aow->items);
                }

                $sectionData->area_of_works = collect($sectionData->area_of_works)->reject(function ($aow) {
                    return count($aow->items) == 0;
                })->values()->all();
            }

            $quotationSectionsData = collect($quotationSectionsData)->reject(function ($sectionData) {
                return count($sectionData->area_of_works) == 0;
            })->values()->all();
        }


        $final_result->reno_document_id = $unsignedVODocuments ? $unsignedVODocuments->id : '';
        $final_result->version = "/VO" . ($countVODocs + 1);
        $final_result->gst = $gst;
        $final_result->special_discount_percentage = $unsignedVODocuments ? $unsignedVODocuments->special_discount_percentage : $sign_quotation_discount_percentage;
        $final_result->singed_items =  $signedItemArray ?? [];
        $final_result->sectionsData = $sectionsData;
        $final_result->remark = $unsignedVODocuments ? $unsignedVODocuments->remark : '';

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

                    // $item->document_id = $documentEloquent->id;

                    // $item->section_id = $sectionId;
                    // $item->area_of_work_id = $aowId;

                    // $itemId = $this->validateItem($item);

                    // //Assign itemId to item id (Only use for newly created item)
                    // $item->id = $itemId;

                    // $itemSequence[] = $itemId;

                    // //changes
                    // if($item->is_edited || $item->is_selected_in_variation_order) {
                    //     //Return both index and data
                    //     $renoSection = $this->validateRenovationSections($sectionId, $section, $documentEloquent, $itemCheckedCount, $renoSectionIndex);

                    //     //Reassign the index and data section
                    //     $renoSectionIndex  = $renoSection['index'];
                    //     $renoSectionData  = $renoSection['data'];

                    //     //Return both index and data
                    //     $renoAOW = $this->validateRenoAreaOfWork($aow, $aowId, $documentEloquent->id, $renoAOWIndex);

                    //     //Reassign the index and data reno aow
                    //     $renoAOWIndex  = $renoAOW['index'];
                    //     $renoAOWData  = $renoAOW['data'];

                    //     $cancellationId = null;
                    //     $prevItemId = $item->is_edited ? $item->document_item_id : null;

                    //     $renoItemData = $this->createRenovationItem($documentEloquent, $item, $itemId, $renoSectionData, $renoAOWData, $cancellationId, $prevItemId, false, false);
                    // }
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
            $documentEloquent->remark = $documentEloquent->remark
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
                $sectionData->is_page_break = $section->is_page_break ?? false;
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
                                $itemData->sub_description = $item->sub_description;

                                
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
                            $itemData->is_selected_in_variation_order = false;
                            $itemData->is_Cancellation = false;
                            $itemData->is_signed_in_VO = false;
                            $itemData->is_signed_in_FOC = false;
                            $itemData->is_signed_in_CN = false;
                            $itemData->is_show_in_summary = false;
                            $itemData->is_page_break = false;


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
            // logger([$item->name,$item->quantity,$item->id]);
            $itemData = (object) [
                'id' => $item->id,
                'from_quotation' => $item->from_quotation,
                'parent_id' => $item->parent_id,
                'is_selected_in_quotation' => $item->is_selected_in_quotation,
                'document_item_id' => $item->document_item_id,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'lengthmm' => $item->lengthmm ?? null,
                'breadthmm' => $item->breadthmm ?? null,
                'heightmm' => $item->heightmm ?? null,
                'price' => $item->price,
                'cost_price' => $item->cost_price,
                'profit_margin' => $item->profit_margin,
                'measurement' => $item->measurement,
                'is_fixed_measurement' => $item->is_fixed_measurement,
                'is_FOC' => $item->is_FOC,
                'is_CN' => $item->is_CN,
                'is_page_break' => $item->is_page_break,
                'is_edited' => $item->is_edited,
                'is_selected_in_variation_order' => $item->is_selected_in_variation_order,
                'is_Cancellation' => $item->is_Cancellation,
                'is_signed_in_VO' => $item->is_signed_in_VO,
                'is_signed_in_FOC' => $item->is_signed_in_FOC,
                'is_signed_in_CN' => $item->is_signed_in_CN,
                'is_show_in_summary' => $item->is_show_in_summary,
                'sub_description' => $item->sub_description,
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
                 $item->is_signed_in_VO = false;
                 $item->is_show_in_summary = true;
                 $item->is_page_break = $renoItem->is_page_break;
                 $item->sub_description = $renoItem->sub_description;

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
        $sectionData->original_total_section_price = $renoSection->total_price ? $renoSection->total_price : 0;
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
                $itemData->sub_description = $item->sub_description;

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
                'is_page_break' => $section->is_page_break ?? false
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
                'description' => $item->name,
                'index' => 0,
                'quantity' => $item->quantity,
                'unit_of_measurement' => $item->measurement ?? $item->unit_of_measurement ?? null,
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
            $prevItemId = $item->is_edited ? $item->document_item_id : null;

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
                'name' => $item->name,
                'quotation_template_item_id' => isset($itemId) ? $itemId : null,
                'renovation_item_section_id' => isset($renoSectionData->id) ? $renoSectionData->id : null,
                'renovation_item_area_of_work_id' => isset($renoAOWData->id) ? $renoAOWData->id : null,
                'quantity' => isset($item->quantity) ? $item->quantity : null,
                'length' => isset($item->lengthmm) ? $item->lengthmm : null,
                'breadth' => isset($item->breadthmm) ? $item->breadthmm : null,
                'height' => isset($item->lengthmm) ? $item->lengthmm : null,
                'price' => isset($item->price) ? $item->price : null,
                'cost_price' => isset($item->cost_price) ? $item->cost_price : null,
                'profit_margin' => isset($item->profit_margin) ? $item->profit_margin : 0,
                'is_FOC' => isset($item->is_FOC) ? $item->is_FOC : 0,
                'is_CN' => isset($item->is_CN) ? $item->is_CN : 0,
                'is_page_break' => isset($item->is_page_break) ? $item->is_page_break : 0,
                'is_excluded' => $is_excluded,
                'unit_of_measurement' => isset($item->measurement) ? $item->measurement : null,
                'is_fixed_measurement' => isset($item->is_fixed_measurement) ? $item->is_fixed_measurement : 0,
                'completed' => $completed,
                'active' => $active,
                'sub_description' => $item->sub_description

            ]
        );
    }

    // initilize new data from previous signed VO
    private function initializeNewVariationData($sectionData, $renoItem)
    {
        $sectionId = $renoItem->renovation_sections->section_id; //get section id from new items

        $aowId = $renoItem->renovation_area_of_work->section_area_of_work_id; //get aow id from new items

        $filta_section_data = collect($sectionData)->firstWhere('id', $sectionId); //filte original section data by section id

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
                $itemData->is_signed_in_VO = true;
                $itemData->is_signed_in_FOC = false;
                $itemData->is_signed_in_CN = false;
                $itemData->is_show_in_summary = false;
                $itemData->is_page_break = $renoItem->is_page_break;
                $itemData->sub_description = $renoItem->sub_description;


                $aow->items[] = $itemData;
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
        $newItem->sub_description = $renoItem->sub_description;
    
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
