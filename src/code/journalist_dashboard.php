<?php
include 'db_connect.php';

// Check if journalist is logged in
if (!isset($_SESSION['journalist_id'])) {
    header("Location: journalist_login.php");
    exit();
}

// Fetch categories for dropdown
$categories = [];
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $full_content = $conn->real_escape_string($_POST['full_content']);
    $category_id = intval($_POST['category_id']);

    // Initialize media arrays
    $cover_image = "";
    $additional_images = [];
    $videos = [];

    // Cover image upload
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $file_extension = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        $cover_image = time() . "_cover_" . basename($_FILES['cover_image']['name']);
        $target_path = "uploads/" . $cover_image;
        
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($file_extension, $image_extensions)) {
            move_uploaded_file($_FILES['cover_image']['tmp_name'], $target_path);
        } else {
            $error = "Cover image must be an image file (JPG, PNG, GIF, WEBP).";
        }
    }

    // Additional images upload
    if (empty($error) && !empty($_FILES['additional_images']['name'][0])) {
        foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['additional_images']['error'][$key] == 0) {
                $file_extension = strtolower(pathinfo($_FILES['additional_images']['name'][$key], PATHINFO_EXTENSION));
                $image_name = time() . "_additional_" . $key . "_" . basename($_FILES['additional_images']['name'][$key]);
                $target_path = "uploads/" . $image_name;
                
                if (in_array($file_extension, $image_extensions)) {
                    move_uploaded_file($tmp_name, $target_path);
                    $additional_images[] = $image_name;
                }
            }
        }
    }

    // Videos upload
    if (empty($error) && !empty($_FILES['videos']['name'][0])) {
        $video_extensions = ['mp4', 'mov', 'avi', 'wmv', 'webm'];
        foreach ($_FILES['videos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['videos']['error'][$key] == 0) {
                $file_extension = strtolower(pathinfo($_FILES['videos']['name'][$key], PATHINFO_EXTENSION));
                $video_name = time() . "_video_" . $key . "_" . basename($_FILES['videos']['name'][$key]);
                $target_path = "uploads/" . $video_name;
                
                if (in_array($file_extension, $video_extensions)) {
                    move_uploaded_file($tmp_name, $target_path);
                    $videos[] = $video_name;
                }
            }
        }
    }

    // Insert into news_requests if no error
