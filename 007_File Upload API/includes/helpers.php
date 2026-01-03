<?php
require_once 'config.php';

class UploadHelper {
    
    public static function handleUpload($file, $customName = null) {
        $validator = new FileValidator();
        $errors = $validator::validateFile($file);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Generate safe filename
        if ($customName) {
            $filename = FileValidator::sanitizeFilename($customName . '_' . $file['name']);
        } else {
            $filename = FileValidator::sanitizeFilename($file['name']);
        }
        
        $targetPath = UPLOAD_DIR . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            
            // Additional security for images
            if (strpos(mime_content_type($targetPath), 'image/') === 0) {
                self::secureImage($targetPath);
            }
            
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $targetPath,
                'size' => $file['size'],
                'type' => $file['type'],
                'url' => self::getFileUrl($filename)
            ];
        } else {
            return [
                'success' => false,
                'errors' => ['Failed to move uploaded file.']
            ];
        }
    }
    
    private static function secureImage($imagePath) {
        // Strip EXIF data for privacy
        $image = imagecreatefromstring(file_get_contents($imagePath));
        if ($image !== false) {
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($image, $imagePath, 90);
                    break;
                case 'png':
                    imagepng($image, $imagePath, 9);
                    break;
                case 'gif':
                    imagegif($image, $imagePath);
                    break;
                case 'webp':
                    imagewebp($image, $imagePath, 90);
                    break;
            }
            
            imagedestroy($image);
        }
    }
    
    public static function getFileUrl($filename) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $base = dirname($_SERVER['PHP_SELF']);
        
        return $protocol . $host . rtrim($base, '/') . '/' . UPLOAD_DIR . $filename;
    }
    
    public static function getUploadedFiles() {
        $files = [];
        $uploadedFiles = scandir(UPLOAD_DIR);
        
        foreach ($uploadedFiles as $file) {
            if ($file !== '.' && $file !== '..' && !is_dir(UPLOAD_DIR . $file)) {
                $filePath = UPLOAD_DIR . $file;
                $files[] = [
                    'name' => $file,
                    'size' => filesize($filePath),
                    'type' => mime_content_type($filePath),
                    'modified' => filemtime($filePath),
                    'url' => self::getFileUrl($file)
                ];
            }
        }
        
        return $files;
    }
    
    public static function deleteFile($filename) {
        $filePath = UPLOAD_DIR . basename($filename);
        
        if (file_exists($filePath) && is_file($filePath)) {
            if (unlink($filePath)) {
                return ['success' => true, 'message' => 'File deleted successfully.'];
            } else {
                return ['success' => false, 'error' => 'Failed to delete file.'];
            }
        }
        
        return ['success' => false, 'error' => 'File not found.'];
    }
}
?>