<?php
include '../php/cookie.php';
?>

<!DOCTYPE html>

<head>
<title>Rockhopper login page</title>
<meta name="description" content="a project management system for Scrum">
<meta name="keywords" content="Rockhopper, Scrum, project management">
<meta http-equiv="author" content="estel">
<link rel="stylesheet" href="css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">
</head>

<body>
<header id="index_header">
  <div class="container">
    <div class="row">
      <div class="span12">
        <div class="header_block clearfix">
          <div class="logo_block clearfix">
            <h1><a href="index.php">Rock<span class="logo_colour">hopper</span></a></h1>
          </div>
          
          <div class="navbar navbar_ clearfix">
            <button class="btn btn_facebook pull-right">Connect with Facebook</button>
            <button class="btn btn_twitter pull-right">Connect with Twitter</button>
          </div>
        </div>
      </div>
    </div>
  </div> 
</header>

<section id="index_content">
  <div class="container">
    <div class="row">
      <div class="span6">
        <div class="discription">
          <h2>Rockhopper</h2>
          <p>Agile Software Development<br>
          Project Management<br>
          Team Collaboration<br><br>
          Based on Bugzilla</p>
        </div>
      </div>
      
 
      <div class="span6">
        <div class="discription">
      
       <?php
       				if($_SESSION['msg']['login-err'])
						{
							echo '<div class="err">'.$_SESSION['msg']['login-err'].'</div>';
							unset($_SESSION['msg']['login-err']);
						}
						
						if($_SESSION['msg']['reg-err'])
						{
							echo '<div class="err">'.$_SESSION['msg']['reg-err'].'</div>';
							unset($_SESSION['msg']['reg-err']);
						}
						
						if($_SESSION['msg']['reg-success'])
						{
							echo '<div class="success">'.$_SESSION['msg']['reg-success'].'</div>';
							unset($_SESSION['msg']['reg-success']);
						}
					?>
			   </div>
      </div>		
      
      <div class="span6">
        <div class="modal-body modal-body_compact">
            <ul class="nav nav-tabs nav-tabs_compact">
              <li class="active"><a href="#login" data-toggle="tab">Login</a></li>
              <li><a href="#create_account" data-toggle="tab">Create Account</a></li>
            </ul>
        
            <div class="tab-content tab-content_compact">
              <div class="tab-pane tab-pane_compact active in"  id="login">
                <form action="index.php#login" method="post">
                
				
                  <p><input type="text" name="username" placeholder="Username or Email" class="input-xlarge"></p>
                  <p><input type="password" name="password" placeholder="Password" class="input-xlarge"></p>
                  <label class="checkbox"><input type="checkbox">Remember me</label>
                  <input type="submit" name="submit" value="Login" class="btn pull-right" />
                </form>
                 <div>
               	 </div>
              </div>
          
              <div class="tab-pane tab-pane_compact fade" id="create_account">
                <form class="form-horizontal" id="tab" method="post" action="index.php#create_account">
	                   
						<div class="control-group">
	                <label class="control-label">Name</label>
	                <div class="controls">
	                  <input type="text" name="name">
	                </div>
                  </div>
                  
					<div class="control-group">
	                <label class="control-label">Username</label>
	                <div class="controls">
	                  <input type="text" name="username">
	                </div>
                  </div>
                  
                  <div class="control-group">
                    <label class="control-label">Email</label>
                    <div class="controls">
                      <input type="text" name="email">
                    </div>
                  </div>
                  
                  <div class="control-group">
                    <label class="control-label">Password</label>
                    <div class="controls">
                      <input type="password" name="pwd">
                    </div>
                  </div>
                  
                  <div class="control-group">
                    <label class="control-label">Confirm Password</label>
                    <div class="controls">
                      <input type="password" name="cpwd">
                    </div>
                  </div>
                  <input type="submit" name="submit" value="Create Account" class="btn pull-right" />
                                  </form>
                                  
                   
              </div>  
            </div>
            
        </div>
      </div>
                  
    </div>
  </div>
</section>
  
<footer> 
  <div class="container">
    <div class="row">
      <div class="span12">
        <div class="clearfix">Any suggestion, please contact Estel: estel.z@gmail.com.</div>
      </div>
    </div>
  </div>
</footer>
</body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
</html>