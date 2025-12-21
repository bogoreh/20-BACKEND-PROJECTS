<?php
require_once 'models/User.php';
require_once 'includes/Response.php';

class UserController {
    private $user;
    
    public function __construct() {
        $this->user = new User();
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch($method) {
            case 'GET':
                $this->handleGet();
                break;
            case 'POST':
                $this->handlePost();
                break;
            case 'PUT':
                $this->handlePut();
                break;
            case 'DELETE':
                $this->handleDelete();
                break;
            default:
                Response::error('Method not allowed', 405);
        }
    }
    
    private function handleGet() {
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $user = $this->user->getById($id);
            
            if($user) {
                Response::send($user, 200, 'User retrieved successfully');
            } else {
                Response::error('User not found', 404);
            }
        } else {
            $users = $this->user->getAll();
            Response::send($users, 200, 'Users retrieved successfully');
        }
    }
    
    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['name']) || empty($data['email'])) {
            Response::error('Name and email are required', 400);
        }
        
        if($this->user->emailExists($data['email'])) {
            Response::error('Email already exists', 409);
        }
        
        $this->user->name = $data['name'];
        $this->user->email = $data['email'];
        $this->user->phone = $data['phone'] ?? '';
        
        $id = $this->user->create();
        
        if($id) {
            $newUser = $this->user->getById($id);
            Response::send($newUser, 201, 'User created successfully');
        } else {
            Response::error('Failed to create user', 500);
        }
    }
    
    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['id'])) {
            Response::error('User ID is required', 400);
        }
        
        if(!$this->user->getById($data['id'])) {
            Response::error('User not found', 404);
        }
        
        $this->user->id = $data['id'];
        $this->user->name = $data['name'] ?? '';
        $this->user->email = $data['email'] ?? '';
        $this->user->phone = $data['phone'] ?? '';
        
        if($this->user->update()) {
            $updatedUser = $this->user->getById($data['id']);
            Response::send($updatedUser, 200, 'User updated successfully');
        } else {
            Response::error('Failed to update user', 500);
        }
    }
    
    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['id'])) {
            Response::error('User ID is required', 400);
        }
        
        if(!$this->user->getById($data['id'])) {
            Response::error('User not found', 404);
        }
        
        if($this->user->delete($data['id'])) {
            Response::send([], 200, 'User deleted successfully');
        } else {
            Response::error('Failed to delete user', 500);
        }
    }
}
?>