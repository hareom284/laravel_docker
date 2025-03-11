<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;


class PaymentTypeEloquentModel extends Model
{

    protected $table = 'payment_types';
    protected $fillable = ['name'];

}
