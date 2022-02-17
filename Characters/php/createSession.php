<?php
    require('config.php'); 
    $strUsername = $_POST['strEmail'];
    $strPassword = $_POST['strPassword'];

    $strUsername = strip_tags($strUsername);
    $strPassword = strip_tags($strPassword);


    if(verifyUsernamePassword($strUsername,$strPassword) == 'true'){
        echo createNewSession($strUsername);
    } else {
        echo '{"Outcome":"Login Failed"}';
    }  
?>