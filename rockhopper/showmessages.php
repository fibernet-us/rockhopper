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
		<h3>Your messages list:</h3>
        <p>Search: <input id="filter" type="text"/>
		   <a href="#clear" class="clear-filter" title="clear filter">[clear]</a>
        </p>
        
        <?php if($curUser) {
			$userid = $curUser->getId();
			$mymsg = Message::getMessageByUserId($dbh, $userid);
			
			$newReceiver = '';
			$newTitle = '';
			$newMessage = '';
			
			if(empty ($mymsg)) {
				echo 'You have no message.  ';
				echo '<button class="btn btn-info" data-toggle="modal" data-target="#myModal"><i class="icon-plus icon-white"></i> COMPOSE</button>';
			}
			else { ?>
              
            
              <table class="table footable demo" id="message_table" data-filter="#filter" data-page-size="5">
              <!-- th must be the same as those in the databash -->
              
                <thead>
                  <tr>
                    <th data-sort-ignore="true" data-toggle="true" class="span4">Title</th>
                    <th data-sort-ignore="true" class="span1">Replies</th> 
                    <th data-hide="phone" class="span2">From</th>
                    <th data-hide="phone" class="span2">To</th>
                    <th data-hide="phone" class="span2">Date of Creation</th>
                    <th data-sort-ignore="true" data-hide="phone" class="span1"></th>
                  </tr>
                </thead>
                
                <tbody>
				  <?php foreach($mymsg as $singlemessage) {
					  $from_user = User::getUserById($dbh, $singlemessage->getFrom_id());
					  $to_user = User::getUserById($dbh, $singlemessage->getTo_id());
					  
					  if ($singlemessage->getRead_status() == 0 && $singlemessage->getTo_id() == $userid) {
					  	echo "<tr id =" .$singlemessage->getSerial_id(). ">";
					  	echo "<td onClick=\"window.location.href='readmessage.php?id=".$singlemessage->getSerial_id()."'\" style='cursor: pointer;'><b>".$singlemessage->getTitle()."</b></td>";
					  	echo "<td onClick=\"window.location.href='readmessage.php?id=".$singlemessage->getSerial_id()."'\" style='cursor: pointer;'><b>".($singlemessage->getRound_num()-1)."</b></td>";
					  	echo "<td onClick=\"window.location.href='readmessage.php?id=".$singlemessage->getSerial_id()."'\" style='cursor: pointer;'><b><div class='user_profile'><img src='".$from_user->getIconUrl()."'> ".$from_user->getUsername()."</div></b></td>";
					  	echo "<td onClick=\"window.location.href='readmessage.php?id=".$singlemessage->getSerial_id()."'\" style='cursor: pointer;'><b><div class='user_profile'><img src='".$to_user->getIconUrl()."'> ".$to_user->getUsername()."</div></b></td>";
					  	echo "<td onClick=\"window.location.href='readmessage.php?id=".$singlemessage->getSerial_id()."'\" style='cursor: pointer;'><b>".$singlemessage->getCreation_ts()."</b></td>";
					  	echo "<td align='center'><input type='checkbox' name='chk' id='chk' value='".$singlemessage->getSerial_id()."'></td>";
					  	echo "</tr>";
					  }
					  
					  else {
					  	echo "<tr id =" .$singlemessage->getSerial_id(). ">";
					  	echo "<td onClick=\"window.location.href='readmessage.php?id=".$singlemessage->getSerial_id()."'\" style='cursor: pointer;'>".$singlemessage->getTitle()."</td>";
					  	echo "<td onClick=\"window.location.href='readmessage.php?id=".$singlemessage->getSerial_id()."'\" style='cursor: pointer;'>".($singlemessage->getRound_num()-1)."</td>";
					 	echo "<td onClick=\"window.location.href='readmessage.php?id=".$singlemessage->getSerial_id()."'\" style='cursor: pointer;'><div class='user_profile'><img src='".$from_user->getIconUrl()."'> ".$from_user->getUsername()."</div></td>";
					  	echo "<td onClick=\"window.location.href='readmessage.php?id=".$singlemessage->getSerial_id()."'\" style='cursor: pointer;'><div class='user_profile'><img src='".$to_user->getIconUrl()."'> ".$to_user->getUsername()."</div></td>";
					 	echo "<td onClick=\"window.location.href='readmessage.php?id=".$singlemessage->getSerial_id()."'\" style='cursor: pointer;'>".$singlemessage->getCreation_ts()."</td>";
					  	echo "<td align='center'><input type='checkbox' name='chk' id='chk' value='".$singlemessage->getSerial_id()."'></td>";
					  echo "</tr>";
					  }
				  } ?>
                
                </tbody>
                <tfoot>
                  <tr>
                    <td><button id="add" class="btn" data-toggle="modal" data-target="#myModal"><i class="icon-plus"></i> New message</button><button class="btn" id="delete"><i class="icon-trash"></i> Delete </button></td> 
                    <td colspan="3">
                      <div class="pagination pagination-centered"></div>
                    </td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              </table>
			
              <?php 
			} ?>
            
			  <div style="background-color:white;" class="modal fade span8" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
				  <div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">New message</h4>
					</div>
					<div class="modal-body">
                      <div class="row-fluid">
                        <!--- no need to use form since we are using ajax -->
        				<div><span class="span1" style="margin-left:0px;">To:</span><input class="span11" type="text" value="" id="recip" name="recip" /></div>
                        <div><span class="span1" style="margin-left:0px;">Title:</span><input class="span11" type="text" value="" id="title" name="title" /></div>
						<div><label for="message">Message</label><textarea class="span12" rows="8" id="message" name="message"></textarea></div>
                        <div class="pull-left" id="alert_modal"></div>
                      </div>
      				</div>
      				<div class="modal-footer">
                        <!---The button has to be put in modal-footer to keep modal open and close as wish.-->
    					<button class="btn btn-primary" id="newMessage">Send</button>
      				</div>
    			  </div>
  				</div>
			  </div>
            
			<?php 
			
		}
		else {
			echo '<div>You must be logged to access this page.</div>';
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
			
            $('.clear-filter').click(function (e) {
                e.preventDefault();
                $('table.demo').trigger('footable_clear_filter');
				$('.filter-status').val('');
            });

            $('.filter-status').change(function (e) {
                e.preventDefault();
				var filter = $(this).val();
                $('#filter').val($(this).text());
                $('table.demo').trigger('footable_filter', {filter: filter});
            });	
			
			$('#newMessage').click(function (e) {
				var newRecord = {};
				newRecord.datatype = "newMessage";
				newRecord.newReceiver = $("#recip").val();
				newRecord.newTitle = $("#title").val();
				newRecord.newMessage = $("#message").val();
				if (newRecord.newReceiver !='' && newRecord.newTitle !='' && newRecord.newMessage !='') {
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
								$("#message_table").find('tbody').prepend(insertMessage);
                    		}
							else if (dat.serverResponsea == "norec") {
								$("#alert_message").html("The recipient does not exist.").css("color","red");
							}
							else {
								$("#alert_message").html("The action is wrong.").css("color","red");
							}
						}
					});
					$('#myModal').modal('hide');
				}
				else {
					$("#alert_modal").html("Please input recepient/title/message.").css("color","red");
				}
			});
			
			
			$('button[id="delete"]').click(function (e) {
				var selectItem = new Array();
        		$("input[name='chk']:checked").each(function() {
            		selectItem.push($(this).val());
				});
				
				if (selectItem.length == 0) {
            		$("#alert_message").html("Please select the messages to be deleted.").css("color","red");
				} 
				else {
					var newRecord = {};
					newRecord.datainfo = selectItem.join(",");
					newRecord.datatype = "deleteMessage";
					
					$.ajax({
                		type : "POST",
                		url : "message_ajax.php",
						contentType: "application/json; charset=utf-8",
						data : JSON.stringify(newRecord),
                		//data : 'items=' + selectItem.join(","),
                		success : function(data) {
                    		if (data == "success") {
								$("#alert_message").html("Message(s) deleted.").css("color","green");
                        		$("input[name='chk']:checked").each(function() {
                            		$(this).parent().parent().remove();
                        		});
                    		}
							else if (data == "error") {
								$("#alert_message").html("The action is wrong.").css("color","red");
							}
							else {
								$("#alert_message").html("The message(s) has already been deleted.").css("color","red");
							}
						}
					});
				}
				
			});
        });
    </script>

</body>