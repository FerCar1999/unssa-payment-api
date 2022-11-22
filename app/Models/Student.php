<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    use HasFactory;

    public function checkLogin($code, $password, $ip)
    {
        $validate = DB::connection('sqlsrv')->select('SET NOCOUNT ON;EXEC ValidarAlumno ?,?,?', [$code, $password, $ip]);
        return $validate[0]->valor;
    }

    public function getInformation($code)
    {
        $data = DB::connection('sqlsrv')->select('SET NOCOUNT ON;EXEC PPAGOS_OBTIENE_DATOS_ESTUDIANTE ?', [$code]);
        return $data[0];
    }
}
