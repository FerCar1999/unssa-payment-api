<?php

namespace App\Models;

use App\Traits\TraitUuid as TraitUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    protected $connection = 'mysql';

    protected $fillable = ['date_time_transaction', 'transaction_id','order_id', 'amount', 'code', 'career', 'cycle', 'complete_name'];

    //RELACIONES
    public function paymentDetails()
    {
        return $this->hasMany(PaymentDetail::class);
    }

    //FUNCIONES DE SQL SERVER

    public function getPaymentsMade($cycle, $code)
    {
        return DB::connection('sqlsrv')->select('EXEC PPAGOS_OBTIENE_PAGOS_REALIZADOS ?, ?', [$cycle, $code]);
    }

    //funcion para obtener aranceles
    public function getDuty($carnet_student)
    {
        return DB::connection('sqlsrv')->select('EXEC PPAGOS_OBTIENE_ARANCELES_CARRERA ?', [$carnet_student]);
    }
}
