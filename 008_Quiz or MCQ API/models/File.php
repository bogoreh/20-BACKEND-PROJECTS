<?php
// models/File.php
class File {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Find all uploaded files
     * @param int $limit Number of files to return
     * @param int $offset Offset for pagination
     * @return array List of files
     */
    public function findAll($limit = 100, $offset = 0) {
        $this->db->query('SELECT * FROM uploaded_files ORDER BY uploaded_at DESC LIMIT :limit OFFSET :offset');
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }
    
    /**
     * Find file by ID
     * @param int $id File ID
     * @return mixed File data or false if not found
     */
    public function findById($id) {
        $this->db->query('SELECT * FROM uploaded_files WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Find files by type
     * @param string $type File MIME type
     * @return array List of files
     */
    public function findByType($type) {
        $this->db->query('SELECT * FROM uploaded_files WHERE file_type LIKE :type ORDER BY uploaded_at DESC');
        $this->db->bind(':type', '%' . $type . '%');
        return $this->db->resultSet();
    }
    
    /**
     * Save file information to database
     * @param array $data File data
     * @return int|bool File ID on success, false on failure
     */
    public function create($data) {
        $this->db->query(
            'INSERT INTO uploaded_files 
            (filename, original_name, file_path, file_size, file_type, uploader_ip) 
            VALUES (:filename, :original_name, :file_path, :file_size, :file_type, :uploader_ip)'
        );
        
        $this->db->bind(':filename', $data['filename']);
        $this->db->bind(':original_name', $data['original_name']);
        $this->db->bind(':file_path', $data['file_path']);
        $this->db->bind(':file_size', $data['file_size']);
        $this->db->bind(':file_type', $data['file_type']);
        $this->db->bind(':uploader_ip', $this->getClientIp());
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update file information
     * @param int $id File ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = ['filename', 'original_name', 'file_path', 'file_size', 'file_type'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = $field . ' = :' . $field;
                $params[':' . $field] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = 'UPDATE uploaded_files SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->execute();
    }
    
    /**
     * Delete file record from database
     * @param int $id File ID
     * @return bool Success status
     */
    public function delete($id) {
        $this->db->query('DELETE FROM uploaded_files WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Get total number of uploaded files
     * @return int Total count
     */
    public function countAll() {
        $this->db->query('SELECT COUNT(*) as total FROM uploaded_files');
        $result = $this->db->single();
        return $result['total'];
    }
    
    /**
     * Get files by date range
     * @param string $start Start date (YYYY-MM-DD)
     * @param string $end End date (YYYY-MM-DD)
     * @return array List of files
     */
    public function findByDateRange($start, $end) {
        $this->db->query(
            'SELECT * FROM uploaded_files 
            WHERE DATE(uploaded_at) BETWEEN :start_date AND :end_date 
            ORDER BY uploaded_at DESC'
        );
        $this->db->bind(':start_date', $start);
        $this->db->bind(':end_date', $end);
        return $this->db->resultSet();
    }
    
    /**
     * Get recent files
     * @param int $limit Number of recent files to return
     * @return array List of recent files
     */
    public function findRecent($limit = 10) {
        $this->db->query(
            'SELECT * FROM uploaded_files 
            ORDER BY uploaded_at DESC 
            LIMIT :limit'
        );
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
    
    /**
     * Get files by size range
     * @param int $minSize Minimum size in bytes
     * @param int $maxSize Maximum size in bytes
     * @return array List of files
     */
    public function findBySizeRange($minSize, $maxSize) {
        $this->db->query(
            'SELECT * FROM uploaded_files 
            WHERE file_size BETWEEN :min_size AND :max_size 
            ORDER BY file_size DESC'
        );
        $this->db->bind(':min_size', $minSize);
        $this->db->bind(':max_size', $maxSize);
        return $this->db->resultSet();
    }
    
    /**
     * Search files by name or type
     * @param string $query Search query
     * @return array List of matching files
     */
    public function search($query) {
        $searchTerm = '%' . $query . '%';
        $this->db->query(
            'SELECT * FROM uploaded_files 
            WHERE original_name LIKE :query 
               OR filename LIKE :query 
               OR file_type LIKE :query 
            ORDER BY uploaded_at DESC'
        );
        $this->db->bind(':query', $searchTerm);
        return $this->db->resultSet();
    }
    
    /**
     * Get storage statistics
     * @return array Storage statistics
     */
    public function getStorageStats() {
        $this->db->query('
            SELECT 
                COUNT(*) as file_count,
                SUM(file_size) as total_size,
                AVG(file_size) as avg_size,
                MIN(file_size) as min_size,
                MAX(file_size) as max_size,
                file_type,
                COUNT(*) as type_count
            FROM uploaded_files 
            GROUP BY file_type
            ORDER BY type_count DESC
        ');
        
        $typeStats = $this->db->resultSet();
        
        $this->db->query('
            SELECT 
                DATE(uploaded_at) as upload_date,
                COUNT(*) as daily_count,
                SUM(file_size) as daily_size
            FROM uploaded_files 
            WHERE uploaded_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(uploaded_at)
            ORDER BY upload_date DESC
        ');
        
        $dailyStats = $this->db->resultSet();
        
        $this->db->query('
            SELECT 
                EXTRACT(HOUR FROM uploaded_at) as upload_hour,
                COUNT(*) as hourly_count
            FROM uploaded_files 
            GROUP BY EXTRACT(HOUR FROM uploaded_at)
            ORDER BY upload_hour
        ');
        
        $hourlyStats = $this->db->resultSet();
        
        return [
            'type_stats' => $typeStats,
            'daily_stats' => $dailyStats,
            'hourly_stats' => $hourlyStats
        ];
    }
    
    /**
     * Clean up orphaned files (files in database but not on disk)
     * @return array Cleanup results
     */
    public function cleanupOrphanedFiles() {
        $this->db->query('SELECT id, file_path FROM uploaded_files');
        $files = $this->db->resultSet();
        
        $orphaned = [];
        $cleaned = [];
        
        foreach ($files as $file) {
            if (!file_exists($file['file_path'])) {
                $orphaned[] = $file;
                // Remove from database
                $this->db->query('DELETE FROM uploaded_files WHERE id = :id');
                $this->db->bind(':id', $file['id']);
                if ($this->db->execute()) {
                    $cleaned[] = $file['id'];
                }
            }
        }
        
        return [
            'orphaned_count' => count($orphaned),
            'cleaned_count' => count($cleaned),
            'orphaned_files' => $orphaned,
            'cleaned_ids' => $cleaned
        ];
    }
    
    /**
     * Get client IP address
     * @return string Client IP
     */
    private function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    
    /**
     * Format file size for display
     * @param int $bytes File size in bytes
     * @param int $precision Number of decimal places
     * @return string Formatted file size
     */
    public static function formatFileSize($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    /**
     * Get file extension from filename
     * @param string $filename Filename
     * @return string File extension
     */
    public static function getFileExtension($filename) {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }
    
    /**
     * Get safe filename (remove special characters)
     * @param string $filename Original filename
     * @return string Safe filename
     */
    public static function getSafeFilename($filename) {
        $extension = self::getFileExtension($filename);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Remove special characters and replace spaces with underscores
        $safeName = preg_replace('/[^A-Za-z0-9\-]/', '_', $name);
        
        return $safeName . '.' . $extension;
    }
    
    /**
     * Generate unique filename
     * @param string $originalName Original filename
     * @return string Unique filename
     */
    public static function generateUniqueFilename($originalName) {
        $extension = self::getFileExtension($originalName);
        $timestamp = time();
        $random = bin2hex(random_bytes(4)); // 8 characters
        
        return $timestamp . '_' . $random . '.' . $extension;
    }
    
    /**
     * Validate image file
     * @param array $file $_FILES array element
     * @param int $maxSize Maximum file size in bytes
     * @param array $allowedTypes Allowed MIME types
     * @return array Validation result
     */
    public static function validateImageFile($file, $maxSize = 5242880, $allowedTypes = null) {
        if ($allowedTypes === null) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        }
        
        $errors = [];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
            ];
            
            $errors[] = $uploadErrors[$file['error']] ?? 'Unknown upload error';
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum limit of ' . self::formatFileSize($maxSize);
        }
        
        // Check file type
        if (!empty($file['tmp_name'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes);
            }
        }
        
        // Additional image validation
        if (empty($errors) && function_exists('getimagesize')) {
            $imageInfo = @getimagesize($file['tmp_name']);
            if (!$imageInfo) {
                $errors[] = 'Invalid image file';
            } else {
                // Check image dimensions (optional)
                $maxWidth = 5000;
                $maxHeight = 5000;
                if ($imageInfo[0] > $maxWidth || $imageInfo[1] > $maxHeight) {
                    $errors[] = "Image dimensions too large. Maximum: {$maxWidth}x{$maxHeight}";
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'mime_type' => $mimeType ?? null
        ];
    }
    
    /**
     * Get file info from path
     * @param string $filePath Path to file
     * @return array File information
     */
    public static function getFileInfo($filePath) {
        if (!file_exists($filePath)) {
            return [
                'exists' => false,
                'error' => 'File not found'
            ];
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        $size = filesize($filePath);
        $lastModified = filemtime($filePath);
        
        return [
            'exists' => true,
            'size' => $size,
            'size_formatted' => self::formatFileSize($size),
            'mime_type' => $mimeType,
            'last_modified' => date('Y-m-d H:i:s', $lastModified),
            'is_readable' => is_readable($filePath),
            'is_writable' => is_writable($filePath)
        ];
    }
}
?>