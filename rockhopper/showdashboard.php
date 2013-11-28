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
<script src="js/footable.editable.js" type="text/javascript"></script>

  <script type="text/javascript">
    $(function() {
      $('table').footable();
    });
  </script>


</head>

<body>

<?php
require_once 'header.php';
?>

<div class="container" style="padding-top: 30px;">
  <div class="row">
    <div class="span6">
      <table class="table table-bordered table-condensed">
        <tr class="success">
          <td><b>TASKS</b></td>
        </tr>
        
        <tr>
          <td>
            <table class="table footable demo" data-filter="#filter" data-page-size="5">
              <thead>
                <tr>
                  <th>Username</th>
                  <th>Fullname</th>
                  <th>Email</th>
                  <th>Timezone</th>
                </tr>
              </thead>
          
              <tbody>
                <?php            
                  $users = $curUser->getUsers();
                  if($users) {
                    foreach($users as $user) {
                        echo "<tr>"; 
                        echo "<td>" . $user->getUsername() . "</td>";
                        echo "<td>" . $user->getFullname() . "</td>";
                        echo "<td>" . $user->getEmail() . "</td>";
                        echo "<td>" . $user->getTimezone() . "</td>";
                        echo "</tr>";
                    }
                  }         
                ?>
              </tbody>
              
              <tfoot>
                <tr>
                  <td colspan="4">
                    <div class="pagination pagination-centered"></div>
                  </td>
                </tr>
              </tfoot>
             </table>
            </td>
          </tr> 
      </table>
    </div>
    
    <div class="span6">
      <table class="table table-bordered table-condensed">
        <tr class="success">
          <td><b>SPRINTS</b></td>
        </tr>
        
        <tr>
          <td>
            <table class="table footable demo" data-filter="#filter" data-page-size="5">
              <thead>
                <tr>
                  <th>Username</th>
                  <th>Fullname</th>
                  <th>Email</th>
                  <th>Timezone</th>
                </tr>
              </thead>
          
              <tbody>
                <?php            
                  $users = $curUser->getUsers();
                  if($users) {
                    foreach($users as $user) {
                        echo "<tr>"; 
                        echo "<td>" . $user->getUsername() . "</td>";
                        echo "<td>" . $user->getFullname() . "</td>";
                        echo "<td>" . $user->getEmail() . "</td>";
                        echo "<td>" . $user->getTimezone() . "</td>";
                        echo "</tr>";
                    }
                  }         
                ?>
              </tbody>
              
              <tfoot>
                <tr>
                  <td colspan="4">
                    <div class="pagination pagination-centered"></div>
                  </td>
                </tr>
              </tfoot>
             </table>
            </td>
          </tr> 
      </table>
    </div>
  </div>
  
  <div class="row">
    <div class="span6">
      <table class="table table-bordered table-condensed">
        <tr class="success">
          <td><b>PROJECTS</b></td>
        </tr>
        
        <tr>
          <td>
            <table class="table footable demo" data-filter="#filter" data-page-size="5">
              <thead>
                <tr>
                  <th>Username</th>
                  <th>Fullname</th>
                  <th>Email</th>
                  <th>Timezone</th>
                </tr>
              </thead>
          
              <tbody>
                <?php            
                  $users = $curUser->getUsers();
                  if($users) {
                    foreach($users as $user) {
                        echo "<tr>"; 
                        echo "<td>" . $user->getUsername() . "</td>";
                        echo "<td>" . $user->getFullname() . "</td>";
                        echo "<td>" . $user->getEmail() . "</td>";
                        echo "<td>" . $user->getTimezone() . "</td>";
                        echo "</tr>";
                    }
                  }         
                ?>
              </tbody>
              
              <tfoot>
                <tr>
                  <td colspan="4">
                    <div class="pagination pagination-centered"></div>
                  </td>
                </tr>
              </tfoot>
             </table>
            </td>
          </tr> 
      </table>
    </div>
    
    
    <div class="span6">
      <table class="table table-bordered table-condensed">
        <tr class="success">
          <td><b>MESSAGES</b></td>
        </tr>
        
        <tr>
          <td>
            <table class="table footable demo" data-filter="#filter" data-page-size="5">
              <thead>
                <tr>
                  <th>Username</th>
                  <th>Fullname</th>
                  <th>Email</th>
                  <th>Timezone</th>
                </tr>
              </thead>
          
              <tbody>
                <?php            
                  $users = $curUser->getUsers();
                  if($users) {
                    foreach($users as $user) {
                        echo "<tr>"; 
                        echo "<td>" . $user->getUsername() . "</td>";
                        echo "<td>" . $user->getFullname() . "</td>";
                        echo "<td>" . $user->getEmail() . "</td>";
                        echo "<td>" . $user->getTimezone() . "</td>";
                        echo "</tr>";
                    }
                  }         
                ?>
              </tbody>
              
              <tfoot>
                <tr>
                  <td colspan="4">
                    <div class="pagination pagination-centered"></div>
                  </td>
                </tr>
              </tfoot>
             </table>
            </td>
          </tr> 
      </table>
    </div>
  </div>
</div>




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
</html>