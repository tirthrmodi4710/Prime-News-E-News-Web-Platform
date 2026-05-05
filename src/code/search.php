<?php
include 'db_connect.php';

$query = '';
$news = [];

if (isset($_GET['query'])) {
    $query = trim($conn->real_escape_string($_GET['query']));

    if ($query !== '') {
        $sql = "SELECT * FROM news WHERE title LIKE '%$query%' OR description LIKE '%$query%' ORDER BY id DESC";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $news[] = $row;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="UTF-8">
    <title>Search Results - Prime Report</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f4f4f4; 
            padding: 20px; 
        }
        .search-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-bar input[type="text"] {
            width: 300px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .search-bar button {
            padding: 10px 20px;
            font-size: 16px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background: #0056b3;
        }
        h2 { color: #007BFF; text-align: center; }
        .news-item { 
            background: white; 
            padding: 15px; 
            margin: 15px 0; 
            border-radius: 8px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
            display: flex; 
            gap: 15px; 
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            color: inherit;
        }
        .news-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .news-item img { 
            width: 200px; 
            height: 120px; 
            object-fit: cover; 
            border-radius: 6px; 
            flex-shrink: 0;
        }
        .news-content { flex: 1; }
        .news-content h3 { margin: 0 0 10px 0; color: #333; }
        .news-content p { margin: 0; color: #666; line-height: 1.5; }
        .no-results {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-link:hover { background: #0056b3; }
    </style>
</head>
<body>

    <!-- 🔍 Search Bar -->
    <form method="GET" action="search.php" class="search-bar" id="searchForm">
        <input type="text" name="query" placeholder="Search news..." value="<?= htmlspecialchars($query) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($query !== '' && empty($news)): ?>
        <div class="no-results">
            <p>No news found for "<?= htmlspecialchars($query) ?>"</p>
        </div>
    <?php elseif (!empty($news)): ?>
        <h2>Search Results for "<?= htmlspecialchars($query) ?>"</h2>
        <?php foreach ($news as $item): ?>
            <?php 
                // 🖼 Handle image path dynamically
                $imagePath = trim($item['image']); 
                if (empty($imagePath)) {
                    $imagePath = 'images/default-news.jpg';
                } elseif (!preg_match('/^(images|uploads)\//', $imagePath)) {
                    $imagePath = 'uploads/' . $imagePath;
                }
            ?>
            <a href="news.php?id=<?= $item['id'] ?>" class="news-item">
                <img src="<?= htmlspecialchars($imagePath) ?>" alt="News Image" 
                     onerror="this.src='images/default-news.jpg'">
                <div class="news-content">
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= htmlspecialchars(substr($item['description'], 0, 200)) ?>...</p>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="index.php" class="back-link">Go back to home</a>

    <script>
        // 🧠 Prevent form submission if query is empty
        document.getElementById("searchForm").addEventListener("submit", function(e) {
            const query = this.query.value.trim();
            if (query === "") {
                e.preventDefault(); // Stop the form from submitting
            }
        });
    </script>

</body>
</html>
