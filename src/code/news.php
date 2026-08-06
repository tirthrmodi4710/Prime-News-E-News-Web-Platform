<?php
include 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$news = null;
$save_message = "";  // Changed from $message
$like_message = "";

// Fetch news based on ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM news WHERE id = $id");
    if ($res->num_rows > 0) {
        $news = $res->fetch_assoc();
        
        // Decode JSON arrays for additional images and videos
        if (!empty($news['additional_images'])) {
            $news['additional_images'] = json_decode($news['additional_images'], true);
        } else {
            $news['additional_images'] = [];
        }
        
        if (!empty($news['videos'])) {
            $news['videos'] = json_decode($news['videos'], true);
        } else {
            $news['videos'] = [];
        }
    }
}

// Handle Save News
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['news_id']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $news_id = intval($_POST['news_id']);

    // Check if this is a like request or save request
    if (isset($_POST['like_news'])) {
        // Handle Like News
        $check_like = $conn->prepare("SELECT id FROM news_likes WHERE user_id = ? AND news_id = ?");
        $check_like->bind_param("ii", $user_id, $news_id);
        $check_like->execute();
        $check_like->store_result();

        if ($check_like->num_rows === 0) {
            // Insert like
            $stmt_like = $conn->prepare("INSERT INTO news_likes (user_id, news_id, liked_at) VALUES (?, ?, NOW())");
            $stmt_like->bind_param("ii", $user_id, $news_id);
            if ($stmt_like->execute()) {
                $like_message = "❤️ Thank You for liking this news!";
            } else {
                $like_message = "❌ Failed to like news.";
            }
        } else {
            $like_message = "⚠️ You already liked this news.";
        }
    } else {
        // Handle Save News
        $check = $conn->prepare("SELECT id FROM saved_news WHERE user_id = ? AND news_id = ?");
        $check->bind_param("ii", $user_id, $news_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO saved_news (user_id, news_id, saved_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $user_id, $news_id);
            if ($stmt->execute()) {
                $save_message = "✅ News saved successfully!";
            } else {
                $save_message = "❌ Failed to save news.";
            }
        } else {
            $save_message = "⚠️ News already saved.";
        }
    }
}

