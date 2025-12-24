<?php
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';

class CommentController {
    private $commentModel;
    private $postModel;
    
    public function __construct() {
        $this->commentModel = new Comment();
        $this->postModel = new Post();
    }
    
    public function create($data) {
        $userData = Auth::getCurrentUser();
        
        if (!$userData) {
            Response::error('Authentication required', 401);
        }
        
        if (!isset($data['post_id']) || !isset($data['content'])) {
            Response::error('Post ID and content are required');
        }
        
        // Verify post exists
        $post = $this->postModel->getById($data['post_id']);
        if (!$post) {
            Response::error('Post not found', 404);
        }
        
        $comment = $this->commentModel->create(
            $data['post_id'],
            $userData['user_id'],
            $data['content']
        );
        
        if ($comment) {
            Response::success($comment, 'Comment added successfully');
        } else {
            Response::error('Failed to add comment');
        }
    }
    
    public function getByPost($postId) {
        $comments = $this->commentModel->getByPostId($postId);
        Response::success($comments);
    }
    
    public function update($id, $data) {
        $userData = Auth::getCurrentUser();
        
        if (!$userData) {
            Response::error('Authentication required', 401);
        }
        
        if (!isset($data['content'])) {
            Response::error('Content is required');
        }
        
        // First, check if comment exists and belongs to user
        $comment = $this->commentModel->getById($id);
        
        if (!$comment) {
            Response::error('Comment not found', 404);
        }
        
        if ($comment['user_id'] != $userData['user_id']) {
            Response::error('You can only edit your own comments', 403);
        }
        
        $success = $this->commentModel->update($id, $data['content']);
        
        if ($success) {
            Response::success(null, 'Comment updated successfully');
        } else {
            Response::error('Failed to update comment');
        }
    }
    
    public function delete($id) {
        $userData = Auth::getCurrentUser();
        
        if (!$userData) {
            Response::error('Authentication required', 401);
        }
        
        $success = $this->commentModel->delete($id, $userData['user_id']);
        
        if ($success) {
            Response::success(null, 'Comment deleted successfully');
        } else {
            Response::error('Failed to delete comment');
        }
    }
    
    public function getCommentsByUser() {
        $userData = Auth::getCurrentUser();
        
        if (!$userData) {
            Response::error('Authentication required', 401);
        }
        
        $comments = $this->commentModel->getByUserId($userData['user_id']);
        Response::success($comments);
    }
}
?>