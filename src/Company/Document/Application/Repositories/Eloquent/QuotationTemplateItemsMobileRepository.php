<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\QuotationTemplateItemsData;
use Src\Company\Document\Application\Mappers\QuotationTemplateItemsMapper;
use Src\Company\Document\Domain\Model\Entities\QuotationTemplateItems;
use Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryInterface;
use Src\Company\Document\Domain\Resources\QuotationItemsResource;
use Src\Company\Document\Domain\Resources\QuotationTemplateItemsResource;
use Src\Company\Document\Domain\Resources\QuotationTemplateResource;
use Src\Company\Document\Domain\Resources\QuotationTemplateSectionResource;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplatesEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Illuminate\Support\Facades\Log;
use Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryMobileInterface;

class QuotationTemplateItemsMobileRepository implements QuotationTemplateItemsRepositoryMobileInterface
{
    public function getQuotationItems($templateId)
    {
        $sections = SectionsEloquentModel::where('is_active', 1)
                                         ->where('quotation_template_id', $templateId)
                                         ->with(['areaOfWorks' => function ($query) {
                                            $query->whereNull('document_id')
                                            ->where('is_active', 1)
                                            ->orderBy('index')
                                                ->with(['items' => function ($query) {
                                                    $query->whereNull('document_id')
                                                    ->whereNull('parent_id')
                                                    ->where('is_active' , 1)
                                                    ->orderBy('index')
                                                    ->with('items');
                                                }]);
                                        }])
                                        ->orderBy('index')
                                        ->get();
        foreach($sections as $section){

            $section->total_cost_amount = 0;
            $section->total_amount = 0;

            // to track the original cost_price of quotation template item
            foreach ($section->areaOfWorks as $area_of_work) {
                foreach ($area_of_work->items as $item) {
                    $item->original_cost_price = $item->cost_price;
                    $item->original_profit_margin = $item->profit_margin;
                }
            }

        }


        return $sections;
    }

    //Not in use
    public function store(QuotationTemplateItems $quotationTemplateItems): QuotationTemplateItemsData
    {
        $section = SectionsEloquentModel::where('name', $quotationTemplateItems->section_name)->first();
        if (!$section) {
            $section = SectionsEloquentModel::create([
                'name' => $quotationTemplateItems->section_name,
                'calculation_type' => $quotationTemplateItems->calculation_type,
                'index' => SectionsEloquentModel::count() + 1,
                'salesperson_id' => $quotationTemplateItems->salesperson_id,
            ]);
        }
        $sectionId = $section->id;

        $aowId = null;

        if ($quotationTemplateItems->area_of_work_name != "") {

            $sectionCheck = SectionAreaOfWorkEloquentModel::where('section_id', $sectionId)->count();

            if ($sectionCheck > 0) {

                $nameCheck = SectionAreaOfWorkEloquentModel::where('section_id', $sectionId)->where('name', $quotationTemplateItems->area_of_work_name)->first();

                if (!$nameCheck) {

                    $sectionAOW = SectionAreaOfWorkEloquentModel::create([
                        'section_id' => $sectionId,
                        'name' => $quotationTemplateItems->area_of_work_name,
                        'index' => SectionAreaOfWorkEloquentModel::where('section_id', $sectionId)->count() + 1
                    ]);
                }

                $aowId = $nameCheck ? $nameCheck->id : $sectionAOW->id;
            } else {

                $sectionAOW = SectionAreaOfWorkEloquentModel::create([
                    'section_id' => $sectionId,
                    'name' => $quotationTemplateItems->area_of_work_name,
                    'index' => 1
                ]);

                $aowId = $sectionAOW->id;
            }
        }

        return DB::transaction(function () use ($quotationTemplateItems, $sectionId, $aowId) {

            $quotationTemplateItemsEloquent = QuotationTemplateItemsMapper::toEloquent($quotationTemplateItems, $sectionId, $aowId);

            $quotationTemplateItemsEloquent->save();

            return QuotationTemplateItemsData::fromEloquent($quotationTemplateItemsEloquent);
        });
    }

