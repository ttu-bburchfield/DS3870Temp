<?php
    require('config.php'); 
    $strUsername = $_POST['strUsername'];
    $strPassword = $_POST['strPassword'];

    $strUsername = strip_tags($strUsername);
    $strPassword = strip_tags($strPassword);

    echo newUser($strUsername,$strPassword);
?>