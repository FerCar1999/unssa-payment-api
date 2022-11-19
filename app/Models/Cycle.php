<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cycle extends Model
{
    use HasFactory;

    public function getActualCycle()
    {
        $cycle = DB::connection('sqlsrv')->select('SET NOCOUNT ON;EXEC PPAGOS_OBTIENE_CICLO_VIGENTE');
        return $cycle[0]->cil_codigo;
    }
}
