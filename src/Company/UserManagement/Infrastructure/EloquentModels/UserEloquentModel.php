<?php

declare(strict_types=1);

namespace Src\Company\UserManagement\Infrastructure\EloquentModels;

use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ContactUserEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleCommissionEloquentModel;

class UserEloquentModel extends  Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $table = 'users';

    protected $appends = ['permissions'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'prefix',
        'contact_no',
        'profile_pic',
        'name_prefix',
        'is_active',
        'quick_book_user_id',
        'xero_user_id',
        'relation_with_referrer',
        'is_referrer',
        'device_id'
    ];

    protected $hidden = [
        'password',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // hased password

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function roles()
    {
        return $this->belongsToMany(RoleEloquentModel::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Write an accessor for fetching permissions from multiple roles.
     * The previous implementation only worked for a single role.
     */
    public function getPermissionsAttribute()
    {
        $permissions = [];

        foreach ($this->roles as $role) {
            $permissions = array_merge($permissions, $role->permissions->pluck('name')->toArray());
        }

        $permissions = array_unique($permissions);

        return $permissions;
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(ProjectEloquentModel::class, 'salesperson_projects', 'salesperson_id', 'project_id')->withTimestamps();
    }

    public function staffs()
    {
        return $this->hasOne(StaffEloquentModel::class, 'user_id');
    }

    public function assignedSalepersons()
    {
        return $this->hasMany(StaffEloquentModel::class, 'mgr_id', 'id');
    }

    public function customers()
    {
        return $this->hasOne(CustomerEloquentModel::class, 'user_id');
    }

    public function checklists()
    {
        return $this->hasMany(CheckListEloquentModel::class, 'customer_id');
    }

    public function permissions()
    {
        return $this->hasManyThrough(PermissionEloquentModel::class, RoleEloquentModel::class);
    }

    public function customer_project()
    {
        return $this->hasOne(ProjectEloquentModel::class, 'customer_id');
    }

    public function projectPivot(): BelongsToMany
    {
        return $this->belongsToMany(ProjectEloquentModel::class, 'customer_project','user_id', 'project_id')->withPivot('property_id')->withTimestamps();
    }

    public function company()
    {
        return $this->belongsTo(CompanyEloquentModel::class);
    }

    public function contactUser(): HasOne
    {
        return $this->hasOne(ContactUserEloquentModel::class, 'user_id');
    }

    public function teams()
    {
        return $this->belongsToMany(TeamEloquentModel::class,'team_members','team_member_id','team_id')->withTimestamps();
    }

    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions);
    }

    public function saleCommissions()
    {
        return $this->hasMany(SaleCommissionEloquentModel::class, 'user_id');
    }

    public function approvedDocuments()
    {
        return $this->belongsToMany(RenovationDocumentsEloquentModel::class, 'document_approvers', 'user_id', 'renovation_document_id')->withTimestamps();
    }
    
    public function scopeFilter($query, $filters)
    {

        if (isset($filters['order'])) {
            $names = explode(' ', trim($filters['name'] ? $filters['name'] : ''));

            if (count($names) == 2) {
                $query->when($names, function ($query, $name) {
                    $query->where('first_name', 'like', '%' . $name[0] . '%')
                        ->Where('last_name', 'like', '%' . $name[1] . '%');
                });
            } else {
                $query->when($filters['name'] ?? false, function ($query, $name) {
                    $query->where('first_name', 'like', '%' . $name . '%')
                        ->orWhere('last_name', 'like', '%' . $name . '%');
                });
            }

            if (isset($filters['created_at'])) {
                $query->whereDate('created_at', $filters['created_at']);
            }

            $query->orderBy('first_name', $filters['order']);
        } else {
            // if (isset($filters['name'])) {
            //     $query->where(function ($query) use ($filters) {
            //         $query->where('first_name', 'like', '%' . $filters['name'] . '%')
            //             ->orWhere('last_name', 'like', '%' . $filters['name'] . '%');
            //     });
            // }

            // filter users by name and properties    
            if (isset($filters['name'])) {
            $query->where(function ($query) use ($filters) {
                // Search in first_name and last_name
                $query->where('first_name', 'like', '%' . $filters['name'] . '%')
                      ->orWhere('last_name', 'like', '%' . $filters['name'] . '%');
                
                $query->orWhereHas('customers.customer_properties', function ($query) use ($filters) {
                    $query->where('street_name', 'like', '%' . $filters['name'] . '%')
                          ->orWhere('postal_code', 'like', '%' . $filters['name'] . '%');
                });
            });
            }
            
            if (isset($filters['email'])) {
                $query->where('email', 'like', '%' . $filters['email'] . '%');
            }
        }

        $query->when($filters['status'] ?? false, function ($query, $status) {
            $query->whereHas('customers', function ($query) use ($status) {
                $query->where('id_milestone_id', $status);
            });
        });
        $query->when($filters['lead_source'] ?? false, function ($query, $source) {
            $query->whereHas('customers', function ($query) use ($source) {
                $query->where('source', $source);
            });
        });

        $query->when(isset($filters['start_date']) && isset($filters['end_date']), function ($query) use ($filters) {
            $query->whereBetween('created_at', [
                $filters['start_date'].' 00:00:00', 
                $filters['end_date']. ' 23:59:59'
            ]);
        });

        $query->when($filters['budget'] ?? false, function ($query, $budget) {
            $query->whereHas('customers', function ($query) use ($budget) {
                $query->where('budget', $budget);
            });
        });

        $query->when($filters['date'] ?? false, function ($query, $date) {
            $parsedDate = Carbon::createFromFormat('F Y', $date);
            $startOfMonth = $parsedDate->startOfMonth()->toDateString();
            $endOfMonth = $parsedDate->endOfMonth()->toDateString();
            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        });
    }



}
