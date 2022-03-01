<?php
    require('config.php'); 
    $strUsername = $_POST['strEmail'];
    $strPassword = $_POST['strPassword'];
    $strFirstName = $_POST['strFirstName'];
    $strLastName = $_POST['strLastName'];


    $strUsername = strip_tags($strUsername);
    $strPassword = strip_tags($strPassword);
    $strFirstName = strip_tags($strFirstName);
    $strLastName = strip_tags($strLastName);


    echo newUser($strUsername,$strPassword,$strFirstName,$strLastName);
?>