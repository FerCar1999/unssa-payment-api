<?php

use App\Helpers;

//Funcion para verificar los valores que vienen de eci
function checkECI($eci)
{
    switch ($eci) {
        case '05':
        case '02':
            return array(
                'status' => true
            );
            break;
        case '01':
        case '06':
            return array(
                'status' => false,
                'message' => 'Se intentó realizar autenticación 3DS'
            );
            break;
        case '07':
        case '00':
            return array(
                'status' => false,
                'message' => 'La autenticación 3DS fracasó o no estaba disponible. La transacción se considera no 3DS'
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
