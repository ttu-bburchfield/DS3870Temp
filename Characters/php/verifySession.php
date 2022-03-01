<?php
    require('config.php');
    $strSessionID = $_GET['strSessionID'];

    $strSessionID = strip_tags($strSessionID);
    echo verifySession($strSessionID);
?>