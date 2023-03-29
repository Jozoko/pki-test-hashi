<?php
header('Content-Type: application/json');
require './controllers/authController.php';

$username = $_POST['username'];
$password = $_POST['password'];

$authController = new AuthController();
$response = $authController->authenticate($username, $password);

echo json_encode($response);
