<?php
session_start();
include 'db_connect.php';

// Fetch categories
$categories = [];
$res = $conn->query("SELECT * FROM categories");
while ($row = $res->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch news
$news = [];
if (isset($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);
    $result = $conn->query("SELECT * FROM news WHERE category_id=$category_id");
} else {
    $result = $conn->query("SELECT * FROM news LIMIT 5");
}
while ($row = $result->fetch_assoc()) {
    $news[] = $row;
}

// Fetch other news
$other_news = [];
$res = $conn->query("SELECT * FROM other_news LIMIT 5");
while ($row = $res->fetch_assoc()) {
    $other_news[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prime Report</title>
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
            color: #2d3748;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }
        
        body.dark-mode {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            color: #e2e8f0;
        }
        
        .container {
            display: flex;
            padding: 30px 40px;
            gap: 30px;
            max-width: 1200px;
            margin: auto;
        }
        
        .sidebar,
        .rightbar {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px 20px;
            width: 22%;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
        }
        
        .main-content {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px 25px;
            width: 56%;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
        }
        
        body.dark-mode .sidebar,
        body.dark-mode .main-content,
        body.dark-mode .rightbar {
            background: rgba(45, 55, 72, 0.95);
            color: #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .sidebar h2,
        .rightbar h2,
        .main-content h2 {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #1a365d;
            font-size: 24px;
        }
        
        body.dark-mode .sidebar h2,
        body.dark-mode .rightbar h2,
        body.dark-mode .main-content h2 {
            color: #e2e8f0;
            border-bottom: 2px solid #4a5568;
        }
        
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar ul li {
            margin: 14px 0;
        }
        
        .sidebar ul li a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s ease;
        }
        
        body.dark-mode .sidebar ul li a {
            color: #a0aec0;
        }
        
        .sidebar ul li a:hover {
            color: #1a365d;
            text-decoration: underline;
        }
        
        body.dark-mode .sidebar ul li a:hover {
            color: #e2e8f0;
        }
        
        .news-item {
            display: flex;
            gap: 18px;
            margin-bottom: 22px;
            align-items: flex-start;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 18px;
        }
        
        body.dark-mode .news-item {
            border-bottom: 1px solid #4a5568;
        }
        
        .news-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .news-item img {
            width: 160px;
            height: 110px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }
        
        .news-item h3 {
            margin: 0 0 8px 0;
            font-size: 19px;
            font-weight: 700;
            color: #1a365d;
            line-height: 1.3;
        }
        
        body.dark-mode .news-item h3 {
            color: #e2e8f0;
        }
        
        .news-link {
            text-decoration: none;
            color: inherit;
            transition: color 0.3s ease, text-decoration 0.3s ease;
        }
        
        .news-link:hover {
            text-decoration: underline;
            color: #2a4d8e;
        }
        
        body.dark-mode .news-link:hover {
            color: #90cdf4;
        }
        
        .news-item p {
            margin: 0;
            font-size: 14.5px;
            color: #4a5568;
            line-height: 1.6;
        }
        
        body.dark-mode .news-item p {
            color: #a0aec0;
        }
        
        .rightbar h4 {
            margin: 0 0 6px 0;
            font-weight: 600;
            color: #1a365d;
            font-size: 17px;
        }
        
        body.dark-mode .rightbar h4 {
            color: #e2e8f0;
        }
        
        .rightbar p {
            font-size: 14px;
            color: #4a5568;
            margin: 0 0 15px 0;
            line-height: 1.6;
        }
        
        body.dark-mode .rightbar p {
            color: #a0aec0;
        }
        
        .rightbar hr {
            border: none;
            border-bottom: 1px solid #e2e8f0;
            margin: 15px 0;
        }
        
        body.dark-mode .rightbar hr {
            border-bottom: 1px solid #4a5568;
        }
        
        footer {
            text-align: center;
            padding: 25px;
            color: #718096;
            margin-top: 40px;
        }
        
        body.dark-mode footer {
            color: #a0aec0;
        }
        
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.success {
            background: rgba(56, 161, 105, 0.1);
            color: #38a169;
            border: 1px solid rgba(56, 161, 105, 0.2);
        }
        
        .message.error {
            background: rgba(229, 62, 62, 0.1);
            color: #e53e3e;
            border: 1px solid rgba(229, 62, 62, 0.2);
        }
        
        .message.warning {
            background: rgba(237, 137, 54, 0.1);
            color: #ed8936;
            border: 1px solid rgba(237, 137, 54, 0.2);
        }
        
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                padding: 20px;
            }
            
            .sidebar,
            .rightbar,
            .main-content {
                width: 100%;
            }
            
            .news-item {
                flex-direction: column;
            }
            
            .news-item img {
                width: 100%;
                height: 200px;
            }
        }

        .goog-logo-link, .goog-te-gadget span {
            display: none !important;
        }

        .goog-te-gadget {
            font-size: 0 !important;
            color: transparent !important;
        }

        #translate-container {
            display: none;
            position: absolute;
            top: 70px;
            right: 20px;
            background: white;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 9999;
        }

        #translate-spinner {
            display: none;
            color: #333;
            font-size: 15px;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>   

<div class="container">
    <div class="sidebar">
        <h2>Categories</h2>
        <ul>
            <?php foreach ($categories as $cat): ?>
                <li><a href="index.php?category_id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="main-content">
        <h2><?= isset($category_id) ? 'News' : 'Latest News' ?></h2>
        <?php if (!empty($news)): ?>
            <?php foreach ($news as $item): ?>
                <div class="news-item">
                    <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="News Image">
                    <div>
                        <h3>
                            <a href="news.php?id=<?= $item['id'] ?>" class="news-link">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                        </h3>
                        <p><?= htmlspecialchars(substr($item['description'], 0, 100)) ?>...</p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No news available currently.</p>
        <?php endif; ?>
    </div>

    <div class="rightbar">
        <h2>Other News</h2>
        <?php if (!empty($other_news)): ?>
            <?php foreach ($other_news as $on): ?>
                <h4><?= htmlspecialchars($on['title']) ?></h4>
                <p><?= htmlspecialchars(substr($on['content'], 0, 100)) ?>...</p>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No other news available currently.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2025 Prime Report. All rights reserved.</p>
</footer>

<script>
    function toggleDarkMode() {
        document.body.classList.toggle("dark-mode");
        localStorage.setItem("darkMode", document.body.classList.contains("dark-mode"));
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        const isDark = localStorage.getItem("darkMode") === "true";
        if (isDark) {
            document.body.classList.add("dark-mode");
        }
    });
</script>

<!-- Translate Container -->
<div id="translate-container">
    <div id="google_translate_element"></div>
    <div id="translate-spinner">🌐 Loading translation options...</div>
</div>
<script type="text/javascript">
function toggleTranslate() {
    const box = document.getElementById("translate-container");
    const spinner = document.getElementById("translate-spinner");

    if (box.style.display === "none" || box.style.display === "") {
        box.style.display = "block";
        spinner.style.display = "block";
    } else {
        box.style.display = "none";
    }
}
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'en,gu,hi',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE
    }, 'google_translate_element');
    document.getElementById("translate-spinner").style.display = "none";
}
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>