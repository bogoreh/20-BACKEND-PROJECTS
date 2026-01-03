<?php
require_once 'config.php';

class FileValidator {
    
    public static function validateFile($file, $index = 0) {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['error']) || $file['error'] == UPLOAD_ERR_NO_FILE) {
            $errors[] = 'No file was uploaded.';
            return $errors;
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = self::getUploadError($file['error']);
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'File size exceeds maximum limit of ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB.';
        }
        
        // Get file info
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        // Validate MIME type
        if (!in_array($mimeType, ALLOWED_TYPES)) {
            $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', ALLOWED_EXTENSIONS);
        }
        
        // Validate extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            $errors[] = 'File extension not allowed.';
        }
        
        // Check for malicious content in images
        if (strpos($mimeType, 'image/') === 0) {
            if (!self::isValidImage($file['tmp_name'])) {
                $errors[] = 'Invalid image file.';
            }
        }
        
        return $errors;
    }
    
    private static function getUploadError($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize directive in php.ini.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE directive in HTML form.';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk.';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload.';
            default:
                return 'Unknown upload error.';
        }
    }
    
    private static function isValidImage($filePath) {
        $imageInfo = getimagesize($filePath);
        return $imageInfo !== false;
    }
    
    public static function sanitizeFilename($filename) {
        // Remove path information and sanitize
        $filename = basename($filename);
        
        // Replace spaces and special characters
        $filename = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $filename);
        
        // Limit filename length
        if (strlen($filename) > 255) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - strlen($extension) - 1);
            $filename = $name . '.' . $extension;
        }
        
        // Add timestamp to prevent overwriting
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $timestamp = time();
        
        return $name . '_' . $timestamp . '.' . $extension;
    }
}
?>