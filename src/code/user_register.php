<?php
include 'db_connect.php';
include 'send_otp.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password validation regex
    $password_pattern = "/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/";

    // Check if user already exists
    $check_sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        $message = "User already registered with this email.";
    } elseif (!preg_match($password_pattern, $password)) {
        $message = "Password must be at least 6 characters, include one uppercase letter, one number, and one special character.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Save details in session
        $_SESSION['pending_username'] = $username;
        $_SESSION['pending_email'] = $email;
        $_SESSION['pending_password'] = password_hash($password, PASSWORD_DEFAULT);

        // Generate and send OTP
        $otp = rand(100000, 999999);
        $_SESSION['pending_otp'] = $otp;

        if (sendOTP($email, $otp)) {
            header("Location: verify_otp.php");
            exit();
        } else {
            $message = "Failed to send OTP.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Register - Prime Report</title>
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
  }

  .register-wrapper {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px 50px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    width: 100%;
    max-width: 450px;
    text-align: center;
  }

  .register-wrapper h2 {
    margin-bottom: 25px;
    font-weight: 600;
    color: #1e3c72;
  }

  .register-wrapper form {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .register-wrapper input[type="text"],
  .register-wrapper input[type="email"],
  .register-wrapper input[type="password"] {
    padding: 14px 15px;
    font-size: 16px;
    border: 1.5px solid #ddd;
    border-radius: 8px;
    transition: border-color 0.3s ease;
  }

  .register-wrapper input[type="text"]:focus,
  .register-wrapper input[type="email"]:focus,
  .register-wrapper input[type="password"]:focus {
    outline: none;
    border-color: #1e3c72;
    box-shadow: 0 0 8px rgba(30, 60, 114, 0.4);
  }

  .register-wrapper button {
    padding: 14px;
    background-color: #1e3c72;
    color: white;
    border: none;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .register-wrapper button:hover {
    background-color: #16305b;
  }

  .register-wrapper .message {
    color: #e74c3c;
    margin-bottom: 15px;
    font-weight: 500;
  }

  .register-wrapper .login-link {
    margin-top: 20px;
    font-size: 14px;
  }

  .register-wrapper .login-link a {
    color: #1e3c72;
    text-decoration: none;
    font-weight: 600;
  }

  .register-wrapper .login-link a:hover {
    text-decoration: underline;
  }

  .password-container {
    position: relative;
    width: 100%;
  }

  .password-container input {
    width: 100%;
    padding-right: 45px;
  }

  .toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;
  }

  .toggle-password:active {
    transform: translateY(-50%) scale(0.95);
  }

  @media (max-width: 480px) {
    .register-wrapper {
      padding: 30px 25px;
    }
  }
</style>
</head>
<body>

<div class="register-wrapper">
  <h2>Create Your Prime Report Account</h2>

  <?php if (!empty($message)): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="post" autocomplete="off" novalidate>
    <input type="text" name="username" placeholder="Username" required />
    <input type="email" name="email" placeholder="Email Address" required />
    
    <div class="password-container">
      <input type="password" name="password" id="password" placeholder="Password" required />
      <button type="button" class="toggle-password" onmousedown="showPassword('password')" onmouseup="hidePassword('password')" onmouseleave="hidePassword('password')" ontouchstart="showPassword('password')" ontouchend="hidePassword('password')">👁️</button>
    </div>
    
    <div class="password-container">
      <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required />
      <button type="button" class="toggle-password" onmousedown="showPassword('confirm_password')" onmouseup="hidePassword('confirm_password')" onmouseleave="hidePassword('confirm_password')" ontouchstart="showPassword('confirm_password')" ontouchend="hidePassword('confirm_password')">👁️</button>
    </div>
    
    <button type="submit">Register</button>
  </form>

  <div class="login-link">
    Already have an account? <a href="user_login.php">Log in here</a>
  </div>
</div>

<script>
function showPassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    passwordField.type = 'text';
}

function hidePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    passwordField.type = 'password';
}
</script>

</body>
</html>