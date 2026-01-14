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
    
    // Get query parameters with default values
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(max(1, intval($_GET['limit'])), 100) : 10;
    $offset = ($page - 1) * $limit;
    
    // Filter parameters
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
    $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
    $sort_order = isset($_GET['sort_order']) && in_array(strtoupper($_GET['sort_order']), ['ASC', 'DESC']) 
        ? $_GET['sort_order'] 
        : 'ASC';
    
    // Build WHERE clause
    $whereClauses = [];
    $params = [];
    
    if ($category) {
        $whereClauses[] = "category = :category";
        $params[':category'] = $category;
    }
    
    if ($min_price !== null) {
        $whereClauses[] = "price >= :min_price";
        $params[':min_price'] = $min_price;
    }
    
    if ($max_price !== null) {
        $whereClauses[] = "price <= :max_price";
        $params[':max_price'] = $max_price;
    }
    
    $whereSQL = '';
    if (!empty($whereClauses)) {
        $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
    }
    
    // Validate sort column
    $allowedSortColumns = ['id', 'name', 'price', 'category', 'stock', 'created_at'];
    if (!in_array($sort_by, $allowedSortColumns)) {
        $sort_by = 'id';
    }
    
    // Get total count for pagination
    $countSQL = "SELECT COUNT(*) as total FROM products $whereSQL";
    $countStmt = $conn->prepare($countSQL);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalResult = $countStmt->fetch();
    $totalItems = $totalResult['total'];
    $totalPages = ceil($totalItems / $limit);
    
    // Get paginated data
    $sql = "SELECT * FROM products $whereSQL ORDER BY $sort_by $sort_order LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $products = $stmt->fetchAll();
    
    // Build pagination info
    $pagination = [
        'current_page' => $page,
        'per_page' => $limit,
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'has_next' => $page < $totalPages,
        'has_prev' => $page > 1
    ];
    
    // Get available categories for filter
    $categoryStmt = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
    
    $response = [
        'products' => $products,
        'pagination' => $pagination,
        'filters' => [
            'available_categories' => $categories
        ],
        'query_params' => [
            'page' => $page,
            'limit' => $limit,
            'category' => $category,
            'min_price' => $min_price,
            'max_price' => $max_price,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order
        ]
    ];
    
    Response::json($response);
    
} catch (Exception $e) {
    Response::error($e->getMessage());
}
?>