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
        <h3>Projects</h3>
        
        
        <p>Search: <input id="filter" type="text"/>
           Status: <select class="filter-status">
				   <option></option>
				   <option value="active">Active</option>
				   <option value="completed">Completed</option>
				   <option value="suspended">Suspended</option>
				   <option value="not started">Not Started</option>
                   </select>
		   <a href="#clear" class="clear-filter" title="clear filter">[clear]</a>
        </p>
        
         <table class="table footable demo" id="project_table" data-filter="#filter" data-page-size="5" server-table="RH_USER">
          <thead>
            <tr>
              <th class="fooId" data-toggle="true">Name</th>
              <th data-type="option" data-option="Completed:Active:Not Started" option-value="0:1:2">Status</th>
              <th data-type="integer" data-sort-ignore="true" data-hide="phone,tablet">Est.Effort</th>
              <th data-type="integer" data-sort-ignore="true" data-hide="phone,tablet">Act.Effort</th>
              <th data-sort-ignore="true" data-hide="phone">Team Leader</th>
              <th data-type="integer" data-sort-ignore="true" data-hide="phone,tablet">No. of Items</th>
              <th data-type="date" data-hide="phone,tablet">Start Date</th>
              <th data-type="date" data-hide="phone,tablet">End Date</th>
              <th data-type="progress" data-sort-ignore="true" data-hide="phone">%Completed</th>
              <th data-sort-ignore="true" data-hide="phone" data-ft-buttons="both"></th>
            </tr>
          </thead>
          
          <tbody>
            <tr>
              <td>Bugzillar Original</td>
              <td data-value="2"><span>Completed</span></td>
              <td>500</td>
              <td>600</td>
              <td>
                <div class="user_profile">
                  <!--<img src="img/photo2.jpg"> Hao Wu-->
                  Hao Wu
                </div>
              </td>
              <td>100</td>
              <td data-value="1265004726078">2010-01-01</td>
              <td data-value="1359654826010">2012-12-31</td>
              <td>
                <div class="progress progress-success progress-striped">
                  <div class="bar" style="width: 100%">100%</div>
                </div>
              </td>
            </tr>
            <tr>
              <td>Rockhopper Extension</td>
              <td data-value="1"><span>Active</span></td>
              <td>200</td>
              <td>54</td>
              <td>
                <div class="user_profile">
                  Bian Wen
                </div>
              </td>
              <td>3</td>
              <td data-value="1373909209284">2013-06-15</td>
              <td data-value="1380648441114">2013-08-31</td>
              <td>
                <div class="progress progress-success progress-striped">
                  <div class="bar" style="width: 21%">21%</div>
                </div>
              </td>
            </tr>
            <tr>
              <td>Rockhopper Android</td>
              <td data-value="4"><span>Not Started</span></td>
              <td>20</td>
              <td></td>
              <td>
                <div class="user_profile">
                  Bian Wen
                </div>
              </td>
              <td>3</td>
              <td data-value="1380651010907">2013-01-09</td>
              <td></td>
              <td>
                <div class="progress progress-success progress-striped">
                  <div class="bar" style="width: 0%">0%</div>
                </div>
              </td>
            </tr>
            <tr>
              <td>Rockhopper iOS</td>
              <td data-value="4"><span>Not Started</span></td>
              <td>10</td>
              <td></td>
              <td>
                <div class="user_profile">
                  Chao Shu
                </div>
              </td>
              <td>3</td>
              <td data-value="1381515097043">2013-09-11</td>
              <td></td>
              <td>
                <div class="progress progress-success progress-striped">
                  <div class="bar" style="width: 0%">0%</div>
                </div>
              </td>
            </tr>
            <tr>
              <td>puzzle arena</td>
              <td data-value="3"><span>Suspended</span></td>
              <td>200</td>
              <td></td>
              <td>
                <div class="user_profile">
                  Estel Zhao
                </div>
              </td>
              <td>7</td>
              <td data-value="1371322048556">2013-05-15</td>
              <td data-value="1387136864252">2013-11-15</td>
              <td>
                <div class="progress progress-success progress-striped">
                  <div class="bar" style="width: 30%">30%</div>
                </div>
              </td>
            </tr>
            <tr>
              <td>puzzle/dragons</td>
              <td data-value="2"><span>Completed</span></td>
              <td>500</td>
              <td>600</td>
              <td>
                <div class="user_profile">
                  Chao Shu
                </div>
              </td>
              <td>100</td>
              <td data-value="1277146197021">2010-05-01</td>
              <td data-value="1367347838525">2013-03-30</td>
              <td>
                <div class="progress progress-success progress-striped">
                  <div class="bar" style="width: 100%">100%</div>
                </div>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td><button class="btn" type="button" value="Add"><i class="icon-plus-sign"></i> New</button></td>
              <!-------- 为了平衡分页符居中 ------------->
              <td colspan="8">
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
  
  
  <div class="container">
    <div class="row">
      <div class="span12">
        <h3>Messages</h3>
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
</html>