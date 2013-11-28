<?php
require_once 'tracking.php';
     
$curUser = doAutoLogin($dbh);

// update user's profile 
if($_POST['submit'] == 'Update Profile') {

    if($_POST['fullname'] && $_POST['email'] && $_POST['timezone']) {
        $_POST['fullname'] = safe_var($_POST['fullname']);
        $_POST['email'] = safe_var($_POST['email']);
        $_POST['type'] = safe_var($_POST['type']);
        $_POST['status'] = safe_var($_POST['status']);
		$curUser->setFullname($_POST['fullname']);
		$curUser->setEmail($_POST['email']);
		$curUser->setType($_POST['type']);
		$curUser->setStatus($_POST['status']);
		$curUser->setTimezone($_POST['timezone']);
        $_SESSION['msg']['updateprofile-success'] = 'Your profile is updated!';
					
	}
}

// change user's password
if($_POST['submit'] == 'Change Password') {

    if($_POST['oldpwd'] && $_POST['newpwd'] && $_POST['newcpwd']) {
        $_POST['oldpwd'] = safe_var($_POST['oldpwd']);
        $_POST['newpwd'] = safe_var($_POST['newpwd']);
		
		$changeResult = $curUser->changePassword($_POST['oldpwd'], $_POST['newpwd']);
        if ($changeResult==false)
			$_SESSION['msg']['changepwd-err'] = 'Please input the correct password.';
		else
			$_SESSION['msg']['changepwd-success'] = 'Password is changed!';
					
	}
}

?>

<!DOCTYPE html>

<head>
<title>Rockhopper Project Page</title>
<meta name="description" content="a project management system for Scrum">
<meta name="keywords" content="Rockhopper, Scrum, project management">
<meta http-equiv="author" content="estel">
<link rel="stylesheet" href="css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">
<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css">
<!--script type="text/javascript" src="js/jquery.min.js"></script-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript"></script>
<script src="http://demos.9lessons.info/ajaximageupload/scripts/jquery.form.js" type="text/javascript"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/jquery.validationEngine.js"></script>
<script type="text/javascript" src="js/jquery.validationEngine-en.js"></script>

<!---------for the photo upload using------------->
<script type="text/javascript" >

//upload user's icon dynamically
 $(document).ready(function() {
	 $('#photoimg').live('change', function() {
		 $("#preview").html('');
		 $("#preview").html('<img src="img/loader.gif" alt="Uploading...."/>');
		 $("#imageform").ajaxForm({
			 target: '#preview',
		 }).submit();
	 });
 }); 


//register forms that need to be input-validated
$(document).ready(function(){
    $("#profileForm").validationEngine();
    $("#passwordForm").validationEngine();
   });
</script>
</head>

