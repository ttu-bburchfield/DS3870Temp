<?php
    require('config.php'); 
    $strSessionID = $_POST['strSessionID'];
    $strLocation = $_POST['strLocation'];
    $strTaskName = $_POST['strTaskName'];
    $datDueDate = $_POST['datDueDate'];
    $strNotes = $_POST['strNotes'];

    $strSessionID = strip_tags($strSessionID);
    $strLocation = strip_tags($strLocation);
    $strTaskName = strip_tags($strTaskName);
    $datDueDate = strip_tags($datDueDate);
    $strNotes = strip_tags($strNotes);

    echo addNewTask($strTaskName,$strLocation,$datDueDate,$strNotes,$strSessionID);
?>