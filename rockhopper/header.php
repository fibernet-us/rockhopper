    
<header>
 
<div class="container">
<div class="navbar navbar_ clearfix">
<div class="navbar-inner navbar-inner_">

<div class="container">
    <a class="brand" href="#">Rockhopper</a>
    
    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    
    <div class="nav-collapse collapse">
        <ul class="nav">
        <li><a href="index.php">Home</a></li>
        <li><a href="">Dashboard</a></li>
        <li><a href="">Task</a></li>
        <li><a href="">Sprint</a></li>
        <li><a href="showmessages.php">Message</a></li>
        <li><a href="showprojects.php">Project</a></li>
        <li><a href="showusers.php">User</a></li>
        </ul>
    </div>
    
    <?php 
    require_once 'tracking.php';
    
    $curUser = doAutoLogin($dbh);
    if($curUser) { ?>
        <li class="dropdown pull-right navbar-text user_profile">
        	<img src="<?php echo $curUser->getIconUrl() ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color: #e7e5e5"> <?php echo $curUser->getUsername() ?> <span class="caret"></span></a>
      		<ul class="dropdown-menu">
        		<li><a href="showprofile.php"><i class="icon-edit"></i>Profile</a></li>
        		<li><a href="logout.php"><i class="icon-off"></i>Logout</a></li>
         	</ul>
        </li>
    <?php } ?>

</div>

</div>
</div>
</div>

</header>