    //Not in use
    public function templateStore($quotationTemplate)
    {
        if (isset($quotationTemplate[0]['salesperson_id'])) {
            $salepersonId = $quotationTemplate[0]['salesperson_id'];
        } else {
            $salepersonId = null;
        }

        $index = 1; // Start index value

        foreach ($quotationTemplate as $section) {
            $section['index'] = $index++; // Update section index

            // Reindex AOWs within the section
            $aowIndex = 1;
            foreach ($section['area_of_works'] as $aow) {
                $aow['index'] = $aowIndex++; // Update AOW index

                // Reindex items within the AOW
                $itemIndex = 1;
                foreach ($aow['items'] as $item) {
                    $item['index'] = $itemIndex++; // Update item index
                }
            }
        }


        $storedSections = SectionsEloquentModel::query()->where('salesperson_id', $salepersonId)->pluck('id')->toArray();

        $newIds = array_map(function ($item) {
            return isset($item['id']) ? $item['id'] : null;
        }, $quotationTemplate);

        $deleteData = array_diff($storedSections, $newIds);

        if (!empty($deleteData)) {
            SectionsEloquentModel::whereIn('id', $deleteData)->update(['is_active' => 0]);
            SectionAreaOfWorkEloquentModel::whereIn('section_id', $deleteData)->update(['is_active' => 0]);
            QuotationTemplateItemsEloquentModel::whereIn('section_id', $deleteData)->delete();
        }

        foreach ($quotationTemplate as $template) {

            $data = json_decode(json_encode($template));

            $section = '';

            if (isset($data->id)) {
                // Update existing section
                SectionsEloquentModel::findOrFail($data->id)->update([
                    'name' => $data->name,
                    'calculation_type' => $data->calculation_type,
                    'index' => $data->index,
                    'description' => $data->description ?? ''
                ]);

                $section = SectionsEloquentModel::find($data->id);
            } else {
                // Check if a section with the same name exists and is inactive
                $inactiveSection = SectionsEloquentModel::where('name', $data->name)
                    ->where('is_active', 0)
                    ->where('salesperson_id', $salepersonId)
                    // ->where('calculation_type', $data->calculation_type)
                    ->first();

                if (isset($inactiveSection)) {
                    // Activate the existing inactive section
                    $inactiveSection->update([
                        'is_active' => 1,
                        'description' => $data->description ?? '',
                        'index' => $data->index,
                    ]);
                    $section = $inactiveSection;

                    //Set all AOW in that section to active
                    SectionAreaOfWorkEloquentModel::where('section_id', $section->id)
                        ->where('document_id', null)
                        ->update(['is_active' => 1]);
                } else {
                    // Create a new section
                    $section = SectionsEloquentModel::create([
                        'name' => $data->name,
                        'calculation_type' => $data->calculation_type,
                        'index' => $data->index ?? SectionsEloquentModel::where('salesperson_id', $salepersonId)->count() + 1,
                        'salesperson_id' => $salepersonId,
                        'is_active' => 1,
                        'description' => $data->description ?? ''

                    ]);
                }
            }

            if(isset($data->vendors))
            $section->vendors()->sync($data->vendors);

            $storedAows = SectionAreaOfWorkEloquentModel::query()->where('section_id', isset($data->id) ? $data->id : '')
                ->pluck('id')
                ->toArray();


            $newIds = array_map(function ($item) {
                return  isset($item['id']) ? $item['id'] : null;
            }, $template['area_of_works']);

            $deleteData = array_diff($storedAows, $newIds);

            if (!empty($deleteData)) {
                SectionAreaOfWorkEloquentModel::whereIn('id', $deleteData)->update(['is_active' => 0]);
                QuotationTemplateItemsEloquentModel::whereIn('area_of_work_id', $deleteData)->delete();
            }

            $aow = json_decode(json_encode($data->area_of_works));

            foreach ($aow as $index => $data1) {

                if (isset($data1->id)) {

                    SectionAreaOfWorkEloquentModel::where('id', $data1->id)->where('section_id', $section->id)
                        ->first()->update([
                            'name' => $data1->name,
                            'index' => $data1->index
                        ]);

                    $areaOfWorks = SectionAreaOfWorkEloquentModel::find($data1->id);
                } else {

                    $inactiveAOW = SectionAreaOfWorkEloquentModel::where('name', $data1->name)
                        ->where('section_id', $section->id)
                        ->where('is_active', 0)
                        ->where('document_id', null)
                        ->first();

                    if (isset($inactiveAOW)) {
                        $inactiveAOW->update([
                            'is_active' => 1,
                        ]);

                        $areaOfWorks = $inactiveAOW;
                    } else {
                        $areaOfWorks = SectionAreaOfWorkEloquentModel::firstOrCreate([
                            'section_id' => $section->id,
                            'name' => $data1->name,
                            'is_active' => 1
                        ], [
                            'index' => $data1->index ?? SectionAreaOfWorkEloquentModel::where('section_id', $section->id)->count() + 1
                        ]);
                    }
                }

                $storedItems = QuotationTemplateItemsEloquentModel::query()->where('section_id', isset($data->id) ? $data->id : '')->where('area_of_work_id', isset($data1->id) ? $data1->id : null)->pluck('id')->toArray();

                $newIds = array_map(function ($item) {
                    return  isset($item['id']) ? $item['id'] : null;
                }, $template['area_of_works'][$index]['items']);

                $deleteData = array_diff($storedItems, $newIds);

                if (!empty($deleteData)) {
                    QuotationTemplateItemsEloquentModel::whereIn('id', $deleteData)->delete();
                }

                $items = json_decode(json_encode($data1->items));

                foreach ($items as $item) {

                    if (isset($item->id)) {
                        $quotationTemplateItems = QuotationTemplateItemsEloquentModel::find($item->id);

                        if ($quotationTemplateItems) {
                            $quotationTemplateItems->update([
                                'description' => $item->description,
                                'index' => $item->index ?? 0,
                                'unit_of_measurement' => $item->measurement,
                                'is_fixed_measurement' => $item->is_fixed_measurement ?? 0,
                                'section_id' => $section->id,
                                'area_of_work_id' => $areaOfWorks ? $areaOfWorks->id : null,
                                'price_without_gst' => $item->price_without_gst,
                                'price_with_gst' => $item->price_with_gst,
                                'cost_price' => $item->cost_price,
                                'profit_margin' => $item->profit_margin,
                                'quantity' => $item->quantity,
                                'salesperson_id' => $salepersonId
                            ]);
                        }
                    } else {
                        QuotationTemplateItemsEloquentModel::firstOrCreate([
                            'description' => $item->description,
                            'index' => $item->index ?? 0,
                            'unit_of_measurement' => $item->measurement,
                            'is_fixed_measurement' => $item->is_fixed_measurement ?? 0,
                            'section_id' => $section->id,
                            'area_of_work_id' => $areaOfWorks ? $areaOfWorks->id : null,
                            'price_without_gst' => $item->price_without_gst,
                            'price_with_gst' => $item->price_with_gst,
                            'cost_price' => $item->cost_price,
                            'profit_margin' => $item->profit_margin,
                            'quantity' => $item->quantity,
                            'salesperson_id' => $salepersonId
                        ]);
                    }
                }
            }
        }

        return $quotationTemplate;
    }

