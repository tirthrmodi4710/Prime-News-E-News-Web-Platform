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

// Fetch categories for displaying name
$categories = [];
$res = $conn->query("SELECT * FROM categories ORDER BY id ASC");
while ($row = $res->fetch_assoc()) {
    $categories[$row['id']] = $row['name'];
}

// Publish news to main news table
if (isset($_GET['publish_id'])) {
    $publish_id = intval($_GET['publish_id']);
    $result = $conn->query("SELECT * FROM news_requests WHERE id=$publish_id");

    if ($result && $result->num_rows == 1) {
        $pending_news = $result->fetch_assoc();

        // Insert into news table with ALL data
        $stmt = $conn->prepare("INSERT INTO news (title, description, full_content, image, additional_images, videos, category_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param(
            "ssssssi",
            $pending_news['title'],
            $pending_news['description'],
            $pending_news['full_content'],
            $pending_news['image'],
            $pending_news['additional_images'],
            $pending_news['videos'],
            $pending_news['category_id']
        );
        
        if ($stmt->execute()) {
            // Update status in news_requests instead of deleting
            $conn->query("UPDATE news_requests SET status = 'published' WHERE id=$publish_id");
            $message = "News published to website successfully.";
        } else {
            $message = "Error publishing news: " . $stmt->error;
        }
    } else {
        $message = "News not found or error fetching data.";
    }
}

// Fetch pending news from news_requests (only with pending status)
$pending_news_list = [];
$result = $conn->query("SELECT * FROM news_requests WHERE status = 'pending' ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $pending_news_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Received News - Prime Report</title>
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
                width: 120px;
            }
        }
        
        /* Message Styling */
        .message {
            color: #38a169;
            text-align: center;
            font-weight: 500;
            padding: 12px 20px;
            background: rgba(56, 161, 105, 0.1);
            border-radius: 8px;
            margin-bottom: 25px;
            opacity: 0;
            animation: fadeIn 0.6s forwards ease-out;
        }
        
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
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
        
        /* News Content Styling */
        .news-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }
        
        .news-description {
            color: #718096;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .news-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .news-image:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .no-image {
            color: #a0aec0;
            font-style: italic;
            font-size: 14px;
        }
        
        .category-badge {
            background: rgba(66, 153, 225, 0.1);
            color: #4299e1;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
        }
        
        /* Publish Button */
        .publish-btn {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(56, 161, 105, 0.2);
        }
        
        .publish-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(56, 161, 105, 0.3);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #718096;
            opacity: 0;
            animation: fadeIn 0.6s 0.6s forwards ease-out;
        }
        
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: #cbd5e0;
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
            
            .news-image {
                width: 60px;
                height: 45px;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 80px 15px 20px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            th, td {
                padding: 10px 6px;
                font-size: 12px;
            }
            
            .publish-btn {
                padding: 8px 12px;
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
                <a href="admin_manage_news.php" class="nav-link">
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
                <a href="admin_view_received_news.php" class="nav-link active">
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
            <h2>📨 Received News from Journalists</h2>

            <?php if (!empty($message)): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <?php if (count($pending_news_list) > 0): ?>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Action</th>
                        </tr>
                        <?php foreach ($pending_news_list as $news): ?>
                            <tr>
                                <td><?= $news['id'] ?></td>
                                <td>
                                    <div class="news-title"><?= htmlspecialchars($news['title']) ?></div>
                                </td>
                                <td>
                                    <div class="news-description"><?= htmlspecialchars(substr($news['description'], 0, 50)) ?>...</div>
                                </td>
                                <td>
                                    <?php if (!empty($news['image'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($news['image']) ?>" alt="News Image" class="news-image">
                                    <?php else: ?>
                                        <span class="no-image">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="category-badge">
                                        <?= isset($categories[$news['category_id']]) ? htmlspecialchars($categories[$news['category_id']]) : 'Unknown' ?>
                                    </span>
                                </td>
                                <td>
                                    <a class="publish-btn" href="admin_view_received_news.php?publish_id=<?= $news['id'] ?>" onclick="return confirm('Publish this news to the website?');">Publish</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p>No pending news from journalists currently.</p>
                </div>
            <?php endif; ?>

            <div class="back-link">
                <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
            </div>
        </div>
    </div>

    <script>
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
            
            // Add confirmation for publish action
            const publishButtons = document.querySelectorAll('.publish-btn');
            
            publishButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to publish this news to the website?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>