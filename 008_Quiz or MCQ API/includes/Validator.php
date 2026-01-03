<?php
// includes/Validator.php
class Validator {
    
    public static function sanitize($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = self::sanitize($value);
            }
            return $input;
        }
        
        return htmlspecialchars(strip_tags(trim($input)));
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validateLength($string, $min, $max) {
        $length = strlen($string);
        return $length >= $min && $length <= $max;
    }
    
    public static function validateImage($file, $maxSize = 5242880, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Upload error: ' . $file['error'];
        }
        
        if ($file['size'] > $maxSize) {
            return 'File too large. Maximum size: ' . self::formatBytes($maxSize);
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes);
        }
        
        return true;
    }
    
    public static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
?>