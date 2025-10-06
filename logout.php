<?php
	include 'configuration.php';
    session_start();
	unset($_SESSION['user_id']);
	unset($_SESSION['role_type_name']);
	unset($_SESSION['user_name']);
	unset($_SESSION['user_type_id']);
	unset($_SESSION['user_img']);
	unset($_SESSION['user_mob']);
	unset($_SESSION['user_add']);
	unset($_SESSION['comp_id']);
	unset($_SESSION['comp_id']);
	unset($_SESSION['ExpirationAlert']);
	
	session_destroy();
	echo "<script>window.location='index.php';</script>";
?>