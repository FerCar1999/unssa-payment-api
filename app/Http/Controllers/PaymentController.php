<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PaymentController extends Controller
{
    private $student, $payment, $cycle;

    public function __construct(Student $student, Payment $payment, Cycle $cycle)
    {
        $this->student = $student;
        $this->payment = $payment;
        $this->cycle = $cycle;
    }

    public function paymentsMade(Request $request)
    {
        $token = $request->header('token');
        if ($token) {
            $code = Crypt::decrypt($token);
            $cycle = $this->cycle->getActualCycle();
            $data = $this->payment->getPaymentsMade($cycle, $code);
            return $data;
        } else {
            return message(false, "Debe iniciar sesión", null, 400);
        }
    }

    public function duty(Request $request)
    {
        $token = $request->header('token');
        if ($token) {
            $code = Crypt::decrypt($token);
            $student = $this->student->getInformation($code);
            $data = $this->payment->getDuty($student->car_codigo);
            return $data;
        } else {
            return message(false, "Debe iniciar sesión", null, 400);
        }
    }
}
