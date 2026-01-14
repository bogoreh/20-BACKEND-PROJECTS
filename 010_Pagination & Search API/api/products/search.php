<?php
require_once '../../api/config/database.php';
require_once '../../api/includes/Database.php';
require_once '../../api/includes/Response.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get query parameters
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(max(1, intval($_GET['limit'])), 100) : 10;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    if (empty($search)) {
        Response::error('Search query is required', 400);
    }
    
    // Search in multiple columns
    $sql = "SELECT * FROM products 
            WHERE name LIKE :search 
               OR description LIKE :search 
               OR category LIKE :search 
            LIMIT :limit OFFSET :offset";
    
    $countSQL = "SELECT COUNT(*) as total FROM products 
                 WHERE name LIKE :search 
                    OR description LIKE :search 
                    OR category LIKE :search";
    
    $searchParam = "%" . $search . "%";
    
    // Get total count
    $countStmt = $conn->prepare($countSQL);
    $countStmt->bindValue(':search', $searchParam);
    $countStmt->execute();
    $totalResult = $countStmt->fetch();
    $totalItems = $totalResult['total'];
    $totalPages = ceil($totalItems / $limit);
    
    // Get search results
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':search', $searchParam);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    $response = [
        'search_query' => $search,
        'results' => $results,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total_results' => $totalItems,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ]
    ];
    
    Response::json($response);
    
} catch (Exception $e) {
    Response::error($e->getMessage());
}
?>