<?php
    require('config.php');
    $strSessionID = $_POST['strSessionID'];

    $strSessionID = strip_tags($strSessionID);
    echo killSession($strSessionID);
?>