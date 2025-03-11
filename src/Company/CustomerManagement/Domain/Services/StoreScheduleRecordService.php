<?php

namespace Src\Company\CustomerManagement\Domain\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\ScheduleEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class StoreScheduleRecordService
{

    public function storeRecord($receiver_id, $title = '', $message = '', $notification_types = '', $sender_id = '', $duration = 0, $whatsapp_template, $whatsapp_language)
    {

        try {
            DB::beginTransaction();

            $deadline = Carbon::now()->addMinutes($duration);
            $receiver = UserEloquentModel::find($receiver_id);
            if ($receiver) {
                $exists_record = ScheduleEloquentModel::where('receiver_id', $receiver->id)->first();
                if ($exists_record) {
                    $exists_record->delete();
                }
                $email = $receiver->email ?? null;
                $whatsapp_number = $receiver->prefix ? $receiver->prefix . $receiver->contact_no : $receiver->contact_no;
                ScheduleEloquentModel::create(
                    [
                        'receiver_id' => $receiver->id,
                        'deadline' => $deadline,
                        'email' => $email,
                        'title' => $title,
                        'message' => $message,
                        'whatsapp_number' => $whatsapp_number,
                        'notification_types' => $notification_types,
                        'sender_id' => $sender_id,
                        'whatsapp_template' => $whatsapp_template,
                        'whatsapp_language' => $whatsapp_language
                    ]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
