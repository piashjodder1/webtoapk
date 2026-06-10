<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'type',
        'payment_instructions',
        'credentials',
        'user_input_fields',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'credentials' => 'json',
        'user_input_fields' => 'json',
        'is_active' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
