<?php
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/PostController.php';

class Router {
    private $routes = [];
    
    public function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/blog-backend/public', '', $path);
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path, $params)) {
                call_user_func($route['callback'], $params);
                return;
            }
        }
        
        // If no route matches
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
    }
    
    private function matchPath($routePath, $requestPath, &$params) {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));
        
        if (count($routeParts) !== count($requestParts)) {
            return false;
        }
        
        $params = [];
        
        for ($i = 0; $i < count($routeParts); $i++) {
            if (strpos($routeParts[$i], ':') === 0) {
                $paramName = substr($routeParts[$i], 1);
                $params[$paramName] = $requestParts[$i];
            } elseif ($routeParts[$i] !== $requestParts[$i]) {
                return false;
            }
        }
        
        return true;
    }
}

// Create router instance
$router = new Router();
$userController = new UserController();
$postController = new PostController();

// User routes
$router->addRoute('POST', '/api/register', function() use ($userController) {
    $data = json_decode(file_get_contents('php://input'), true);
    $userController->register($data);
});

$router->addRoute('POST', '/api/login', function() use ($userController) {
    $data = json_decode(file_get_contents('php://input'), true);
    $userController->login($data);
});

$router->addRoute('GET', '/api/profile', function() use ($userController) {
    $userController->getProfile();
});

// Post routes
$router->addRoute('POST', '/api/posts', function() use ($postController) {
    $data = json_decode(file_get_contents('php://input'), true);
    $postController->create($data);
});

$router->addRoute('GET', '/api/posts', function() use ($postController) {
    $postController->getAll($_GET);
});

$router->addRoute('GET', '/api/posts/:slug', function($params) use ($postController) {
    $postController->getBySlug($params['slug']);
});

$router->addRoute('PUT', '/api/posts/:id', function($params) use ($postController) {
    $data = json_decode(file_get_contents('php://input'), true);
    $postController->update($params['id'], $data);
});

$router->addRoute('DELETE', '/api/posts/:id', function($params) use ($postController) {
    $postController->delete($params['id']);
});

// Comment routes
$router->addRoute('POST', '/api/posts/:postId/comments', function($params) use ($postController) {
    $data = json_decode(file_get_contents('php://input'), true);
    $postController->addComment($params['postId'], $data);
});

$router->addRoute('DELETE', '/api/comments/:commentId', function($params) use ($postController) {
    $postController->deleteComment($params['commentId']);
});

// Handle the request
$router->handleRequest();

require_once __DIR__ . '/../controllers/CommentController.php';
$commentController = new CommentController();

// Comment routes
$router->addRoute('POST', '/api/comments', function() use ($commentController) {
    $data = json_decode(file_get_contents('php://input'), true);
    $commentController->create($data);
});

$router->addRoute('GET', '/api/posts/:postId/comments', function($params) use ($commentController) {
    $commentController->getByPost($params['postId']);
});

$router->addRoute('PUT', '/api/comments/:id', function($params) use ($commentController) {
    $data = json_decode(file_get_contents('php://input'), true);
    $commentController->update($params['id'], $data);
});

$router->addRoute('DELETE', '/api/comments/:id', function($params) use ($commentController) {
    $commentController->delete($params['id']);
});

$router->addRoute('GET', '/api/my-comments', function() use ($commentController) {
    $commentController->getCommentsByUser();
});

?>