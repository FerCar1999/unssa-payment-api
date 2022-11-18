<?php

namespace App\Models;

use App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use TraitUuid;

    protected $keyType = 'string';

    public function getPaymentsMade($cycle, $code)
    {
        return DB::connection('sqlsrv')->select('EXEC PPAGOS_OBTIENE_PAGOS_REALIZADOS ?, ?', [$cycle, $code]);
    }

    //funcion para obtener aranceles
    public function getDuty($career_code)
    {
        return DB::connection('sqlsrv')->select('EXEC PPAGOS_OBTIENE_ARANCELES_CARRERA ?', [$career_code]);
    }
}
