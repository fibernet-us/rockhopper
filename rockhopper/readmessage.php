<?php
require_once 'message.php';
?>

<!DOCTYPE html>

<head>
<title>Rockhopper Project Page</title>
<meta name="description" content="a project management system for Scrum">
<meta name="keywords" content="Rockhopper, Scrum, project management">
<meta http-equiv="author" content="estel">
<link rel="stylesheet" href="css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="css/footable.core.css" type="text/css">
<link href="css/footable.editable-1.0.css" rel="stylesheet" type="text/css" >
<link rel="stylesheet" href="css/style.css" type="text/css">
<!--script type="text/javascript" src="js/jquery.min.js"></script-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/footable.js"></script>
  
</head>

<body>

<?php
require_once 'header.php';
?>

<section id="message_content">
  <div class="container">
    <div class="row">
      <div class="span12"> 
        <h4>Your selected message:</h4>
        
        <?php
        //check if the user is logged 
		if($curUser) {
			$userid = $curUser->getId();
			//check if the ID of the discussion is defined
			if(isset($_GET['id'])) {
				$id = $_GET['id'];
				$messages = Message::getMessageBySerial_id($dbh, $id);
				$last_message = $messages[0];
				if($last_message) {
					//check if the user have the right to read this discussion
					if($last_message->getFrom_id() == $userid or $last_message->getTo_id() == $userid){
						
						//update read_status
						if ($last_message->getTo_id() == $userid) {
							$last_message->setRead_status(1);
						}
						
						$rev_messages = array_reverse($messages); ?>
                        
						<div>
                            <span style="font-size:18px;"><b>Title: </b><?php echo $last_message->getTitle(); ?></span>
                            <button class="btn inline-block pull-right" id="delete" value="<?php echo $id; ?>"><i class="icon-trash"></i><span class="left_padding"> Delete this discussion</button> 
                            <button onclick="window.location.href='showmessages.php'" class="btn inline-block pull-right" id="back" style='margin-right:5px;'><i class="icon-arrow-left"></i><span class="left_padding"> Back to messages list</button> 
                            <table id="message_table" class="table footable demo" data-page-size="5">
                                <thead>
									<tr>
    									<th class="span2">From:</th>
    									<th class="span2">To:</th>
       									<th class="span8">Message:</th>
    								</tr>
                                </thead>
                                <tbody>
                                    <?php foreach($rev_messages as $singlemessage) {
										$from_user = User::getUserById($dbh, $singlemessage->getFrom_id());
										$to_user = User::getUserById($dbh, $singlemessage->getTo_id());
										echo "<tr>";
										echo "<td><div class='user_profile'><img src='".$from_user->getIconUrl()."'> ".$from_user->getUsername()."</td>";
										echo "<td><div class='user_profile'><img src='".$to_user->getIconUrl()."'> ".$to_user->getUsername()."</td>";
										echo "<td><div class='pull-right'><i>Sent: ".$singlemessage->getCreation_ts()."</i></div>".$singlemessage->getMessage()."</td>";
									} ?>
                                </tbody>
                                <tfoot>
                  					<tr>
                    			      <td colspan="3">
                                        <div class="pagination pagination-centered"></div>
                                      </td>
                 					 </tr>
                				</tfoot>
                            </table>
                                
                            <h4>Reply:</h4>
                            <div>
                                <textarea class="span12" rows="8" name="message" id="message"></textarea><br />
                                <button class="btn btn-primary" id="newReply" listId="<?php echo $id; ?>">Send</button>
                            </div>
                        </div>	
					
                    <?php }
					else {
						echo '<div id="alert_message">You dont have the rights to access this page.</div>';
					}
				}
				else {
					echo '<div id="alert_message"> This discussion does not exists.</div>';
				}
			}
			else {
				echo '<div id="alert_message">The discussion ID is not defined.</div>';
			}
		} 
		else {
			echo '<div id="alert_message">You must be logged to access this page.</div>';
		} ?>
        
      </div>
    </div>
    
  	<div  class="span6">  
      <div id="alert_message">
      </div>
 	</div>
  </div>
  
</section>
    <script type="text/javascript">
        $(function () {
			$('table').footable();
			
			$('#newReply').click(function (e) {
				var newRecord = {};
				newRecord.datatype = "newReply";
				newRecord.newId = $('#newReply').attr('listId');
				newRecord.newMessage = $("#message").val();
				
				if (newRecord.newMessage !='') {
					$.ajax({
                		type : "POST",
                		url : "message_ajax.php",
						contentType: "application/json; charset=utf-8",
						data:  JSON.stringify(newRecord),
                		success : function(data) {
							data = JSON.parse(data);
                    		if (data.serverResponse == "success") {
								$("#alert_message").html("Message has been sent.").css("color","green");
								var insertMessage = data.insertString;
								$("#message_table").find('tbody').append(insertMessage);
                    		}
							else if (data.serverResponsea == "norec") {
								$("#alert_message").html("The recipient does not exist.").css("color","red");
							}
							else {
								$("#alert_message").html("The action is wrong.").css("color","red");
							}
						}
					});
				}
				else {
					$("#alert_message").html("Please input message.").css("color","red");
				}
			});
			
			$('button[id="delete"]').click(function (e) {
				var del_id = $(this).attr('value');
				
				if (del_id) {
					var selectItem = new Array();
					selectItem.push(del_id);
					
					var newRecord = {};
					newRecord.datainfo = selectItem.join(",");
					newRecord.datatype = "deleteMessage";
					
					
					$.ajax({
                		type : "POST",
                		url : "message_ajax.php",
						contentType: "application/json; charset=utf-8",
						data : JSON.stringify(newRecord),
                		success : function(data) {
                    		if (data == "success") {
                        		alert("The message has been deleted.");
                       			window.location.href="showmessages.php";
                    		}
							else if (data == "error") {
								$("#alert_message").html("The action is wrong.").css("color","red");
							}
							else {
								$("#alert_message").html("The message has already been deleted.").css("color","red");
							}
						}
					});
				} 
				else {
					$("#alert_message").html("This discussion does not exists.").css("color","red");
				}
			});
			
					
        });
    </script>
</body>