<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../'); // Adjust this path if necessary

$dotenv->load();

// Access environment variables
$api_key = $_ENV['API_KEY'];
$domain = $_ENV['DOMAIN'];
$sqsso_key = $_ENV['SQSSO_KEY'];
?>
