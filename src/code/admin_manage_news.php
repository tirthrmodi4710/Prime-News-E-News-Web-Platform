<?php
include 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];

// Fetch categories for dropdown
$categories = [];
$res = $conn->query("SELECT * FROM categories ORDER BY id ASC");
while ($row = $res->fetch_assoc()) {
    $categories[] = $row;
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image_upload'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $target_file = $target_dir . basename($_FILES["image_upload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image_upload"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
    
    // Check file size (5MB limit)
    if ($_FILES["image_upload"]["size"] > 5000000) {
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image_upload"]["tmp_name"], $target_file)) {
            $uploaded_filename = basename($_FILES["image_upload"]["name"]);
            echo "<script>document.getElementById('imageInput').value = '$uploaded_filename';</script>";
        }
    }
}

// Update News
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_news'])) {
    $news_id = intval($_POST['news_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $image = $conn->real_escape_string($_POST['image']);
    $category_id = intval($_POST['category_id']);

    $conn->query("UPDATE news SET title='$title', description='$description', image='$image', category_id=$category_id WHERE id=$news_id");
}

// Delete News
if (isset($_GET['delete_id'])) {
    $news_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM news WHERE id=$news_id");
}

// Search functionality
$search_query = "";
$news_list = [];

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
    if (!empty($search_query)) {
        $result = $conn->query("SELECT news.*, categories.name AS category_name 
                               FROM news 
                               JOIN categories ON news.category_id = categories.id 
                               WHERE news.title LIKE '%$search_query%' 
                               OR news.description LIKE '%$search_query%'
                               OR categories.name LIKE '%$search_query%'
                               ORDER BY news.id DESC");
    } else {
        $result = $conn->query("SELECT news.*, categories.name AS category_name FROM news JOIN categories ON news.category_id = categories.id ORDER BY news.id DESC");
    }
} else {
    $result = $conn->query("SELECT news.*, categories.name AS category_name FROM news JOIN categories ON news.category_id = categories.id ORDER BY news.id DESC");
}

while ($row = $result->fetch_assoc()) {
    $news_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage News - Prime Report</title>
    <style>
        /* All existing CSS remains exactly the same - no changes */
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
            color: #2d3748;
            display: flex;
        }
        
        /* Side Panel Styles */
        .side-panel {
            width: 260px;
            background: linear-gradient(180deg, #1a365d 0%, #2a4d8e 100%);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 30px 0;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }
        
        .panel-header {
            text-align: center;
            padding: 0 20px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .panel-header h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .panel-header p {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.1);
            margin: 0 15px 20px;
            border-radius: 10px;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 18px;
        }
        
        .admin-details {
            flex: 1;
        }
        
        .admin-name {
            font-weight: 600;
            font-size: 15px;
        }
        
        .admin-role {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .nav-menu {
            list-style: none;
            padding: 0 15px;
        }
        
        .nav-item {
            margin-bottom: 8px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }
        
        .nav-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 18px;
            text-align: center;
        }
        
        .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 20px 15px;
        }
        
        /* Main Content Area */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 40px 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
            transform: scale(0.95);
            opacity: 0;
            animation: zoomIn 0.6s forwards ease-out;
        }
        
        @keyframes zoomIn {
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        h2 {
            text-align: center;
            color: #1a365d;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
            opacity: 0;
            transform: translateY(-20px);
            animation: fadeInDown 0.6s 0.3s forwards ease-out;
        }
        
        @keyframes fadeInDown {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #1a365d, #2a4d8e);
            animation: expandLine 0.8s 0.5s forwards ease-out;
            border-radius: 2px;
        }
        
        @keyframes expandLine {
            to {
                width: 100px;
            }
        }
        
        /* Search Form */
        .search-form {
            background: rgba(26, 54, 93, 0.05);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.5s 0.4s forwards ease-out;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .search-form input[type="text"] {
            flex: 1;
            padding: 14px 16px;
            font-size: 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: #f7fafc;
            color: #2d3748;
        }
        
        .search-form input[type="text"]:focus {
            outline: none;
            border-color: #2a4d8e;
            box-shadow: 0 0 0 3px rgba(42, 77, 142, 0.15);
            background: #fff;
            transform: translateY(-2px);
        }
        
        .search-form button {
            padding: 14px 24px;
            background: linear-gradient(135deg, #1a365d 0%, #2a4d8e 100%);
            color: white;
            border: none;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(26, 54, 93, 0.2);
            white-space: nowrap;
        }
        
        .search-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(26, 54, 93, 0.3);
        }
        
        .clear-search {
            padding: 14px 20px;
            background: #718096;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .clear-search:hover {
            background: #4a5568;
            transform: translateY(-2px);
        }
        
        /* News Table */
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeIn 0.6s 0.6s forwards ease-out;
        }
        
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }
        
        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        th {
            background: linear-gradient(135deg, #1a365d 0%, #2a4d8e 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        tr {
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateX(-20px);
            animation: slideInRight 0.5s forwards ease-out;
        }
        
        tr:nth-child(1) { animation-delay: 0.7s; }
        tr:nth-child(2) { animation-delay: 0.8s; }
        tr:nth-child(3) { animation-delay: 0.9s; }
        tr:nth-child(4) { animation-delay: 1.0s; }
        tr:nth-child(5) { animation-delay: 1.1s; }
        
        @keyframes slideInRight {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        tr:hover {
            background: rgba(26, 54, 93, 0.03);
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        /* Update Form */
        .update-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .update-form input[type="text"],
        .update-form textarea,
        .update-form select {
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            transition: all 0.3s ease;
            background: #f7fafc;
            width: 100%;
        }
        
        .update-form input[type="text"]:focus,
        .update-form textarea:focus,
        .update-form select:focus {
            outline: none;
            border-color: #2a4d8e;
            box-shadow: 0 0 0 2px rgba(42, 77, 142, 0.15);
            background: #fff;
        }
        
        .update-form textarea {
            min-height: 60px;
            resize: vertical;
        }
        
        .update-form button {
            padding: 10px 16px;
            background: #38a169;
            color: white;
            border: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 5px;
        }
        
        .update-form button:hover {
            background: #2f855a;
            transform: translateY(-1px);
        }
        
        /* Delete Link */
        .delete-btn {
            color: #e53e3e;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-block;
            background: rgba(229, 62, 62, 0.1);
            margin-top: 8px;
        }
        
        .delete-btn:hover {
            background: rgba(229, 62, 62, 0.2);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(229, 62, 62, 0.2);
        }
        
        /* Back Link */
        .back-link {
            text-align: center;
            margin-top: 30px;
            opacity: 0;
            animation: fadeIn 0.6s 1.2s forwards ease-out;
        }
        
        .back-link a {
            color: #2a4d8e;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border: 1px solid #2a4d8e;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .back-link a:hover {
            background: #2a4d8e;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(42, 77, 142, 0.2);
        }
        
        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
            background: #1a365d;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .side-panel {
                transform: translateX(-100%);
            }
            
            .side-panel.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 80px 20px 40px;
            }
            
            .mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .container {
                padding: 30px 20px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            th, td {
                padding: 12px 8px;
                font-size: 14px;
            }
            
            .update-form {
                min-width: 200px;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-form input[type="text"],
            .search-form button,
            .clear-search {
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 80px 15px 20px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .search-form {
                padding: 15px;
            }
            
            th, td {
                padding: 10px 6px;
                font-size: 12px;
            }
            
            .update-form input[type="text"],
            .update-form textarea,
            .update-form select {
                padding: 8px 10px;
                font-size: 12px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Side Panel -->
    <div class="side-panel" id="sidePanel">
        <div class="panel-header">
            <h3>Prime Report</h3>
            <p>Admin Panel</p>
        </div>
        
        <div class="admin-info">
            <div class="admin-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="admin-details">
                <div class="admin-name"><?= htmlspecialchars($admin_username) ?></div>
                <div class="admin-role">Administrator</div>
            </div>
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="admin_dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_manage_categories.php" class="nav-link">
                    <i class="fas fa-folder"></i>
                    <span>Manage Categories</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_manage_news.php" class="nav-link active">
                    <i class="fas fa-newspaper"></i>
                    <span>Manage News</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="admin_manage_other_news.php" class="nav-link">
                    <i class="fas fa-globe"></i>
                    <span>Manage Other News</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="admin_view_received_news.php" class="nav-link">
                    <i class="fas fa-envelope-open-text"></i>
                    <span>View Received News</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="news_likes.php" class="nav-link active">
                    <i class="fas fa-heart"></i>
                    <span>Liked News</span>
                </a>
            </li>
        </ul>
        
        <div class="nav-divider"></div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="admin_login.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h2>📰 Manage News</h2>

            <!-- Search Form -->
            <form method="get" class="search-form">
                <input type="text" name="search" placeholder="Search news by title, description, or category..." value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if (!empty($search_query)): ?>
                    <a href="admin_manage_news.php" class="clear-search">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </form>

            <!-- News List -->
            <div class="table-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                    <?php if (empty($news_list)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                <?php if (!empty($search_query)): ?>
                                    No news found matching "<?= htmlspecialchars($search_query) ?>"
                                <?php else: ?>
                                    No news available
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($news_list as $item): ?>
                            <tr>
                                <td>
                                    <?= $item['id'] ?>
                                    <input type="hidden" name="news_id" value="<?= $item['id'] ?>">
                                </td>
                                <td>
                                    <form method="post" class="update-form">
                                        <input type="hidden" name="news_id" value="<?= $item['id'] ?>">
                                        <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required>
                                </td>
                                <td>
                                        <textarea name="description" rows="2" required><?= htmlspecialchars($item['description']) ?></textarea>
                                </td>
                                <td>
                                        <div style="display: flex; gap: 8px; align-items: center;">
                                            <input type="text" name="image" value="<?= htmlspecialchars($item['image']) ?>" required style="flex: 1; margin: 0;">
                                            <button type="button" class="update-browse-btn" onclick="document.getElementById('imageUpload_<?= $item['id'] ?>').click()" style="padding: 10px 12px; background: #38a169; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; transition: all 0.3s ease; white-space: nowrap;">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <input type="file" id="imageUpload_<?= $item['id'] ?>" name="image_upload" accept="image/*" style="display: none;" onchange="handleImageUpload(this, '<?= $item['id'] ?>')">
                                        </div>
                                </td>
                                <td>
                                        <select name="category_id" required>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $item['category_id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                </td>
                                <td>
                                        <button type="submit" name="update_news">Update</button>
                                    </form>
                                    <a href="admin_manage_news.php?delete_id=<?= $item['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure to delete this news?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>

            <div class="back-link">
                <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
            </div>
        </div>
    </div>

    <script>
        function handleImageUpload(input, rowId = null) {
            const file = input.files[0];
            if (file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPEG, PNG, GIF)');
                    input.value = '';
                    return;
                }
                
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should be less than 5MB');
                    input.value = '';
                    return;
                }
                
                // Set the filename in the corresponding input field
                if (rowId) {
                    // For update forms
                    const imageInput = input.previousElementSibling.previousElementSibling;
                    imageInput.value = file.name;
                }
                
                // You can add file upload logic here if needed
                // For now, we just set the filename
                alert('Image selected: ' + file.name + '\nThe filename has been automatically filled.');
            }
        }

        // Mobile toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobileToggle');
            const sidePanel = document.getElementById('sidePanel');
            
            mobileToggle.addEventListener('click', function() {
                sidePanel.classList.toggle('active');
                
                // Change icon based on panel state
                const icon = this.querySelector('i');
                if (sidePanel.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
            
            // Close panel when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && 
                    !sidePanel.contains(event.target) && 
                    !mobileToggle.contains(event.target) &&
                    sidePanel.classList.contains('active')) {
                    sidePanel.classList.remove('active');
                    mobileToggle.querySelector('i').classList.remove('fa-times');
                    mobileToggle.querySelector('i').classList.add('fa-bars');
                }
            });
            
            // Highlight current page in navigation
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                if (linkHref === currentPage) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
            
            // Add form submission animations
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const button = this.querySelector('button');
                    if (button) {
                        const originalText = button.textContent;
                        button.textContent = 'Processing...';
                        button.style.opacity = '0.8';
                        button.disabled = true;
                        
                        // Reset button after 2 seconds
                        setTimeout(() => {
                            button.textContent = originalText;
                            button.style.opacity = '1';
                            button.disabled = false;
                        }, 2000);
                    }
                });
            });
        });
    </script>
</body>
</html>