    //Not in use
    public function salepersonTemplateStore($quotationTemplate)
    {
        $salepersonId = $quotationTemplate[0]['salesperson_id'];

        foreach ($quotationTemplate as $template) {

            $data = json_decode(json_encode($template));

            $section = '';

            $section = SectionsEloquentModel::create([
                'name' => $data->name,
                'calculation_type' => $data->calculation_type,
                'index' => SectionsEloquentModel::where('salesperson_id', $salepersonId)->count() + 1,
                'salesperson_id' => $salepersonId,
                'description' => $data->description ?? ''

            ]);

            $section->vendors()->sync($data->vendors);

            $aow = json_decode(json_encode($data->area_of_works));

            foreach ($aow as $index => $data1) {

                if ($data1->name != 'General Item') {
                    $areaOfWorks = SectionAreaOfWorkEloquentModel::create([
                        'section_id' => $section->id,
                        'name' => $data1->name,
                        'index' => SectionAreaOfWorkEloquentModel::where('section_id', $section->id)->count() + 1
                    ]);
                } else {
                    $areaOfWorks = null;
                }

                $items = json_decode(json_encode($data1->items));

                foreach ($items as $item) {

                    QuotationTemplateItemsEloquentModel::create([
                        'description' => $item->description,
                        'unit_of_measurement' => $item->measurement,
                        'is_fixed_measurement' => $item->is_fixed_measurement ?? 0,
                        'section_id' => $section->id,
                        'area_of_work_id' => $areaOfWorks ? $areaOfWorks->id : null,
                        'price_without_gst' => $item->price_without_gst,
                        'price_with_gst' => $item->price_with_gst,
                        'cost_price' => $item->cost_price,
                        'profit_margin' => $item->profit_margin,
                        'quantity' => $item->quantity,
                        'salesperson_id' => $salepersonId
                    ]);
                }
            }
        }

        return $quotationTemplate;
    }

