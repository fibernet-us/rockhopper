    
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
        <li><a href="">Home</a></li>
        <li><a href="">Project</a></li>
        <li><a href="">Sprint</a></li>
        <li><a href="">Backlog</a></li>
        <li><a href="">Team</a></li>
        <li><a href="showusers.php">User</a></li>
        </ul>
    </div>
    
    <?php 
    require_once 'tracking.php';
    
    $curUser = doAutoLogin($dbh);
    if($curUser) {
        echo "<li class=\"dropdown pull-right navbar-text user_profile\">";
        echo "<img src=\"" . $curUser->getIconUrl() . "\">";
        echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" style=\"color: #e7e5e5\">";
        echo $curUser->getUsername();
        echo "<span class=\"caret\"></span></a>";
      
        echo "<ul class=\"dropdown-menu\">";
        echo "<li><a href=\"showprofile.php\"><i class=\"icon-edit\"></i>Profile</a></li>";
        echo "<li><a href=\"logout.php\"><i class=\"icon-off\"></i>Logout</a></li>";
        echo "</ul>";
        echo "</li>";
    }
    
    ?>

</div>

</div>
</div>
</div>

</header>
