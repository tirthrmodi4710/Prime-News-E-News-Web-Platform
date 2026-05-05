<?php
include 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if journalist is logged in
if (!isset($_SESSION['journalist_id'])) {
    header("Location: journalist_login.php");
    exit();
}

$journalist_username = $_SESSION['journalist_username'];
$success = "";
$error = "";

// Handle Add Other News
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_other_news'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    
    if (!empty($title) && !empty($content)) {
        $query = "INSERT INTO other_news (title, content, created_at) VALUES ('$title', '$content', NOW())";
        
        if ($conn->query($query)) {
            $success = "Other news added successfully!";
        } else {
            $error = "Error adding news: " . $conn->error;
        }
    } else {
        $error = "Please fill in all fields!";
    }
}

// Fetch existing other news
$other_news = [];
$result = $conn->query("SELECT * FROM other_news ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $other_news[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Other News - Prime Report</title>
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
            max-width: 800px;
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
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }
        
        .form-group input,
        .form-group textarea {
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
        .form-group textarea:focus {
            outline: none;
            border-color: #2a4d8e;
            box-shadow: 0 0 0 3px rgba(42, 77, 142, 0.15);
            background: #fff;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
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
        
        /* News List */
        .news-list {
            margin-top: 40px;
        }
        
        .news-list h3 {
            color: #1a365d;
            margin-bottom: 20px;
            text-align: center;
            font-size: 22px;
        }
        
        .news-item {
            background: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
        }
        
        .news-title {
            font-weight: 600;
            color: #1a365d;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .news-content {
            color: #4a5568;
            line-height: 1.6;
        }
        
        .news-date {
            color: #718096;
            font-size: 14px;
            margin-top: 10px;
        }
        
        /* Back Button */
        .back-container {
            text-align: center;
            margin-top: 30px;
        }
        
        .back-button {
            color: #2a4d8e;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border: 1px solid #2a4d8e;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-button:hover {
            background: #2a4d8e;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(42, 77, 142, 0.2);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            .dashboard-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h2>Change Other News</h2>
            <div class="welcome-text">Welcome, <?= htmlspecialchars($journalist_username) ?></div>
        </div>

        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" name="title" placeholder="Enter news title" required>
            </div>
            
            <div class="form-group">
                <label for="content">Description *</label>
                <textarea name="content" placeholder="Enter news description" rows="6" required></textarea>
            </div>
            
            <button type="submit" name="add_other_news" class="submit-button">Add Other News</button>
        </form>

        <div class="news-list">
            <h3>Existing Other News</h3>
            <?php if (empty($other_news)): ?>
                <div style="text-align: center; color: #718096; padding: 40px;">
                    No other news available yet.
                </div>
            <?php else: ?>
                <?php foreach ($other_news as $news): ?>
                    <div class="news-item">
                        <div class="news-title"><?= htmlspecialchars($news['title']) ?></div>
                        <div class="news-content"><?= htmlspecialchars($news['content']) ?></div>
                        <div class="news-date">Added on: <?= date('F j, Y g:i A', strtotime($news['created_at'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="back-container">
            <a href="journalist_dashboard.php" class="back-button">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitButton = document.querySelector('.submit-button');
            
            if (form) {
                form.addEventListener('submit', function() {
                    submitButton.textContent = 'Adding News...';
                    submitButton.disabled = true;
                });
            }
        });
    </script>
</body>
</html>