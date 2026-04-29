<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'stripe_payment_id',
        'amount',
        'currency',
        'status',
    ];

    // علاقة: كل عملية شراء تخص مستخدم واحد
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة: كل عملية شراء تخص خطة واحدة
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}