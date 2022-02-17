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
    
    
    function newUser($Username,$Password,$FirstName,$LastName){
        if(checkUserExists($Username) == true){
            return '{"Outcome":"User Already Exists"}';
        } else {
            global $conCustodial;
            $strQuery = "INSERT INTO tblCharacterUsers VALUES (?,?,?,?,NOW())";
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
             $statCustodial->bind_param('ssss', $Username, $Password, $FirstName, $LastName);
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
        $strQuery = "INSERT INTO tblCharacterSessions VALUES (?,?,SYSDATE(),SYSDATE(),'ACTIVE')";
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
        $strQuery = "SELECT SessionID FROM tblCharacterSessions WHERE SessionID = ? AND Status = 'ACTIVE' AND StartDateTime >= NOW() - INTERVAL 12 HOUR";
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
    
    function getCharacters($SessionID){
        global $conCustodial;
        $strQuery = "SELECT * FROM tblCharacters WHERE ? IN (SELECT SessionID FROM tblCharacterSessions WHERE Status = 'ACTIVE' AND StartDateTime >= NOW() - INTERVAL 12 HOUR )";
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

    function addNewCharacter($Name,$Location,$SuperPower,$Status,$SessionID) {
        global $conCustodial;
        if(verifySession($SessionID) == '{"Outcome":"Valid Session"}'){
            $strTaskID = guidv4();
            $strQuery = "INSERT INTO tblCharacters VALUES (?,?,?,?,?)";
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
             $statCustodial->bind_param('sssss', $strTaskID,$Name,$Location,$SuperPower,$Status);
             if($statCustodial->execute()){
                return '{"Outcome":"'.$strTaskID.'"}';
             } else {
                return '{"Outcome":"Error"}';
             }
    
             $statCustodial->close();
        } else {
            return '{"Outcome":"InValid Session"}';
        }
        
    }
   
    function getUserDetails($SessionID){
        global $conCustodial;
        $strQuery = "SELECT Email, FirstName, LastName FROM tblCharacterUsers WHERE Email IN (SELECT Email FROM tblCharacterSessions WHERE SessionID = ?)";
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

    function checkUserExists($strEmail){
        global $conCustodial;
        $strQuery = "SELECT Email FROM tblCharacterUsers WHERE Email = ?";
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


    function verifyUsernamePassword($strUsername,$strPassword){
        global $conCustodial;
        $strQuery = "SELECT Email FROM tblCharacterUsers WHERE UPPER(Email) = UPPER(?) AND Password = ?";
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