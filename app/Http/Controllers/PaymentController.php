<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PaymentController extends Controller
{
    private $student, $payment, $payment_detail, $cycle;

    public function __construct(Student $student, Payment $payment, PaymentDetail $payment_detail, Cycle $cycle)
    {
        $this->student = $student;
        $this->payment = $payment;
        $this->payment_detail = $payment_detail;
        $this->cycle = $cycle;
    }

    public function paymentsMade(Request $request)
    {
        $token = $request->header('token');
        if ($token) {
            $code = Crypt::decrypt($token);
            $cycle = $this->cycle->getActualCycle();
            $mades = $this->payment->getPaymentsMade($cycle, $code);
            //return $mades;
            $student = $this->student->getInformation($code);
            $online_payments = $this->payment->with('paymentDetails')->where('code', $student->per_carnet)->get();
            foreach ($online_payments as $online_payment) {
                foreach ($online_payment->paymentDetails as $paymentDetail) {
                    //Flag para saber si el pago se encuentra en registrados
                    $flag = false;
                    foreach ($mades as $made) {
                        if ($made->dmo_codtmo == $paymentDetail->tariff_code) {
                            $made->mov_recibo = $online_payment->transaction_id;
                            $made->estado = "En línea y facturado";
                            $flag = true;
                        }
                    }
                    if ($flag == false) {
                        array_push($mades, (object) array(
                            'mov_recibo' => $online_payment->transaction_id,
                            'tmo_descripcion' => $paymentDetail->tariff_name,
                            'mov_fecha' => Carbon::parse($online_payment->date_time_transaction,)->format('d/m/Y'),
                            'dmo_codtmo' => $paymentDetail->tariff_code,
                            'valor' => $paymentDetail->tariff_amount,
                            'estado' => "En línea"
                        ));
                    }
                }
            }
            return $mades;
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
            $duties = $this->payment->getDuty($student->per_carnet);
            //Obteniendo todos los pagos en linea registrados por el estudiante
            $online_payments = $this->payment->with('paymentDetails')->where('code', $student->per_carnet)->get();
            foreach ($duties as $duty) {
                foreach ($online_payments as $online_payment) {
                    foreach ($online_payment->paymentDetails as $paymentDetail) {
                        if ($duty->tmo_codigo == $paymentDetail->tariff_code) {
                            $duty->estado = "Pagado en Línea";
                        }
                    }
                }
            }
            return $duties;
        } else {
            return message(false, "Debe iniciar sesión", null, 400);
        }
    }

    //FUNCIONES YA INTERNAS

    public function store(Request $request)
    {
        //Verificando si viene token
        $token = $request->header('token');
        if ($token) {
            //Obteniendo información del estudiante
            $code = Crypt::decrypt($token);
            $student = $this->student->getInformation($code);
            if ($student) {
                $payment = new Payment();
                //Obteniendo receipt_id
                $payment->receipt_id = rand(10000000, 99999999);;
                $payment->date_time_transaction = Carbon::now();
                $payment->transaction_id = "198347";
                //Sacando monto total de la transaccion
                $payment->amount = 0;
                foreach ($request->input('details') as $detail) {
                    $payment->amount += $detail['tariff_amount'];
                }
                $payment->code = $student->per_carnet;
                $payment->career = $student->carrera;
                $payment->cycle = $this->cycle->getActualCycle();
                $payment->complete_name = $student->per_nombres_apellidos;
                if ($payment->save()) {
                    foreach ($request->input('details') as $detail) {
                        $payment_detail = new PaymentDetail();
                        $payment_detail->payment_id = $payment->id;
                        $payment_detail->tariff_code = $detail['tariff_code'];
                        $payment_detail->tariff_name = $detail['tariff_name'];
                        $payment_detail->tariff_amount = $detail['tariff_amount'];
                        $payment_detail->save();
                    }
                    return message(true, "Pago registrado con éxito", null, 201);
                } else {
                    return message(false, "Ha sucedido un error al intentar registrar el pago", null, 200);
                }
            } else {
                return message(false, "No se ha encontrado el estudiante al cual registrar el pago", null, 200);
            }
        } else {
            return message(false, "Al parecer, no se encuentra registrado para realizar el pago", null, 200);
        }
    }

    //FUNCIONES DE ADMINISTRADOR
    function getOnlinePayments()
    {
        return $this->payment->with('paymentDetails')->get();
    }

    //Obtener un código que no se ha registrado hoy
    public function getReceiptId()
    {
        $receiptId = null;
        $record = null;
        while ($record == null) {
            $receiptId = rand(10000000, 99999999);
            $record = $this->payment->where('receipt_id', $receiptId)->whereDate('date_time_transaction', Carbon::now()->format('Y-m-d'))->first();
        }
        return $receiptId;
    }
}
