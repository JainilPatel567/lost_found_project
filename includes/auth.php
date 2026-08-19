<?php
session_start();

// Set basic variables if the user is logged in
if (isset($_SESSION['user_id'])) {
    $logged_in_id    = $_SESSION['user_id'];
    $logged_in_name  = $_SESSION['user_name'];
    $logged_in_email = $_SESSION['user_email']; 
}
?>