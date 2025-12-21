document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const showUsersBtn = document.getElementById('showUsers');
    const showAddFormBtn = document.getElementById('showAddForm');
    const userListSection = document.getElementById('userList');
    const userFormSection = document.getElementById('userForm');
    const usersTableBody = document.getElementById('usersTableBody');
    const addUserForm = document.getElementById('addUserForm');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const responseMessage = document.getElementById('responseMessage');
    const loadingUsers = document.getElementById('loadingUsers');
    
    // Base API URL
    const apiUrl = 'api.php';
    
    // Show all users
    showUsersBtn.addEventListener('click', function() {
        showUserList();
        loadUsers();
    });
    
    // Show add user form
    showAddFormBtn.addEventListener('click', function() {
        showAddForm();
    });
    
    // Cancel form
    cancelBtn.addEventListener('click', function() {
        showUserList();
        resetForm();
    });
    
    // Form submission
    addUserForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveUser();
    });
    
    // Initial load
    loadUsers();
    
    // Function to show user list section
    function showUserList() {
        userListSection.style.display = 'block';
        userFormSection.style.display = 'none';
        responseMessage.style.display = 'none';
    }
    
    // Function to show add form
    function showAddForm() {
        userListSection.style.display = 'none';
        userFormSection.style.display = 'block';
        formTitle.innerHTML = '<i class="fas fa-user-plus"></i> Add New User';
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Save User';
        responseMessage.style.display = 'none';
    }
    
    // Function to show edit form
    function showEditForm(user) {
        showAddForm();
        formTitle.innerHTML = '<i class="fas fa-user-edit"></i> Edit User';
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Update User';
        
        document.getElementById('userId').value = user.id;
        document.getElementById('name').value = user.name;
        document.getElementById('email').value = user.email;
        document.getElementById('phone').value = user.phone;
    }
    
    // Function to reset form
    function resetForm() {
        addUserForm.reset();
        document.getElementById('userId').value = '';
    }
    
    // Function to show message
    function showMessage(message, type = 'success') {
        responseMessage.textContent = message;
        responseMessage.className = `alert alert-${type}`;
        responseMessage.style.display = 'block';
        
        setTimeout(() => {
            responseMessage.style.display = 'none';
        }, 5000);
    }
    
    // Function to load all users
    async function loadUsers() {
        loadingUsers.style.display = 'block';
        usersTableBody.innerHTML = '';
        
        try {
            const response = await fetch(apiUrl);
            const result = await response.json();
            
            if (result.success) {
                displayUsers(result.data);
            } else {
                showMessage(result.message || 'Failed to load users', 'error');
            }
        } catch (error) {
            showMessage('Error loading users: ' + error.message, 'error');
        } finally {
            loadingUsers.style.display = 'none';
        }
    }
    
    // Function to display users in table
    function displayUsers(users) {
        usersTableBody.innerHTML = '';
        
        if (users.length === 0) {
            usersTableBody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px;">
                        <i class="fas fa-users-slash" style="font-size: 2rem; color: #bdc3c7; margin-bottom: 10px; display: block;"></i>
                        No users found. Click "Add New User" to create one.
                    </td>
                </tr>
            `;
            return;
        }
        
        users.forEach(user => {
            const row = document.createElement('tr');
            const createdDate = new Date(user.created_at).toLocaleDateString();
            
            row.innerHTML = `
                <td>${user.id}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.phone || '-'}</td>
                <td>${createdDate}</td>
                <td class="actions">
                    <button class="btn btn-success btn-sm edit-btn" data-id="${user.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="${user.id}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            `;
            
            usersTableBody.appendChild(row);
        });
        
        // Add event listeners to edit buttons
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                getUserById(userId);
            });
        });
        
        // Add event listeners to delete buttons
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                deleteUser(userId);
            });
        });
    }
    
    // Function to get user by ID
    async function getUserById(id) {
        try {
            const response = await fetch(`${apiUrl}?id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                showEditForm(result.data);
            } else {
                showMessage(result.message || 'User not found', 'error');
            }
        } catch (error) {
            showMessage('Error loading user: ' + error.message, 'error');
        }
    }
    
    // Function to save/update user
    async function saveUser() {
        const userId = document.getElementById('userId').value;
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        
        if (!name || !email) {
            showMessage('Name and email are required', 'error');
            return;
        }
        
        const userData = {
            name,
            email,
            phone
        };
        
        let method = 'POST';
        let url = apiUrl;
        
        if (userId) {
            method = 'PUT';
            userData.id = userId;
        }
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage(result.message, 'success');
                resetForm();
                showUserList();
                loadUsers();
            } else {
                showMessage(result.message, 'error');
            }
        } catch (error) {
            showMessage('Error saving user: ' + error.message, 'error');
        }
    }
    
    // Function to delete user
    async function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) {
            return;
        }
        
        const userData = {
            id: id
        };
        
        try {
            const response = await fetch(apiUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage(result.message, 'success');
                loadUsers();
            } else {
                showMessage(result.message, 'error');
            }
        } catch (error) {
            showMessage('Error deleting user: ' + error.message, 'error');
        }
    }
});