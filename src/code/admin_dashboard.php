<?php
include 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Prime Report</title>
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
        
        /* Header animation */
        .dashboard-header {
            text-align: center;
            margin-bottom: 50px;
            opacity: 0;
            transform: translateY(-20px);
            animation: fadeInDown 0.8s forwards ease-out;
        }
        
        @keyframes fadeInDown {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dashboard-header h2 {
            font-size: 32px;
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
            width: 0;
            height: 4px;
            background: linear-gradient(90deg, #1a365d, #2a4d8e);
            animation: expandLine 1s 0.5s forwards ease-out;
            border-radius: 2px;
        }
        
        @keyframes expandLine {
            to {
                width: 100px;
            }
        }
        
        .dashboard-header p {
            color: #718096;
            font-size: 16px;
            margin-top: 20px;
        }
        
        .admin-welcome {
            font-weight: 600;
            color: #2a4d8e;
            background: rgba(42, 77, 142, 0.1);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 15px;
        }
        
        /* Dashboard container */
        .dashboard {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
            transform: scale(0.95);
            opacity: 0;
            animation: zoomIn 0.6s 0.3s forwards ease-out;
        }
        
        @keyframes zoomIn {
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        /* Action cards */
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .action {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: linear-gradient(135deg, #1a365d 0%, #2a4d8e 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.4s ease;
            box-shadow: 0 6px 12px rgba(26, 54, 93, 0.2);
            opacity: 0;
            transform: translateY(20px);
            animation: cardAppear 0.6s forwards ease-out;
        }
        
        .action:nth-child(1) { animation-delay: 0.4s; }
        .action:nth-child(2) { animation-delay: 0.5s; }
        .action:nth-child(3) { animation-delay: 0.6s; }
        .action:nth-child(4) { animation-delay: 0.7s; }
        .action:nth-child(5) { animation-delay: 0.8s; }
        .action:nth-child(6) { animation-delay: 0.9s; }
        
        @keyframes cardAppear {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .action:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 12px 20px rgba(26, 54, 93, 0.3);
        }
        
        .action i {
            font-size: 36px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.15);
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .action:hover i {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.25);
        }
        
        .action span {
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
        }
        
        /* Logout button specific styling */
        .action:last-child {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            box-shadow: 0 6px 12px rgba(229, 62, 62, 0.2);
        }
        
        .action:last-child:hover {
            box-shadow: 0 12px 20px rgba(229, 62, 62, 0.3);
        }
        
        /* Footer */
        footer {
            text-align: center;
            margin-top: 60px;
            color: #718096;
            font-size: 14px;
            opacity: 0;
            animation: fadeIn 0.8s 1s forwards ease-out;
        }
        
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .dashboard {
                padding: 30px 20px;
            }
            
            .actions {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 20px;
            }
            
            .action {
                padding: 25px 15px;
            }
            
            .action i {
                font-size: 30px;
                width: 60px;
                height: 60px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 20px 15px;
            }
            
            .dashboard-header h2 {
                font-size: 26px;
            }
            
            .actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard">
        <div class="dashboard-header">
            <h2>Admin Dashboard</h2>
            <p>Manage your news platform efficiently</p>
            <div class="admin-welcome">Welcome, <?= htmlspecialchars($admin_username) ?></div>
        </div>

        <div class="actions">
            <a class="action" href="admin_manage_categories.php">
                <i class="fas fa-folder"></i>
                <span>Manage Categories</span>
            </a>

            <a class="action" href="admin_manage_news.php">
                <i class="fas fa-newspaper"></i>
                <span>Manage News</span>
            </a>

            <a class="action" href="admin_view_received_news.php">
                <i class="fas fa-envelope-open-text"></i>
                <span>View Received News</span>
            </a>

            <a class="action" href="admin_manage_other_news.php">
                <i class="fas fa-globe"></i>
                <span>Manage Other News</span>
            </a>

            <a class="action" href="news_likes.php">
                <i class="fas fa-heart"></i>
                <span>Liked News</span>
            </a>

            <a class="action" href="admin_login.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>

        <footer>&copy; 2025 Prime Report | Admin Panel</footer>
    </div>

    <script>
        // Add subtle hover effect for action cards
        document.addEventListener('DOMContentLoaded', function() {
            const actions = document.querySelectorAll('.action');
            
            actions.forEach(action => {
                action.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.03)';
                });
                
                action.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>