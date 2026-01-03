<?php
// controllers/FileController.php
require_once '../includes/Database.php';
require_once '../models/File.php';

class FileController {
    private $db;
    private $fileModel;
    private $uploadDir = '../uploads/images/';
    private $tempDir = '../uploads/temp/';
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxSize = 5 * 1024 * 1024; // 5MB
    
    public function __construct() {
        $this->db = new Database();
        $this->fileModel = new File();
        $this->createDirectories();
    }
    
    private function createDirectories() {
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }
    
    public function getAllFiles() {
        header('Content-Type: application/json');
        
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            
            $files = $this->fileModel->findAll($limit, $offset);
            
            // Add URL and formatted size to each file
            foreach ($files as &$file) {
                $file['url'] = str_replace('../', '', $file['file_path']);
                $file['size_formatted'] = $this->fileModel->formatFileSize($file['file_size']);
                $file['extension'] = $this->fileModel->getFileExtension($file['original_name']);
            }
            
            $total = $this->fileModel->countAll();
            
            echo json_encode([
                'success' => true,
                'data' => $files,
                'count' => count($files),
                'total' => $total,
                'pagination' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'has_more' => ($offset + count($files)) < $total
                ]
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function getFile($id) {
        header('Content-Type: application/json');
        
        try {
            $file = $this->fileModel->findById($id);
            
            if ($file) {
                $file['url'] = str_replace('../', '', $file['file_path']);
                $file['size_formatted'] = $this->fileModel->formatFileSize($file['file_size']);
                $file['extension'] = $this->fileModel->getFileExtension($file['original_name']);
                $file['file_info'] = $this->fileModel->getFileInfo($file['file_path']);
                
                echo json_encode(['success' => true, 'data' => $file]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'File not found']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function uploadImage() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        if (!isset($_FILES['image'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }
        
        $file = $_FILES['image'];
        
        // Validate file using File model
        $validation = $this->fileModel->validateImageFile($file, $this->maxSize, $this->allowedTypes);
        
        if (!$validation['valid']) {
            http_response_code(400);
            echo json_encode(['error' => implode(', ', $validation['errors'])]);
            return;
        }
        
        // Generate unique filename
        $filename = $this->fileModel->generateUniqueFilename($file['name']);
        $filepath = $this->uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Prepare data for database
            $fileData = [
                'filename' => $filename,
                'original_name' => $file['name'],
                'file_path' => $filepath,
                'file_size' => $file['size'],
                'file_type' => $validation['mime_type']
            ];
            
            // Save to database using File model
            $fileId = $this->fileModel->create($fileData);
            
            if ($fileId) {
                // Get the saved file data
                $savedFile = $this->fileModel->findById($fileId);
                $savedFile['url'] = str_replace('../', '', $savedFile['file_path']);
                $savedFile['size_formatted'] = $this->fileModel->formatFileSize($savedFile['file_size']);
                
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'File uploaded successfully',
                    'data' => $savedFile
                ]);
            } else {
                // Delete file if database insert fails
                unlink($filepath);
                http_response_code(500);
                echo json_encode(['error' => 'Failed to save file info to database']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload file']);
        }
    }
    
    public function deleteImage($id) {
        header('Content-Type: application/json');
        
        try {
            // Get file info from database
            $file = $this->fileModel->findById($id);
            
            if (!$file) {
                http_response_code(404);
                echo json_encode(['error' => 'File not found']);
                return;
            }
            
            // Delete file from filesystem
            if (file_exists($file['file_path'])) {
                if (!unlink($file['file_path'])) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to delete file from filesystem']);
                    return;
                }
            }
            
            // Delete from database using File model
            if ($this->fileModel->delete($id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete file from database']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function searchFiles() {
        header('Content-Type: application/json');
        
        if (!isset($_GET['q']) || empty($_GET['q'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Search query is required']);
            return;
        }
        
        try {
            $query = trim($_GET['q']);
            $files = $this->fileModel->search($query);
            
            // Add URL and formatted size to each file
            foreach ($files as &$file) {
                $file['url'] = str_replace('../', '', $file['file_path']);
                $file['size_formatted'] = $this->fileModel->formatFileSize($file['file_size']);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $files,
                'count' => count($files),
                'query' => $query
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function getStorageStats() {
        header('Content-Type: application/json');
        
        try {
            $stats = $this->fileModel->getStorageStats();
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function cleanupOrphanedFiles() {
        header('Content-Type: application/json');
        
        try {
            $result = $this->fileModel->cleanupOrphanedFiles();
            
            echo json_encode([
                'success' => true,
                'message' => 'Cleanup completed',
                'data' => $result
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
?>