<body>
<?php
require_once 'header.php';
?>
<section>
<div class="container">
  <div class="row">
    <div class="span6">
    
    
      <h4 style='text-align: left; margin-top: 40px;'><?php echo $curUser->getUsername() ?>'s photo</h4>
      <!--------------------------------
      <form  class="form-horizontal">
        <div class="control-group">
          <label class="control-label">Choose default</label>
          <div class="controls">
            <select name="user_face" onchange="document.images['idface'].src=options[selectedIndex].value;">
              <option value="img/photo.jpg">photo0</option>
              <option value="img/photo1.jpg">photo1</option>
              <option value="img/photo2.jpg">photo2</option>
              <option value="img/photo3.jpg">photo3</option>
            </select>
          </div>
        </div>
      </form>
      ------------------------------------->
      
      <form id="imageform" method="post" enctype="multipart/form-data" action='uploadicon.php'>
        Upload your image
        <input type="file" name="photoimg" id="photoimg" />
        <br/>
        <div style='font-size:11px'>Max 256 KB JPG, PNG, GIF, JPEG and BMP</div>
      </form>
      <div class="span1 img-polaroid" id="preview" style="width:60px; height:60px;"><img src="<?php echo $curUser->getIconUrl() ?>" id="idface"></div>
    </div>
  </div>
      
      
  <div class="row">
    <div class="span6">
      <h4 style='text-align: left; margin-top: 30px;'><?php echo $curUser->getUsername() ?>'s profile</h4>
      <form class="form-horizontal" id="profileForm" method="post" action="showprofile.php">
      
        <div class="control-group">
          <label class="control-label">Real Name</label>
          <div class="controls">
            <input type="text" name="fullname" value="<?php echo $curUser->getFullname() ?>" class="input-xlarge" maxlength="20">
          </div>
        </div>
        
        <div class="control-group">
          <label class="control-label">Email</label>
          <div class="controls">
            <input type="text" name="email" value="<?php echo $curUser->getEmail() ?>" class="input-xlarge validate[required,custom[email]] text-input" maxlength="40">
          </div>
        </div>
        
        <div class="control-group">
          <label class="control-label">Type</label>
          <div class="controls">
            <select name="type" class="input-xlarge">
              <option value="0" <?php echo $curUser->getType()==0?'selected="selected"':'' ?>>Unknown</option>
              <option value="1" <?php echo $curUser->getType()==1?'selected="selected"':'' ?>>Admin</option>
              <option value="2" <?php echo $curUser->getType()==2?'selected="selected"':'' ?>>Project Owner</option>
              <option value="3" <?php echo $curUser->getType()==3?'selected="selected"':'' ?>>Dev</option>
              <option value="4" <?php echo $curUser->getType()==4?'selected="selected"':'' ?>>Scrum Master</option>
              <option value="5" <?php echo $curUser->getType()==5?'selected="selected"':'' ?>>Chicken</option>
            </select>
          </div>
        </div>
        
        <div class="control-group">
          <label class="control-label">Status</label>
          <div class="controls">
            <select name="status" class="input-xlarge">
              <option value="0" <?php echo $curUser->getType()==0?'selected="selected"':'' ?>>Unknown</option>
              <option value="1" <?php echo $curUser->getType()==1?'selected="selected"':'' ?>>Working</option>
              <option value="2" <?php echo $curUser->getType()==2?'selected="selected"':'' ?>>Idle</option>
              <option value="3" <?php echo $curUser->getType()==3?'selected="selected"':'' ?>>On leave</option>
              <option value="4" <?php echo $curUser->getType()==4?'selected="selected"':'' ?>>Left</option>
            </select>
          </div>
        </div>
        
        <div class="control-group">
          <label class="control-label">Time Zone</label>
          <div class="controls">
            <select name="timezone" class="input-xlarge">
              <option value="-12.0" <?php echo $curUser->getTimezone()==-12?'selected="selected"':'' ?>>(GMT -12:00) Eniwetok, Kwajalein</option>
              <option value="-11.0" <?php echo $curUser->getTimezone()==-11?'selected="selected"':'' ?>>(GMT -11:00) Midway Island, Samoa</option>
              <option value="-10.0" <?php echo $curUser->getTimezone()==-10?'selected="selected"':'' ?>>(GMT -10:00) Hawaii</option>
              <option value="-9.0" <?php echo $curUser->getTimezone()==-9?'selected="selected"':'' ?>>(GMT -9:00) Alaska</option>
              <option value="-8.0" <?php echo $curUser->getTimezone()==-8?'selected="selected"':'' ?>>(GMT -8:00) Pacific Time (US & Canada)</option>
              <option value="-7.0" <?php echo $curUser->getTimezone()==-7?'selected="selected"':'' ?>>(GMT -7:00) Mountain Time (US & Canada)</option>
              <option value="-6.0" <?php echo $curUser->getTimezone()==-6?'selected="selected"':'' ?>>(GMT -6:00) Central Time (US & Canada), Mexico City</option>
              <option value="-5.0" <?php echo $curUser->getTimezone()==-5?'selected="selected"':'' ?>>(GMT -5:00) Eastern Time (US & Canada), Bogota, Lima</option>
              <option value="-4.0" <?php echo $curUser->getTimezone()==-4?'selected="selected"':'' ?>>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
              <option value="-3.0" <?php echo $curUser->getTimezone()==-3?'selected="selected"':'' ?>>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
              <option value="-2.0" <?php echo $curUser->getTimezone()==-2?'selected="selected"':'' ?>>(GMT -2:00) Mid-Atlantic</option>
              <option value="-1.0" <?php echo $curUser->getTimezone()==-1?'selected="selected"':'' ?>>(GMT -1:00 hour) Azores, Cape Verde Islands</option>
              <option value="0.0" <?php echo $curUser->getTimezone()==0?'selected="selected"':'' ?>>(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
              <option value="1.0" <?php echo $curUser->getTimezone()==1?'selected="selected"':'' ?>>(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
              <option value="2.0" <?php echo $curUser->getTimezone()==2?'selected="selected"':'' ?>>(GMT +2:00) Kaliningrad, South Africa</option>
              <option value="3.0" <?php echo $curUser->getTimezone()==3?'selected="selected"':'' ?>>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
              <option value="4.0" <?php echo $curUser->getTimezone()==4?'selected="selected"':'' ?>>(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
              <option value="5.0" <?php echo $curUser->getTimezone()==5?'selected="selected"':'' ?>>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
              <option value="6.0" <?php echo $curUser->getTimezone()==6?'selected="selected"':'' ?>>(GMT +6:00) Almaty, Dhaka, Colombo</option>
              <option value="7.0" <?php echo $curUser->getTimezone()==7?'selected="selected"':'' ?>>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
              <option value="8.0" <?php echo $curUser->getTimezone()==8?'selected="selected"':'' ?>>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
              <option value="9.0" <?php echo $curUser->getTimezone()==9?'selected="selected"':'' ?>>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
              <option value="10.0" <?php echo $curUser->getTimezone()==10?'selected="selected"':'' ?>>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
              <option value="11.0" <?php echo $curUser->getTimezone()==11?'selected="selected"':'' ?>>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
              <option value="12.0" <?php echo $curUser->getTimezone()==12?'selected="selected"':'' ?>>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
            </select>
          </div>
        </div>
        <div>
          <button type="submit" name="submit" value="Update Profile" class="btn btn-primary pull-right">Update</button>
        </div>
      </form>
      
      
      <h4 style='text-align: left; margin-top: 30px;'>Change password:</h4>
      <form  class="form-horizontal" id="passwordForm" method="post" action="showprofile.php">
        <div class="control-group">
          <label class="control-label">Old Password</label>
          <div class="controls">
            <input type="password" value="" name="oldpwd" class="validate[required] input-xlarge" maxlength="20">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">New Password</label>
          <div class="controls">
            <input type="password" value="" name="newpwd" class="input-xlarge validate[required] text-input"  id="newPassword" maxlength="20">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Repeat Password</label>
          <div class="controls">
            <input type="password" value="" name="newcpwd" class="input-xlarge validate[required,equals[newPassword]] text-input" maxlength="20">
          </div>
        </div>
        <div>
          <button type="submit" name="submit" value="Change Password" class="btn btn-primary pull-right">Change Password</button>
        </div>
      </form>
    </duv>
    
    <div  class="span6">  
      <div class="errmessage">
        <?php
              
                if($_SESSION['msg']['changepwd-success']) {
                    echo '<p style="color: green;">'.$_SESSION['msg']['changepwd-success'].'</p>';
                    unset($_SESSION['msg']['changepwd-success']);
                }
                 
                if($_SESSION['msg']['changepwd-err']) {
                    echo $_SESSION['msg']['changepwd-err'];
                    unset($_SESSION['msg']['changepwd-err']);
                }
                 
                if($_SESSION['msg']['updateprofile-success']) {
                    echo '<p style="color: green;">'.$_SESSION['msg']['updateprofile-success'].'</p>';
                    unset($_SESSION['msg']['updateprofile-success']);
                }
                
              ?>
      </div>
      
    </div>
  </div>
</div>
</section>
<footer> </footer>
</body>
</html>