function isVideoFile($filename) {
    $video_extensions = ['mp4', 'mov', 'avi', 'wmv', 'webm'];
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, $video_extensions);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>News Details - Prime Report</title>
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
        
        .news-container {
            max-width: 900px;
            margin: 30px auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
        }
        
        body.dark-mode .news-container {
            background: rgba(45, 55, 72, 0.95);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .news-title {
            font-size: 32px;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        body.dark-mode .news-title {
            color: #e2e8f0;
        }
        
        .news-meta {
            color: #718096;
            font-size: 14px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        body.dark-mode .news-meta {
            color: #a0aec0;
            border-bottom: 1px solid #4a5568;
        }
        
        .media-container {
            margin: 25px 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .news-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 12px;
        }
        
        .news-video {
            width: 100%;
            max-height: 500px;
            border-radius: 12px;
            background: #000;
        }
        
        .additional-media-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .additional-media-item {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .additional-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .additional-video {
            width: 100%;
            height: 250px;
            background: #000;
        }
        
        .news-description {
            font-size: 18px;
            line-height: 1.6;
            color: #4a5568;
            margin: 20px 0;
            font-weight: 500;
            font-style: italic;
        }
        
        .news-full-content {
            font-size: 16px;
            line-height: 1.8;
            color: #2d3748;
            margin: 30px 0;
            white-space: pre-line;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 100%;
        }
        
        body.dark-mode .news-description,
        body.dark-mode .news-full-content {
            color: #e2e8f0;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1a365d 0%, #2a4d8e 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 54, 93, 0.3);
        }
        
        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }
        
        body.dark-mode .btn-secondary {
            background: #4a5568;
            color: #e2e8f0;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-like {
            background: linear-gradient(135deg, #e53e3e 0%, #fc8181 100%);
            color: white;
        }
        
        .btn-like:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
        }
        
        .btn-like.liked {
            background: linear-gradient(135deg, #38a169 0%, #68d391 100%);
        }
        
        .btn-share {
            background: linear-gradient(135deg, #38a169 0%, #48bb78 100%);
            color: white;
        }
        
        .btn-share:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(56, 161, 105, 0.3);
        }
        
        .translate-container {
            margin: 25px 0;
            padding: 20px;
            background: #f7fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        
        body.dark-mode .translate-container {
            background: #2d3748;
            border: 1px solid #4a5568;
        }
        
        .translate-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #2d3748;
        }
        
        body.dark-mode .translate-label {
            color: #e2e8f0;
        }
        
        .lang-selector {
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            color: #2d3748;
            font-size: 16px;
            width: 200px;
        }
        
        body.dark-mode .lang-selector {
            background: #4a5568;
            border-color: #718096;
            color: #e2e8f0;
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
        
        .message.like {
            background: rgba(229, 62, 62, 0.1);
            color: #e53e3e;
            border: 1px solid rgba(229, 62, 62, 0.2);
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 600;
            color: #1a365d;
            margin: 40px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        body.dark-mode .section-title {
            color: #e2e8f0;
            border-bottom: 2px solid #4a5568;
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
        
        #stopButton {
            display: none;
        }
        
        #stopButton.show {
            display: inline-flex !important;
        }
        
        /* Share Modal Styles */
        .share-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .share-modal.show {
            display: flex;
        }
        
        .share-modal-content {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
            text-align: center;
        }
        
        body.dark-mode .share-modal-content {
            background: #2d3748;
            color: #e2e8f0;
        }
        
        .share-modal h3 {
            margin-bottom: 20px;
            color: #1a365d;
            font-size: 24px;
        }
        
        body.dark-mode .share-modal h3 {
            color: #e2e8f0;
        }
        
        .share-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }
        
        .share-option {
            padding: 15px 20px;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }
        
        body.dark-mode .share-option {
            background: #4a5568;
            border-color: #718096;
            color: #e2e8f0;
        }
        
        .share-option:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }
        
        body.dark-mode .share-option:hover {
            background: #5a6578;
        }
        
        .share-option i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }
        
        .close-share {
            background: #e53e3e;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .close-share:hover {
            background: #c53030;
            transform: translateY(-2px);
        }
        
        .copy-success {
            color: #38a169;
            font-weight: 500;
            margin-top: 10px;
            display: none;
        }
        
        @media (max-width: 768px) {
            .news-container {
                margin: 15px;
                padding: 25px;
            }
            
            .news-title {
                font-size: 26px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .lang-selector {
                width: 100%;
            }
            
            .additional-media-container {
                grid-template-columns: 1fr;
            }
            
            .share-modal-content {
                padding: 20px;
                margin: 20px;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>   

<div class="news-container">
    <?php if ($save_message): ?>
        <div class="message <?php 
            if (strpos($save_message, '✅') !== false) echo 'success';
            elseif (strpos($save_message, '❌') !== false) echo 'error';
            else echo 'warning';
        ?>"><?= $save_message ?></div>
    <?php endif; ?>

    <?php if ($like_message): ?>
        <div class="message <?php 
            if (strpos($like_message, '❤️') !== false) echo 'success';
            elseif (strpos($like_message, '❌') !== false) echo 'error';
            else echo 'warning';
        ?>"><?= $like_message ?></div>
    <?php endif; ?>

    <?php if ($news): ?>
        <h1 id="newsTitle" class="news-title"><?= htmlspecialchars($news['title']) ?></h1>
        
        <div class="news-meta">
            <strong>Posted on:</strong> <?= htmlspecialchars($news['created_at']) ?>
        </div>
        
        <!-- Cover Image/Video -->
        <?php if (!empty($news['image'])): ?>
            <div class="media-container">
                <?php if (isVideoFile($news['image'])): ?>
                    <video class="news-video" controls>
                        <source src="uploads/<?= htmlspecialchars($news['image']) ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php else: ?>
                    <img src="uploads/<?= htmlspecialchars($news['image']) ?>" alt="Cover Image" class="news-image">
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Short Description -->
        <div class="news-description">
            <?= nl2br(htmlspecialchars($news['description'])) ?>
        </div>

        <!-- Full Content -->
        <?php if (!empty($news['full_content'])): ?>
            <div id="newsContent" class="news-full-content">
                <?= nl2br(htmlspecialchars($news['full_content'])) ?>
            </div>
        <?php else: ?>
            <div class="message warning">
                ⚠️ Full content not available for this news article.
            </div>
        <?php endif; ?>

        <!-- Additional Images -->
        <?php if (!empty($news['additional_images'])): ?>
            <h3 class="section-title">Additional Images</h3>
            <div class="additional-media-container">
                <?php foreach ($news['additional_images'] as $image): ?>
                    <div class="additional-media-item">
                        <img src="uploads/<?= htmlspecialchars($image) ?>" alt="Additional Image" class="additional-image">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Videos -->
        <?php if (!empty($news['videos'])): ?>
            <h3 class="section-title">Videos</h3>
            <div class="additional-media-container">
                <?php foreach ($news['videos'] as $video): ?>
                    <div class="additional-media-item">
                        <video class="additional-video" controls>
                            <source src="uploads/<?= htmlspecialchars($video) ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="btn btn-primary" onclick="speakText()">
                    🔊 Listen to Article
                </button>
                
                <button class="btn btn-secondary" onclick="stopSpeech()" id="stopButton">
                    ⏹️ Stop
                </button>

                <form method="post" action="" style="display: inline;">
                    <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                    <button type="submit" class="btn btn-secondary">
                        💾 Save News
                    </button>
                </form>

                <!-- Like News Button -->
                <form method="post" action="" style="display: inline;">
                    <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                    <input type="hidden" name="like_news" value="1">
                    <button type="submit" class="btn btn-like" id="likeButton">
                        ❤️ Like News
                    </button>
                </form>

                <button class="btn btn-share" onclick="openShareModal()">
                    📤 Share
                </button>
            <?php else: ?>
                <div style="color: #718096; font-style: italic;">
                    Please <a href="user_login.php" style="color: #0073e6;">login</a> to access Listen to Article, Save News, and Like features.
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="translate-container">
                <label class="translate-label">Translate this article:</label>
                <select id="langSelector" class="lang-selector" onchange="translateNewsContent()">
                    <option value="">Select Language</option>
                    <option value="en">English</option>
                    <option value="gu">Gujarati</option>
                    <option value="hi">Hindi</option>
                    <option value="ta">Tamil</option>
                    <option value="te">Telugu</option>
                    <option value="ml">Malayalam</option>
                    <option value="kn">Kannada</option>
                </select>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="message error">❌ News not found or invalid ID.</div>
    <?php endif; ?>
</div>

<!-- Share Modal -->
<div id="shareModal" class="share-modal">
    <div class="share-modal-content">
        <h3>Share This News</h3>
        <div class="share-options">
            <div class="share-option" onclick="copyLink()">
                <span>🔗</span> Copy Link
            </div>
            <div class="share-option" onclick="shareEmail()">
                <span>📧</span> Share via Email
            </div>
            <div class="share-option" onclick="shareWhatsApp()">
                <span>💬</span> Share on WhatsApp
            </div>
            <div class="share-option" onclick="shareFacebook()">
                <span>📘</span> Share on Facebook
            </div>
            <div class="share-option" onclick="shareTwitter()">
                <span>🐦</span> Share on Twitter
            </div>
        </div>
        <div class="copy-success" id="copySuccess">✅ Link copied to clipboard!</div>
        <button class="close-share" onclick="closeShareModal()">Close</button>
    </div>
</div>

<footer>
    <p>&copy; 2025 Prime Report. All rights reserved.</p>
</footer>

<script>
    let currentUtterance = null;
    let isPaused = false;

    // Share functionality
    function openShareModal() {
        document.getElementById('shareModal').classList.add('show');
    }

    function closeShareModal() {
        document.getElementById('shareModal').classList.remove('show');
        document.getElementById('copySuccess').style.display = 'none';
    }

    function copyLink() {
        const newsLink = window.location.href;
        navigator.clipboard.writeText(newsLink).then(() => {
            const copySuccess = document.getElementById('copySuccess');
            copySuccess.style.display = 'block';
            setTimeout(() => {
                copySuccess.style.display = 'none';
            }, 3000);
        });
    }

    function shareEmail() {
        const subject = encodeURIComponent("Check out this news: " + document.getElementById('newsTitle').innerText);
        const body = encodeURIComponent("I found this interesting news article:\n\n" + window.location.href);
        window.open(`mailto:?subject=${subject}&body=${body}`, '_blank');
    }

    function shareWhatsApp() {
        const text = encodeURIComponent("Check out this news: " + document.getElementById('newsTitle').innerText + " - " + window.location.href);
        window.open(`https://wa.me/?text=${text}`, '_blank');
    }

    function shareFacebook() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
    }

    function shareTwitter() {
        const text = encodeURIComponent("Check out this news: " + document.getElementById('newsTitle').innerText);
        const url = encodeURIComponent(window.location.href);
        window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
    }

    // Close modal when clicking outside
    document.getElementById('shareModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeShareModal();
        }
    });

    // Existing functions remain the same
    function speakText() {
        const title = document.getElementById('newsTitle').innerText;
        const content = document.getElementById('newsContent').innerText;
        const text = `${title}. ${content}`;
        
        const speakButton = document.querySelector('.btn-primary');
        const stopButton = document.getElementById('stopButton');
        
        if (currentUtterance && !isPaused) {
            speechSynthesis.pause();
            isPaused = true;
            speakButton.innerHTML = '▶️ Resume Article';
        } else if (currentUtterance && isPaused) {
            speechSynthesis.resume();
            isPaused = false;
            speakButton.innerHTML = '⏸️ Pause Article';
            stopButton.classList.add('show');
        } else {
            currentUtterance = new SpeechSynthesisUtterance(text);
            currentUtterance.rate = 0.9;
            currentUtterance.pitch = 1;
            currentUtterance.volume = 0.8;
            
            currentUtterance.onend = function() {
                currentUtterance = null;
                isPaused = false;
                speakButton.innerHTML = '🔊 Listen to Article';
                stopButton.classList.remove('show');
            };
            
            currentUtterance.onerror = function() {
                currentUtterance = null;
                isPaused = false;
                speakButton.innerHTML = '🔊 Listen to Article';
                stopButton.classList.remove('show');
            };
            
            speechSynthesis.speak(currentUtterance);
            speakButton.innerHTML = '⏸️ Pause Article';
            stopButton.classList.add('show');
        }
    }

    function stopSpeech() {
        if (currentUtterance) {
            speechSynthesis.cancel();
            currentUtterance = null;
            isPaused = false;
            const speakButton = document.querySelector('.btn-primary');
            speakButton.innerHTML = '🔊 Listen to Article';
            document.getElementById('stopButton').classList.remove('show');
        }
    }

    window.addEventListener('beforeunload', function() {
        if (currentUtterance) {
            speechSynthesis.cancel();
        }
    });

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

    function translateNewsContent() {
        const targetLang = document.getElementById('langSelector').value;
        if (!targetLang) return;

        const titleElem = document.getElementById('newsTitle');
        const contentElem = document.getElementById('newsContent');
        const originalTitle = titleElem.dataset.original || titleElem.innerText;
        const originalContent = contentElem.dataset.original || contentElem.innerText;
        
        if (!titleElem.dataset.original) {
            titleElem.dataset.original = originalTitle;
            contentElem.dataset.original = originalContent;
        }

        const originalButtonText = document.querySelector('.btn-primary').innerHTML;
        document.querySelector('.btn-primary').innerHTML = 'Translating...';

        translateText(originalTitle, targetLang).then(translatedTitle => {
            titleElem.innerText = translatedTitle;
        });

        translateText(originalContent, targetLang).then(translatedContent => {
            contentElem.innerText = translatedContent;
            document.querySelector('.btn-primary').innerHTML = originalButtonText;
        }).catch(() => {
            document.querySelector('.btn-primary').innerHTML = originalButtonText;
        });
    }

    async function translateText(text, targetLang) {
        try {
            const response = await fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=${targetLang}&dt=t&q=${encodeURIComponent(text)}`);
            const result = await response.json();
            return result[0].map(item => item[0]).join("");
        } catch (err) {
            alert("Translation failed. Please try again.");
            console.error(err);
            return text;
        }
    }

    // Like button animation
    document.addEventListener('DOMContentLoaded', function() {
        const likeButton = document.getElementById('likeButton');
        if (likeButton) {
            likeButton.addEventListener('click', function() {
                // Add a simple animation
                this.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            });
        }
    });
</script>
</body>
</html>