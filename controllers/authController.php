<?php
require './models/authModel.php';

class AuthController
{
    public function authenticate($username, $password): array
    {
        $authModel = new AuthModel();
        $response = $authModel->authenticate($username, $password);

        if ($response['success']) {
            session_start();
            $_SESSION['authenticated'] = true;
            $_SESSION['username'] = $username;
        }

        return $response;
    }
}

