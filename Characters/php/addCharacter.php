<?php
    require('config.php'); 
    $strSessionID = $_POST['strSessionID'];
    $strLocation = $_POST['strLocation'];
    $strName = $_POST['strName'];
    $strSuperPower = $_POST['strSuperPower'];
    $strStatus = $_POST['strStatus'];

    $strSessionID = strip_tags($strSessionID);
    $strLocation = strip_tags($strLocation);
    $strName = strip_tags($strName);
    $strSuperPower = strip_tags($strSuperPower);
    $strStatus = strip_tags($strStatus);

    echo addNewCharacter($strName,$strLocation,$strSuperPower,$strStatus,$strSessionID);
?>