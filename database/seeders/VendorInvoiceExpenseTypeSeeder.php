<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\VendorInvoiceExpenseTypeEloquentModel;

class VendorInvoiceExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $expenseTypes = [
            [
                'name' => 'Project Relate Expense Type',
                "project_related" => true
            ],
            [
                'name' => 'Non Project Relate Expense Type',
                "project_related" => false
            ]
        ];

        foreach ($expenseTypes as $expenseType) {
            VendorInvoiceExpenseTypeEloquentModel::create($expenseType);
        }

        //update project related expense type in vendor invoice
        SupplierCostingEloquentModel::whereNotNull('project_id')->update(['vendor_invoice_expense_type_id' => 1]);

        //update non project related expense type in vendor invoice
        SupplierCostingEloquentModel::whereNull('project_id')->update(['vendor_invoice_expense_type_id' => 2]);
    }
}
