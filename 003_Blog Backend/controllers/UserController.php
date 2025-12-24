<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';

class UserController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function register($data) {
        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
            Response::error('Username, email, and password are required');
        }
        
        $userId = $this->userModel->register(
            $data['username'],
            $data['email'],
            $data['password']
        );
        
        if ($userId) {
            Response::success(['user_id' => $userId], 'User registered successfully');
        } else {
            Response::error('Registration failed. User may already exist.');
        }
    }
    
    public function login($data) {
        if (!isset($data['email']) || !isset($data['password'])) {
            Response::error('Email and password are required');
        }
        
        $user = $this->userModel->login($data['email'], $data['password']);
        
        if ($user) {
            $token = Auth::generateToken($user['id'], $user['username']);
            Response::success([
                'token' => $token,
                'user' => $user
            ], 'Login successful');
        } else {
            Response::error('Invalid email or password');
        }
    }
    
    public function getProfile() {
        $userData = Auth::getCurrentUser();
        
        if (!$userData) {
            Response::error('Authentication required', 401);
        }
        
        $user = $this->userModel->getById($userData['user_id']);
        
        if ($user) {
            Response::success($user);
        } else {
            Response::error('User not found');
        }
    }
}
?>