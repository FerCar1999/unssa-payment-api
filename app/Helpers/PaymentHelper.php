<?php

use App\Helpers;

//Funcion para verificar los valores que vienen de eci
function checkECI($eci)
{
    switch ($eci) {
        case '05':
        case '06':
            return array(
                'status' => true
            );
            break;

        default:
            return array(
                'status' => false,
                'message' => 'No se pudo aplicar 3DS a la transacción'
            );
            break;
    }
}

function checkAuthentication($authentication)
{
    switch ($authentication) {
        case 'Y':
            return array(
                'status' => true
            );
            break;
        case 'A':
        case 'N':
        case 'U':
        case 'R':
            return array(
                'status' => false,
                'message' => 'No se pudo aplicar 3DS a la transacción'
            );
            break;
    }
}
