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
        default:
            return array(
                'status' => false,
                'message' => 'No se pudo aplicar 3DS a la transacción'
            );
            break;
    }
}

function checkISO($iso)
{
    switch ($iso) {
        case '03':
            return "Comercio Inválido";
            break;
        case '05':
            return "Transacción Declinada";
            break;
        case '12':
            return "Transacción Inválida";
            break;
        case '57':
            return "Tipo de tarjeta inváliido";
            break;
        case '89':
            return "Autenticación Fracasó";
            break;
        case '91':
            return "Se agotó el tiempo de respuesta";
            break;
        case '96':
        case '98':
        case '99':
            return "Error del sistema";
            break;
        case '97':
            return "Fracasó la solicitud de validación";
            break;
        case 'HP0':
            return "Prepocesamiento finalizado en la Página Alojadas";
            break;
        case 'SP4':
            return "Prepocesamiento SPI completado";
            break;
        case 'TK0':
            return "Tokenización completada";
            break;
        default:
            return 'No se pudo aplicar 3DS a la transacción';
            break;
    }
}

function check3DS($code)
{
    switch ($code) {
        default:
            return 'No se pudo aplicar 3DS a la transacción';
            break;
    }
}
