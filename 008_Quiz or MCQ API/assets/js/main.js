// Enhanced main.js additions

// Load uploaded files
async function loadUploadedFiles() {
    try {
        const response = await fetch(`${API_BASE}/upload.php`);
        const data = await response.json();
        
        if (data.success) {
            const filesList = document.getElementById('uploadedFilesList');
            
            if (data.data.length === 0) {
                filesList.innerHTML = `
                    <div class="no-files" style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #7f8c8d;">
                        <i class="fas fa-image fa-3x" style="margin-bottom: 1rem;"></i>
                        <p>No images uploaded yet</p>
                    </div>
                `;
                return;
            }
            
            filesList.innerHTML = data.data.map(file => `
                <div class="file-item">
                    <div class="file-icon">
                        <i class="fas fa-file-image"></i>
                    </div>
                    <div class="file-info">
                        <div class="file-name">${file.original_name}</div>
                        <div class="file-size">${file.size_formatted}</div>
                        <div class="file-date">${new Date(file.uploaded_at).toLocaleDateString()}</div>
                    </div>
                    <button class="delete-btn" onclick="deleteFile(${file.id})" title="Delete file">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading uploaded files:', error);
        const filesList = document.getElementById('uploadedFilesList');
        filesList.innerHTML = `
            <div class="error" style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #ff6b6b;">
                <i class="fas fa-exclamation-triangle fa-2x" style="margin-bottom: 1rem;"></i>
                <p>Failed to load files</p>
            </div>
        `;
    }
}

// Delete file
async function deleteFile(fileId) {
    if (!confirm('Are you sure you want to delete this file?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/upload.php/${fileId}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('File deleted successfully');
            loadUploadedFiles();
            updateStats();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        console.error('Error deleting file:', error);
        alert('Failed to delete file');
    }
}

// Enhanced updateStats function
async function updateStats() {
    try {
        // Get quizzes
        const quizzesResponse = await fetch(`${API_BASE}/quizzes.php`);
        const quizzesData = await quizzesResponse.json();
        
        if (quizzesData.success) {
            document.getElementById('quiz-count').textContent = quizzesData.count || quizzesData.data.length;
            
            // Calculate total questions
            let totalQuestions = 0;
            for (const quiz of quizzesData.data) {
                const questionsResponse = await fetch(`${API_BASE}/questions.php?quiz_id=${quiz.id}`);
                const questionsData = await questionsResponse.json();
                if (questionsData.success) {
                    totalQuestions += questionsData.count || questionsData.data.length;
                }
            }
            document.getElementById('question-count').textContent = totalQuestions;
        }
        
        // Get uploaded files count
        const filesResponse = await fetch(`${API_BASE}/upload.php`);
        const filesData = await filesResponse.json();
        
        if (filesData.success) {
            document.getElementById('image-count').textContent = filesData.count || filesData.data.length;
        }
        
    } catch (error) {
        console.error('Error updating stats:', error);
        // Set placeholder values if API fails
        document.getElementById('quiz-count').textContent = '--';
        document.getElementById('question-count').textContent = '--';
        document.getElementById('image-count').textContent = '--';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Your existing initialization code...
    
    // Call updateStats every 30 seconds
    setInterval(updateStats, 30000);
});