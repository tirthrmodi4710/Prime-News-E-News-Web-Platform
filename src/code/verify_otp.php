<?php
include 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp = $_POST['otp'];

    if (isset($_SESSION['pending_otp']) && $user_otp == $_SESSION['pending_otp']) {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_verified) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $_SESSION['pending_username'], $_SESSION['pending_email'], $_SESSION['pending_password']);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['user_username'] = $_SESSION['pending_username'];
            $_SESSION['user_email'] = $_SESSION['pending_email'];

            // Clear temp registration session variables
            unset($_SESSION['pending_username'], $_SESSION['pending_email'], $_SESSION['pending_password'], $_SESSION['pending_otp']);

            header("Location: index.php");
            exit();
        } else {
            $message = "Error inserting user. Email may already be used.";
        }
    } else {
        $message = "Incorrect OTP. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
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

        .otp-container {
            background: #fff;
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0 12px 25px rgba(30, 60, 114, 0.3);
            width: 320px;
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #1e3c72;
            font-weight: 600;
            font-size: 1.8rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 14px 15px;
            font-size: 16px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #1e3c72;
            box-shadow: 0 0 8px rgba(30, 60, 114, 0.4);
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #1e3c72;
            color: white;
            border: none;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #16305b;
        }

        .message {
            color: #e74c3c;
            margin-bottom: 15px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="otp-container">
    <h2>Verify OTP</h2>
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
    </form>
</div>

</body>
</html>
