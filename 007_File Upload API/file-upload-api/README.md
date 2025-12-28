# File Upload API - PHP

A secure, modern file upload API built with PHP featuring drag-and-drop interface, file validation, and responsive design.

## Features

- **Secure File Uploads**: Multiple security layers including file validation, MIME type checking, and EXIF data stripping
- **Drag & Drop Interface**: Modern, user-friendly interface with drag-and-drop support
- **Multiple File Upload**: Upload up to 5 files simultaneously (10MB max each)
- **File Type Validation**: Supports images (JPG, PNG, GIF, WebP), PDF, DOC, DOCX, and TXT files
- **Progress Indicators**: Real-time upload progress with visual feedback
- **File Management**: View, download, and delete uploaded files
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **RESTful API**: Clean API endpoints for integration with other applications

## Installation

1. **Requirements**:
   - PHP 7.4 or higher
   - Fileinfo extension enabled
   - Write permissions on the `uploads/` directory

2. **Setup**:
   ```bash
   # Clone or extract the project to your web server
   cd /var/www/html
   
   # Set proper permissions
   chmod 755 uploads/
   chmod 644 includes/*.php
   
   # Ensure uploads directory is writable
   chown -R www-data:www-data uploads/