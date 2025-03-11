<?php

declare(strict_types=1);

namespace Src\Company\UserManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;


class UserMetaEloquentModel extends Model
{

    protected $table = 'user_meta';
    protected $fillable = ['user_id', 'name','val'];
}
