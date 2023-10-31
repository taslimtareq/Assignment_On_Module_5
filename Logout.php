<?php

class Logout {
    public function __construct() {
        session_start();
    }

    public function logoutAction() {
        // Check if the user is logged in (optional but recommended)
        if (isset($_SESSION['user'])) {
            // Destroy the session
            session_destroy();

            // Redirect to the login page
            header('Location: Login.php');
            exit; // Terminate the script to ensure the redirection is executed
        }
    }
}

$logoutObj = new Logout();
$logoutObj->logoutAction();
