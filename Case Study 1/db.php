<?php
// 1. DATABASE CREDENTIALS
$host = 'localhost';
$dbname = 'customer_segmentation_ph';
$username = 'root'; 
$password = '';     

// 2. ERROR HANDLING FUNCTION (Must be defined before use)
function handleAppError($userMessage, $technicalDetail = "", $isFatal = false) {
    // This logs the technical details to your server's hidden error log
    error_log("APP ERROR: $userMessage | Details: $technicalDetail"); 
    
    if ($isFatal) {
        // Display a clean message to the user instead of raw code
        echo "<div style='padding:20px; background:#fff5f5; border:1px solid #feb2b2; color:#c53030; margin:20px; font-family:sans-serif; border-radius:5px;'>";
        echo "<strong>System Error:</strong> We are experiencing technical difficulties. Please try again later.";
        echo "</div>";
        exit;
    }
}

// 3. DATABASE CONNECTION
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Now this call will work because the function is defined above
    handleAppError("Database Connection Failed", $e->getMessage(), true);
}
?>