if (empty($error)) {
    $journalist_id = $_SESSION['journalist_id'];
    
    // Convert arrays to JSON strings for database storage
    $additional_images_json = json_encode($additional_images);
    $videos_json = json_encode($videos);
    
    $stmt = $conn->prepare("INSERT INTO news_requests (journalist_id, title, description, full_content, image, additional_images, videos, category_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("issssssi", $journalist_id, $title, $description, $full_content, $cover_image, $additional_images_json, $videos_json, $category_id);

    if ($stmt->execute()) {
        $success = "News submitted successfully to Admin for review.";
    } else {
        $error = "Error submitting news: " . $stmt->error;
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Journalist Dashboard - Prime Report</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            padding: 40px 20px;
            color: #2d3748;
        }
        
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .dashboard-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }
        
        .dashboard-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, #1a365d, #2a4d8e);
            border-radius: 2px;
        }
        
        .dashboard-header h3 {
            color: #2a4d8e;
            font-size: 20px;
            margin-bottom: 15px;
        }
        
        .welcome-text {
            font-weight: 600;
            color: #2a4d8e;
            background: rgba(42, 77, 142, 0.1);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 15px;
        }
        
        /* Form Styling */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 16px;
            font-size: 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: #f7fafc;
            color: #2d3748;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2a4d8e;
            box-shadow: 0 0 0 3px rgba(42, 77, 142, 0.15);
            background: #fff;
        }
        
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        
        .form-group select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%234a5568' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
        }
        
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .file-input-container input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-input-label {
            display: block;
            padding: 14px 16px;
            background: #f7fafc;
            border: 1.5px dashed #cbd5e0;
            border-radius: 8px;
            text-align: center;
            color: #718096;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-input-label:hover {
            border-color: #2a4d8e;
            background: #edf2f7;
        }
        
        .file-types-info {
            font-size: 12px;
            color: #718096;
            margin-top: 5px;
        }
        
        .multi-file-container {
            background: #f7fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .add-more-btn {
            background: #e2e8f0;
            color: #2d3748;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        
        .add-more-btn:hover {
            background: #cbd5e0;
        }
        
        .submit-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #1a365d 0%, #2a4d8e 100%);
            color: white;
            border: none;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(26, 54, 93, 0.2);
        }
        
        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(26, 54, 93, 0.3);
        }
        
        .submit-button:active {
            transform: translateY(0);
        }
        
        /* Messages */
        .success-message {
            color: #38a169;
            text-align: center;
            font-weight: 500;
            padding: 12px 20px;
            background: rgba(56, 161, 105, 0.1);
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(56, 161, 105, 0.2);
        }
        
        .error-message {
            color: #e53e3c;
            text-align: center;
            font-weight: 500;
            padding: 12px 20px;
            background: rgba(229, 62, 62, 0.1);
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(229, 62, 62, 0.2);
        }
        
        /* Logout Button */
        .logout-container {
            text-align: center;
            margin-top: 30px;
        }
        
        .logout-button {
            color: #e53e3e;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border: 1px solid #e53e3e;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .logout-button:hover {
            background: #e53e3e;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(229, 62, 62, 0.2);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            .dashboard-header h2 {
                font-size: 24px;
            }
            
            .dashboard-header h3 {
                font-size: 18px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 20px 15px;
            }
            
            .dashboard-header h2 {
                font-size: 22px;
            }
            
            .form-group input,
            .form-group textarea,
            .form-group select {
                padding: 12px 14px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h2>Journalist Dashboard</h2>
            <div class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['journalist_username']) ?></div>
            <h3>Submit News Article</h3>
        </div>
    <div style="text-align: center; margin-bottom: 20px; padding: 15px; background: rgba(26, 54, 93, 0.05); border-radius: 8px;">
        <p style="margin-bottom: 10px; color: #2d3748; font-weight: 500;">Want to change other news?</p>
        <a href="change_other_news.php" style="display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #1a365d 0%, #2a4d8e 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;">
            Change Other News
    </a>
        </div>
        
        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">News Title *</label>
                <input type="text" name="title" placeholder="Enter news title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Short Description *</label>
                <textarea name="description" placeholder="Brief description of the news" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="full_content">Full Content *</label>
                <textarea name="full_content" placeholder="Complete news content" rows="6" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Cover Image -->
            <div class="form-group">
                <label>Cover Image *</label>
                <div class="file-input-container">
                    <div class="file-input-label" id="coverImageLabel">
                        🖼️ Choose Cover Image
                    </div>
                    <input type="file" name="cover_image" accept="image/*" required>
                </div>
                <div class="file-types-info">
                    This image will be displayed as the main cover on the homepage
                </div>
            </div>
            
            <!-- Additional Images -->
            <div class="form-group">
                <label>Additional Images</label>
                <div class="multi-file-container">
                    <div class="file-input-container">
                        <div class="file-input-label" id="additionalImagesLabel">
                            🖼️ Choose Additional Images
                        </div>
                        <input type="file" name="additional_images[]" accept="image/*" multiple>
                    </div>
                    <div class="file-types-info">
                        You can select multiple images (JPG, PNG, GIF, WEBP)
                    </div>
                </div>
            </div>
            
            <!-- Videos -->
            <div class="form-group">
                <label>Videos</label>
                <div class="multi-file-container">
                    <div class="file-input-container">
                        <div class="file-input-label" id="videosLabel">
                            🎥 Choose Videos
                        </div>
                        <input type="file" name="videos[]" accept="video/*" multiple>
                    </div>
                    <div class="file-types-info">
                        You can select multiple videos (MP4, MOV, AVI, WMV, WEBM)
                    </div>
                </div>
            </div>
            
            <button type="submit" class="submit-button">Submit News to Admin</button>
        </form>

        <div class="logout-container">
            <a href="journalist_login.php" class="logout-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Logout
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cover image
            const coverImageInput = document.querySelector('input[name="cover_image"]');
            const coverImageLabel = document.getElementById('coverImageLabel');
            
            coverImageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    coverImageLabel.innerHTML = `🖼️ ${this.files[0].name}`;
                    coverImageLabel.style.borderColor = '#2a4d8e';
                    coverImageLabel.style.background = '#edf2f7';
                }
            });
            
            // Additional images
            const additionalImagesInput = document.querySelector('input[name="additional_images[]"]');
            const additionalImagesLabel = document.getElementById('additionalImagesLabel');
            
            additionalImagesInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    additionalImagesLabel.innerHTML = `🖼️ ${this.files.length} image(s) selected`;
                    additionalImagesLabel.style.borderColor = '#2a4d8e';
                    additionalImagesLabel.style.background = '#edf2f7';
                }
            });
            
            // Videos
            const videosInput = document.querySelector('input[name="videos[]"]');
            const videosLabel = document.getElementById('videosLabel');
            
            videosInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    videosLabel.innerHTML = `🎥 ${this.files.length} video(s) selected`;
                    videosLabel.style.borderColor = '#2a4d8e';
                    videosLabel.style.background = '#edf2f7';
                }
            });
            
            // Form submission
            const form = document.querySelector('form');
            const submitButton = document.querySelector('.submit-button');
            
            if (form) {
                form.addEventListener('submit', function() {
                    submitButton.textContent = 'Submitting...';
                    submitButton.disabled = true;
                });
            }
        });
    </script>
</body>
</html>