<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model
{
    use HasFactory;

    public function checkLogin($user, $password)
    {
        $validate = DB::connection('sqlsrv')->select('EXEC PPAGOS_VALIDA_USUARIO ?,?', [$user, $password]);
        return $validate[0]->hayRegistros;
    }
}
