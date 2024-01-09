<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Merchant extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = [
        'external_id',
        'reference',
        'email',
        'live_on',
        'disbursement_frequency',
        'minimum_monthly_fee',
        'ingest_date',
        'origin',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}
