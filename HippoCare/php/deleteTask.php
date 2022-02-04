<?php
    require('config.php'); 
    $strSessionID = $_POST['strSessionID'];
    $strTaskID = $_POST['strTaskID'];


    $strSessionID = strip_tags($strSessionID);
    $strTaskID = strip_tags($strTaskID);


    echo deleteTask($strSessionID, $strTaskID);
?>