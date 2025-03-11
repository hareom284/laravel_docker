<?php

namespace Src\Company\CustomerManagement\Domain\Services;

use Illuminate\Support\Facades\DB;

class StoreRecordToStatusHistoryService
{

    public function storeRecord($customer_id, $id_milestone_id, $remark = null, $message_sent = 0, $duration = 0)
    {

        try {
            DB::beginTransaction();
            DB::table('status_histories')->insert([
                'customer_id' => $customer_id,
                'id_milestone_id' => $id_milestone_id,
                'remark' => $remark,
                'message_sent' => $message_sent,
                'duration' => $duration,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
