<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <i class="fas fa-brain"></i>
                <h1>QuizMaster API</h1>
            </div>
            <nav class="nav">
                <a href="#dashboard" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="#quizzes" class="nav-link"><i class="fas fa-list-alt"></i> Quizzes</a>
                <a href="#upload" class="nav-link"><i class="fas fa-upload"></i> Upload</a>
                <a href="#api" class="nav-link"><i class="fas fa-code"></i> API Docs</a>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Section -->
            <section id="dashboard" class="section active">
                <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #4e73df;">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Quizzes</h3>
                            <p id="quiz-count">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #1cc88a;">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Questions</h3>
                            <p id="question-count">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #f6c23e;">
                            <i class="fas fa-image"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Uploaded Images</h3>
                            <p id="image-count">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #e74a3b;">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="stat-info">
                            <h3>API Endpoints</h3>
                            <p>12</p>
                        </div>
                    </div>
                </div>

                <div class="quick-actions">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="action-buttons">
                        <button class="btn btn-primary" onclick="showSection('quizzes')">
                            <i class="fas fa-plus"></i> Create New Quiz
                        </button>
                        <button class="btn btn-success" onclick="showSection('upload')">
                            <i class="fas fa-upload"></i> Upload Image
                        </button>
                        <button class="btn btn-info" onclick="testAPI()">
                            <i class="fas fa-vial"></i> Test API
                        </button>
                    </div>
                </div>
            </section>

            <!-- Quizzes Section -->
            <section id="quizzes" class="section">
                <h2><i class="fas fa-list-alt"></i> Manage Quizzes</h2>
                
                <!-- Create Quiz Form -->
                <div class="form-card">
                    <h3><i class="fas fa-plus-circle"></i> Create New Quiz</h3>
                    <form id="createQuizForm">
                        <div class="form-group">
                            <label for="quizTitle"><i class="fas fa-heading"></i> Quiz Title</label>
                            <input type="text" id="quizTitle" placeholder="Enter quiz title" required>
                        </div>
                        <div class="form-group">
                            <label for="quizDescription"><i class="fas fa-align-left"></i> Description</label>
                            <textarea id="quizDescription" placeholder="Enter quiz description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Quiz
                        </button>
                    </form>
                </div>

                <!-- Add Question Form -->
                <div class="form-card">
                    <h3><i class="fas fa-question-circle"></i> Add Question</h3>
                    <form id="addQuestionForm">
                        <div class="form-group">
                            <label for="quizSelect"><i class="fas fa-list"></i> Select Quiz</label>
                            <select id="quizSelect" required>
                                <option value="">Select a quiz</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="questionText"><i class="fas fa-question"></i> Question</label>
                            <textarea id="questionText" placeholder="Enter your question" rows="3" required></textarea>
                        </div>
                        <div class="options-grid">
                            <div class="form-group">
                                <label for="optionA" class="option-label option-a">Option A</label>
                                <input type="text" id="optionA" placeholder="Enter option A" required>
                            </div>
                            <div class="form-group">
                                <label for="optionB" class="option-label option-b">Option B</label>
                                <input type="text" id="optionB" placeholder="Enter option B" required>
                            </div>
                            <div class="form-group">
                                <label for="optionC" class="option-label option-c">Option C</label>
                                <input type="text" id="optionC" placeholder="Enter option C">
                            </div>
                            <div class="form-group">
                                <label for="optionD" class="option-label option-d">Option D</label>
                                <input type="text" id="optionD" placeholder="Enter option D">
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-check-circle"></i> Correct Answer</label>
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="correctAnswer" value="a" required>
                                    <span class="radio-custom">A</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="correctAnswer" value="b">
                                    <span class="radio-custom">B</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="correctAnswer" value="c">
                                    <span class="radio-custom">C</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="correctAnswer" value="d">
                                    <span class="radio-custom">D</span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                    </form>
                </div>

                <!-- Quizzes List -->
                <div class="form-card">
                    <h3><i class="fas fa-list"></i> All Quizzes</h3>
                    <div id="quizzesList" class="list-container">
                        <!-- Quizzes will be loaded here -->
                    </div>
                </div>
            </section>

            <!-- Upload Section -->
            <section id="upload" class="section">
                <h2><i class="fas fa-upload"></i> File Upload</h2>
                
                <div class="upload-container">
                    <!-- Upload Form -->
                    <div class="upload-card">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h3>Upload Image</h3>
                        <p class="upload-info">
                            Supported formats: JPG, PNG, GIF, WebP<br>
                            Maximum size: 5MB
                        </p>
                        
                        <form id="uploadForm" enctype="multipart/form-data">
                            <div class="file-input-wrapper">
                                <input type="file" id="imageFile" name="image" accept="image/*" hidden>
                                <label for="imageFile" class="file-input-label">
                                    <i class="fas fa-folder-open"></i> Choose File
                                </label>
                                <span id="fileName">No file chosen</span>
                            </div>
                            
                            <div id="previewContainer" class="preview-container">
                                <!-- Image preview will appear here -->
                            </div>
                            
                            <button type="submit" class="btn btn-primary upload-btn">
                                <i class="fas fa-upload"></i> Upload Image
                            </button>
                        </form>
                        
                        <div id="uploadProgress" class="progress-bar" style="display: none;">
                            <div class="progress-fill"></div>
                        </div>
                        
                        <div id="uploadResult" class="result-message"></div>
                    </div>

                    <!-- Uploaded Files -->
                    <div class="uploaded-files">
                        <h3><i class="fas fa-images"></i> Uploaded Images</h3>
                        <div id="uploadedFilesList" class="files-grid">
                            <!-- Files will be loaded here -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- API Documentation Section -->
            <section id="api" class="section">
                <h2><i class="fas fa-code"></i> API Documentation</h2>
                
                <div class="api-docs">
                    <!-- Quizzes API -->
                    <div class="api-endpoint">
                        <div class="endpoint-header">
                            <span class="method get">GET</span>
                            <code>/api/quizzes.php</code>
                            <span class="endpoint-title">Get all quizzes</span>
                        </div>
                        <div class="endpoint-body">
                            <p>Returns a list of all quizzes</p>
                            <pre><code>{
    "success": true,
    "data": [...]
}</code></pre>
                        </div>
                    </div>

                    <div class="api-endpoint">
                        <div class="endpoint-header">
                            <span class="method post">POST</span>
                            <code>/api/quizzes.php</code>
                            <span class="endpoint-title">Create a quiz</span>
                        </div>
                        <div class="endpoint-body">
                            <p>Request body:</p>
                            <pre><code>{
    "title": "Quiz Title",
    "description": "Quiz Description"
}</code></pre>
                        </div>
                    </div>

                    <!-- Upload API -->
                    <div class="api-endpoint">
                        <div class="endpoint-header">
                            <span class="method post">POST</span>
                            <code>/api/upload.php</code>
                            <span class="endpoint-title">Upload image</span>
                        </div>
                        <div class="endpoint-body">
                            <p>Form data with file field named 'image'</p>
                            <pre><code>Content-Type: multipart/form-data
Field: image (file)</code></pre>
                        </div>
                    </div>

                    <!-- Questions API -->
                    <div class="api-endpoint">
                        <div class="endpoint-header">
                            <span class="method get">GET</span>
                            <code>/api/questions.php?quiz_id=1</code>
                            <span class="endpoint-title">Get quiz questions</span>
                        </div>
                        <div class="endpoint-body">
                            <p>Returns all questions for a specific quiz</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2024 QuizMaster API. All rights reserved.</p>
            <p class="api-status">API Status: <span class="status-indicator" id="apiStatus">Checking...</span></p>
        </footer>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>