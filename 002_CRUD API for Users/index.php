<?php
require_once 'config/constants.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-users"></i> User Management System</h1>
            <p>Simple CRUD application for managing users</p>
        </header>
        
        <div class="main-content">
            <div class="sidebar">
                <h2><i class="fas fa-tasks"></i> Actions</h2>
                <button id="showUsers" class="btn btn-primary">
                    <i class="fas fa-list"></i> View All Users
                </button>
                <button id="showAddForm" class="btn btn-secondary">
                    <i class="fas fa-user-plus"></i> Add New User
                </button>
                
                <div class="api-info">
                    <h3><i class="fas fa-code"></i> API Endpoints</h3>
                    <ul>
                        <li><strong>GET</strong> /api.php - Get all users</li>
                        <li><strong>GET</strong> /api.php?id=1 - Get user by ID</li>
                        <li><strong>POST</strong> /api.php - Create user</li>
                        <li><strong>PUT</strong> /api.php - Update user</li>
                        <li><strong>DELETE</strong> /api.php - Delete user</li>
                    </ul>
                </div>
            </div>
            
            <div class="content">
                <div id="userList" class="card">
                    <h2><i class="fas fa-user-friends"></i> Users List</h2>
                    <div id="usersTableContainer">
                        <table id="usersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <!-- Users will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="loading" id="loadingUsers">
                        <i class="fas fa-spinner fa-spin"></i> Loading users...
                    </div>
                </div>
                
                <div id="userForm" class="card" style="display: none;">
                    <h2 id="formTitle"><i class="fas fa-user-plus"></i> Add New User</h2>
                    <form id="addUserForm">
                        <input type="hidden" id="userId">
                        
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Full Name *</label>
                            <input type="text" id="name" required placeholder="Enter full name">
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email Address *</label>
                            <input type="email" id="email" required placeholder="Enter email address">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="tel" id="phone" placeholder="Enter phone number">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" id="submitBtn" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save User
                            </button>
                            <button type="button" id="cancelBtn" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="responseMessage" class="alert" style="display: none;"></div>
            </div>
        </div>
        
        <footer>
            <p>User Management System &copy; <?php echo date('Y'); ?> | Simple PHP CRUD API</p>
        </footer>
    </div>
    
    <script src="script.js"></script>
</body>
</html>