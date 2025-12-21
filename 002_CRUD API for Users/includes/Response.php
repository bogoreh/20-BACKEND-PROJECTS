<?php
class Response {
    public static function send($data, $status = 200, $message = '') {
        header('Content-Type: application/json');
        http_response_code($status);
        
        $response = [
            'success' => $status >= 200 && $status < 300,
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        
        echo json_encode($response);
        exit();
    }
    
    public static function error($message, $status = 400) {
        self::send([], $status, $message);
    }
}
?>