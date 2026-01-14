<?php
class Response {
    public static function json($data = null, $status = 200, $message = '') {
        header('Content-Type: application/json');
        http_response_code($status);
        
        $response = [
            'status' => $status >= 200 && $status < 300,
            'message' => $message,
            'data' => $data
        ];
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }
    
    public static function error($message = 'An error occurred', $status = 500) {
        self::json(null, $status, $message);
    }
}
?>