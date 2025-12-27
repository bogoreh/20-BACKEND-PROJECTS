<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 90vh;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .auth-section {
            display: flex;
            gap: 10px;
        }

        .main-content {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .sidebar {
            width: 300px;
            background: #f8f9fa;
            padding: 20px;
            border-right: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: auto;
        }

        .notes-list {
            flex: 1;
            overflow-y: auto;
        }

        .note-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .note-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .note-item.active {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .note-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        .note-preview {
            font-size: 14px;
            color: #666;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .note-date {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        .editor-container {
            flex: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .note-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex: 1;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-weight: 600;
            color: #333;
        }

        input, textarea {
            padding: 12px 15px;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        #noteContent {
            flex: 1;
            min-height: 300px;
            resize: none;
            font-family: inherit;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 20px;
            max-width: 400px;
            width: 90%;
        }

        .modal h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .form-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading i {
            font-size: 24px;
            color: #667eea;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                max-height: 200px;
            }
            
            .container {
                height: 95vh;
            }
        }
    </style>
</head>
<body>
    <!-- Login/Register Modal -->
    <div id="authModal" class="modal">
        <div class="login-container">
            <h2 id="authTitle">Login</h2>
            <form id="authForm">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="authEmail" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="authPassword" required>
                </div>
                <div class="form-group" id="usernameGroup" style="display: none;">
                    <label>Username</label>
                    <input type="text" id="authUsername">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <span id="authButtonText">Login</span>
                </button>
            </form>
            <div class="form-footer">
                <span id="authToggleText">Don't have an account? </span>
                <a href="#" id="authToggleLink">Sign up</a>
            </div>
        </div>
    </div>

    <!-- Main App Container -->
    <div class="container" id="appContainer" style="display: none;">
        <div class="header">
            <div class="logo">
                <i class="fas fa-sticky-note"></i>
                Notes App
            </div>
            <div class="user-info">
                <span>Welcome, <span id="username">User</span></span>
                <button class="btn btn-secondary" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>

        <div class="main-content">
            <div class="sidebar">
                <button class="btn btn-primary" onclick="createNewNote()">
                    <i class="fas fa-plus"></i> New Note
                </button>
                
                <div class="alert" id="alertMessage"></div>
                
                <div class="notes-list" id="notesList">
                    <div class="loading" id="notesLoading">
                        <i class="fas fa-spinner"></i>
                    </div>
                </div>
            </div>

            <div class="editor-container">
                <form class="note-form" id="noteForm">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" id="noteTitle" placeholder="Enter note title..." required>
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea id="noteContent" placeholder="Start typing your note here..."></textarea>
                    </div>
                    <div class="button-group">
                        <button type="button" class="btn btn-secondary" onclick="cancelEdit()" id="cancelBtn" style="display: none;">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="saveBtn">
                            <i class="fas fa-save"></i> Save Note
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteNote()" id="deleteBtn" style="display: none;">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentUser = null;
        let currentNoteId = null;
        let notes = [];
        let isRegistering = false;

        // DOM Elements
        const authModal = document.getElementById('authModal');
        const appContainer = document.getElementById('appContainer');
        const authForm = document.getElementById('authForm');
        const authTitle = document.getElementById('authTitle');
        const authButtonText = document.getElementById('authButtonText');
        const authToggleText = document.getElementById('authToggleText');
        const authToggleLink = document.getElementById('authToggleLink');
        const usernameGroup = document.getElementById('usernameGroup');
        const notesList = document.getElementById('notesList');
        const noteForm = document.getElementById('noteForm');
        const noteTitle = document.getElementById('noteTitle');
        const noteContent = document.getElementById('noteContent');
        const usernameSpan = document.getElementById('username');
        const alertMessage = document.getElementById('alertMessage');
        const notesLoading = document.getElementById('notesLoading');
        const cancelBtn = document.getElementById('cancelBtn');
        const deleteBtn = document.getElementById('deleteBtn');
        const saveBtn = document.getElementById('saveBtn');

        // Toggle between Login and Register
        authToggleLink.addEventListener('click', function(e) {
            e.preventDefault();
            isRegistering = !isRegistering;
            
            if (isRegistering) {
                authTitle.textContent = 'Register';
                authButtonText.textContent = 'Register';
                authToggleText.textContent = 'Already have an account? ';
                authToggleLink.textContent = 'Login';
                usernameGroup.style.display = 'flex';
            } else {
                authTitle.textContent = 'Login';
                authButtonText.textContent = 'Login';
                authToggleText.textContent = 'Don\'t have an account? ';
                authToggleLink.textContent = 'Sign up';
                usernameGroup.style.display = 'none';
            }
        });

        // Handle Authentication
        authForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('authEmail').value;
            const password = document.getElementById('authPassword').value;
            const username = document.getElementById('authUsername').value;
            
            if (isRegistering) {
                await register(username, email, password);
            } else {
                await login(email, password);
            }
        });

        // Show/Hide alert
        function showAlert(message, type = 'success') {
            alertMessage.textContent = message;
            alertMessage.className = `alert alert-${type}`;
            alertMessage.style.display = 'block';
            
            setTimeout(() => {
                alertMessage.style.display = 'none';
            }, 3000);
        }

        // API Request Helper
        async function apiRequest(endpoint, method = 'GET', data = null) {
            const url = `api/${endpoint}`;
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                }
            };
            
            if (data) {
                options.body = JSON.stringify(data);
            }
            
            try {
                const response = await fetch(url, options);
                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.message || 'Request failed');
                }
                
                return result;
            } catch (error) {
                showAlert(error.message, 'error');
                throw error;
            }
        }

        // Login function
        async function login(email, password) {
            try {
                const result = await apiRequest('auth/login.php', 'POST', { email, password });
                currentUser = result.data;
                localStorage.setItem('userToken', result.data.token);
                authModal.style.display = 'none';
                appContainer.style.display = 'flex';
                usernameSpan.textContent = currentUser.username;
                loadNotes();
                showAlert('Login successful!');
            } catch (error) {
                showAlert('Login failed: ' + error.message, 'error');
            }
        }

        // Register function
        async function register(username, email, password) {
            try {
                const result = await apiRequest('auth/register.php', 'POST', { username, email, password });
                currentUser = result.data;
                authModal.style.display = 'none';
                appContainer.style.display = 'flex';
                usernameSpan.textContent = currentUser.username;
                loadNotes();
                showAlert('Registration successful!');
            } catch (error) {
                showAlert('Registration failed: ' + error.message, 'error');
            }
        }

        // Logout function
        async function logout() {
            try {
                await apiRequest('auth/logout.php', 'POST');
                localStorage.removeItem('userToken');
                currentUser = null;
                currentNoteId = null;
                notes = [];
                appContainer.style.display = 'none';
                authModal.style.display = 'flex';
                showAlert('Logged out successfully');
            } catch (error) {
                showAlert('Logout failed: ' + error.message, 'error');
            }
        }

        // Load notes
        async function loadNotes() {
            notesLoading.style.display = 'block';
            try {
                const result = await apiRequest('notes/read.php');
                notes = result.data || [];
                renderNotesList();
            } catch (error) {
                console.error('Failed to load notes:', error);
            } finally {
                notesLoading.style.display = 'none';
            }
        }

        // Render notes list
        function renderNotesList() {
            notesList.innerHTML = '';
            
            notes.forEach(note => {
                const noteElement = document.createElement('div');
                noteElement.className = `note-item ${note.id === currentNoteId ? 'active' : ''}`;
                noteElement.innerHTML = `
                    <div class="note-title">${note.title || 'Untitled'}</div>
                    <div class="note-preview">${note.content ? note.content.substring(0, 50) + '...' : 'No content'}</div>
                    <div class="note-date">${new Date(note.updated_at).toLocaleDateString()}</div>
                `;
                
                noteElement.addEventListener('click', () => selectNote(note));
                notesList.appendChild(noteElement);
            });
        }

        // Select note
        function selectNote(note) {
            currentNoteId = note.id;
            noteTitle.value = note.title;
            noteContent.value = note.content;
            cancelBtn.style.display = 'inline-block';
            deleteBtn.style.display = 'inline-block';
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Update Note';
            renderNotesList();
        }

        // Create new note
        function createNewNote() {
            currentNoteId = null;
            noteTitle.value = '';
            noteContent.value = '';
            cancelBtn.style.display = 'none';
            deleteBtn.style.display = 'none';
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Note';
            noteTitle.focus();
            renderNotesList();
        }

        // Cancel edit
        function cancelEdit() {
            createNewNote();
        }

        // Handle note form submission
        noteForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const title = noteTitle.value.trim();
            const content = noteContent.value.trim();
            
            if (!title) {
                showAlert('Please enter a title', 'error');
                return;
            }
            
            try {
                if (currentNoteId) {
                    // Update existing note
                    const result = await apiRequest('notes/update.php', 'POST', {
                        id: currentNoteId,
                        title,
                        content
                    });
                    showAlert('Note updated successfully!');
                } else {
                    // Create new note
                    const result = await apiRequest('notes/create.php', 'POST', {
                        title,
                        content
                    });
                    currentNoteId = result.data.id;
                    showAlert('Note created successfully!');
                }
                
                await loadNotes();
            } catch (error) {
                showAlert('Failed to save note: ' + error.message, 'error');
            }
        });

        // Delete note
        async function deleteNote() {
            if (!currentNoteId || !confirm('Are you sure you want to delete this note?')) {
                return;
            }
            
            try {
                await apiRequest('notes/delete.php', 'POST', { id: currentNoteId });
                showAlert('Note deleted successfully!');
                createNewNote();
                await loadNotes();
            } catch (error) {
                showAlert('Failed to delete note: ' + error.message, 'error');
            }
        }

        // Check if user is logged in on page load
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('userToken');
            if (token) {
                // Try to load user data from token
                authModal.style.display = 'none';
                appContainer.style.display = 'flex';
                // Note: In a real app, you'd validate the token here
            } else {
                authModal.style.display = 'flex';
            }
        });
    </script>
</body>
</html>