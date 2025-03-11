<?php

declare(strict_types=1);

namespace Src\Company\CustomerManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class ScheduleEloquentModel extends Model
{

    protected $table = 'schedules';

    protected $fillable = [
        'deadline',
        'receiver_id',
        'notification_types',
        'whatsapp_number',
        'whatsapp_template',
        'whatsapp_language',
        'title',
        'message',
        'email',
        'sender_id',
    ];

    public function receiver()
    {
        return $this->belongsTo(UserEloquentModel::class, 'receiver_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(UserEloquentModel::class, 'sender_id', 'id');
    }
}
