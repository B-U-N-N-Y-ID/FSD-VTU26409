<?php
session_start();
session_unset();
session_destroy();

// Redirect back to the homepage
header("Location: index.php"); 
exit();
?>