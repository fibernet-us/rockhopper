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
  
</head>

<body>

<?php
require_once 'header.php';
?>

<section id="project_content">
  <div class="container">
    <div class="row">
      <div class="span12"> 
      	<h3>Users</h3>
        
        <p>Search: <input id="filter" type="text"/>
           Type: <select class="filter-status">
				   <option></option>
				   <option value="unknown">Unknown</option>
				   <option value="admin">Admin</option>
				   <option value="project owner">Project owner</option>
				   <option value="developer">Developer</option>
				   <option value="scrum master">Scrum master</option>
				   <option value="chicken">Chicken</option>
                   </select>
		   <a href="#clear" class="clear-filter" title="clear filter">[clear]</a>
        </p>
        
        <table class="table footable demo" id="user_table" data-filter="#filter" data-page-size="5" server-table="RH_USER">
        <!-- th must be the same as those in the databash -->
          <thead>
            <tr>
              <th data-toggle="true">Username</th>
              <th data-sort-ignore="true" data-hide="phone, tablet">Fullname</th>
              <th data-sort-ignore="true" data-hide="phone">Email</th>
              <th data-type="option" data-option="unknown:admin:project owner:developer:scrum master:chicken" option-value="0:1:2:3:4:5">Type</th>
              <th data-sort-ignore="true" data-type="option" data-option="unknown:working:idle:on leave:left" option-value="0:1:2:3:4">Status</th>
              <th data-hide="phone,tablet">Location</th>
              <th data-hide="phone,tablet">Timezone</th>
              <th data-sort-ignore="true" data-hide="phone" data-ft-buttons="both"></th>
            </tr>
          </thead>
          
          <tbody>
            <?php            
                $users = $curUser->getUsers();
                if($users) {
                    foreach(array_reverse($users) as $user) {
						// Don't forget to add current Row's id
                        echo "<tr id =" .$user->getId(). ">"; 
                        echo "<td>" . $user->getUsername() . "</td>";
                        echo "<td>" . $user->getFullname() . "</td>";
                        echo "<td>" . $user->getEmail() . "</td>";
                        echo "<td>" . $user->getTypeString() . "</td>";
                        echo "<td>" . $user->getStatusString() . "</td>";
                        echo "<td>" . $user->getLocation() . "</td>";
                        echo "<td>" . $user->getTimezone() . "</td>";
						//if the buttons are at the end of row, no need to write <td></td> for buttons.
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
        });
    </script>



</body>
</html>
