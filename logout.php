<?php
require_once 'includes/functions.php';

// Destroy session
session_start();
session_destroy();

// Redirect to home page
header("Location: index.php");
exit();
?>
