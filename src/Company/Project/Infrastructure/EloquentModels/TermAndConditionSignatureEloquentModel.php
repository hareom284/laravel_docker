<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class TermAndConditionSignatureEloquentModel extends Model implements HasMedia
{

    use InteractsWithMedia;

    protected $table = 'term_and_condition_signatures';
    protected $fillable = ['contract_id', 'term_and_condition_page_id', 'customer_signatures'];
    protected $appends = ['customer_signatures_with_data'];
    
    public function getCustomerSignaturesWithDataAttribute()
    {
        $customerSignatures = json_decode($this->customer_signatures);
        if (!is_array($customerSignatures)) {
            return [];
        }
        if(is_null($this->updated_at)){
            $date = Carbon::parse($this->created_at)->format('d F Y');
        }else{
            $date = Carbon::parse($this->updated_at)->format('d F Y');
        }
        // Extract user_ids from the signatures
        $userIds = array_column($customerSignatures, 'user_id');

        // Fetch the associated users
        $users = UserEloquentModel::whereIn('id', $userIds)
            ->with('customers')
            ->get()
            ->keyBy('id'); // Key by user_id for easy lookup

        // Map signatures to include user data
        return array_map(function ($signature) use ($users, $date) {
            return [
                'user_id' => $signature->user_id, // Access as object property
                'user_data' => $users->get($signature->user_id), // Fetch user data
                'signature' => $signature->signature, // Access as object property,
                'signed_date' => $date
            ];
        }, $customerSignatures);
    }

    public function content()
    {
        return $this->belongsTo(TermAndConditionParagraphEloquentModel::class, 'term_and_condition_paragraph_id');
    }
}
