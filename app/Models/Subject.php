<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Subject extends Model
{
    use HasFactory;

    public function getSubjects($code, $cycle)
    {
        return DB::select('EXEC PPAGOS_OBTIENE_MATERIAS_INSCRITAS ?,?', [$code, $cycle]);
    }
}
