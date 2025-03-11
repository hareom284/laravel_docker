<?php

namespace Src\Common\Presentation\CLI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UpdateMultipleTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'template:update';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update multiple template';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('sections')) { 
            $sections = DB::table('sections')
                        ->select('salesperson_id')
                        ->groupBy('salesperson_id')
                        ->get();

            foreach($sections as $section)
            {
                if(Schema::hasTable('quotation_templates'))
                {
                    $templateId = DB::table('quotation_templates')->insertGetId([
                        'salesperson_id' => $section->salesperson_id,
                        'name' => 'Default',
                        'archive' => 0
                    ]);                    

                    $this->info("Created Template for salesperson {$section->salesperson_id}");

                    DB::table('sections')->where('salesperson_id', $section->salesperson_id)->update([
                        'quotation_template_id' => $templateId
                    ]);
                }
            }
        }
        Schema::enableForeignKeyConstraints();
    }
}