    public function duplicateTemplate($request)
    {
       //id for duplicate
        $templateId = $request['template_id'];

        $sections = SectionsEloquentModel::where('quotation_template_id', $templateId)
                                        ->where('is_active', 1)
                                        ->with([
                                            'vendors',
                                            'areaOfWorks' => function ($query) {
                                                $query->where('is_active', 1);
                                            },
                                            'areaOfWorks.items'
                                        ])
                                        ->get();


        $quotationTemplate = QuotationTemplatesEloquentModel::where('id',  $templateId)
                                                            ->first();
        //Duplicated template with new ID
        $newTemplate =  QuotationTemplatesEloquentModel::create([
            'salesperson_id' => $quotationTemplate->salesperson_id,
            'name' => $quotationTemplate->name . '-Copy'
        ]);

        $itemMap = [];

        // Duplicate the sections
        foreach ($sections as $section) {
            $duplicatedSection = $section->replicate();
            $duplicatedSection->quotation_template_id = $newTemplate->id;
            $duplicatedSection->save();

            // Duplicate the area of works (AOW) and associate them with the new section
            foreach ($section->areaOfWorks as $aow) {
                $duplicatedAOW = $aow->replicate();
                $duplicatedAOW->section_id = $duplicatedSection->id;
                $duplicatedAOW->save();

                // Duplicate the items and associate them with the new AOW
                foreach ($aow->items as $item) {
                    $originalItemId = $item->id;
                    $duplicatedItem = $item->replicate();
                    $duplicatedItem->area_of_work_id = $duplicatedAOW->id;
                    $duplicatedItem->section_id = $duplicatedSection->id;
                    $duplicatedItem->save();

                    // Map old item ID to new item ID
                    $itemMap[$originalItemId] = $duplicatedItem->id;
                }
            }
        }

        // Update parent_id for sub items
        foreach ($itemMap as $oldId => $newId) {
            // Only update items that are part of the new template
            QuotationTemplateItemsEloquentModel::where('parent_id', $oldId)
                ->whereIn('section_id', function ($query) use ($newTemplate) {
                    $query->select('id')
                          ->from('sections')
                          ->where('quotation_template_id', $newTemplate->id);
                })
                ->update(['parent_id' => $newId]);
        }

        $templates = QuotationTemplatesEloquentModel::where('salesperson_id', $newTemplate->salesperson_id)
                                                    ->get();

        return $templates;
    }

    public function retrieveAllTemplate($salespersonId)
    {
        $templates = QuotationTemplatesEloquentModel::where('salesperson_id', isset($salespersonId) ? $salespersonId : null)
                                                    ->get();

        return  $templates;
    }
    public function retrieveTemplate($templateId)
    {
        // example query if FE provide salepersonId
        if(isset($templateId)) {
            // ====================Get section only if there's items=====================

            $section = SectionsEloquentModel::where('quotation_template_id', $templateId)
                                            ->where('is_active', 1)
                                            ->with([
                                                'vendors',
                                                'areaOfWorks' => function ($query) {
                                                    $query->where('is_active', 1);
                                                },
                                                'areaOfWorks.items' => function ($query) {
                                                    $query->where('is_active', 1);
                                                }
                                            ])
                                            ->get();

            $quotationTemplate = QuotationTemplatesEloquentModel::where('id', $templateId)
                                                                ->first();

        }

        //Create an empty object
        $data = new \stdClass();
        $data->id = isset($quotationTemplate) ? $quotationTemplate->id : null;
        $data->name = isset($quotationTemplate) ? $quotationTemplate->name : null;

        if ($section->isEmpty())
        {
            $data->sections = [];
        }
        else{
            //Sort sections
            $sortedSections = $section->sortBy('index');

            // Iterate over the sorted sections
            $sortedSections->transform(function ($section) {
                // Sort the area of work inside each section by their index
                $section->areaOfWorks = $section->areaOfWorks->sortBy('index')->transform(function ($areaOfWork) {
                    // Ignore items with parent id (the child items will be added into the parent items object itself)
                    $items = $areaOfWork->items->where('parent_id', null)->whereNull('document_id');
                    // Assuming $areaOfWork->items exists and needs to be sorted by 'index'
                    if ($areaOfWork->items && $items->isNotEmpty()) {
                        $areaOfWork->items = $items->sortBy('index');
                    }
                    return $areaOfWork;
                });
                return $section;
            });

            // $sortedSectionsCollection = QuotationTemplateResource::collection($sortedSections);
            $sortedSectionsCollection = $this->getTemplateItems($sortedSections);

            $data->sections =  $sortedSectionsCollection;
        }
        return $data;
    }

    //Not in use
    public function update(QuotationTemplateItems $quotationTemplateItems): QuotationTemplateItemsEloquentModel
    {
        $quotationItemsEloquent = QuotationTemplateItemsMapper::toEloquent($quotationTemplateItems);

        $quotationItemsEloquent->save();

        return $quotationItemsEloquent;
    }

