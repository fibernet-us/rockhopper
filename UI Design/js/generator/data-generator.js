(function(w, undefined) {
  var projectNames = ['Bugzillar Original', 'Rockhopper Extension', 'Rockhopper Android', 'Rockhopper iOS', 'puzzle arena', 'Gwyn Consuelo', 'world of warcraft', 'Jesusa Bernie', 'Alvina Ilda'];
  var statuses = [{ 'name': 'Active', 'value': 1 }, { 'name': 'Completed', 'value': 2 }, { 'name': 'Suspended', 'value': 3 }, { 'name': 'Not Started', 'value': 4 }];
  var assignees = ['Wen Bian', 'Estel Zhao', '3', '11', '20', 'Shu Chao', 'Hao Wu', '7'];

function randomDate() {
    var start = new Date(2000, 9, 1), end = new Date(2013, 3, 1);
	var selectedDate=new Object();
	selectedDate.first = new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
	selectedDate.second = new Date(selectedDate.first.getTime() + Math.random() * 100000000000);
	return selectedDate;
}

  w.generateRows = function(rows, extraCols, return_rows) {
    rows = rows || 100;
    extraCols = extraCols || 0;
	return_rows = return_rows || false;
	var output = '';
    for (var i = 0; i < rows; i++) {
      var data = {
        name: projectNames[Math.floor(Math.random() * projectNames.length)],
        status: statuses[Math.floor(Math.random() * statuses.length)],
        assignee: assignees[Math.floor(Math.random() * assignees.length)],
		date: randomDate(),
		progress:parseInt(Math.random()*100)
      };
      var row = '<tr>';
	  
      row += '<td>' + data.name + '</td>';
      row += '<td data-value="' + data.status.value + '">' + data.status.name + '</td>';
      row += '<td>' + parseInt(Math.random()*500) + '</td>';
      row += '<td>' + parseInt(Math.random()*500) + '</td>';
      row += '<td>' + data.assignee + '</td>';
      row += '<td>' + parseInt(Math.random()*20) + '</td>';
      row += '<td data-value="' + data.date.first.getTime() + '">' + data.date.first.getMonth() + '/' + data.date.first.getDate() + '/' + data.date.first.getFullYear() +'</td>';
      row += '<td data-value="' + data.date.second.getTime() + '">' + data.date.second.getMonth() + '/' + data.date.second.getDate() + '/' + data.date.second.getFullYear() +'</td>';
      row += '<td class="progress progress-success progress-sriped"><div class="bar" style="width:' + data.progress + '%">' + data.progress + '%</div></div></td>';
	  
      for (var j = 0; j < extraCols; j++) {
        row += '<td>' + (i+1) + '.' + (j+1) + '</td>';
      }
      row += '</tr>';
	  if (!return_rows) document.writeln(row);
	  else
		output += row;
    }
	
	if (return_rows) return output;
  };
})(window);