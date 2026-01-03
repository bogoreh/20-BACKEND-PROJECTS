<?php
// includes/Auth.php
class Auth {
    private static $apiKeys = [
        'test_key_123' => ['name' => 'Test Client', 'permissions' => ['read', 'write']],
        'admin_key_456' => ['name' => 'Admin', 'permissions' => ['read', 'write', 'delete']]
    ];
    
    public static function authenticate() {
        $headers = getallheaders();
        
        // Check for API key in headers
        $apiKey = isset($headers['X-API-Key']) ? $headers['X-API-Key'] : 
                 (isset($_GET['api_key']) ? $_GET['api_key'] : null);
        
        if (!$apiKey || !isset(self::$apiKeys[$apiKey])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized: Invalid or missing API key']);
            exit();
        }
        
        return self::$apiKeys[$apiKey];
    }
    
    public static function checkPermission($user, $permission) {
        return in_array($permission, $user['permissions']);
    }
}
?>