    //Not in use
    public function delete(int $items_id): void
    {
        $quotationTemplateItemsEloquent = QuotationTemplateItemsEloquentModel::query()->findOrFail($items_id);

        $quotationTemplateItemsEloquent->delete();
    }
    public function deleteTemplate(int $template_id): void
    {
        $quotationTemplateEloquent = QuotationTemplatesEloquentModel::query()->findOrFail($template_id);

        $quotationTemplateEloquent->delete();
    }
    public function createTemplate($requestQuotationTemplate)
    {
        if(isset($requestQuotationTemplate['salesperson_id']))
        {
            $salespersonId = $requestQuotationTemplate['salesperson_id'];
        }
        else{
            $salespersonId = null;
        }

        //Create quotation template first
        $quotationTemplate =  QuotationTemplatesEloquentModel::create([
            'salesperson_id' => $salespersonId,
            'name' => $requestQuotationTemplate['name']
        ]);

        $sectionIndex = 1;

        foreach($requestQuotationTemplate['sections'] as $requestSection) {

            $section = SectionsEloquentModel::create([
                'name' => $requestSection['name'],
                'calculation_type' => $requestSection['calculation_type'],
                'index' => $sectionIndex++,
                'salesperson_id' => $salespersonId,
                'description' => isset($requestSection['description']) ? $requestSection['description'] : null,
                'quotation_template_id' => $quotationTemplate->id
            ]);

            //=========To find out more about vendors??===================
            // $section->vendors()->sync($data->vendors);

            $aowIndex = 1;

            foreach($requestSection['area_of_works'] as $requestAOW)
            {
                $aow = SectionAreaOfWorkEloquentModel::create([
                    'section_id' => $section->id,
                    'name' => $requestAOW['name'],
                    'index' => $aowIndex++,
                ]);

                // Recursive function to handle sub items
                $this->createItems($requestAOW, $section->id, $aow->id, $salespersonId);

                // $itemIndex = 1;

                // foreach($requestAOW['items'] as $requestItem)
                // {
                //       QuotationTemplateItemsEloquentModel::create([
                //         'salesperson_id' => $salespersonId,
                //         'description' => $requestItem['description'],
                //         'index' => $itemIndex++,
                //         'quantity' => $requestItem['quantity'],
                //         'unit_of_measurement' => $requestItem['measurment'],
                //         'is_fixed_measurement' => $requestItem['is_fixed_measurement'],
                //         'section_id' => $section->id,
                //         'area_of_work_id' => $aow->id,
                //         'price_without_gst' => $requestItem['price_without_gst'],
                //         'price_with_gst' =>$requestItem['price_with_gst'],
                //         'cost_price' => $requestItem['cost_price'],
                //         'profit_margin' => $requestItem['profit_margin']
                //     ]);
                // }
            }
        }

        $sectionData = SectionsEloquentModel::where('quotation_template_id', $quotationTemplate->id)
                                        ->where('is_active', 1)
                                        ->with([
                                            'vendors',
                                            'areaOfWorks' => function ($query) {
                                                $query->where('is_active', 1);
                                            },
                                            'areaOfWorks.items'
                                        ])
                                        ->get();

        return $sectionData;
    }

    private function createItems($parentObj, $sectionId, $aowId, $salespersonId) // A recursive function for create template
    {
        $itemIndex = 1;

        foreach($parentObj['items'] as $requestItem)
        {
            $item = QuotationTemplateItemsEloquentModel::create([
                'salesperson_id' => $salespersonId,
                'description' => $requestItem['description'],
                'index' => $itemIndex++,
                'quantity' => $requestItem['quantity'],
                'unit_of_measurement' => $requestItem['measurement'],
                'is_fixed_measurement' => $requestItem['is_fixed_measurement'],
                'section_id' => $sectionId,
                'area_of_work_id' => $aowId,
                'price_without_gst' => $requestItem['price_without_gst'],
                'price_with_gst' =>$requestItem['price_with_gst'],
                'cost_price' => $requestItem['cost_price'],
                'profit_margin' => $requestItem['profit_margin'],
                'parent_id' => isset($parentObj['id']) && isset($requestItem['parent_item_index']) ? $parentObj['id'] : null // parent_item_index is an attribute passed from frontend, if this attribute exists, it means this particular item belongs to another item
            ]);

            if(isset($requestItem['items']) && count($requestItem['items']) > 0) {
                $requestItem['id'] = $item->id;
                $this->createItems($requestItem, $sectionId, $aowId, $salespersonId);
            }
        }
    }

