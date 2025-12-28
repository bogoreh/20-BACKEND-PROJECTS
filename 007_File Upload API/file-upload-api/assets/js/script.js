document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('uploadArea');
    const selectedFiles = document.getElementById('selectedFiles');
    const fileList = document.getElementById('fileList');
    const uploadBtn = document.getElementById('uploadBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const progressContainer = document.getElementById('progressContainer');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    const uploadResults = document.getElementById('uploadResults');
    const filesGrid = document.getElementById('filesGrid');
    const refreshBtn = document.getElementById('refreshBtn');
    const successModal = document.getElementById('successModal');
    const closeModal = document.querySelector('.close-modal');
    const okBtn = document.querySelector('.ok-btn');

    // State
    let selectedFilesData = [];
    const MAX_FILES = 5;

    // Initialize
    loadUploadedFiles();

    // Event Listeners
    fileInput.addEventListener('change', handleFileSelect);
    
    uploadArea.addEventListener('click', () => fileInput.click());
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        
        if (e.dataTransfer.files.length) {
            handleFiles(e.dataTransfer.files);
        }
    });
    
    uploadBtn.addEventListener('click', handleUpload);
    cancelBtn.addEventListener('click', clearAllFiles);
    refreshBtn.addEventListener('click', loadUploadedFiles);
    closeModal.addEventListener('click', () => successModal.style.display = 'none');
    okBtn.addEventListener('click', () => successModal.style.display = 'none');
    
    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === successModal) {
            successModal.style.display = 'none';
        }
    });

    // Functions
    function handleFileSelect(e) {
        handleFiles(e.target.files);
    }

    function handleFiles(files) {
        const newFiles = Array.from(files).slice(0, MAX_FILES - selectedFilesData.length);
        
        newFiles.forEach(file => {
            if (selectedFilesData.length >= MAX_FILES) {
                alert(`Maximum ${MAX_FILES} files allowed per upload`);
                return;
            }
            
            if (file.size > 10 * 1024 * 1024) {
                alert(`File "${file.name}" exceeds 10MB limit`);
                return;
            }
            
            const fileId = Date.now() + Math.random();
            selectedFilesData.push({
                id: fileId,
                file: file,
                name: file.name,
                size: formatFileSize(file.size),
                type: file.type
            });
        });
        
        updateFileList();
        fileInput.value = '';
    }

    function updateFileList() {
        if (selectedFilesData.length > 0) {
            selectedFiles.style.display = 'block';
            uploadBtn.disabled = false;
            
            fileList.innerHTML = selectedFilesData.map(file => `
                <div class="file-item" data-id="${file.id}">
                    <div class="file-info">
                        <i class="fas fa-file ${getFileIcon(file.type)} file-icon"></i>
                        <div>
                            <div class="file-name" title="${file.name}">${file.name}</div>
                            <div class="file-size">${file.size}</div>
                        </div>
                    </div>
                    <button class="remove-file" onclick="removeFile('${file.id}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
        } else {
            selectedFiles.style.display = 'none';
            uploadBtn.disabled = true;
        }
    }

    function removeFile(fileId) {
        selectedFilesData = selectedFilesData.filter(file => file.id !== fileId);
        updateFileList();
    }

    function clearAllFiles() {
        selectedFilesData = [];
        updateFileList();
        progressContainer.style.display = 'none';
        uploadResults.innerHTML = '';
    }

    function getFileIcon(fileType) {
        if (fileType.startsWith('image/')) return 'fa-image';
        if (fileType === 'application/pdf') return 'fa-file-pdf';
        if (fileType.includes('word')) return 'fa-file-word';
        if (fileType === 'text/plain') return 'fa-file-alt';
        return 'fa-file';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    async function handleUpload() {
        if (selectedFilesData.length === 0) return;
        
        const formData = new FormData();
        selectedFilesData.forEach(fileData => {
            formData.append('files[]', fileData.file);
        });
        
        // Show progress container
        progressContainer.style.display = 'block';
        progressFill.style.width = '0%';
        progressText.textContent = '0%';
        uploadResults.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Uploading files...</div>';
        
        try {
            const response = await fetch('api/upload.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Simulate progress for better UX
                let progress = 0;
                const interval = setInterval(() => {
                    progress += 20;
                    progressFill.style.width = progress + '%';
                    progressText.textContent = progress + '%';
                    
                    if (progress >= 100) {
                        clearInterval(interval);
                        showSuccess(result);
                        clearAllFiles();
                        loadUploadedFiles();
                    }
                }, 100);
            } else {
                showErrors(result.results || [{errors: [result.error || 'Upload failed']}]);
                progressFill.style.width = '100%';
                progressText.textContent = '100%';
            }
        } catch (error) {
            showErrors([{errors: ['Network error: ' + error.message]}]);
            progressFill.style.width = '100%';
            progressText.textContent = '100%';
        }
    }

    function showSuccess(result) {
        uploadResults.innerHTML = `
            <div class="upload-result success">
                <h4><i class="fas fa-check-circle"></i> ${result.message}</h4>
                <p>${result.results ? result.results.length + ' file(s) processed' : ''}</p>
            </div>
        `;
        
        // Show modal with uploaded files
        const successFiles = result.results?.filter(r => r.success) || [];
        if (successFiles.length > 0) {
            const successFilesHtml = successFiles.map(file => `
                <div class="file-item">
                    <div class="file-info">
                        <i class="fas fa-file ${getFileIcon(file.type)}"></i>
                        <div>
                            <div class="file-name">${file.filename}</div>
                            <div class="file-size">${formatFileSize(file.size)}</div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('successMessage').textContent = 
                `Successfully uploaded ${successFiles.length} file(s)`;
            document.getElementById('successFiles').innerHTML = successFilesHtml;
            successModal.style.display = 'flex';
        }
    }

    function showErrors(results) {
        const errorMessages = [];
        results.forEach(result => {
            if (result.errors) {
                errorMessages.push(...result.errors);
            }
        });
        
        uploadResults.innerHTML = `
            <div class="upload-result error">
                <h4><i class="fas fa-exclamation-circle"></i> Upload Failed</h4>
                <ul>
                    ${errorMessages.map(msg => `<li>${msg}</li>`).join('')}
                </ul>
            </div>
        `;
    }

    async function loadUploadedFiles() {
        filesGrid.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading files...</div>';
        
        try {
            const response = await fetch('api/get-files.php');
            const result = await response.json();
            
            if (result.success) {
                displayFiles(result.files);
            } else {
                filesGrid.innerHTML = '<div class="error">Failed to load files</div>';
            }
        } catch (error) {
            filesGrid.innerHTML = '<div class="error">Network error: ' + error.message + '</div>';
        }
    }

    function displayFiles(files) {
        if (files.length === 0) {
            filesGrid.innerHTML = '<div class="empty-state">No files uploaded yet. Upload your first file!</div>';
            return;
        }
        
        filesGrid.innerHTML = files.map(file => `
            <div class="file-card">
                <div class="file-preview">
                    ${file.type.startsWith('image/') 
                        ? `<img src="${file.url}" alt="${file.name}" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-file-image file-icon-large\'></i>'">`
                        : `<i class="fas ${getFileIcon(file.type)} file-icon-large"></i>`
                    }
                </div>
                <div class="file-details">
                    <div class="file-name-large" title="${file.name}">${file.name}</div>
                    <div class="file-meta">
                        <span>${formatFileSize(file.size)}</span>
                        <span>${new Date(file.modified * 1000).toLocaleDateString()}</span>
                    </div>
                    <div class="file-actions">
                        <button class="action-btn view-btn" onclick="window.open('${file.url}', '_blank')">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteFile('${file.name}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    async function deleteFile(filename) {
        if (!confirm('Are you sure you want to delete this file?')) return;
        
        try {
            const response = await fetch('api/delete-file.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ filename: filename })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('File deleted successfully');
                loadUploadedFiles();
            } else {
                alert('Error: ' + (result.error || 'Failed to delete file'));
            }
        } catch (error) {
            alert('Network error: ' + error.message);
        }
    }

    // Make functions available globally
    window.removeFile = removeFile;
    window.deleteFile = deleteFile;
});