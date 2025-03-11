<?php

namespace Src\Common\Presentation\CLI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationAreaOfWorkEloquentModel;
use Carbon\Carbon;

class ReformatDataStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'template:reformat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reformats data structure';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();

        // Add reno items which has no quotation_template_item_id into quotation template items table
        $renovationItems = DB::table('renovation_items')
        ->whereNull('quotation_template_item_id')
        ->whereNotNull('renovation_item_area_of_work_id')
        ->get()->keyBy('id');
        
        foreach ($renovationItems as $renovationItem) {
            $renovationItemSection = RenovationSectionsEloquentModel::find($renovationItem->renovation_item_section_id);
            $sectionId = $renovationItemSection->section_id;
            $renovationItemAreaOfWork = RenovationAreaOfWorkEloquentModel::find($renovationItem->renovation_item_area_of_work_id);
            $areaOfWorkId = $renovationItemAreaOfWork->section_area_of_work_id;
            // Create a new row in the quotation_template_items table
            $quotationTemplateItem = QuotationTemplateItemsEloquentModel::create([
                'salesperson_id' => null,
                'document_id' => $renovationItem->renovation_document_id,
                'index' => 0,
                'quantity' => $renovationItem->quantity,
                'description' => $renovationItem->name,
                'section_id' => $sectionId,
                'area_of_work_id'=> $areaOfWorkId,
                'unit_of_measurement' => $renovationItem->unit_of_measurement,
                'is_fixed_measurement' => $renovationItem->is_fixed_measurement,
                'price_with_gst' => $renovationItem->price,
                'price_without_gst' => $renovationItem->price,
                'cost_price' => $renovationItem->cost_price,
                'profit_margin' => $renovationItem->profit_margin,
            ]);

            // Update the renovation item with the new quotation_template_item_id
            DB::table('renovation_items')
            ->where('id', $renovationItem->id)
            ->update(['quotation_template_item_id' => $quotationTemplateItem->id]);
        }
        $renovationSections = DB::table('renovation_item_sections')->get()->keyBy('id');
        foreach ($renovationSections as $renovationSection) {
            $section = DB::table('sections')
                ->where('id',$renovationSection->section_id)
                ->first();
                // Update the $renovationSection with the new values
            DB::table('renovation_item_sections')
                ->where('id', $renovationSection->id)
                ->update([
                    'name' => $section->name
                ]);
        }
        //getting all reno documents
        $renovation_document_ids = DB::table('renovation_documents')
        ->join('projects', 'renovation_documents.project_id', '=', 'projects.id')
        ->where('projects.project_status', '!=', 'Cancelled')
        ->pluck('renovation_documents.id');
        foreach ($renovation_document_ids as $renovation_document_id) {
            // looking for all renovation sections that match current document id
            $renovation_section = DB::table('renovation_item_sections')
                ->where('document_id', $renovation_document_id)
                ->first();
            if (isset($renovation_section)) {
                $section = DB::table('sections')
                    ->where('id',$renovation_section->section_id)
                    ->whereNull('deleted_at')
                    ->first();
                if (isset($section)){
                    $quotation_template_id = $section->quotation_template_id;
                    // Retrieve the IDs of all rows where quotation_template_id matches
                    $section_ids = DB::table('sections')
                        ->where('quotation_template_id', $quotation_template_id)
                        ->whereNull('deleted_at')
                        ->pluck('id');

                    // dd($section_ids);
                    DB::table('section_index')->insert([
                        'document_id' => $renovation_document_id,
                        'section_sequence' => json_encode($section_ids) // Storing array as JSON
                    ]);
            

                    foreach ($section_ids as $section_id) {
                        $section_area_of_works_ids = DB::table('section_area_of_works')
                            ->where('section_id', $section_id)
                            ->where(function($query) use ($renovation_document_id) {
                                $query->where('document_id', $renovation_document_id)
                                    ->orWhereNull('document_id');
                            })
                            ->pluck('id');

                        DB::table('aow_index')->insert([
                            'document_id' => $renovation_document_id,
                            'section_id' => $section_id,
                            'aow_sequence' => json_encode($section_area_of_works_ids) // Storing array as JSON
                        ]);
                        foreach ($section_area_of_works_ids as $section_area_of_works_id) {
                            $quotation_template_items_ids = DB::table('quotation_template_items')
                            ->where('area_of_work_id', $section_area_of_works_id)
                            ->where(function ($query) use ($renovation_document_id) {
                                $query->where('document_id', $renovation_document_id)
                                    ->orWhereNull('document_id');
                            })
                            ->where(function ($query) use ($renovation_document_id) {
                                $query->whereNull('deleted_at')
                                    ->orWhereExists(function ($query) use ($renovation_document_id) {
                                        $query->select(DB::raw(1))
                                            ->from('renovation_items')
                                            ->whereRaw('renovation_items.quotation_template_item_id = quotation_template_items.id')
                                            ->where(function ($query) use ($renovation_document_id) {
                                                $query->where('renovation_items.renovation_document_id', $renovation_document_id);
                                            });
                                    });
                            })
                            ->pluck('id');
                                DB::table('items_index')->insert([
                                    'document_id' => $renovation_document_id,
                                    'aow_id' => $section_area_of_works_id,
                                    'items_sequence' => json_encode($quotation_template_items_ids) // Storing array as JSON
                                ]);  
                        }

                        
                    }
                
                }
            }

        }
        

        Schema::enableForeignKeyConstraints();

        return 0;
    }
}