    public function updateTemplate($quotationTemplate)
    {
        if(isset($quotationTemplate['salesperson_id']))
        {
            $salespersonId = $quotationTemplate['salesperson_id'];
        }
        else{
            $salespersonId = null;
        }

        $templateId = $quotationTemplate['id'];
        $templateName = $quotationTemplate['name'];
        $requestSections = $quotationTemplate['sections'];

        //Update templates name
        $currentTemplate = QuotationTemplatesEloquentModel::where('id', $templateId)->first();

        // Check if template with the same name exists, if yes, return error
        if($currentTemplate->name != $quotationTemplate['name']) {
            $templateWithSameName = QuotationTemplatesEloquentModel::where('id', '!=', $templateId)
                                    ->where('name', $quotationTemplate['name'])
                                    ->where('salesperson_id',$salespersonId)
                                    ->first();

            if(isset($templateWithSameName))
                abort(500, 'Template name exists. Please enter another name for this template.');
        }

        QuotationTemplatesEloquentModel::where('id', $templateId)->update([
            'name' => $templateName
        ]);

        //=====Reindex the sections========
        $index = 1; // Start index value

        foreach ($requestSections as &$requestSectionData) {
            $requestSectionData['index'] = $index++; // Update section index

            // Reindex AOWs within the section
            $aowIndex = 1;
            foreach ($requestSectionData['area_of_works'] as &$requestAOWData) {
                $requestAOWData['index'] = $aowIndex++; // Update AOW index

                // Reindex items within the AOW
                $itemIndex = 1;
                foreach ($requestAOWData['items'] as &$requestItemData) {
                    $requestItemData['index'] = $itemIndex++; // Update item index
                }
            }
        }

        //=====end of reindexing======

        //delete missing sections from frontend against database
        $this->deleteSections($requestSections, $templateId);

        foreach ($requestSections as $requestSection) {
            //if section got ID, means not newly created, just need update
            if (isset($requestSection['id'])) {

                // Update existing section
                $section = $this->updateSection($requestSection);

            }
            //Else if no ID
            else{
                 // Check if a section with the same name exists and is inactive
                 $section = SectionsEloquentModel::where('name', $requestSection['name'])
                                                        ->where('is_active', 0)
                                                        ->where('quotation_template_id', $templateId)
                                                        ->first();


                if ($section) {
                    // Activate the existing inactive section
                    $section->is_active = 1;
                    $section->description = isset($requestSection['description']) ? $requestSection['description'] : null;
                    $section->index = $requestSection['index'];

                    $section->save();

                    //Set all AOW in that section to active
                    SectionAreaOfWorkEloquentModel::where('section_id', $section['id'])
                                                  ->where('document_id', null)
                                                  ->update(['is_active' => 1]);

                } else {
                    // Create a new section
                    $section = SectionsEloquentModel::create([
                            'name' => $requestSection['name'],
                            'quotation_template_id' => $templateId,
                            'calculation_type' => $requestSection['calculation_type'],
                            'index' => $requestSection['index'],
                            'salesperson_id' => $salespersonId,
                            'is_active' => 1,
                            'description' => isset($requestSection['description']) ? $requestSection['description'] : null
                    ]);
                }
            }
            //update request section id if missing id
            $requestSection['id'] = $section->id;

            $this->deleteAOWs($requestSection);

            foreach($requestSection['area_of_works'] as $requestAOW)
            {
                //Have id, means not newly created
                if(isset($requestAOW['id'])){
                    $areaOfWork = $this->updateAOW($requestAOW);
                }
                else {
                    //Try to find area of work with same name as request then set to active
                    $areaOfWork = SectionAreaOfWorkEloquentModel::where('name', $requestAOW['name'])
                                                                ->where('section_id', $section->id)
                                                                ->where('is_active', 0)
                                                                ->where('document_id', null)
                                                                ->first();

                    if (isset($areaOfWork)) {
                        $areaOfWork->is_active = 1;
                        $areaOfWork->save();
                    }
                    //Else create new aow
                     else {
                        $areaOfWork = SectionAreaOfWorkEloquentModel::Create([
                            'section_id' => $section->id,
                            'name' => $requestAOW['name'],
                            'is_active' => 1,
                            'index' => $requestAOW['index']
                        ]);
                    }
                }
                $this->deleteItems($requestAOW);

                // A recursive function to keep looking for items/subitems, loop through and create/update them
                $this->createUpdateItems($requestAOW,$section->id, $areaOfWork['id'], $salespersonId);
            }

            if(isset($requestSection['vendor_categories']) && !empty($requestSection['vendor_categories'])) {
                $this->assignVendorCategoryToSection($section->id, $requestSection['vendor_categories']);
            }
            else {
                DB::table('section_vendor')->where('section_id', $section->id)->delete();
            }
        }

        $sectionsData = SectionsEloquentModel::where('quotation_template_id', $templateId)
                                            ->where('is_active', 1)
                                            ->with([
                                                'vendors',
                                                'areaOfWorks' => function ($query) {
                                                    $query->where('is_active', 1);
                                                },
                                                'areaOfWorks.items'
                                            ])
                                            ->get();

        return $sectionsData;
    }

