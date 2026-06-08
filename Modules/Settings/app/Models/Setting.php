<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'company_name',
        'email',
        'contact_no',
        'phone',
        'local_address',
        'full_address',
        'pan_vat',
        'brief_description',

        'facebook',
        'instagram',
        'whatsapp',
        'youtube',
        'twitter',
        'linkedin',
        'github',

        'meta_title',
        'meta_keywords',
        'meta_description',

        'company_logo',
        'company_favicon',
        'footer_logo',
        'home_bg_img',
    ];
}
