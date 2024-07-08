<?php
    include 'partials/api_key.php'; 

    $email = isset($_GET['email']) ? $_GET['email'] : '';
    $enrollmentId = isset($_GET['enrollmentId']) ? $_GET['enrollmentId'] : '';

    if (!$email || !$enrollmentId) {
        echo 'Invalid parameters';
        exit;
    }
    // Generates the current Unix UTC timestamp
    $timestamp = time();
    // Message build with the email address, timestamp and key assigned to their values
    $message = "USER={$email}&TS={$timestamp}&KEY={$sqsso_key}";
    // Using the PHP built-in function that generates a hash value (message digest) using a specified hashing algorithm
    $token = hash('sha256', $message);
    // The redirect URL is built with the values from the api_key file, and what was passed from the learner dashboard (email and enrollment id)
    $redirect_url = "https://{$domain}.learnupon.com/sqsso?Email=" . urlencode($email) . "&TS={$timestamp}&SSOToken={$token}&redirect_uri=/enrollments/{$enrollmentId}";
    // The user is redirected to the SSO URL
    header("Location: $redirect_url");
    exit;
?>
