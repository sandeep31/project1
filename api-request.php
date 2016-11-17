<?php
 include_once('application_main.php');
 ini_set("date.timezone", "Asia/Kolkata");
 $time = time();
        $MsgArr = array();
    if (isset($_REQUEST['action'])) {
        switch ($_REQUEST['action']) {
        case 'authenticateAppUser':
            $MsgArr = authenticateAppUser($_REQUEST['username'], $_REQUEST['password']);
            break; 
        
        case 'newData': 
            $MsgArr = InsertRecord();
            break;
        default:
            $MsgArr["error"] = 'Invalid API call';
            break;
        }
        
        echo json_encode($MsgArr);
    }
 
function authenticateAppUser($username , $password) {
    global $mysql;
    $sql = "SELECT * FROM `user`  WHERE `username` = '$username'  AND `password` = '".md5($password)."'";
    $rowArray = $mysql->getSqlData( $sql , $resultType='Record' );
    $err = array();  
    if(count($rowArray) > 0){
        return $rowArray;
    }
    $err['error'] = "Username and Password did not match.";
    return $err;
}

function InsertRecord(){   
    global $mysql;
        $date = date('Y-m-d H:i:s');
        $title    = isset($_REQUEST["title"]) && !empty($_REQUEST["title"])?$_REQUEST["title"]:'';
        $doc_user_id    = isset($_REQUEST["doc_user_id"]) && !empty($_REQUEST["doc_user_id"])?$_REQUEST["doc_user_id"]:'';
        $location = isset($_REQUEST["location"]) && !empty($_REQUEST["location"])?$_REQUEST["location"]:'';
        $type     = isset($_REQUEST["type"]) && !empty($_REQUEST["type"])?$_REQUEST["type"]:'';
        $number   = isset($_REQUEST["number"]) && !empty($_REQUEST["number"])?$_REQUEST["number"]:'';
        $amount   = isset($_REQUEST["amount"]) && !empty($_REQUEST["amount"])?$_REQUEST["amount"]:'';
        $ticket_number  = isset($_REQUEST["ticket_number"]) && !empty($_REQUEST["ticket_number"])?$_REQUEST["ticket_number"]:'';
        $MsgArr = array();
        if (!empty($doc_user_id) && !empty($location) && !empty($type) && !empty($number) && !empty($amount) && !empty($ticket_number)) {
            $sqlInsert = "INSERT INTO `document_master` ( 
                                            `doc_user_id` ,
                                            `doc_title` , 
                                            `doc_status`,
                                            `is_read`,
                                            doc_posttime,
                                            location,
                                            type,
                                            number,
                                            amount ,
                                            ticket_number
                                            )
                                            VALUES ( '".$doc_user_id."', '".$title."', '1', '0','".$date."',"
                    . " '".$location."','".$type."','".$number."','".$amount."','".$ticket_number."'  )";

           $ID =  $mysql->objDB->query($sqlInsert);
        
            if ($ID) { 
                $MsgArr["res"] = "success";
                $MsgArr["docID"] = $mysql->objDB->insert_id();
                return $MsgArr;
            } 
        
        } else {
                $MsgArr["res"] = "error";
                $MsgArr["msg"] = "all mandetory field are not pass";
        }
        return $MsgArr;
}

?>