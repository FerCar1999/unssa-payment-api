<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory;

    public function getPaymentsMade($cycle, $code)
    {
        return DB::select('EXEC PPAGOS_OBTIENE_PAGOS_REALIZADOS ?, ?', [$cycle, $code]);
    }

    //funcion para obtener aranceles
    public function getDuty($career_code)
    {
        return DB::select('EXEC PPAGOS_OBTIENE_ARANCELES_CARRERA ?', [$career_code]);
    }
}
