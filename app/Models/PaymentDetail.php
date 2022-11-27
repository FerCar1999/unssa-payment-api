<?php

namespace App\Models;

use App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{

    protected $connection = 'mysql';

    protected $fillable = ['payment_id', 'tariff_code', 'tariff_name', 'tariff_amount'];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
