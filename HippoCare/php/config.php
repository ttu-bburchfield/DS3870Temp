<?php
	header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

    
	$host_name = 'db5005876205.hosting-data.io';
    $database = 'dbs4927532';
    $user_name = 'dbu2353338';
    $password = 'Lifelonglearningisimportant01!';

    $conCustodial = new mysqli($host_name, $user_name, $password, $database);

    function guidv4(){
        if (function_exists('com_create_guid') === true)
            return trim(com_create_guid(), '{}');

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    
    function verifySession($strUserSessionID){
        global $conCustodial;
        $strQuery = "SELECT SessionID FROM tblCurrentSessions WHERE SessionID = ? AND StartTime >= NOW() - INTERVAL 12 HOUR";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $strUserSessionID);
         $statCustodial->execute();      
         $statCustodial->bind_result($strSessionID);
         $statCustodial->fetch();
         $intRows = $statCustodial->num_rows;
         if($strSessionID){
            return '{"Outcome":"Valid"}';
         } else {
            return '{"Outcome":"InValid"}';
         }
         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
         $statCustodial->close();
    }

    function sendVerificationEmail($strEmailAddress){
        $emailTo = $strEmailAddress;
        $emailSubject = "Verify New Account";
        $emailHeaders = "MIME-Version: 1.0" . "\r\n";
        $emailHeaders = $emailHeaders . "Content-type:text/html;charset=UTF-8" . "\r\n";
        $emailHeaders = $emailHeaders . 'From: <noreply@swollenhippo.com>' . "\r\n";
        
        $message = "<!DOCTYPE html><html lang=\"en\" class=\"\"><head><!DOCTYPE html><html lang=\"en\" class=\"\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><head>
        <title>Verify Account</title>
        </head><body>
        <div class=\"jumbotron\"><h1>Thank you for setting up an account</h1><p>Please click the link below to verify and activate your account. <br> <a href='http://www.swollenhippo.com/DS3870/verifyAccount.php?UserName=" . $emailTo . "'>http://www.swollenhippo.com/DS3870/verifyAccount.php?UserName=" . $emailTo . "</a></p></div>
        </body>
        </html>
        "; 
        
        mail($emailTo,$emailSubject,$message,$emailHeaders);
    }

    function newTeam($TeamName,$StreetAddress,$ZIP,$State,$ContactNumber,$Owner,$APIKey,$FirstName,$LastName,$Phone,$Password){
        global $conCustodial;
        $strTeamID = guidv4();
        $strStatus = 'ACTIVE';
        $strQuery = "INSERT INTO tblTeams VALUES (?,?,?,?,?,?,?,?,?,(SELECT TeamKey FROM tblTeamKeys WHERE TeamKey NOT IN (SELECT b.TeamKey FROM tblTeams b) LIMIT 1))";
      	// Check Connection
        try {
            if ($conCustodial->connect_errno) {
                $blnError = "true";
                $strErrorMessage = $conCustodial->connect_error;
                $arrError = array('error' => $strErrorMessage);
                echo json_encode($arrError);
                exit();
            }
          
            if ($conCustodial->ping()) {
            } else {
                $blnError = "true";
                $strErrorMessage = $conCustodial->error;
                $arrError = array('error' => $strErrorMessage);
                echo json_encode($arrError);
            }
          
             $statCustodial = $conCustodial->prepare($strQuery);
    
             // Bind Parameters
             $statCustodial->bind_param('sssssssss', $strTeamID, $TeamName, $StreetAddress, $ZIP, $State, $ContactNumber, $Owner, $strStatus,$APIKey);
             
             if($statCustodial->execute()){
                if(newUser($Owner,$FirstName,$LastName,$Phone,$strTeamID,$Password) == '{"Outcome":"New User Created"}'){
                    return '{"Outcome":"'.$strTeamID.'"}';
                } else {
                    return '{"Outcome":"Error"}';
                }
                
             } else {
                return '{"Outcome":"Error"}';
             }
    
             // $result = $statCustodial->get_result();
             
             // echo json_encode(($result->fetch_assoc()));
             $statCustodial->close();
        }
        catch (exception $ex) {
            var_dump($ex);
        }
        
    }

    function updateTeam($TeamID,$TeamName,$StreetAddress,$ZIP,$State,$ContactNumber,$Owner,$Status){
        global $conCustodial;
        $strTeamID = guidv4();
        $strStatus = 'ACTIVE';
        $strQuery = "UPDATE tblTeams SET TeamName = ?, StreetAddress = ?, ZIP = ?, State = ?, ContactNumber = ?, Owner = ?, Status = ? WHERE TeamID = ?)";
      	// Check Connection
        try {
            if ($conCustodial->connect_errno) {
                $blnError = "true";
                $strErrorMessage = $conCustodial->connect_error;
                $arrError = array('error' => $strErrorMessage);
                echo json_encode($arrError);
                exit();
            }
          
            if ($conCustodial->ping()) {
            } else {
                $blnError = "true";
                $strErrorMessage = $conCustodial->error;
                $arrError = array('error' => $strErrorMessage);
                echo json_encode($arrError);
            }
          
             $statCustodial = $conCustodial->prepare($strQuery);
    
             // Bind Parameters
             $statCustodial->bind_param('ssssssss', $TeamName, $StreetAddress, $ZIP, $State, $ContactNumber, $Owner, $strStatus,$TeamID);
             
             if($statCustodial->execute()){
                return '{"Outcome":"Team Updated"}';
                
             } else {
                return '{"Outcome":"Error"}';
             }
    
             $statCustodial->close();
        }
        catch (exception $ex) {
            var_dump($ex);
        }
        
    }

    function newUserWithCode($Username,$FirstName,$LastName,$Phone,$Password,$TeamCode){
        global $conCustodial;
        $strQuery = "INSERT INTO tblUsers VALUES (?,?,?,?,(SELECT TeamID FROM tblTeams WHERE TeamKey = ? AND Status = 'ACTIVE'),?,SYSDATE(),'NEW')";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssss', $Username, $FirstName, $LastName, $Phone, $TeamCode, $Password);
         if($statCustodial->execute()){
            sendVerificationEmail($Username);
            return '{"Outcome":"New User Created"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
         $statCustodial->close();
    }

    function setUserInactive($Username,$TeamCode){
        global $conCustodial;
        $strQuery = "UPDATE tblUsers SET UserStatus = 'INACTIVE' WHERE Email = ? AND Team = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ss', $Username, $TeamCode);
         if($statCustodial->execute()){
            return '{"Outcome":"User Set Inactive"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
         $statCustodial->close();
    }

    function setUserActive($Username,$TeamCode){
        global $conCustodial;
        $strQuery = "UPDATE tblUsers SET UserStatus = 'ACTIVE' WHERE Email = ? AND Team = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ss', $Username, $TeamCode);
         if($statCustodial->execute()){
            return '{"Outcome":"User Set Active"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
         $statCustodial->close();
    }

    function getUserDetailsBySessionID($SessionID){
        try{
            global $conCustodial;
            $strQuery = "SELECT Email, FirstName, LastName, LastUsed, Phone, Team, UserStatus FROM tblUsers LEFT JOIN tblCurrentSessions ON tblUsers.Email = tblCurrentSessions.UserID AND tblUsers.Team = tblCurrentSessions.TeamID WHERE SessionID = ? AND StartTime >= NOW() - INTERVAL 12 HOUR";
              // Check Connection
            if ($conCustodial->connect_errno) {
                $blnError = "true";
                $strErrorMessage = $conCustodial->connect_error;
                $arrError = array('error' => $strErrorMessage);
                echo json_encode($arrError);
                exit();
            }
          
            if ($conCustodial->ping()) {
            } else {
                $blnError = "true";
                $strErrorMessage = $conCustodial->error;
                $arrError = array('error' => $strErrorMessage);
                echo json_encode($arrError);
            }
          
             $statCustodial = $conCustodial->prepare($strQuery);
    
             // Bind Parameters
             $statCustodial->bind_param('s', $SessionID);
             $statCustodial->execute();      
             $result = $statCustodial->get_result();
             $myArray = array();
    
             while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                     $myArray[] = $row;
             }
             echo json_encode($myArray);
                
             $statCustodial->close();
        } catch (exception $e) {
            echo 'Error: '.$e;
        }
        
    }

    function updateUserPassword($Username,$TeamID,$Password){
        global $conCustodial;
        $strQuery = "UPDATE tblUsers SET Password = ? WHERE Email = ? AND Team = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('sss', $Password, $Username, $TeamID);
         if($statCustodial->execute()){
            return '{"Outcome":"Password Updated"}';
         } else {
            return '{"Outcome":"Error"}';
         }
         $statCustodial->close();
    }

    function updateUserDetails($Username,$FirstName,$LastName,$Phone,$TeamID){
        global $conCustodial;
        $strQuery = "UPDATE tblUsers SET Email= ?, FirstName = ?, LastName = ?, Phone = ? WHERE Email = ? AND Team = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssss', $Username, $FirstName, $LastName, $Phone, $Username, $TeamID);
         if($statCustodial->execute()){
            return '{"Outcome":"User Updated"}';
         } else {
            return '{"Outcome":"Error"}';
         }
         $statCustodial->close();
    }

    function newUser($Username,$FirstName,$LastName,$Phone,$TeamID,$Password){
        global $conCustodial;
        $strQuery = "INSERT INTO tblUsers VALUES (?,?,?,?,?,?,SYSDATE(),'NEW')";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssss', $Username, $FirstName, $LastName, $Phone, $TeamID, $Password);
         if($statCustodial->execute()){
            sendVerificationEmail($Username);
            return '{"Outcome":"New User Created"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
         $statCustodial->close();
    }

    function assignUserRole($Username,$Role){
        global $conCustodial;
        $strAssingmentID = guidv4();
        $strQuery = "INSERT INTO tblUserRoleAssignments VALUES (?,?,(SELECT DISTINCT(Team) FROM tblUsers WHERE UPPER(Email) = UPPER(?)),?,NOW())";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssss', $strAssingmentID, $Username, $Username, $Role);
         if($statCustodial->execute()){
            sendVerificationEmail($Username);
            return '{"Outcome":"'.$strAssingmentID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
         $statCustodial->close();
    }

    function addInventoryRecord($MaterialID,$TransactionType,$Quantity,$SessionID){
        global $conCustodial;
        $strTransactionID = guidv4();
        $strQuery = "INSERT INTO tblInventory VALUES (?,?,?,?,(SELECT UserID FROM tblCurrentSessions WHERE SessionID = ?),SYSDATE(),(SELECT TeamID FROM tblCurrentSessions WHERE SessionID = ?))";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssss', $strTransactionID, $MaterialID, $TransactionType, $Quantity, $SessionID, $SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strTransactionID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
         $statCustodial->close();
    }

    function addNewTask($TaskName,$Description,$Directions,$ExpectedMinutes,$ExternalDirections,$Category,$SessionID) {
        global $conCustodial;
        $strTaskID = guidv4();
        $strQuery = "INSERT INTO tblTasks VALUES (?,?,?,?,?,?,?,?,(SELECT TeamID FROM tblCurrentSessions WHERE SessionID = ?))";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssssss', $strTaskID,$TaskName,$Description,$Directions,$ExpectedMinutes,$ExternalDirections,$Category,$SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strTaskID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function updateTask($TaskID,$TaskName,$Description,$Directions,$ExpectedMinutes,$ExternalDirections,$Category) {
        global $conCustodial;
        $strQuery = "UPDATE tblTasks SET TaskName = ?, Description = ?, Directions = ?, ExpectedMinutes = ?, ExternalDirections = ?, Category = ? WHERE TaskID = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('sssssss', $TaskName,$Description,$Directions,$ExpectedMinutes,$ExternalDirections,$Category,$TaskID);
         if($statCustodial->execute()){
            return '{"Outcome":"Task Updated"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function addNewCategory($Description,$Icon,$SessionID) {
        global $conCustodial;
        $strCategoryID = guidv4();
        $strQuery = "INSERT INTO tblCategories  VALUES (?,?,?,(SELECT TeamID FROM tblCurrentSessions WHERE SessionID = ?),'ACTIVE')";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssss', $strCategoryID,$Description,$Icon,$SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strCategoryID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function addLocation($Campus,$Building,$Room,$SessionID) {
        global $conCustodial;
        $strLocationID = guidv4();
        $strQuery = "INSERT INTO tblLocations VALUES (?,?,?,?,NOW(),(SELECT UserID FROM tblCurrentSessions WHERE SessionID = ?),'ACTIVE',(SELECT TeamID FROM tblCurrentSessions WHERE SessionID = ?))";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssss',$strLocationID,$Campus,$Building,$Room,$SessionID,$SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strLocationID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function updateLocation($LocationID,$Campus,$Building,$Room,$Status,$SessionID) {
        global $conCustodial;
        $strQuery = "UPDATE tblLocations SET Campus = ?, Buidling = ?, Room = ?, LastModifiedDate = NOW(), ModifiedBy =(SELECT UserID FROM tblCurrentSessions WHERE SessionID = ?),Status = ? WHERE LocationID = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssss',$Campus,$Building,$Room,$SessionID,$Status,$LocationID);
         if($statCustodial->execute()){
            return '{"Outcome":"Location Updated"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function addCategory($SessionID,$Description,$Icon) {
        global $conCustodial;
        $strCategoryID = guidv4();
        $strQuery = "INSERT INTO tblCategories VALUES (?,?,?,(SELECT DISTINCT(TeamID) FROM tblCurrentSessions WHERE SessionID = ?),'ACTIVE')";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssss', $strCategoryID,$Description,$Icon,$SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strCategoryID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function addMaterials($SessionID,$Description,$MinOnHand,$Unit,$LatestCost,$ProductNumber,$MaterialHandling) {
        global $conCustodial;
        $strMaterialID = guidv4();
        $strQuery = "INSERT INTO tblMaterials VALUES (?,?,?,?,?,?,?,(SELECT TeamID FROM tblCurrentSessions WHERE SessionID = ?),'ACTIVE')";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssssss', $strMaterialID,$Description,$MinOnHand,$Unit,$LatestCost,$ProductNumber,$MaterialHandling,$SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strMaterialID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function addProblem($Description,$LocationID,$Submitter,$SessionID) {
        global $conCustodial;
        $strProblemID = guidv4();
        $strQuery = "INSERT INTO tblProblems (ProblemID, Description, LocationID, SubmittedDate, SubmittedBy, Status, TeamID) VALUES (?,?,?,NOW(),?,'NEW',(SELECT TeamID FROM tblCurrentSessions WHERE SessionID = ?))";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('sssss', $strProblemID,$Description,$LocationID,$Submitter,$SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strProblemID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function addProblemExternal($Description,$LocationID,$Submitter,$TeamID) {
        global $conCustodial;
        $strProblemID = guidv4();
        $strQuery = "INSERT INTO tblProblems (ProblemID, Description, LocationID, SubmittedDate, SubmittedBy, Status, TeamID) VALUES (?,?,?,NOW(),?,'NEW',?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('sssss', $strProblemID,$Description,$LocationID,$Submitter,$TeamID);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strProblemID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function addTaskMaterials($TaskID,$MaterialID,$Quantity) {
        global $conCustodial;
        $strConsumptionID = guidv4();
        $strQuery = "INSERT INTO tblTaskMaterials VALUES (?,?,?,?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssss', $strConsumptionID,$TaskID,$MaterialID,$Quantity);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strProblemID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function updateTaskMaterials($ConsumptionID,$TaskID,$MaterialID,$Quantity) {
        global $conCustodial;
        $strConsumptionID = guidv4();
        $strQuery = "UPDATE tblTaskMaterials SET TaskID = ?, MaterialID = ?, Quantity = ? WHERE ConsumptionID = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssss',$TaskID,$MaterialID,$Quantity,$ConsumptionID);
         if($statCustodial->execute()){
            return '{"Outcome":"Consumption Udpated"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function updateProblem($ProblemID,$Description,$LocationID,$Status,$CompletedDate,$SessionID) {
        global $conCustodial;
        $strQuery = "UDPATE tblProblems SET Description = ?, LocationID = ?, Status = ?, CompletedDate = ?, CompletedBy =(SELECT UserID FROM tblSessions WHERE SessionID = ?) WHERE ProblemID = ? ";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssss', $Description,$LocationID,$Status,$CompletedDate,$SessionID,$ProblemID);
         if($statCustodial->execute()){
            return '{"Outcome":"Problem Updated"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function updateMaterials($MaterialID,$Description,$MinOnHand,$Unit,$LatestCost,$ProductNumber,$MaterialHandling,$Status) {
        global $conCustodial;
        $strQuery = "UPDATE tblMaterials SET Description = ?, MinOnHand = ?, Unit = ?, LatestCost = ?, ProductNumber = ?, MaterialHandling = ?,Status = ? WHERE MaterialID = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssssss', $Description,$MinOnHand,$Unit,$LatestCost,$ProductNumber,$MaterialHandling,$Status,$MaterialID);
         if($statCustodial->execute()){
            return '{"Outcome":"Material Updated"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function updateCategory($CategoryID,$Description,$Icon,$Status) {
        global $conCustodial;
        $strQuery = "UPDATE tblCategories SET Description = ?, Icon = ?, Status = ? WHERE CategoryID = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssss', $Description,$Icon,$Status,$CategoryID);
         if($statCustodial->execute()){
            return '{"Outcome":"Category Updated"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function killSession($SessionID) {
        global $conCustodial;
        $strQuery = "DELETE FROM tblCurrentSessions WHERE SessionID = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"User Session Ended"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function updateUserLastUsed($SessionID) {
        global $conCustodial;
        $strQuery = "UPDATE tblUsers SET LastUsed = NOW() WHERE Email = (SELECT UserID FROM tblCurrentSessions WHERE SessionID = ?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"User Updated"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function addAssignedTask($TaskID,$Location,$User,$DueDate,$SessionID) {
        global $conCustodial;
        $strAssingmentID = guidv4();
        $strQuery = "INSERT INTO tblAssignedTasks (AssignmentID,TaskID,LocationID,PersonID,DueDate,TeamID) VALUES (?,?,?,?,?,(SELECT TeamID FROM tblSessions WHERE SessionID = ?))";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssss', $strAssingmentID,$TaskID,$Location,$User,$DueDate,$SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strAssingmentID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function updateAssignedTask($AssignmentID,$TaskID,$Location,$User,$DueDate,$CompletionDateTime,$StartedDateTime,$CompletedBy) {
        global $conCustodial;
        $strQuery = "UPDATE tblAssignedTasks SET TaskID = ?, LocationID = ?, PersonID = ?, DueDate = ?, CompletionDate = ?, StartedDate = ?, CompletedBy = ? WHERE AssignmentID = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssssssss', $TaskID,$Location,$User,$DueDate,$CompletedDateTime,$StartedDateTime,$CompletedBy,$AssignmentID);
         if($statCustodial->execute()){
            return '{"Outcome":"Assigned Task Updated"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function verifyUser($Username){
        global $conCustodial;
        $strQuery = "UPDATE tblUsers SET UserStatus = 'ACTIVE' WHERE UPPER(Email) = UPPER(?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $Username);
         if($statCustodial->execute()){
            return '{"Outcome":"Verified"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function getRedirectURL($Username){
        global $conCustodial;
        $strQuery = "SELECT RedirectURL FROM tblUsers LEFT JOIN tblTeams ON tblUsers.Team = tblTeams.TeamID LEFT JOIN InternalTeams ON tblTeams.InternalTeamOwnership = InternalTeams.TeamID WHERE Email = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $Username);
         if($statCustodial->execute()){
            return '{"Outcome":"Verified"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function verifyUsernamePassword($strUsername,$strPassword){
        global $conCustodial;
        $strQuery = "SELECT Email FROM tblUsers WHERE UPPER(Email) = UPPER(?) AND UserPassword= ? AND UserStatus = 'ACTIVE'";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);
		 // Bind Parameters
		 $statCustodial->bind_param('ss', $strUsername, $strPassword);
         $statCustodial->execute();      
         $statCustodial->bind_result($strEmail);
         $statCustodial->fetch();
         $intRows = $statCustodial->num_rows;
         if($strEmail){
            return 'true';
         } else {
            return 'false';
         }
         
         $statCustodial->close();
    }

    function getTeamKeyByUsername($strUsername){
        global $conCustodial;
        $strQuery = "SELECT TeamKey FROM tblUsers LEFT JOIN tblTeams ON tblUsers.Team = tblTeams.TeamID WHERE Email = ? AND UserStatus = 'ACTIVE'";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);
		 // Bind Parameters
		 $statCustodial->bind_param('s', $strUsername);
         $statCustodial->execute();      
         $statCustodial->bind_result($strTeamKey);
         $statCustodial->fetch();
         if($strTeamKey){
            return '{"Outcome":"'.$strTeamKey.'"}';
         } else {
            return '{"Outcome":"Key Not Found"}';
         }
         
         $statCustodial->close();
    }

    function getCurrentRole($Username){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblUserRoleAssignments WHERE UserID = ? AND EffectiveDate = (SELECT MAX(EffectiveDate) FROM tblUserRoleAssignments WHERE UserID = ?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ss', $Username, $Username);
         $statCustodial->execute();      
         $intRows = $statCustodial->num_rows;
         if($intRows > 0){
            return true;
         } else {
            return false;
         }
         
         $statCustodial->close();
    }

    function getUserTeamInfo($SessionID){
        global $conCustodial;
        $strQuery = "SELECT TeamID, TeamName, InternalTeamOwnership, TeamKey FROM tblUsers LEFT JOIN tblTeams ON tblUsers.Team = tblTeams.TeamID WHERE Email = (SELECT UserID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $Username);
         $statCustodial->execute();      
         $intRows = $statCustodial->num_rows;
         if($intRows > 0){
            return true;
         } else {
            return false;
         }
         
         $statCustodial->close();
    }

    function createNewSession($Username){
        global $conCustodial;
        $strSessionID = guidv4();
        $strQuery = "INSERT INTO tblCurrentSessions VALUES (?,?,SYSDATE(),SYSDATE(),(SELECT Team FROM tblUsers WHERE Email = ?),(SELECT InternalTeams.TeamID FROM tblUsers LEFT JOIN tblTeams ON tblUsers.Team = tblTeams.TeamID LEFT JOIN InternalTeams ON tblTeams.InternalTeamOwnership = InternalTeams.TeamID WHERE Email = ?))";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('ssss', $strSessionID,$Username,$Username,$Username);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strSessionID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
         $statCustodial->close();
    }

    function getTeamMembersWithCode($TeamCode){
        global $conCustodial;
        $strQuery = "SELECT Email, FirstName, LastName, LastUsed, Phone, Team, UserStatus FROM tblUsers LEFT JOIN tblTeams ON tblUsers.Team = tblTeams.TeamID WHERE TeamKey = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $TeamCode);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getTeamMembersBySessionID($SessionID){
        global $conCustodial;
        $strQuery = "SELECT Email, FirstName, LastName, LastUsed, Phone, Team, UserStatus FROM tblUsers LEFT JOIN tblCurrentSessions ON tblUsers.Team = tblCurrentSessions.TeamID WHERE SessionID = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getAssignedTasksByTeam($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblAssignedTasks WHERE TeamID = (SELECT TeamID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getMyAssignedTasks($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblAssignedTasks WHERE PersonID = (SELECT UserID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getAllTasksByTeam($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblTasks WHERE TeamID = (SELECT TeamID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getAllCompletedTasksByTeam($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblAssignedTasks WHERE CompletionDate IS NOT NULL AND TeamID = (SELECT TeamID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getAllOpenTasksForTeam($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblAssignedTasks WHERE CompletionDate IS NULL AND TeamID = (SELECT TeamID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getAllSessionsByTeam($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblCurrentSessions WHERE TeamID = (SELECT TeamID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getTaskMaterialsByTask($Task){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblTaskMaterials WHERE TaskID = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $Task);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getMaterials($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblMaterials WHERE TeamID = (SELECT TeamID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getTaskCategories($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblCategories WHERE TeamID = (SELECT TeamID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getInventory($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblInventory WHERE TeamID = (SELECT TeamID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getLocations($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblLocations WHERE TeamID = (SELECT TeamID FROM tblCurrentSessions WHERE SessionID=?)";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getRoles(){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblRoles";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $SessionID);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }

    function getTeamInfoByTeamCode($TeamCode){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblTeams WHERE TeamID = ?";
      	// Check Connection
        if ($conCustodial->connect_errno) {
            $blnError = "true";
            $strErrorMessage = $conCustodial->connect_error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
            exit();
        }
      
        if ($conCustodial->ping()) {
        } else {
            $blnError = "true";
            $strErrorMessage = $conCustodial->error;
            $arrError = array('error' => $strErrorMessage);
            echo json_encode($arrError);
        }
      
		 $statCustodial = $conCustodial->prepare($strQuery);

		 // Bind Parameters
		 $statCustodial->bind_param('s', $TeamCode);
         $statCustodial->execute();      
         $result = $statCustodial->get_result();
         $myArray = array();

         while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                 $myArray[] = $row;
         }
         echo json_encode($myArray);
            
         $statCustodial->close();
    }
?>