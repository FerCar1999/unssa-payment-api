<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;400;500;700&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            font-family: 'Roboto', Arial, sans-serif;
        }

        h1 {
            margin-bottom: 1.5rem;
        }

        h3 {
            margin-bottom: 1rem;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .row {
            display: flex;
            flex-direction: column;
        }

        @media screen and (min-width: 568px) {
            .row {
                flex-direction: row;
            }
        }

        .col {
            flex: 1 1;
        }

        .justify-between {
            justify-content: space-between;
        }

        hr {
            height: 2px;
            background: #e0e0e0;
            border: 0;
        }

        table {
            border: 1px solid #e0e0e0;
            border-collapse: collapse;
            padding: 15px 20px;
            width: 100%;
        }

        table th {
            padding: 12px 0;
            text-align: left;
            font-weight: bold;
        }

        table td,
        table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tbody tr:hover {
            background-color: #ddd;
        }

        .buttons {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 15px;
        }

        .buttons a {
            text-decoration: none;
            color: black
        }

        .buttons button,
        .buttons a {
            cursor: pointer;
            border: 0;
            border-radius: 5px;
            letter-spacing: 0.5px;
            padding: 10px 15px;
            text-transform: uppercase;
        }

        .buttons .button__go-back {
            background-color: #FDAF04;
        }
    </style>
</head>

<body>
    <h1 class="text-center">¡Pago realizado con éxito!</h1>
    <h3>Información del pago</h3>
    <div class="row justify-between">
        <div class="col">
            <p><span class="text-bold">Código del Estudiante:</span> {{ $payment->code }}</p>
            <p><span class="text-bold">Nombre Estudiante:</span> {{ $payment->complete_name }}</p>
            <p><span class="text-bold">Hora de la transacción:</span> {{ $payment->date_time_transaction }}</p>
            <p><span class="text-bold">Monto total:</span> ${{ $payment->amount }}</p>
        </div>
        <div class="col">
            <p><span class="text-bold">Número de Autorización:</span> {{ $payment->transaction }}</p>
        </div>
    </div>
    <p class="text-bold">Detalle</p>
    <hr>
    <div style="overflow-x: auto; margin-bottom: 1rem;">
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Código de Arancel</th>
                    <th style="width: 50%;">Nombre de Arancel</th>
                    <th>Precios</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payment->paymentDetails as $paymentDetail)
                    <tr>
                        <td>{{ $paymentDetail->tariff_code }}</td>
                        <td>{{ $paymentDetail->tariff_name }}</td>
                        $ <td>{{ $paymentDetail->tariff_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="display: flex; justify-content: end;">
            <p><span class="text-bold">Monto total:</span> ${{ $payment->amount }}</p>
        </div>
    </div>
</body>

</html>
