<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Disbursement extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = [
        'amount',
        'merchant_id',
        'merchant_id',
        'commission',
        'commission_percent',
        'orders_start',
        'orders_end',
        'reference',
        'nb_of_orders',
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
