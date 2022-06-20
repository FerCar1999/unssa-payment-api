<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function login(LoginRequest $request)
    {
        $validateData = $request->validated();
        $code = $validateData['code'];
        $password = $validateData['password'];
        $ip = $request->ip();
        if ($this->student->checkLogin($code, $password, $ip)) {
            $data = $this->student->getInformation($code);
            $data->token = Crypt::encrypt($code);
            return message(true, "Sesión iniciada con éxito", $data, 200);
        } else {
            return message(false, "Verifique sus credenciales", null, 200);
        }
    }

    public function me(Request $request)
    {
        $token = $request->header('token');
        if ($token) {
            $code = Crypt::decrypt($token);
            $data = $this->student->getInformation($code);
            return $data;
        } else {
            return message(false, "Debe iniciar sesión", null, 400);
        }
    }
}
