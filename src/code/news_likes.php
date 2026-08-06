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

// Fetch news likes statistics
$likes_query = "
    SELECT 
        n.id,
        n.title,
        n.category_id,
        c.name as category_name,
        COUNT(nl.id) as like_count,
        n.created_at
    FROM news n
    LEFT JOIN news_likes nl ON n.id = nl.news_id
    LEFT JOIN categories c ON n.category_id = c.id
    GROUP BY n.id
    ORDER BY like_count DESC, n.created_at DESC
";

$result = $conn->query($likes_query);
$news_likes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $news_likes[] = $row;
    }
}

// Get total likes count
$total_likes_result = $conn->query("SELECT COUNT(*) as total FROM news_likes");
$total_likes = $total_likes_result->fetch_assoc()['total'];

// Get most liked news
$most_liked_result = $conn->query("
    SELECT n.title, COUNT(nl.id) as likes 
    FROM news n 
    JOIN news_likes nl ON n.id = nl.news_id 
    GROUP BY n.id 
    ORDER BY likes DESC 
    LIMIT 1
");
$most_liked = $most_liked_result->num_rows > 0 ? $most_liked_result->fetch_assoc() : null;

// Get category-wise likes for pie chart
$category_likes_query = "
    SELECT 
        c.name as category_name,
        COUNT(nl.id) as like_count
    FROM categories c
    LEFT JOIN news n ON c.id = n.category_id
    LEFT JOIN news_likes nl ON n.id = nl.news_id
    GROUP BY c.id, c.name
    HAVING like_count > 0
    ORDER BY like_count DESC
";
$category_likes_result = $conn->query($category_likes_query);
$category_likes = [];
while ($row = $category_likes_result->fetch_assoc()) {
    $category_likes[] = $row;
}

// Get top 10 most liked news for bar chart
$top_news_likes = array_slice($news_likes, 0, 10);

// Handle PDF Report Generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report'])) {
    // Include TCPDF library
    require_once('tcpdf/tcpdf.php');
    
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Prime Report');
    $pdf->SetAuthor('Prime Report Admin');
    $pdf->SetTitle('News Likes Analysis Report');
    $pdf->SetSubject('News Engagement Analysis');
    
    // Set default header data
    $pdf->SetHeaderData('', 0, 'News Likes Analysis Report', 'Generated on ' . date('F j, Y g:i A'));
    
    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    
    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    
    // Set margins
    $pdf->SetMargins(15, 25, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font for the title
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor(44, 90, 160);
    $pdf->Cell(0, 15, 'NEWS LIKES ANALYSIS REPORT', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Statistics Section
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, 'Summary Statistics', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    
    // Create statistics table
    $stats_html = '
    <table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%; margin: 10px 0;">
        <tr style="background-color: #f8f9fa;">
            <td width="33%" style="text-align: center; border: 1px solid #ddd;">
                <strong style="font-size: 18px; color: #e74c3c;">' . count($news_likes) . '</strong><br/>
                <span style="color: #666;">Total News Articles</span>
            </td>
            <td width="33%" style="text-align: center; border: 1px solid #ddd;">
                <strong style="font-size: 18px; color: #3498db;">' . $total_likes . '</strong><br/>
                <span style="color: #666;">Total Likes Received</span>
            </td>
            <td width="34%" style="text-align: center; border: 1px solid #ddd;">
                <strong style="font-size: 18px; color: #2ecc71;">' . ($most_liked ? $most_liked['likes'] : '0') . '</strong><br/>
                <span style="color: #666;">Most Liked News</span>
            </td>
        </tr>
    </table>';
    
    $pdf->writeHTML($stats_html, true, false, true, false, '');
    $pdf->Ln(10);
    
    // Performance Analysis
    if (!empty($news_likes)) {
        $articles_with_likes = array_filter($news_likes, function($news) { return $news['like_count'] > 0; });
        $engagement_rate = count($news_likes) > 0 ? ($total_likes / count($news_likes)) * 100 : 0;
        
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Performance Analysis', 0, 1);
        $pdf->SetFont('helvetica', '', 11);
        
        $analysis_html = '
        <table border="0" cellpadding="6" style="width: 100%; background-color: #f0f8ff;">
            <tr>
                <td width="50%"><strong>Articles with likes:</strong> ' . count($articles_with_likes) . ' / ' . count($news_likes) . ' (' . number_format((count($articles_with_likes) / count($news_likes)) * 100, 1) . '%)</td>
                <td width="50%"><strong>Average likes per article:</strong> ' . number_format($total_likes / count($news_likes), 2) . '</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Engagement rate:</strong> ' . number_format($engagement_rate, 2) . '%</td>
            </tr>
        </table>';
        
        $pdf->writeHTML($analysis_html, true, false, true, false, '');
        $pdf->Ln(10);
        
        // Top 5 Most Liked Articles
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Top 5 Most Liked Articles:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        
        $top_articles = array_slice($news_likes, 0, 5);
        foreach ($top_articles as $index => $article) {
            $pdf->Cell(10, 8, ($index + 1) . '.', 0, 0);
            $pdf->Cell(0, 8, substr($article['title'], 0, 80) . (strlen($article['title']) > 80 ? '...' : '') . ' - ' . $article['like_count'] . ' likes', 0, 1);
        }
        $pdf->Ln(10);
    }
    
    // Charts Section - Create visual representations
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Visual Analysis', 0, 1);
    
    // Bar Chart Representation - Top 10 Articles
    if (!empty($top_news_likes)) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Top 10 Most Liked Articles', 0, 1);
        $pdf->SetFont('helvetica', '', 9);
        
        $max_likes = max(array_column($top_news_likes, 'like_count'));
        
        foreach ($top_news_likes as $index => $news) {
            $percentage = $max_likes > 0 ? ($news['like_count'] / $max_likes) * 100 : 0;
            $bar_width = $percentage * 1.2;
            
            $pdf->Cell(50, 6, ($index + 1) . '. ' . substr($news['title'], 0, 25) . '...', 0, 0);
            $pdf->Cell(3, 6, '', 0, 0);
            
            // Create bar visualization
            $pdf->SetFillColor(54, 162, 235);
            $pdf->Cell($bar_width, 6, '', 'F', 0, '', true);
            $pdf->SetFillColor(255, 255, 255);
            
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->Cell(20, 6, $news['like_count'] . ' likes', 0, 1);
        }
        $pdf->Ln(8);
    }
    
    // Pie Chart Representation - Categories (Improved)
    if (!empty($category_likes)) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Likes Distribution by Category', 0, 1);
        $pdf->SetFont('helvetica', '', 9);
        
        $total_category_likes = array_sum(array_column($category_likes, 'like_count'));
        
        // Create a better visual representation
        $pdf->Ln(3);
        
        // Define colors for categories
        $colors = array(
            array(255, 99, 132),   // Red
            array(54, 162, 235),   // Blue
            array(255, 206, 86),   // Yellow
            array(75, 192, 192),   // Teal
            array(153, 102, 255),  // Purple
            array(255, 159, 64),   // Orange
            array(201, 203, 207),  // Gray
            array(255, 99, 132),   // Red (repeat if needed)
            array(54, 162, 235),   // Blue
            array(255, 206, 86)    // Yellow
        );
        
        // Create a visual pie chart representation
        $start_x = 20;
        $start_y = $pdf->GetY();
        $radius = 25;
        $center_x = $start_x + $radius;
        $center_y = $start_y + $radius;
        
        // Draw the pie chart
        $current_angle = 0;
        foreach ($category_likes as $index => $category) {
            $percentage = $total_category_likes > 0 ? ($category['like_count'] / $total_category_likes) * 100 : 0;
            $angle = ($percentage / 100) * 360;
            
            if ($angle > 0) {
                $pdf->SetFillColor($colors[$index % count($colors)][0], 
                                 $colors[$index % count($colors)][1], 
                                 $colors[$index % count($colors)][2]);
                
                // Draw pie slice
                $pdf->PieSector($center_x, $center_y, $radius, $current_angle, $current_angle + $angle, 'F', false, 0);
                
                $current_angle += $angle;
            }
        }
        
        // Add legend on the right side
        $legend_x = $center_x + $radius + 15;
        $legend_y = $start_y;
        
        foreach ($category_likes as $index => $category) {
            $percentage = $total_category_likes > 0 ? ($category['like_count'] / $total_category_likes) * 100 : 0;
            
            // Color box
            $pdf->SetFillColor($colors[$index % count($colors)][0], 
                             $colors[$index % count($colors)][1], 
                             $colors[$index % count($colors)][2]);
            $pdf->Rect($legend_x, $legend_y, 4, 4, 'F');
            
            // Category name and percentage
            $pdf->SetTextColor(0);
            $pdf->SetXY($legend_x + 6, $legend_y - 1);
            $pdf->Cell(40, 4, $category['category_name'], 0, 0, 'L');
            $pdf->Cell(20, 4, number_format($percentage, 1) . '%', 0, 0, 'R');
            $pdf->Cell(15, 4, '(' . $category['like_count'] . ')', 0, 1, 'R');
            
            $legend_y += 6;
        }
        
        $pdf->SetY($start_y + ($radius * 2) + 10);
        $pdf->Ln(5);
    }
    
    // Detailed Table
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Detailed Likes Analysis', 0, 1);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Table header
    $header = array('#', 'News Title', 'Category', 'Likes', 'Date');
    $pdf->SetFillColor(44, 90, 160);
    $pdf->SetTextColor(255);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->SetLineWidth(0.3);
    
    // Column widths
    $w = array(10, 90, 40, 20, 30);
    
    // Header
    for($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    }
    $pdf->Ln();
    
    // Data
    $pdf->SetFillColor(255);
    $pdf->SetTextColor(0);
    $pdf->SetFont('helvetica', '', 9);
    
    $fill = false;
    foreach ($news_likes as $index => $news) {
        $pdf->Cell($w[0], 6, $index + 1, 'LR', 0, 'C', $fill);
        $pdf->Cell($w[1], 6, substr($news['title'], 0, 50), 'LR', 0, 'L', $fill);
        $pdf->Cell($w[2], 6, $news['category_name'] ?? 'Uncategorized', 'LR', 0, 'C', $fill);
        $pdf->Cell($w[3], 6, $news['like_count'], 'LR', 0, 'C', $fill);
        $pdf->Cell($w[4], 6, date('M j, Y', strtotime($news['created_at'])), 'LR', 0, 'C', $fill);
        $pdf->Ln();
        $fill = !$fill;
    }
    
    // Closing line
    $pdf->Cell(array_sum($w), 0, '', 'T');
    
    // Footer note
    $pdf->SetY(-30);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(128);
    $pdf->Cell(0, 10, 'Generated by Prime Report Admin Panel - ' . date('F j, Y g:i A'), 0, 0, 'C');
    
    // Output PDF as download
    $pdf->Output('news_likes_report_' . date('Y-m-d') . '.pdf', 'D');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liked News - Prime Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            color: #2d3748;
            display: flex;
        }
        
        /* Side Panel Styles */
        .side-panel {
            width: 260px;
            background: linear-gradient(180deg, #1a365d 0%, #2a4d8e 100%);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 30px 0;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }
        
        .panel-header {
            text-align: center;
            padding: 0 20px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .panel-header h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .panel-header p {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.1);
            margin: 0 15px 20px;
            border-radius: 10px;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 18px;
        }
        
        .admin-details {
            flex: 1;
        }
        
        .admin-name {
            font-weight: 600;
            font-size: 15px;
        }
        
        .admin-role {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .nav-menu {
            list-style: none;
            padding: 0 15px;
        }
        
        .nav-item {
            margin-bottom: 8px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }
        
        .nav-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 18px;
            text-align: center;
        }
        
        .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 20px 15px;
        }
        
        /* Main Content Area */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 40px 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Header Styles */
        .page-header {
            text-align: center;
            margin-bottom: 40px;
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
        
        .page-header h1 {
            font-size: 36px;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }
        
        .page-header h1::after {
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
        
        .page-header p {
            color: #718096;
            font-size: 16px;
            margin-top: 20px;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
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
            text-decoration: none;
            font-size: 14px;
        }
        
        .btn-report {
            background: linear-gradient(135deg, #805ad5 0%, #6b46c1 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(107, 70, 193, 0.3);
        }
        
        .btn-report:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(107, 70, 193, 0.4);
        }
        
        .btn-dashboard {
            background: #e2e8f0;
            color: #2d3748;
        }
        
        .btn-dashboard:hover {
            background: #cbd5e0;
            transform: translateY(-2px);
        }
        
        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: cardAppear 0.6s forwards ease-out;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.3s; }
        .stat-card:nth-child(2) { animation-delay: 0.4s; }
        .stat-card:nth-child(3) { animation-delay: 0.5s; }
        
        @keyframes cardAppear {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        
        .stat-card:nth-child(1) .stat-icon {
            background: linear-gradient(135deg, #e53e3e, #fc8181);
            color: white;
        }
        
        .stat-card:nth-child(2) .stat-icon {
            background: linear-gradient(135deg, #3182ce, #63b3ed);
            color: white;
        }
        
        .stat-card:nth-child(3) .stat-icon {
            background: linear-gradient(135deg, #38a169, #68d391);
            color: white;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #718096;
            font-size: 16px;
            font-weight: 500;
        }
        
        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .chart-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            opacity: 0;
            transform: translateY(20px);
            animation: cardAppear 0.6s 0.7s forwards ease-out;
        }
        
        .chart-title {
            text-align: center;
            color: #1a365d;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .chart-wrapper {
            position: relative;
            height: 300px;
        }
        
        /* Table Styles */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeIn 0.8s 0.9s forwards ease-out;
        }
        
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        th {
            background: linear-gradient(135deg, #1a365d 0%, #2a4d8e 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        tr {
            transition: all 0.3s ease;
        }
        
        tr:hover {
            background: rgba(26, 54, 93, 0.03);
        }
        
        .like-count {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(229, 62, 62, 0.1);
            color: #e53e3e;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .like-count.high {
            background: rgba(229, 62, 62, 0.15);
            color: #c53030;
        }
        
        .like-count.medium {
            background: rgba(237, 137, 54, 0.15);
            color: #dd6b20;
        }
        
        .like-count.low {
            background: rgba(101, 163, 13, 0.15);
            color: #65a30d;
        }
        
        .category-badge {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(66, 153, 225, 0.1);
            color: #2a4d8e;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .no-likes {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }
        
        .no-likes i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
            background: #1a365d;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .side-panel {
                transform: translateX(-100%);
            }
            
            .side-panel.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 80px 20px 40px;
            }
            
            .mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .page-header h1 {
                font-size: 28px;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 200px;
                justify-content: center;
            }
            
            .charts-section {
                grid-template-columns: 1fr;
            }
            
            .chart-wrapper {
                height: 250px;
            }
            
            th, td {
                padding: 12px 15px;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 80px 15px 20px;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .chart-container {
                padding: 15px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Side Panel -->
    <div class="side-panel" id="sidePanel">
        <div class="panel-header">
            <h3>Prime Report</h3>
            <p>Admin Panel</p>
        </div>
        
        <div class="admin-info">
            <div class="admin-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="admin-details">
                <div class="admin-name"><?= htmlspecialchars($admin_username) ?></div>
                <div class="admin-role">Administrator</div>
            </div>
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="admin_dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_manage_categories.php" class="nav-link">
                    <i class="fas fa-folder"></i>
                    <span>Manage Categories</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_manage_news.php" class="nav-link">
                    <i class="fas fa-newspaper"></i>
                    <span>Manage News</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_manage_other_news.php" class="nav-link">
                    <i class="fas fa-globe"></i>
                    <span>Manage Other News</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_view_received_news.php" class="nav-link">
                    <i class="fas fa-envelope-open-text"></i>
                    <span>View Received News</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="news_likes.php" class="nav-link active">
                    <i class="fas fa-heart"></i>
                    <span>Liked News</span>
                </a>
            </li>
        </ul>
        
        <div class="nav-divider"></div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="admin_login.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>❤️ Liked News Analytics</h1>
                <p>Track which news articles are getting the most engagement from users</p>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <form method="post" action="" style="display: inline;">
                    <button type="submit" name="generate_report" class="btn btn-report">
                        <i class="fas fa-file-pdf"></i>
                        Generate PDF Report
                    </button>
                </form>
                <a href="admin_dashboard.php" class="btn btn-dashboard">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-number"><?= count($news_likes) ?></div>
                    <div class="stat-label">Total News Articles</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-number"><?= $total_likes ?></div>
                    <div class="stat-label">Total Likes Received</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-number">
                        <?= $most_liked ? $most_liked['likes'] : '0' ?>
                    </div>
                    <div class="stat-label">
                        <?= $most_liked ? 'Most Liked News' : 'No Likes Yet' ?>
                    </div>
                    <?php if ($most_liked): ?>
                        <div style="margin-top: 10px; font-size: 12px; color: #718096;">
                            "<?= htmlspecialchars(substr($most_liked['title'], 0, 30)) ?><?= strlen($most_liked['title']) > 30 ? '...' : '' ?>"
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Charts Section -->
            <?php if (!empty($news_likes)): ?>
            <div class="charts-section">
                <!-- Bar Chart - Top 10 Most Liked Articles -->
                <div class="chart-container">
                    <div class="chart-title">Top 10 Most Liked Articles</div>
                    <div class="chart-wrapper">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <!-- Pie Chart - Category Distribution -->
                <div class="chart-container">
                    <div class="chart-title">Likes Distribution by Category</div>
                    <div class="chart-wrapper">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- News Likes Table -->
            <div class="table-container">
                <?php if (!empty($news_likes)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>News Title</th>
                                <th>Category</th>
                                <th>Likes Count</th>
                                <th>Posted Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($news_likes as $index => $news): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($news['title']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="category-badge">
                                            <?= htmlspecialchars($news['category_name'] ?? 'Uncategorized') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $like_count = $news['like_count'];
                                        $like_class = '';
                                        if ($like_count >= 10) {
                                            $like_class = 'high';
                                        } elseif ($like_count >= 5) {
                                            $like_class = 'medium';
                                        } else {
                                            $like_class = 'low';
                                        }
                                        ?>
                                        <span class="like-count <?= $like_class ?>">
                                            <i class="fas fa-heart"></i>
                                            <?= $like_count ?> likes
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($news['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-likes">
                        <i class="fas fa-heart-broken"></i>
                        <h3>No Likes Yet</h3>
                        <p>News articles haven't received any likes yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Mobile toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobileToggle');
            const sidePanel = document.getElementById('sidePanel');
            
            mobileToggle.addEventListener('click', function() {
                sidePanel.classList.toggle('active');
                
                // Change icon based on panel state
                const icon = this.querySelector('i');
                if (sidePanel.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
            
            // Close panel when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && 
                    !sidePanel.contains(event.target) && 
                    !mobileToggle.contains(event.target) &&
                    sidePanel.classList.contains('active')) {
                    sidePanel.classList.remove('active');
                    mobileToggle.querySelector('i').classList.remove('fa-times');
                    mobileToggle.querySelector('i').classList.add('fa-bars');
                }
            });

            // Initialize Charts
            <?php if (!empty($news_likes)): ?>
            // Prepare data for charts
            const topNews = <?= json_encode(array_slice($news_likes, 0, 10)) ?>;
            const categoryLikes = <?= json_encode($category_likes) ?>;

            // Bar Chart - Top 10 Most Liked Articles
            const barCtx = document.getElementById('barChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: topNews.map(news => news.title.length > 20 ? news.title.substring(0, 20) + '...' : news.title),
                    datasets: [{
                        label: 'Likes Count',
                        data: topNews.map(news => news.like_count),
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Likes'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'News Articles'
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });

            // Pie Chart - Category Distribution
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: categoryLikes.map(cat => cat.category_name),
                    datasets: [{
                        data: categoryLikes.map(cat => cat.like_count),
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#36A2EB'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>