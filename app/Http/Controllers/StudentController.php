<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    private $subject, $cycle;

    public function __construct(Subject $subject, Cycle $cycle)
    {
        $this->subject = $subject;
        $this->cycle = $cycle;
    }

    public function mySubjects(Request $request)
    {
        $token = $request->header('token');
        if ($token) {
            $code = Crypt::decrypt($token);
            $cycle = $this->cycle->getActualCycle();
            $data = $this->subject->getSubjects($code, $cycle);
            return $data;
        } else {
            return message(false, "Debe iniciar sesión", null, 400);
        }
    }
}