    private function createUpdateItems($parentObj, $sectionId, $aowId, $salespersonId) { // A recursive function that loop through, create/update items/subitems
        foreach ($parentObj['items'] as $requestItem) {
            if (isset($requestItem['id'])) {
                $item = QuotationTemplateItemsEloquentModel::find($requestItem['id']);

                if ($item) { // Update existing item
                    $item->description = $requestItem['description'] ?? 'N.a';
                    $item->index = $requestItem['index'] ?? 0;
                    $item->unit_of_measurement = $requestItem['measurement'] ?? '';
                    $item->is_fixed_measurement =$requestItem['is_fixed_measurement'] ?? 0;
                    $item->section_id = $sectionId;
                    $item->area_of_work_id = $aowId ?? null;
                    $item->price_without_gst = $requestItem['price_without_gst'] ?? 0;
                    $item->price_with_gst = $requestItem['price_with_gst'] ?? 0;
                    $item->cost_price = $requestItem['cost_price'] ?? 0;
                    $item->profit_margin = $requestItem['profit_margin'] ?? 0;
                    $item->quantity = $requestItem['quantity'] ?? 0;
                    $item->salesperson_id = $salespersonId;

                    $item->save();
                }

            } else { // Create new item
                $item = QuotationTemplateItemsEloquentModel::Create([
                    'description' => $requestItem['description'] ?? 'N.a',
                    'index' => $requestItem['index'] ?? 0,
                    'unit_of_measurement' => $requestItem['measurement'] ?? '',
                    'is_fixed_measurement' => $requestItem['is_fixed_measurement'] ?? 0,
                    'section_id' => $sectionId,
                    'area_of_work_id' => $aowId ?? null,
                    'price_without_gst' => $requestItem['price_without_gst'] ?? 0,
                    'price_with_gst' => $requestItem['price_with_gst'] ?? 0,
                    'cost_price' => $requestItem['cost_price'] ?? 0,
                    'profit_margin' => $requestItem['profit_margin'] ?? 0,
                    'quantity' => $requestItem['quantity'] ?? 0,
                    'salesperson_id' => $salespersonId,
                    'parent_id' => isset($parentObj['id']) && isset($requestItem['parent_item_index']) ? $parentObj['id'] : null // parent_item_index is an attribute passed from frontend, if this attribute exists, it means this particular item belongs to another item
                ]);
            }

            if(isset($requestItem['items']) && count($requestItem['items']) > 0) {
                $requestItem['id'] = $item->id;
                $this->createUpdateItems($requestItem, $sectionId, $aowId, $salespersonId);
            }
        }
    }

