<?php
session_start();

// Destroy all session data
$_SESSION = array();
session_destroy();

// Redirect back to login page
header("Location: login.php");
exit;
?>
