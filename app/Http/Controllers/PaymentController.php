<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Student;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

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

    //FUNCIONES PARA OBTENER INFORMACION RELACIONADA A LOS PAGOS DEL ALUMNO

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

    public function accountStatus(Request $request)
    {
        $token = $request->header('token');
        if ($token) {
            $code = Crypt::decrypt($token);
            $paymentsMade = $this->paymentsMade($request);
            $student = $this->student->getInformation($code);
            $duties = $this->payment->getDuty($student->per_carnet);
            foreach ($duties as $duty) {
                $flag = false;
                foreach ($paymentsMade as $paymentMade) {
                    if ($duty->tmo_codigo == $paymentMade->dmo_codtmo) {
                        $flag = true;
                    }
                }
                //Si el pago pendiente no ha sido encontrado en los pagos realizados
                if ($flag == false) {
                    array_push($paymentsMade, (object) array(
                        'mov_recibo' => null,
                        'tmo_descripcion' => $duty->tmo_descripcion,
                        'dmo_codtmo' => $duty->tmo_codigo,
                        'valor' => $duty->tmo_valor,
                        'mov_fecha' => null,
                        'estado' => "No realizado",
                    ));
                }
            }
            return $paymentsMade;
        } else {
            return message(false, "Debe iniciar sesión", null, 400);
        }
    }

    //FUNCION PARA REALIZAR EL PAGO

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
                $payment->date_time_transaction = Carbon::now();
                //Generando el UUID para el authorization y el order
                $transaction = Str::uuid();
                $payment->transaction_id = $transaction;
                $order = Str::uuid();
                $payment->order_id = $order;
                //Sacando monto total de la transaccion
                $payment->amount = 0;
                foreach ($request->input('details') as $detail) {
                    $payment->amount += $detail['tariff_amount'];
                }
                $payment->code = $student->per_carnet;
                $payment->career = $student->carrera;
                $payment->cycle = $this->cycle->getActualCycle();
                $payment->complete_name = $student->per_nombres_apellidos;
                $payment->status = 0;

                if ($payment->save()) {
                    $payment_details = array();
                    foreach ($request->input('details') as $detail) {
                        $payment_detail = new PaymentDetail();
                        $payment_detail->payment_id = $payment->id;
                        $payment_detail->tariff_code = $detail['tariff_code'];
                        $payment_detail->tariff_name = $detail['tariff_name'];
                        $payment_detail->tariff_amount = $detail['tariff_amount'];
                        $payment_detail->save();
                        array_push($payment_details, $payment_detail);
                    }
                    $payment->date_time_transaction = Carbon::parse($payment->date_time_transaction)->format('d/m/Y');
                    $payment->paymentDetails = $payment_details;
                    $saleMethod = $this->sendPayment($request, $transaction, $order);
                    return message(true, "Pago registrado con éxito", $saleMethod, 201);
                } else {
                    return message(false, "Ha sucedido un error al intentar registrar el pago", null, 500);
                }
            } else {
                return message(false, "No se ha encontrado el estudiante al cual registrar el pago", null, 401);
            }
        } else {
            return message(false, "Al parecer, no se encuentra registrado para realizar el pago", null, 401);
        }
    }

    //FUNCIONES DE ADMINISTRADOR
    public function getOnlinePayments()
    {
        $start_date = Carbon::now()->format('Y') . '-01-01 00:00:00';
        $end_date = Carbon::now()->format('Y') . '-12-31 23:59:59';
        return $this->getPaymentsByDate($start_date, $end_date);
    }

    public function getOnlinePaymentsByDates($start_date, $end_date)
    {
        return $this->getPaymentsByDate($start_date . ' 00:00:00', $end_date . ' 23:59:59');
    }

    public function getPaymentsByDate($start_date, $end_date)
    {
        $payments = $this->payment->with('paymentDetails')->whereBetween('date_time_transaction', [$start_date, $end_date])->get();
        //Mapeando los datos para enviar solo los necesarios para la tabla
        $payments_map = $payments->map(function ($payment, $key) {
            $payment_details_map = $payment->paymentDetails->map(function ($paymentDetail, $key) use ($payment) {
                return (object) array(
                    'transaction_id' => $payment->transaction_id,
                    'code' => $payment->code,
                    'date_time_transaction' => Carbon::parse($payment->date_time_transaction)->format('d/m/Y'),
                    'tariff_code' => $paymentDetail->tariff_code,
                    'tariff_name' => $paymentDetail->tariff_name,
                    'tariff_amount' => $paymentDetail->tariff_amount,
                );
            });
            return $payment_details_map;
        });
        return $payments_map->flatten()->all();
    }

    public function sendPayment($request, $transaction, $order)
    {
        $data = array(
            'TransactionIdentifier' => $transaction,
            'TotalAmount' => 1,
            'CurrencyCode' => 840,
            'ThreeDSecure' => true,
            'Source' => (object) array(
                'CardPresent' => false,
                'CardEmvFallback' => false,
                'ManualEntry' => false,
                'Debit' => false,
                'Contactless' => false,
                'CardPan' => $request->input('credit_card_number'),
                'CardCvv' => $request->input('credit_card_cvv'),
                'CardExpiration' => $request->input('credit_card_exp_date'),
                'CardholderName' => $request->input('credit_card_name'),
            ),
            'OrderIdentifier' => $order,
            'AddressMatch' => false,
            'ExtendedData' => (object) array(
                'MerchantResponseUrl' => 'https://api.unssa.edu.sv/unssa-payment-api/public/api/payments/receive-payment',
                'ThreeDSecure' => (object) array(
                    'ChallengeWindowSize' => 4
                ),
            )
        );

        $sale = Http::withHeaders([
            'Content-Type' => 'application/json',
            'PowerTranz-PowerTranzId' => env('POWERTRANZ_ID', "88804847"),
            'PowerTranz-PowerTranzPassword' => env('POWERTRANZ_PASSWORD', "ZGkBp6N1x1JfFBYbKzcXd4SN1vz97hkbTCwjrJoxf4nv26132abSp3")
        ])->post(
            'https://staging.ptranz.com/Api/spi/sale',
            $data
        )->object();

        return $sale;
    }

    public function receivePayment(Request $request)
    {
        $data = json_decode($request->input('Response'));
        //Verificando que el proceso de autenticación fue completado
        if ($data->IsoResponseCode == "3D0" && $data->ResponseMessage == "3D-Secure complete") {
            //Evaluando si la transaccion es 3DS
            $eci_response = checkECI($data->iskManagement->ThreeDSecure->Eci);
            if ($eci_response['status']) {
                $authentication_response = checkAuthentication($data->RiskManagement->ThreeDSecure->AuthenticationStatus);
                if ($authentication_response['status']) {

                    //Ejecutando el metodo payment para validar el pago
                    $payment_response = json_decode($this->paymentMethod($data->SpiToken));
                    //Cambiando el estado de la transaccion a 1 para que ese pago sea el pichula
                    if ($payment_response != null) {
                        if ($payment_response->Approved) {
                            $change_payment_status = $this->payment->where('transaction_id', $data->TransactionIdentifier)->update(['transaction' => $payment_response->AuthorizationCode, 'status' => 1]);
                            //Enviando la vista con el pago realizado con éxito
                            $payment = $this->payment->where('transaction_id', $data->TransactionIdentifier)->with('paymentDetails')->first();
                            return view('payment', compact('payment'));
                        } else {
                            return message(false, $payment_response->ResponseMessage, null, 400);
                        }
                    } else {
                        return message(false, "Al parecer no se pudo procesar el pago, intentelo más tarde", null, 400);
                    }
                } else {
                    return $authentication_response['message'];
                }
            } else {
                return $eci_response['message'];
            }
        }
    }

    public function paymentMethod($spi_token)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://staging.ptranz.com/api/spi/payment',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '"' . $spi_token . '"',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
