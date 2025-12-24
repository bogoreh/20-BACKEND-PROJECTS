<?php
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';

class PostController {
    private $postModel;
    private $commentModel;
    
    public function __construct() {
        $this->postModel = new Post();
        $this->commentModel = new Comment();
    }
    
    public function create($data) {
        $userData = Auth::getCurrentUser();
        
        if (!$userData) {
            Response::error('Authentication required', 401);
        }
        
        if (!isset($data['title']) || !isset($data['content'])) {
            Response::error('Title and content are required');
        }
        
        $post = $this->postModel->create(
            $data['title'],
            $data['content'],
            $userData['user_id']
        );
        
        if ($post) {
            Response::success($post, 'Post created successfully');
        } else {
            Response::error('Failed to create post');
        }
    }
    
    public function getAll($queryParams) {
        $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : 10;
        
        $result = $this->postModel->getAll($page, $limit);
        Response::success($result);
    }
    
    public function getBySlug($slug) {
        $post = $this->postModel->getBySlug($slug);
        
        if ($post) {
            // Get comments for this post
            $comments = $this->commentModel->getByPostId($post['id']);
            $post['comments'] = $comments;
            
            Response::success($post);
        } else {
            Response::error('Post not found', 404);
        }
    }
    
    public function update($id, $data) {
        $userData = Auth::getCurrentUser();
        
        if (!$userData) {
            Response::error('Authentication required', 401);
        }
        
        if (!isset($data['title']) || !isset($data['content'])) {
            Response::error('Title and content are required');
        }
        
        $success = $this->postModel->update(
            $id,
            $data['title'],
            $data['content'],
            $userData['user_id']
        );
        
        if ($success) {
            Response::success(null, 'Post updated successfully');
        } else {
            Response::error('Failed to update post');
        }
    }
    
    public function delete($id) {
        $userData = Auth::getCurrentUser();
        
        if (!$userData) {
            Response::error('Authentication required', 401);
        }
        
        $success = $this->postModel->delete($id, $userData['user_id']);
        
        if ($success) {
            Response::success(null, 'Post deleted successfully');
        } else {
            Response::error('Failed to delete post');
        }
    }
    
    public function addComment($postId, $data) {
        $userData = Auth::getCurrentUser();
        
        if (!$userData) {
            Response::error('Authentication required', 401);
        }
        
        if (!isset($data['content'])) {
            Response::error('Comment content is required');
        }
        
        $comment = $this->commentModel->create(
            $postId,
            $userData['user_id'],
            $data['content']
        );
        
        if ($comment) {
            Response::success($comment, 'Comment added successfully');
        } else {
            Response::error('Failed to add comment');
        }
    }
    
    public function deleteComment($commentId) {
        $userData = Auth::getCurrentUser();
        
        if (!$userData) {
            Response::error('Authentication required', 401);
        }
        
        $success = $this->commentModel->delete($commentId, $userData['user_id']);
        
        if ($success) {
            Response::success(null, 'Comment deleted successfully');
        } else {
            Response::error('Failed to delete comment');
        }
    }
}
?>