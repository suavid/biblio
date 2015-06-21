<?php

function template() {

    return BM::singleton()->getObject('temp');
}

function page() {

    return BM::singleton()->getObject('temp')->getPage();
}

function data_model() {

    return BM::singleton()->getObject('db');
}

function set_type($data) {

    $data = (is_string($data)) ? data_model()->sanitizeData($data) : $data;
    $val = (is_string($data)) ? "'$data'" : $data;
    return $val;
}

function sumar_dias_habiles($fecha_actual, $dias) {
    // Formato Y-m-d
    $ct = 0;
    $nueva_fecha = $fecha_actual;
    while ($ct < $dias) {
        $nueva_fecha = strtotime('+1 day', strtotime($nueva_fecha));
        $nueva_fecha = date('Y-m-d', $nueva_fecha);
        $part = explode("-", $nueva_fecha);
        $dia = date("w", mktime(0, 0, 0, $part[1], $part[2], $part[0]));

        if ($dia != 0 && $dia != 6)
            $ct++;
    }

    return $nueva_fecha;
}

function compararFechas($primera, $segunda) {
    $valoresPrimera = explode("/", $primera);
    $valoresSegunda = explode("/", $segunda);

    $diaPrimera = $valoresPrimera[0];
    $mesPrimera = $valoresPrimera[1];
    $anyoPrimera = $valoresPrimera[2];

    $diaSegunda = $valoresSegunda[0];
    $mesSegunda = $valoresSegunda[1];
    $anyoSegunda = $valoresSegunda[2];

    $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);
    $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);

    if (!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)) {
        // "La fecha ".$primera." no es v&aacute;lida";
        return 0;
    } elseif (!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)) {
        // "La fecha ".$segunda." no es v&aacute;lida";
        return 0;
    } else {
        return $diasPrimeraJuliano - $diasSegundaJuliano;
    }
}


function safe_json_encode($value){
    if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
        $encoded = json_encode($value, JSON_PRETTY_PRINT);
    } else {
        $encoded = json_encode($value);
    }
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return $encoded;
        case JSON_ERROR_DEPTH:
            return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
        case JSON_ERROR_STATE_MISMATCH:
            return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
        case JSON_ERROR_CTRL_CHAR:
            return 'Unexpected control character found';
        case JSON_ERROR_SYNTAX:
            return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
        case JSON_ERROR_UTF8:
            $clean = utf8ize($value);
            return safe_json_encode($clean);
        default:
            return 'Unknown error'; // or trigger_error() or throw new Exception()

    }
}

function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } else if (is_string ($mixed)) {
        return utf8_encode($mixed);
    }
    return $mixed;
}


function upload_image($destination_dir, $name_media_field, $productid) {
    $tmp_name = $_FILES[$name_media_field]['tmp_name'];
    if (is_dir($destination_dir) && is_uploaded_file($tmp_name)) {
        $img_type = $_FILES[$name_media_field]['type'];  
        $img_file = 'thumbnail_' . $productid;
        if (((strpos($img_type, "gif") || strpos($img_type, "jpeg") || strpos($img_type, "jpg")) || strpos($img_type, "png"))) {
            if (move_uploaded_file($tmp_name, $destination_dir . '/' . $img_file)) {
                return true;
            }
        }
    }
    return false;
}

function upload_pdf($destination_dir, $name_media_field, $productid) {
    $tmp_name = $_FILES[$name_media_field]['tmp_name'];
    if (is_dir($destination_dir) && is_uploaded_file($tmp_name)) {
        $img_file = 'documento_' . $productid;
        $img_type = $_FILES[$name_media_field]['type'];
        if (strpos($img_type, "pdf")) {
            if (move_uploaded_file($tmp_name, $destination_dir . '/' . $img_file)) {
                return true;
            }
        }
    }
    return false;
}

?>