    function assignVendorCategoryToSection($sectionId, array $categoryIds)
    {
        DB::beginTransaction();
        try {
            // Clear existing data for the section
            DB::table('section_vendor')->where('section_id', $sectionId)->delete();

            foreach ($categoryIds as $categoryId) {
                $vendors = VendorEloquentModel::where('vendor_category_id', $categoryId)->get();

                // Prepare the data to be inserted
                $data = [];
                foreach ($vendors as $vendor) {
                    $data[] = [
                        'section_id'        => $sectionId,
                        'vendor_id'         => $vendor->id,
                        'vendor_category_id'=> $categoryId
                    ];
                }

                DB::table('section_vendor')->insert($data);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    private function deleteSections(array $data, int $templateId)
    {
        $storedSections = SectionsEloquentModel::where('quotation_template_id', $templateId)->whereNull('document_id')->pluck('id')->toArray();

        //Get section ID and make into array
        $newIds = array_map(function ($section) {
            return isset($section['id']) ? $section['id'] : null;
        }, $data);

        $sectionIdsToDelete = array_diff($storedSections, $newIds);

        if (!empty($sectionIdsToDelete)) {
            SectionsEloquentModel::whereIn('id', $sectionIdsToDelete)->update(['is_active' => 0]);
            SectionAreaOfWorkEloquentModel::whereIn('section_id', $sectionIdsToDelete)->update(['is_active' => 0]);
            // QuotationTemplateItemsEloquentModel::whereIn('section_id', $sectionIdsToDelete)->delete();
            QuotationTemplateItemsEloquentModel::whereIn('section_id', $sectionIdsToDelete)->update(['is_active' => 0]);
        }
    }

    private function deleteAOWs($section)
    {
        $storedAows = SectionAreaOfWorkEloquentModel::query()->whereNull('document_id')->where('section_id', isset($section['id']) ? $section['id'] : null)
                                                             ->pluck('id')
                                                             ->toArray();


        $newIds = array_map(function ($aow) {
            return  isset($aow['id']) ? $aow['id'] : null;
        }, $section['area_of_works']);

        $aowToDelete = array_diff($storedAows, $newIds);

        if (!empty($aowToDelete)) {
            SectionAreaOfWorkEloquentModel::whereIn('id', $aowToDelete)->update(['is_active' => 0]);
            // QuotationTemplateItemsEloquentModel::whereIn('area_of_work_id', $aowToDelete)->delete();
            QuotationTemplateItemsEloquentModel::whereIn('area_of_work_id', $aowToDelete)->update(['is_active' => 0]);
        }
    }

    private function deleteItems($aow)
    {
        $storedItems = QuotationTemplateItemsEloquentModel::query()->whereNull('document_id')
                                                                ->where('section_id', isset($aow['section_id']) ? $aow['section_id'] : null)
                                                                ->where('area_of_work_id', isset($aow['id']) ? $aow['id'] : null)
                                                                ->where('is_active', 1)
                                                                ->pluck('id')
                                                                ->toArray();

        //new way considering of sub items
        $this->recursiveDeletItems($aow['items'], $storedItems);

        if (!empty($storedItems)) {
            QuotationTemplateItemsEloquentModel::whereIn('id', $storedItems)->update(['is_active' => 0]);
        }

        //old way not considering of sub items
        // $newIds = array_map(function ($item) {
        //     return  isset($item['id']) ? $item['id'] : null;
        // }, $aow['items']);
        // $itemsToDelete = array_diff($storedItems, $newIds);
        // if (!empty($itemsToDelete)) {
        //     // QuotationTemplateItemsEloquentModel::whereIn('id', $itemsToDelete)->delete();
        //     QuotationTemplateItemsEloquentModel::whereIn('id', $itemsToDelete)->update(['is_active' => 0]);
        // }
    }

    private function recursiveDeletItems($items, &$originalItems)
    {

        foreach ($items as $item) {

            if (array_key_exists('id', $item))
            {
                $originalItems = array_diff($originalItems, (array) $item['id']);
            }

            if(isset($item['items']) && count($item['items']) > 0)
            {
                $this->recursiveDeletItems($item['items'], $originalItems);
            }
        }

    }

    // Extracts all item IDs, including the sub items
    private function extractItemIds($items) {
        $ids = [];
        foreach ($items as $item) {
            if (isset($item['id'])) {
                $ids[] = $item['id'];  // Add the current item's ID
            }
            // Check if the current item has subitems and recursively extract their IDs
            if (isset($item['items']) && is_array($item['items'])) {
                $ids = array_merge($ids, $this->extractItemIds($item['items']));
            }
        }
        return $ids;
    }

    private function updateSection($data)
    {
        $section = SectionsEloquentModel::find($data['id']);

        if ($section) {
            $section->name = $data['name'];
            $section->calculation_type = $data['calculation_type'];
            $section->index = $data['index'];
            $section->description = $data['description'];

            $section->save();
        }


        return $section;
    }

    private function updateAOW($data)
    {
        $areaOfWork = SectionAreaOfWorkEloquentModel::where('id', $data['id'])
                                                     ->first();

        if ($areaOfWork) {
            $areaOfWork->name = $data['name'];
            $areaOfWork->index = $data['index'];

            $areaOfWork->save();
        }

        return $areaOfWork;
    }

    public function getTemplateItems($sortedSections)
    {
         $childItems = QuotationTemplateItemsEloquentModel::whereNull('document_id')
         ->orderBy('index')
         ->where('is_active', 1)
         ->get()
         ->groupBy('parent_id');

        $sortedSectionsCollection = $sortedSections->map(function ($section) use ($childItems) {
            // Fetch vendors and vendor categories
            $vendors = $section->vendors->pluck('id')->toArray();
            $vendor_categories = DB::table('section_vendor')
                ->where('section_id', $section->id)
                ->distinct()
                ->pluck('vendor_category_id')
                ->toArray();

            return [
                'id' => $section->id,
                'name' => $section->name,
                'index' => $section->index,
                'calculation_type' => $section->calculation_type,
                'vendors' => $vendors,
                'vendor_categories' => $vendor_categories,
                'area_of_works' => [...$section->areaOfWorks->map(function ($areaOfWork) use ($childItems) {
                    return [
                        'id' => $areaOfWork->id,
                        'section_id' => $areaOfWork->section_id,
                        'index' => $areaOfWork->index,
                        'name' => $areaOfWork->name,
                        'items' => [...$areaOfWork->items->map(function ($item) use ($childItems) {
                            // For each item, recursively map its child items
                            return $this->mapChildItems($item, $childItems);
                        })]
                    ];
                })],
                'is_misc' => $section->is_misc ?? false,
                'description' => $section->description,
            ];
        });

        return [...$sortedSectionsCollection];
    }

    public function mapChildItems($item, $childItems)
    {
        // Base data for the item
        $data = [
            'id' => $item->id,
            'description' => $item->description,
            'index' => $item->index,
            'price_without_gst' => $item->price_without_gst,
            'price_with_gst' => $item->price_with_gst,
            'cost_price' => $item->cost_price,
            'profit_margin' => $item->profit_margin,
            'measurement' => $item->unit_of_measurement,
            'is_fixed_measurement' => $item->is_fixed_measurement ?? 0,
            'quantity' => $item->quantity,
            'document_id' => $item->document_id,
            'items' => []
        ];

        // Check if the current item has child items
        if ($childItems->has($item->id)) {
            $childItemArray = $childItems->get($item->id);
            foreach ($childItemArray as $childItem) {
                $data['items'][] = $this->mapChildItems($childItem, $childItems);
            }
        }

        return $data;
    }
}
