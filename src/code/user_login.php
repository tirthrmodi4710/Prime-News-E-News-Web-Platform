<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $email;
            
            // Redirect to index.php
            header("Location: index.php");
            exit();
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        $message = "Invalid email or password.";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login - Prime Report</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

  * {
    box-sizing: border-box;
  }

  body, html {
    height: 100%;
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    background: url('images/primenews_user_bg.png') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #333;
    overflow: hidden;
  }

  .login-wrapper {
    background: rgba(255, 255, 255, 0.85); /* More transparent */
    backdrop-filter: blur(5px); /* Added blur effect for better readability */
    padding: 40px 50px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    width: 100%;
    max-width: 400px;
    text-align: center;
    transform: translateY(20px);
    opacity: 0;
    animation: slideUp 0.6s forwards ease-out;
    border: 1px solid rgba(255, 255, 255, 0.3); /* Subtle border */
  }

  @keyframes slideUp {
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .login-wrapper h2 {
    margin-bottom: 25px;
    font-weight: 600;
    color: #1e3c72;
    position: relative;
    display: inline-block;
  }

  .login-wrapper h2::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 0;
    height: 3px;
    background: #1e3c72;
    animation: expandLine 1s 0.5s forwards ease-out;
  }

  @keyframes expandLine {
    to {
      width: 100%;
    }
  }

  .login-wrapper form {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .login-wrapper input[type="email"],
  .login-wrapper input[type="password"] {
    padding: 14px 15px;
    font-size: 16px;
    border: 1.5px solid rgba(221, 221, 221, 0.7);
    border-radius: 8px;
    transition: all 0.3s ease;
    transform: scale(0.95);
    opacity: 0;
    animation: inputReveal 0.5s forwards ease-out;
    background: rgba(255, 255, 255, 0.9);
  }

  .login-wrapper input[type="email"] {
    animation-delay: 0.3s;
  }

  .login-wrapper input[type="password"] {
    animation-delay: 0.4s;
  }

  @keyframes inputReveal {
    to {
      transform: scale(1);
      opacity: 1;
    }
  }

  .login-wrapper input[type="email"]:focus,
  .login-wrapper input[type="password"]:focus {
    outline: none;
    border-color: #1e3c72;
    box-shadow: 0 0 8px rgba(30, 60, 114, 0.4);
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 1);
  }

  .login-wrapper button {
    padding: 14px;
    background-color: #1e3c72;
    color: white;
    border: none;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    transform: translateY(10px);
    opacity: 0;
    animation: buttonReveal 0.5s 0.6s forwards ease-out;
  }

  @keyframes buttonReveal {
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .login-wrapper button:hover {
    background-color: #16305b;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
  }

  .login-wrapper button:active {
    transform: translateY(0);
  }

  .login-wrapper .message {
    color: #e74c3c;
    margin-bottom: 15px;
    font-weight: 500;
    opacity: 0;
    transform: translateY(-10px);
    animation: messageAppear 0.5s forwards ease-out;
  }

  @keyframes messageAppear {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .login-wrapper .register-link {
    margin-top: 20px;
    font-size: 14px;
    opacity: 0;
    animation: fadeIn 0.5s 0.8s forwards ease-out;
  }

  @keyframes fadeIn {
    to {
      opacity: 1;
    }
  }

  .login-wrapper .register-link a {
    color: #1e3c72;
    text-decoration: none;
    font-weight: 600;
    position: relative;
  }

  .login-wrapper .register-link a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 1.5px;
    background: #1e3c72;
    transition: width 0.3s ease;
  }

  .login-wrapper .register-link a:hover::after {
    width: 100%;
  }

  /* Success checkmark animation */
  .success-checkmark {
    display: none;
    width: 80px;
    height: 80px;
    margin: 0 auto;
    position: relative;
  }

  .check-icon {
    width: 80px;
    height: 80px;
    position: relative;
    border-radius: 50%;
    box-sizing: content-box;
    border: 4px solid #4CAF50;
  }

  .check-icon::after {
    content: '';
    position: absolute;
    transform: rotate(45deg) scale(0);
    animation: icon-line-tip 0.5s forwards, icon-line-long 0.5s 0.5s forwards;
    left: 14px;
    top: 38px;
    height: 25px;
    width: 12px;
    border-bottom: 4px solid #4CAF50;
    border-right: 4px solid #4CAF50;
  }

  @keyframes icon-line-tip {
    0% { width: 0; height: 0; opacity: 1; }
    20% { width: 12px; height: 0; opacity: 1; }
    100% { width: 12px; height: 25px; opacity: 1; transform: rotate(45deg) scale(1); }
  }

  @keyframes icon-line-long {
    0% { width: 0; height: 0; opacity: 1; }
    20% { width: 0; height: 0; opacity: 1; }
    100% { width: 12px; height: 25px; opacity: 1; transform: rotate(45deg) scale(1); }
  }

  @media (max-width: 450px) {
    .login-wrapper {
      padding: 30px 25px;
      margin: 0 15px;
    }
  }
</style>
</head>
<body>

<div class="login-wrapper">
  <h2>Welcome Back to Prime Report</h2>

  <?php if (!empty($message)): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="post" autocomplete="off" novalidate id="loginForm">
    <input type="email" name="email" placeholder="Email Address" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Log In</button>
  </form>

  <div class="register-link">
    Don't have an account? <a href="user_register.php">Register here</a>
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
    const form = document.getElementById('loginForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        // Only animate if there are no validation errors
        if (this.checkValidity()) {
          e.preventDefault();

          // Simulate loading animation
          const button = this.querySelector('button');
          const originalText = button.textContent;
          button.textContent = 'Logging in...';
          button.style.opacity = '0.8';
          button.disabled = true;

          // Simulate API call with timeout
          setTimeout(() => {
            this.submit();
          }, 1500);
        }
      });
    }
  });
</script>

</body>
</html>