<?php

namespace Src\Common\Presentation\CLI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UpdateTemplatePL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'template:plupdate';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update template items P&L';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('quotation_template_items')) { 
            DB::table('quotation_template_items')
            ->where('price_with_gst', '>', 0)
            ->where('cost_price', '=', 0)
            ->update(['profit_margin' => 100]);

            DB::table('quotation_template_items')
            ->where('cost_price', '>', 0)
            ->where('price_with_gst', '=', 0)
            ->update(['profit_margin' => -100]);

            DB::table('quotation_template_items')
            ->where('price_with_gst', '>', 0)
            ->where('cost_price', '>', 0)
            ->update([
                'profit_margin' => DB::raw('ROUND(((price_with_gst - cost_price) / price_with_gst) * 100, 2)')
            ]);

            DB::table('quotation_template_items')
            ->where('cost_price', '=', 0)
            ->where('price_with_gst', '=', 0)
            ->update(['profit_margin' => 0]);

            $this->info("Updated P&L for template items");
        }
        Schema::enableForeignKeyConstraints();
    }
}
