<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StadisticController extends Controller
{
    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function countPaymentsByCareers($start_date, $end_date)
    {
        $paymentsByCareer = $this->payment->whereBetween('date_time_transaction', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->select('career', DB::raw('count(career) as total'))->groupBy('career')->get();
        return $paymentsByCareer;
    }

    public function onlineVsRegister($start_date, $end_date)
    {
        $onlinePayments = $this->payment->whereBetween('date_time_transaction', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])->count();
        //Aqui queda pendiente los pagos que se han realizado en las cajas de UNSSA
        $registerPayments = 0;
        return array(
            'online' => $onlinePayments,
            'register' => $registerPayments
        );
    }
}
