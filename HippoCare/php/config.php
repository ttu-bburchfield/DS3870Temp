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
    
    function newUser($Username,$Password){
        if(checkUserExists($Username) == true){
            return '{"Outcome":"User Already Exists"}';
        } else {
            global $conCustodial;
            $strQuery = "INSERT INTO tblTaskUsers VALUES (?,?,NOW())";
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
             $statCustodial->bind_param('ss', $Username, $Password);
             if($statCustodial->execute()){
                return '{"Outcome":"New User Created"}';
             } else {
                return '{"Outcome":"Error - User Not Created"}';
             }
    
             // $result = $statCustodial->get_result();
             
             // echo json_encode(($result->fetch_assoc()));
             $statCustodial->close();
        }
        
    }

    function createNewSession($Username){
        global $conCustodial;
        $strSessionID = guidv4();
        $strQuery = "INSERT INTO tblTaskSessions VALUES (?,?,SYSDATE(),SYSDATE(),'ACTIVE')";
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
		 $statCustodial->bind_param('ss', $strSessionID,$Username);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strSessionID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
         $statCustodial->close();
    }

    function verifySession($strUserSessionID){
        global $conCustodial;
        updateUserLastUsed($strUserSessionID);
        $strQuery = "SELECT SessionID FROM tblTaskSessions WHERE SessionID = ? AND Status = 'ACTIVE' AND StartTimeDateTime >= NOW() - INTERVAL 12 HOUR AND LastUsedDateTime >= NOW() - INTERVAL 2 HOUR";
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
            return '{"Outcome":"Valid Session"}';
         } else {
            return '{"Outcome":"InValid Session"}';
         }
         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
         $statCustodial->close();
    }
    
    function getTasks($SessionID){
        global $conCustodial;
        updateUserLastUsed($SessionID);
        $strQuery = "SELECT * FROM tblTasksTasks WHERE Owner = (SELECT Email FROM tblTaskSessions WHERE SessionID = ?)";
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

    function addNewTask($TaskName,$Location,$DueDate,$Notes,$SessionID) {
        global $conCustodial;
        updateUserLastUsed($SessionID);
        $strTaskID = guidv4();
        $strQuery = "INSERT INTO tblTasksTasks VALUES (?,?,?,?,?,(SELECT Email FROM tblTaskSessions WHERE SessionID = ?),'ACTIVE')";
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
		 $statCustodial->bind_param('ssssss', $strTaskID,$TaskName,$Location,$DueDate,$Notes,$SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"'.$strTaskID.'"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function deleteTask($SessionID, $TaskID) {
        global $conCustodial;
        updateUserLastUsed($SessionID);
        $strQuery = "DELETE FROM tblTasksTasks WHERE TaskID = ? AND Owner = (SELECT Email FROM tblTaskSessions WHERE SessionID = ?)";
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
		 $statCustodial->bind_param('ss', $TaskID, $SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"Task Deleted"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function markTaskComplete($TaskID, $SessionID) {
        global $conCustodial;
        updateUserLastUsed($strUserSessionID, $SessionID);
        $strQuery = "UPDATE tblTasksTasks SET Status = 'COMPLETE' WHERE TaskID = ? AND Owner = (SELECT Email FROM tblTaskSessions WHERE SessionID = ?)";
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
		 $statCustodial->bind_param('ss', $TaskID, $SessionID);
         if($statCustodial->execute()){
            return '{"Outcome":"Task Marked Complete"}';
         } else {
            return '{"Outcome":"Error"}';
         }

         $statCustodial->close();
    }

    function killSession($SessionID) {
        global $conCustodial;
        $strQuery = "DELETE FROM tblTaskSessions WHERE SessionID = ?";
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

    function checkUserExists($strEmail){
        global $conCustodial;
        $strQuery = "SELECT Email FROM tblTaskUsers WHERE Email = ?";
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
		 $statCustodial->bind_param('s', $strEmail);
         $statCustodial->execute();      
         $statCustodial->bind_result($strSessionID);
         $statCustodial->fetch();
         $intRows = $statCustodial->num_rows;
         if($strSessionID){
            return true;
         } else {
            return false;
         }
         // $result = $statCustodial->get_result();
         
         // echo json_encode(($result->fetch_assoc()));
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

    function verifyUsernamePassword($strUsername,$strPassword){
        global $conCustodial;
        $strQuery = "SELECT Email FROM tblTaskUsers WHERE UPPER(Email) = UPPER(?) AND Password = ?";
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
?>