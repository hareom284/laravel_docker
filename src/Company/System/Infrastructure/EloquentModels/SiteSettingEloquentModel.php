<?php

declare(strict_types=1);

namespace Src\Company\System\Infrastructure\EloquentModels;


use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteSettingEloquentModel extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'site_settings';

    protected $fillable = [
        'site_name',
        'ssl',
        'timezone',
        'locale',
        'url',
        'email',
        'contact_number',
        'website_logo',
        'website_favicon'
    ];

    public function getImageAttribute()
    {
        return $this->getMedia('image');
    }


}
