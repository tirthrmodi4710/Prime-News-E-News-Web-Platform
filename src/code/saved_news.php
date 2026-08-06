<?php
include 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle saving news
if (isset($_POST['news_id']) && isset($_POST['save'])) {
    $news_id = intval($_POST['news_id']);

    // Check if already saved
    $check = $conn->prepare("SELECT id FROM saved_news WHERE user_id = ? AND news_id = ?");
    $check->bind_param("ii", $user_id, $news_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO saved_news (user_id, news_id, saved_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $user_id, $news_id);
        if ($stmt->execute()) {
            $message = "✅ News saved successfully.";
        } else {
            $message = "❌ Failed to save news.";
        }
    } else {
        $message = "⚠️ News already saved.";
    }
}

// Handle remove
if (isset($_POST['remove_id'])) {
    $news_id = intval($_POST['remove_id']);
    $del = $conn->prepare("DELETE FROM saved_news WHERE user_id = ? AND news_id = ?");
    $del->bind_param("ii", $user_id, $news_id);
    if ($del->execute()) {
        $message = "🗑 News removed successfully.";
    } else {
        $message = "❌ Failed to remove news.";
    }
}

$sql = "SELECT n.id, n.title, n.image FROM news n
        JOIN saved_news s ON n.id = s.news_id
        WHERE s.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$saved = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Saved News - Prime Report</title>
    <style>
        body { font-family: sans-serif; background: #f2f2f2; padding: 20px; }
        .card { background: #fff; padding: 15px; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 0 6px rgba(0,0,0,0.1); }
        img { max-width: 100px; vertical-align: middle; border-radius: 5px; }
        h2 { color: #007BFF; }
        form button { background: #dc3545; color: white; padding: 8px 12px; border: none; border-radius: 5px; }
    </style>
</head>
<body>
<h2>Your Saved News</h2>
<?php if ($message): ?>
    <p style="color:green"><?= $message ?></p>
<?php endif; ?>
<?php while($row = $saved->fetch_assoc()): ?>
    <div class="card">
        <img src="<?= $row['image'] ?>" alt="">
        <strong><?= $row['title'] ?></strong>
        <br><a href="news.php?id=<?= $row['id'] ?>">📖 Read More</a>
        <form method="post" style="display:inline;">
            <input type="hidden" name="remove_id" value="<?= $row['id'] ?>">
            <button type="submit">❌ Remove</button>
        </form>
    </div>
<?php endwhile; ?>
<p><a href="index.php">⬅️ Back to Home</a></p>
</body>
</html>
