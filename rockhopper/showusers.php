<!DOCTYPE html>

<head>
<title>Rockhopper Project Page</title>
<meta name="description" content="a project management system for Scrum">
<meta name="keywords" content="Rockhopper, Scrum, project management">
<meta http-equiv="author" content="estel">
<link rel="stylesheet" href="css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="css/footable.core.css" type="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">
<link href="css/footable.editable-1.0.css" rel="stylesheet" type="text/css" >
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

<section id="project_content">
  <div class="container">
    <div class="row">
      <div class="span12">      
        <p style='margin-top: 20px;'>Search: <input id="filter" type="text"/></p>
        
        <table class="table footable demo" data-filter="#filter" data-page-size="5">
          <thead>
            <tr>
              <th class="fooId" data-type ="uneditable" data-hide="phone,tablet">Id</th>
              <th data-sort-ignore="true" data-toggle="true">Username</th>
              <th data-sort-ignore="true" data-hide="phone">Fullname</th>
              <th data-sort-ignore="true" data-hide="phone">Email</th>
              <th data-sort-ignore="true" data-hide="phone">Type</th>
              <th data-hide="phone,tablet">Status</th>
              <th data-hide="phone,tablet">Location</th>
              <th data-hide="phone,tablet">Timezone</th>
              <th data-sort-ignore="true" data-hide="phone" data-ft-buttons="True"></th>
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
                        echo "<td>" . $user->getTypeString() . "</td>";
                        echo "<td>" . $user->getStatusString() . "</td>";
                        echo "<td>" . $user->getLocation() . "</td>";
                        echo "<td>" . $user->getTimezone() . "</td>";
                        echo "</tr>";
                    }
                }         
            ?>
          </tbody>
          <tfoot>
            <tr>
              <td><button class="btn" type="button" value="Add"><i class="icon-plus-sign"></i><span class="left_padding">New</button></td>
              <!-------- 为了平衡分页符居中 ------------->
              <td colspan="7">
                <div class="pagination pagination-centered"></div>
              </td>
              <!-------- 为了平衡分页符居中 --------------->
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
  
  
</section>

</body>
</html>
