<?php
include 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize error variable
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if ($password === $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Prime Report</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a365d 0%, #2a4d8e 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }
        
        /* Animated background elements */
        .bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            animation: float 15s infinite linear;
        }
        
        .bg-circle:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -150px;
            animation-delay: 0s;
            animation-duration: 25s;
        }
        
        .bg-circle:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -100px;
            right: 20%;
            animation-delay: -5s;
            animation-duration: 20s;
        }
        
        .bg-circle:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 20%;
            right: -75px;
            animation-delay: -8s;
            animation-duration: 18s;
        }
        
        .bg-circle:nth-child(4) {
            width: 100px;
            height: 100px;
            bottom: 30%;
            left: -50px;
            animation-delay: -12s;
            animation-duration: 15s;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
            100% {
                transform: translateY(0) rotate(360deg);
            }
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.6s forwards ease-out;
        }
        
        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            font-weight: 700;
            color: #1a365d;
            font-size: 28px;
            margin-bottom: 8px;
            position: relative;
            display: inline-block;
        }
        
        .login-header h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #1a365d, #2a4d8e);
            animation: expandLine 0.8s 0.5s forwards ease-out;
        }
        
        @keyframes expandLine {
            to {
                width: 70px;
            }
        }
        
        .login-header p {
            color: #718096;
            font-size: 14px;
            margin-top: 15px;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            position: relative;
            opacity: 0;
            transform: translateX(-20px);
            animation: slideInRight 0.5s forwards ease-out;
        }
        
        .form-group:nth-child(1) {
            animation-delay: 0.3s;
        }
        
        .form-group:nth-child(2) {
            animation-delay: 0.4s;
        }
        
        @keyframes slideInRight {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .form-group input {
            width: 100%;
            padding: 16px 20px;
            font-size: 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #f7fafc;
            color: #2d3748;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2a4d8e;
            box-shadow: 0 0 0 3px rgba(42, 77, 142, 0.15);
            background: #fff;
            transform: translateY(-2px);
        }
        
        .form-group input::placeholder {
            color: #a0aec0;
        }
        
        .login-button {
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
            opacity: 0;
            animation: fadeIn 0.5s 0.6s forwards ease-out;
        }
        
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(26, 54, 93, 0.3);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .error-message {
            color: #e53e3c;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            padding: 12px;
            background: rgba(229, 62, 62, 0.1);
            border-radius: 8px;
            margin-bottom: 20px;
            opacity: 0;
            animation: shake 0.5s forwards ease-out, fadeIn 0.5s forwards;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        
        .admin-badge {
            display: flex;
            justify-content: center;
            margin-top: 25px;
            opacity: 0;
            animation: fadeIn 0.5s 0.8s forwards ease-out;
        }
        
        .admin-badge span {
            background: #edf2f7;
            color: #4a5568;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .admin-badge span::before {
            content: "🔒";
            font-size: 10px;
        }
        
        @media (max-width: 480px) {
            .login-box {
                padding: 30px 25px;
            }
            
            .login-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>
    
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h2>Admin Login</h2>
                <p>Access the Prime Report administration panel</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="post" class="login-form">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required autocomplete="off">
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="login-button">Login</button>
            </form>
            
            <div class="admin-badge">
                <span>Administrator Access Only</span>
            </div>
        </div>
    </div>

    <script>
        // Add subtle input animation on focus
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-5px)';
                    this.parentElement.style.transition = 'transform 0.3s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
            
            // Form submission animation
            const form = document.querySelector('.login-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const button = this.querySelector('.login-button');
                    button.textContent = 'Authenticating...';
                    button.style.opacity = '0.9';
                });
            }
        });
    </script>
</body>
</html>