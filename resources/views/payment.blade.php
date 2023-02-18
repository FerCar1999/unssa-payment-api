<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <p>Pago realizado con éxito</p>
    <p>Hora de la transacción: {{ $payment->date_time_transaction }}</p>
    <p>Número de transacción: {{ $payment->transaction }}</p>
    <p>Monto total: {{ $payment->amount }}</p>
    <p>Código Estudiante: {{ $payment->code }}</p>
    <p>Nombre Estudiante: {{ $payment->complete_name }}</p>
    <ul>
        @foreach ($payment->paymentDetails as $paymentDetail)
            <li>{{$paymentDetail->tariff_name}} ${{$paymentDetail->tariff_amount}}</li>
        @endforeach
    </ul>
</body>

</html>
