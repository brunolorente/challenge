<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = [
        'external_id',
        'merchant_reference',
        'amount',
        'created_at',
        'ingest_date',
        'origin',
        'disbursed',
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
