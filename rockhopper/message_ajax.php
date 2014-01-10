<?php

/**
 * ****************************************************************************
 *
 * message_ajax.php 
 * 
 * Used by showmessages.php and readmessage.php to delete or add messages.
 * 
 *                               
 */
 
 require_once 'connect.php';
 require_once 'user.php';
 require_once 'message.php';
 require_once 'tracking.php';
 
 $curUser = doAutoLogin($dbh);
 $userid = $curUser->getId();
 
 $inputData = json_decode(file_get_contents("php://input"));
 $datatype=$inputData->datatype;
 
 // add a new message
 if ($datatype == "newMessage") {
	$newReceiver = $inputData->newReceiver;
	$newTitle = $inputData->newTitle;
	$newMessage = $inputData->newMessage;
	 
	//remove slashes depending on the configuration
	if(get_magic_quotes_gpc()) {
		$newTitle = stripslashes($newTitle);
		$newMessage = stripslashes($newMessage);
	}
	
	//protect the variables
	$recip = $newReceiver;
	$title = mysql_real_escape_string($newTitle);
	$message = mysql_real_escape_string(nl2br(htmlentities($newMessage, ENT_QUOTES, 'UTF-8')));
	
	//check if the recipient exists
	if ($to_recip = User::getUserByUsername($dbh, $recip)){
		$serial_id = Message::getMaxSerial_id($dbh) + 1;
		$round_num = 1;
		$to_id = $to_recip->getId();
		$creation_ts = date('Y/m/d h:i:s', time());
		if(Message::addMsg($dbh, $serial_id, $round_num, $title, $userid, $to_id, $message, $creation_ts)) {
			$messages = Message::getMessageBySerial_id($dbh, $serial_id);
			$loadmessage = $messages[0];
			$from_user = User::getUserById($dbh, $loadmessage->getFrom_id());
			$to_user = User::getUserById($dbh, $loadmessage->getTo_id());
			//return a string to be added to the html code.
			$str="<tr id =".$loadmessage->getSerial_id().">
					<td onClick=\"window.location.href='readmessage.php?id=".$loadmessage->getSerial_id()."'\" style='cursor: pointer;'>".$loadmessage->getTitle()."</td>
					<td onClick=\"window.location.href='readmessage.php?id=".$loadmessage->getSerial_id()."'\" style='cursor: pointer;'>".($loadmessage->getRound_num()-1)."</td>
					<td onClick=\"window.location.href='readmessage.php?id=".$loadmessage->getSerial_id()."'\" style='cursor: pointer;'><div class='user_profile'><img src='".$from_user->getIconUrl()."'> ".$from_user->getUsername()."</div></td>
					<td onClick=\"window.location.href='readmessage.php?id=".$loadmessage->getSerial_id()."'\" style='cursor: pointer;'><div class='user_profile'><img src='".$to_user->getIconUrl()."'> ".$to_user->getUsername()."</div></td>
					<td onClick=\"window.location.href='readmessage.php?id=".$loadmessage->getSerial_id()."'\" style='cursor: pointer;'>".$loadmessage->getCreation_ts()."</td>
					<td align='center'><input type='checkbox' name='chk' id='chk' value='".$loadmessage->getSerial_id()."'></td>
				</tr>";
			
			//cannot use a new variable		
			$inputData->insertString = $str;
			$inputData->serverResponse = "success";
		}
		else {
			$inputData->serverResponse = "error";
		}
	}
	else $inputData->serverResponse = "norec";
	
	$replyStr = json_encode ($inputData);
	echo $replyStr;
 }
 
 // reply an exist message list
 else if ($datatype == "newReply") {
	$newId = $inputData->newId;
	$newMessage = $inputData->newMessage;
	 
	//remove slashes depending on the configuration
	if(get_magic_quotes_gpc()) {
		$newMessage = stripslashes($newMessage);
	}
	$message = mysql_real_escape_string(nl2br(htmlentities($newMessage, ENT_QUOTES, 'UTF-8')));
	
	$messages = Message::getMessageBySerial_id($dbh, $newId);
	$last_message = $messages[0];
	$round_num = ($last_message->getRound_num())+1;
	$title = $last_message->getTitle();
	$creation_ts = date('Y/m/d h:i:s', time());
	
	if ($last_message->getTo_id() == $userid) {
		$to_id = $last_message->getFrom_id();
	}
	else {
		$to_id = $last_message->getTo_id();
	}
							
	if(Message::addMsg($dbh, $newId, $round_num, $title, $userid, $to_id, $message, $creation_ts)) {
		$updated_messages = Message::getMessageBySerial_id($dbh, $newId);
		$loadmessage = $updated_messages[0];
		$from_user = User::getUserById($dbh, $loadmessage->getFrom_id());
		$to_user = User::getUserById($dbh, $loadmessage->getTo_id());
		//return a string to be added to the html code.
		$str="<tr>
				<td><div class='user_profile'><img src='".$from_user->getIconUrl()."'> ".$from_user->getUsername()."</td>
				<td><div class='user_profile'><img src='".$to_user->getIconUrl()."'> ".$to_user->getUsername()."</td>
				<td><div class='pull-right'><i>Sent: ".$loadmessage->getCreation_ts()."</i></div>".$loadmessage->getMessage()."</td>";
		//cannot use a new variable		
		$inputData->insertString = $str;
		$inputData->serverResponse = "success";
	}
	else {
		$inputData->serverResponse = "error";
	}
	
	$replyStr = json_encode ($inputData);
	echo $replyStr;
 }
 
 // delete message(s)
 else if ($datatype == "deleteMessage") {
 	$datainfo=$inputData->datainfo;
 	$ids=explode(",", $datainfo);
 	$reply_success="";
 	$reply_error="";
 
 	foreach ($ids as $del_id) {
	 	$messages = Message::getMessageBySerial_id($dbh, $del_id);
		//$last_message is the latest msg of the msgs with the same id
	 	$last_message = $messages[0];
	 
	 	// the receiver = the sender, then delete the msg forever
	 	if (($last_message->getFrom_id() == $userid) and ($last_message->getTo_id() == $userid)) {
			if (Message::deleteMsg($dbh, $del_id)) $reply_success="success";
	 	}
		 
	 	// the msg is deleting by sender
	 	else if ($last_message->getFrom_id() == $userid) {
			//the msg hasn't been deleted by receiver
		 	if ($last_message->getDelete_status() == 0) {
			 	if (Message::changesDeleteStatus($dbh, $del_id, $userid)) $reply_success="success";
			 	else $reply_error="error";
		 	}
		 	//the msg has been deleted by receiver, so it can be permently deleted
		 	else if ($last_message->getDelete_status() == 2) {
			 	if (Message::deleteMsg($dbh, $del_id)) $reply_success="success";
			 	else $reply_error="error";
		 	}
	 	}
					 
	 	//the msg is deleting by receiver
	 	else if ($last_message->getTo_id() == $userid) {
		 	if ($last_message->getDelete_status() == 0) {
			 	if (Message::changesDeleteStatus($dbh, $del_id, $userid)) $reply_success="success";
			 	else $reply_error="error";
		 	}
		 	else if ($last_message->getDelete_status() == 1) {
			 	if (Message::deleteMsg($dbh, $del_id)) $reply_success="success";
			 	else $reply_error="error"; 
		 	}
	 	}
	 
	 	else $reply_error="error";
 	}
 
 	if ($reply_success == "success") {
		if ($reply_error == "error") echo "error";
	 	else echo "success";
 	}
 	else echo "error";
 }