<?php
// backend/utils/Response.php

class Response {
    public static function json($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit();
    }
    
    public static function error($msg, $code = 400) {
        self::json(array('msg' => $msg), $code);
    }
    
    public static function success($msg, $data = null, $code = 200) {
        $response = array('msg' => $msg);
        if ($data !== null) {
            $response = array_merge($response, $data);
        }
        self::json($response, $code);
    }
}
?>
