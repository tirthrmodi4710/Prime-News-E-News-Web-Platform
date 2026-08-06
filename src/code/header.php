<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="header-container">
        <div class="header-left">
            <a href="index.php">
                <img src="images/primenewslogo1.png" alt="Prime Report Logo">
            </a>
        </div>
        <div class="header-buttons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                // Fetch user's name from database
                $user_name = "User";
                if (isset($_SESSION['user_id'])) {
                    @include 'db_connect.php';
                    
                    if ($conn && !$conn->connect_error) {
                        $sql = "SELECT username FROM users WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        
                        if ($stmt) {
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result && $result->num_rows === 1) {
                                $user_data = $result->fetch_assoc();
                                
                                if (!empty($user_data['name'])) {
                                    $user_name = htmlspecialchars($user_data['name']);
                                } elseif (!empty($user_data['username'])) {
                                    $user_name = htmlspecialchars($user_data['username']);
                                } elseif (!empty($user_data['full_name'])) {
                                    $user_name = htmlspecialchars($user_data['full_name']);
                                } elseif (!empty($user_data['first_name'])) {
                                    $user_name = htmlspecialchars($user_data['first_name']);
                                }
                            }
                            $stmt->close();
                        }
                    }
                }
                ?>
                
            <?php endif; ?>
            
            <form class="search-form" method="GET" action="search.php" onsubmit="return validateSearch()">
    <input type="text" name="query" id="searchBox" placeholder="Search news...">
    <button type="submit">🔍 Search</button>
</form>

<script>
function validateSearch() {
    const query = document.getElementById("searchBox").value.trim();
    if (query === "") {
        // Stop redirect — stay on same page
        return false;
    }
    return true;
}
</script>

            
            <button onclick="toggleDarkMode()">🌙 Dark Mode</button>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="profile-dropdown">
                    <button class="profile-icon">👤 Hello, <?php echo $user_name; ?></button>
                    <div class="profile-content">
                        <a href="saved_news.php">Saved News</a>
                        <a href="user_logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="user_login.php" class="login-btn">Login</a>
                    <a href="user_register.php" class="register-btn">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<style>
header {
    background: linear-gradient(135deg, #1a365d 0%, #2a4d8e 100%);
    color: white;
    padding: 15px 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: 0 auto;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-left a {
    display: flex;
    align-items: center;
}

.header-left img {
    height: 50px;
    width: auto;
    padding: 2px;
}

.header-buttons {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-buttons a:not(.login-btn):not(.register-btn) {
    color: white;
    text-decoration: none;
    background: #0073e6;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(0,115,230,0.3);
}

.header-buttons a:not(.login-btn):not(.register-btn):hover {
    background: #005bb5;
    box-shadow: 0 3px 8px rgba(0,91,181,0.5);
    transform: translateY(-1px);
}

.search-form {
    display: flex;
    align-items: center;
}

.search-form input[type="text"] {
    padding: 10px 15px;
    width: 280px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px 0 0 6px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.search-form input[type="text"]::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.search-form input[type="text"]:focus {
    border-color: #0073e6;
    outline: none;
    box-shadow: 0 0 5px rgba(0,115,230,0.5);
    background: rgba(255, 255, 255, 0.15);
}

.search-form button {
    padding: 10px 18px;
    background: #0073e6;
    color: white;
    border: 1px solid #0073e6;
    cursor: pointer;
    border-radius: 0 6px 6px 0;
    font-weight: 600;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.search-form button:hover {
    background: #005bb5;
    border-color: #005bb5;
}

button[onclick="toggleDarkMode()"] {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

button[onclick="toggleDarkMode()"]:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
}

.auth-buttons {
    display: flex;
    align-items: center;
    gap: 12px;
}

.login-btn {
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.login-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-1px);
}

.register-btn {
    color: white;
    text-decoration: none;
    background: linear-gradient(135deg, #0073e6, #005bb5);
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 115, 230, 0.3);
}

.register-btn:hover {
    background: linear-gradient(135deg, #005bb5, #004080);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0, 115, 230, 0.4);
}

.profile-dropdown {
    position: relative;
    display: inline-block;
}

.profile-icon {
    background: #005bb5;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,91,181,0.4);
    display: flex;
    align-items: center;
    gap: 8px;
}

.profile-dropdown:hover .profile-icon {
    background: #004080;
    transform: translateY(-1px);
}

.profile-content {
    display: none;
    position: absolute;
    right: 0;
    background: #fff;
    min-width: 200px;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    z-index: 1000;
    padding: 8px 0;
    margin-top: 8px;
}

.profile-content a {
    color: #004080;
    padding: 12px 20px;
    text-decoration: none;
    display: block;
    font-weight: 600;
    font-size: 14px;
    transition: background-color 0.25s ease;
}

.profile-content a:hover {
    background-color: #f0f8ff;
}

.profile-dropdown:hover .profile-content {
    display: block;
}

/* Dark Mode Header Styles */
body.dark header {
    background: linear-gradient(135deg, #1a365d 0%, #2a4d8e 100%);
}

body.dark .header-buttons a:not(.login-btn):not(.register-btn),
body.dark .profile-icon {
    background: #444;
    color: #eee;
    box-shadow: none;
}

body.dark .header-buttons a:not(.login-btn):not(.register-btn):hover,
body.dark .profile-dropdown:hover .profile-icon {
    background: #666;
}

body.dark .search-form input[type="text"] {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    color: white;
}

body.dark button[onclick="toggleDarkMode()"] {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
}

body.dark .login-btn {
    border-color: rgba(255, 255, 255, 0.3);
}

body.dark .login-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        gap: 15px;
    }
    
    .header-buttons {
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }
    
    .search-form input[type="text"] {
        width: 200px;
    }
    
    .auth-buttons {
        gap: 8px;
    }
}
</style>

<script>
function toggleDarkMode() {
    document.body.classList.toggle("dark");
    localStorage.setItem("darkMode", document.body.classList.contains("dark"));
}

document.addEventListener("DOMContentLoaded", function() {
    const isDark = localStorage.getItem("darkMode") === "true";
    if (isDark) {
        document.body.classList.add("dark");
    }
});
</script>