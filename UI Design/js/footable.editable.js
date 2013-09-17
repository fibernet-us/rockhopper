/*!
 * FooTable Editable Plugin - Awesome Responsive FooTables That Are Editable
 * Version : 0.1
 * Author: Jake Drew - http://www.jakemdrew.com
 *
 * Requires jQuery - http://jquery.com/
 * Requires FooTable http://themergency.com/footable
 *
 * FooTable Editable Copyright 2013 Jake Drew
 *
 * Released under the MIT license
 * You are free to use FooTable Editable in commercial projects as long as this copyright header is left intact.
 *
 * Date: 2 Jul 2013
 */

(function ($, w, undefined) {
    if (w.footable == undefined || w.footable == null)
        throw new Error('Please check and make sure footable.js is included in the page and is loaded prior to this script.');

    var defaults = {
        serverTableName: undefined,
        dataHandlerURL: "demo",
		autoLoad: false,
        createDetail:
            function (element, data) {
                var groups = { '_none': { 'name': null, 'data': [] } };
                for (var i = 0; i < data.length; i++) {
                    var groupid = data[i].group;
                    if (groupid != null) {
                        if (!(groupid in groups))
                            groups[groupid] = { 'name': data[i].groupName, 'data': [] };

                        groups[groupid].data.push(data[i]);
                    } else {
                        groups._none.data.push(data[i]);
                    }
                }

                var table = $(element).closest('table');

                for (var group in groups) {
                    if (groups[group].data.length == 0) continue;
                    if (group != '_none') element.append('<h4>' + groups[group].name + '</h4>');

                    for (var j = 0; j < groups[group].data.length; j++) {
                        var separator = (groups[group].data[j].name) ? ':' : '';

                        //check for html input tags or img tags
						var tagTest = '<td>' + groups[group].data[j].display + '</td>';
						//if (groups[group].data[j].display.substring(0, 6) == "<input" ||
						//    groups[group].data[j].display.substring(0, 4) == "<img") {
						if($(tagTest).children().length > 0) {
                            element.append('<div><strong>' + groups[group].data[j].name + '</strong>' + separator + ' ' + groups[group].data[j].display + '</div>');
                        }
                        else {/*
                            if ($.data(w.footable, $(table).attr('id') + '_fooEditableCols').indexOf(groups[group].data[j].name) >= 0) {
                                element.append('<div><strong>' + groups[group].data[j].name + '</strong>' + separator + ' <input type="text" value="' + groups[group].data[j].display + '"/></div>');
                            }
                            else {*/
                                element.append('<div><strong>' + groups[group].data[j].name + '</strong>' + separator + ' <input type="text" readonly value="' + groups[group].data[j].display + '"/></div>');
                            /*}*/
                        }
                    }
                }
                if ($(element).text() == "") {
                    $(element).closest('tr').prev().removeClass('footable-detail-show');
                    $(element).closest('tr').remove();
                }
            },

            parsers: {
                numeric: function (cell) {
                    var val = $(cell).data('value') || $(cell).text().replace(/[^0-9.-]/g, '');
                    val = parseFloat(val);
                    if (isNaN(val)) val = 0;
                    return val;
                },
                JSONDate: function (cell) {
                    if (String(cell).substring(0, 6) == '/Date(') {
                        dt = new Date(parseInt(String(cell).substring(6)));
						d = dt.getDate(); m =  dt.getMonth() + 1; yy = dt.getFullYear();
						cell = m + '/' + d + '/' +  yy;
                    }
                    return cell;
                },
				prettyDate: function (cell) {
					var dt = Date.parse(cell);
                    if (isNaN(dt)==false) {
						dt = new Date(dt);
						d = dt.getDate(); m =  dt.getMonth(); yy = dt.getFullYear();
						cell = m + '/' + d + '/' +  yy;
                    }
                    return cell;
                }
            }
    };
	
	//取得有button的列值，初始化及添加新行的时候用到
    function getButtonIndexes(table) {
        var buttonIndexes = $.data(w.footable, $(table).attr('id') + '_buttonIndexes');
		
		//如果没有定义过，则创建。其他时候跳过，直接读取保存在tableID节点下的值
        if (buttonIndexes === undefined) {

            buttonIndexes = {};
            buttonIndexes.buttonCols = new Array();
			//这个值为以后添加新行的时候计算多加几个空白td做准备
            buttonIndexes.buttonColCt = 0;

            $(table).find('th').each(function (index) {
                var buttons = $(this).attr('data-ft-buttons');
                if (buttons != undefined) {
                    buttonIndexes.buttonCols.push(index);
                    buttonIndexes.buttonColCt++;
                }
            });

            $.data(w.footable, $(table).attr('id') + '_buttonIndexes', buttonIndexes);
        }
        return buttonIndexes;
    }
	
	//给整个table的添加button的程序，其中会调用每行加button的程序，初始化的时候用到
    function addFooRowButtons(table) {
        //This function does NOT add buttons to the footable-row-detail rows 
        //(see fooNewRecord-Populated bind below)
        var buttons = getButtonIndexes(table);
        if (buttons != undefined && $(this).is(":visible")) {
            $(table).find('> tbody > tr').not('.footable-row-detail').each(function () {
                addButtonsToRow(this, buttons);
            });
        }

    }
	
	//给某行添加button的程序，如果button标在每行最尾处，则每行html的<td></td>可省略，否则必须在相应列处加上<td></td>
	//buttons实为getButtonIndexes的结果
	function addButtonsToRow(row, buttons) {
		if ($(row).hasClass('fooNewRow')) {
          $(buttons.buttonCols).each(function (i) {
			//button在中间，数据行有<td></td>
            if ($(row).find('td').eq(buttons.buttonCols[i]).length > 0) {
                    if ($(row).find('td').eq(buttons.buttonCols[i]).find('button[value="Ok"]').length <= 0) {
                        $(row).find('td').eq(buttons.buttonCols[i]).append('<button class="btn" type="button" value="Ok">Ok</button>');
						
                    }
                    if ($(row).find('td').eq(buttons.buttonCols[i]).find('button[value="Cancel"]').length <= 0) {
                        $(row).find('td').eq(buttons.buttonCols[i]).append('<button class="btn" type="button" value="Cancel">Cancel</button>');
                    }
			//如果button在最结尾处，html可以不加<td></td>
            } else {
                $(row).append('<td><button class="btn" type="button" value="Ok">Ok</button><button class="btn" type="button" value="Cancel">Cancel</button></td>');
          }
        });
	  }
	  else {
        $(buttons.buttonCols).each(function (i) {
			//button在中间，数据行有<td></td>
            if ($(row).find('td').eq(buttons.buttonCols[i]).length > 0) {
                    if ($(row).find('td').eq(buttons.buttonCols[i]).find('button[value="Delete"]').length <= 0) {
                        $(row).find('td').eq(buttons.buttonCols[i]).append('<button class="btn" type="button" value="Delete"><i class="icon-trash"></i></button>');
                    }
                    if ($(row).find('td').eq(buttons.buttonCols[i]).find('button[value="Edit"]').length <= 0) {
                        $(row).find('td').eq(buttons.buttonCols[i]).append('<button class="btn" type="button" value="Edit"><i class="icon-pencil"></i></button>');
                    }
			//如果button在最结尾处，html可以不加<td></td>
            } else {
                $(row).append('<td><button class="btn" type="button" value="Delete"><i class="icon-trash"></i></button><button class="btn" type="button" value="Edit"><i class="icon-pencil"></i></button></td>');
            }
        });
	  }
		
    }


    // target是event的DOM
	function processCommand(target, command) {
        var tId = $(target).closest('table').attr('id');

        var curRow = getCurrentRow(target);

        var updateRecord = {};
        updateRecord.command = command;
        updateRecord.table = $.data(w.footable, tId + '_serverTableName');

        if (command == "Load") {
            //send the updateRecord to the server via AJAX
            transportData(target,updateRecord);
            return;
        }
		
        
        $(curRow).find('td').each(function () {
			//储存每个td的col name(head里定义)
            var fieldName = $.data(w.footable, tId + '_colNames')[$(this).index()];
            var fieldIsVisible = $(this).is(":visible");
			//fieldIsControl不然就是undified, 不然就是true，为该td是否可控的标识
            var fieldIsControl = $.data(w.footable, tId + '_colControlType')[$(this).index()];
			//判断改行的index是否在idColIndexes里，在的话返回第几个位置；不在的话返回-1
			var fieldIsIdCol = $.data(w.footable, tId + '_idColIndexes').indexOf($(this).index()) >= 0;
			
            //如果命令是add，那么该row所有内容都要送到服务器；不管命令是什么，都保存fooID那列的td
			if (command == 'Add' || command == 'Update' || fieldIsIdCol) {
                //如果该列非control，则存每个name value队
                if (fieldIsControl === undefined) updateRecord[fieldName] = $(this).text();
                else {
                    var ctlVal = $(this).find('button').val();
                    if (ctlVal != "true") ctlVal = "false";
                    updateRecord[fieldName] = ctlVal;
                }
            }

        });

        //send the updateRecord to the server via AJAX
        transportData(target,updateRecord);
    }

    function transportData(target, updateRecord) {   
        var tId = $(target).closest('table').attr('id');
		var dataHandlerURL = $.data(w.footable, tId + '_dataHandlerURL');
		
		//Do nothing mode...
		if (dataHandlerURL == '') return; 
		
		//Do not make ajax call for demo mode...
        if (dataHandlerURL == 'demo' || updateRecord === undefined) { 
			alert("Demo Mode:\r\nThe following JSON data would be sent to the server: \r\n" + JSON.stringify(updateRecord));
			//mimic server response
			var response = {};
			    response.response = "Success";
				response.message = "Your message here";
				response.data = undefined;
			alert("Demo Mode:\r\nThe server responded: \r\n" + JSON.stringify(response));
			processServerResponse(target, JSON.stringify(response), updateRecord); 
            return;
        }
       
        //Send the updateRecord to the server via AJAX for valid command.
        $.ajax({
            type: "POST",
            url: $.data(w.footable, tId + '_dataHandlerURL'),
            contentType: "application/json; charset=uft-8",
            data: JSON.stringify(updateRecord)
        })
        .done(function (data) { processServerResponse(target, data, updateRecord); })
        .fail(function (msg) { alert("error: " + JSON.stringify(msg.responseText)); });
        //.always(function (msg) { alert("complete" + JSON.stringify(msg)); });
    }
	
	function tryJSONParse(data){
		try {
            p = JSON.parse(data);
            data = p;
        } catch (e) {
            // data was not valid json
        }
		finally{
			return data;
		}
	}
	
    function processServerResponse(target, data, updateRecord) {
		//the data response variable can be json or a valid javascript object...
        data = tryJSONParse(data);
		data.responseData = tryJSONParse(data.responseData);
		
        var table = $(target).closest('table');
        var curRow = getCurrentRow(target);
        var nextRow = $(curRow).next();

        //handle processing for fooButtons
        if ($(target).find('button') && data.response != "Error") {
            if (updateRecord.command == "Delete" && data.response == "Success") {
                deleteRow(curRow);
            }
        }

        /*
		//Handle processing AJAX server responses.
        if (data.response == "Success") {
            //Do nothing!
        }
        else if (data.response == "Load") {
            deleteAllRows(table);
            addRows(table, data.responseData);
        }
        else if (data.response == "Append") {
            addRows(table, data.responseData);
        }
        else if (data.response == "Update") {
            updateRow(curRow, data.responseData);
        }
        else if (data.response == "Delete") {
            deleteRow(curRow);
        }
        else if (data.response == "DeleteAll") {
            deleteAllRows(table);
        }
		
		//如果出现Error，该row要换回OldValue 并重新加button!!!!
        else if (data.response == "Error") {
            alert("The update was not successful\r\n" + data.message);

            if (updateRecord.command == 'Update') {
				//重回旧数据
            }
        }
        else {
            alert('Invalid server response! Response recieved: ' + data.response);
        }
		*/
    }

    function getCurrentRow(target) {
        var curRow = $(target).closest('tr');
        if ($(curRow).hasClass('footable-row-detail')) curRow = $(curRow).prev();
        return curRow;
    }

	function updateRow(row, rowData) {
        var table = $(row).closest('table');
        var rowTd = $(row).find('td');
        $.each(rowData, function (name, value) {
            var colIndex = $.data(w.footable, $(table).attr('id') + '_colNames').indexOf(name);
            if (colIndex != -1) $(rowTd).eq(colIndex).text(value);
        });
    }

    function deleteRow(row) {
        if ($(row).next().hasClass('footable-row-detail')) {
            $(row).next().remove();
        }
        if ($(row).prev().hasClass('footable-detail-show')) {
            $(row).prev().remove();
        }
        $(row).remove();
    }

    /*
	function deleteAllRows(table) {
        $(table).find('tbody > tr').not('.fooNewRecord').remove();
    }
	*/
	function addNewRow(table) {
        var tableTh = $(table).find('th');
        var newRow = '<tr class="fooNewRow">';
		var i = 0;
		
		$(tableTh).each(function () {
			newRow += '<td></td>';
			i++;
		});
		newRow += '</tr>';
		
		$(table).find('tbody').prepend(newRow);
	}
	
    function switchButtons(target) {
		var curTd = $(target).closest('td');
        if ($(curTd).find('button[value="Ok"]').length <= 0) {
			$(curTd).empty();
			$(curTd).append('<button class="btn" type="button" value="Ok">Ok</button><button class="btn" type="button" value="Cancel">Cancel</button>');
        }
		else if($(curTd).find('button[value="Delete"]').length <= 0) {
			$(curTd).empty();
			$(curTd).append('<button class="btn" type="button" value="Delete"><i class="icon-trash"></i></button><button class="btn" type="button" value="Edit"><i class="icon-pencil"></i></button>');
        }
    }
	
	function addCssToRow(row) {
		$(row).css('border', '2px solid #E0BF7C');
	}
	
	function deleteCssToRow(row) {
		$(row).css('border', 'none');
	}

    /*function addRows(table, tableRows) {
        //exit if there are no rows to process.
        if (tableRows === undefined) return;
        //Assume tableRows is a JSON string and try to parse, if it is not already and object

        var tableTh = $(table).find('th');
        var rows = "";
        var fooButtonIndexes = getButtonIndexes(table);

        var ft = $(table).data('ft');

        $(tableRows).each(function () {
            var tr = '<tr>';
            var i = 0;
            //Build valid tr's for row in tableRows 
            $.each(this, function (name, value) {
                //use any custom return parsers to parse the value for each new row's cell
                var retParser = tableTh.eq(i).attr('data-return-type');
                if (retParser != undefined) {
                    var parser = ft.options.parsers[retParser];
                    value = parser(value);
                }

                var fieldIsControl = $.data(w.footable, $(table).attr('id') + '_colControlType')[i];
                if (fieldIsControl != undefined) {
                    if (value == "true" || value ==true) value = '<input type="checkbox" checked="checked" />';
                    else value = '<input type="checkbox" />';
                }
                //capture and add fooTable data-class values.
                var classes = "";
                var dataClass = $(tableTh).eq(i).attr('data-class');
                if (dataClass !== undefined) classes = ' class="' + dataClass + '" ';
                //handle hidden fields    
                var style = "";
                if (!$(tableTh).eq(i).is(":visible")) style = ' style="display:none;" ';
                
                tr += '<td' + classes + style + '>' + value + '</td>';
                i++
            });

            //add 1 empty td for each button column 
            $(fooButtonIndexes.buttonColCt).each(function () {
                tr += '<td></td>';
            });
            tr += '</tr>';
            rows += tr;
        });

        $(table).find('tbody').prepend(rows);
        addFooRowButtons(table);

        $(table).data('ft').bindToggleSelectors();
        $(table).data('ft').resize();  //makes new rows display correct when fields are hidden.
    }*/

	$.fn.ftEditable = function (target) {
		var e = {};
		e.processCommand = processCommand;
		e.transportData = transportData;
		e.processServerResponse  = processServerResponse;
		e.updateRow = updateRow;
		e.deleteRow = deleteRow;
		e.deleteAllRows = deleteAllRows;
		e.checkNewEmptyRecord = checkNewEmptyRecord;
		e.addRows = addRows;
		return e;
	}
	
    function Editable() {
		
		//Expose plugin features to w.footable
		
        var p = this;
        p.name = 'Footable Editable';
        p.init = function (ft) {
			
            //save a reference to ft.
            $(ft.table).data('ft', ft);
			
            //capture any default over-rides by user
            var tId = $(ft.table).attr('id');
			
			$.data(w.footable, tId + '_dataHandlerURL', ft.options.dataHandlerURL);
            $.data(w.footable, tId + '_serverTableName', ft.options.serverTableName);
			$.data(w.footable, tId + '_autoLoad', ft.options.autoLoad);
			
            $(ft.table).bind({
                'footable_initialized': function (e) {

                    //Get array of all the column names, indexes with class='id' and footableButtons
                    ////该列是否有fooID，一般是第一行，是该列的标识符[index1, index2, index3...]
					var idColIndexes = new Array();
                    //fieldName是该td的col Names(head里定义的) [name, staus, days...]
                    var colNames = new Array();
					//后面几处和开头函数都去掉了此参数 //fooEditableCols = new Array();
                    //var expandCols = new Array;
                    var colControlType = new Array();
				

                    $(ft.table).find('th').each(function (index) {
                        var fieldName = $(this).text().trim();
                        colNames.push(fieldName);
                        if ($(this).hasClass('fooId')) {
                            idColIndexes.push(index);
                        }
						
                        colControlType.push($(this).attr('data-ft-control'));

                    });
                    //set global table specific variables
                    $.data(w.footable, tId + '_colNames', colNames);
                    $.data(w.footable, tId + '_idColIndexes', idColIndexes);
                    $.data(w.footable, tId + '_colControlType', colControlType);
					

					$(ft.table).on('click', 'button[value="Add"]', function (e) {
						//新建空白行
                        addNewRow(ft.table);
						
						var buttons = getButtonIndexes(ft.table);
						//找到新建行
						var newRow = $(ft.table).find('tr.fooNewRow');
						addButtonsToRow(newRow, buttons);
							
						 
						 $(newRow).find('td').each(function(index) {
							 if ($(this).find('button').length <= 0) {
								 var data_type = $(ft.table).find('th').eq(index).attr('data-type');
								 
							     if (data_type === undefined) {
								     $(this).append('<input type="text">');
							     }
							     else if (data_type === "integer") {
									 $(this).append('<input type="number">');
								 }
							     else if (data_type === "date") {
									 $(this).append('<input type="date">');
								 }
							     else if (data_type === "option") {
									 var newTd = '<select class="td_option" name="status">';
									 var option=$(ft.table).find('th').eq(index).attr('data-option').split(":"); 
									 for (i=0;i<option.length ;i++ ){
										 newTd +='<option value="' + option[i] +'">' + option[i] +'</option>';
									 }
									 newTd += '</select>';
									 $(this).append(newTd);
								 }
							     else if (data_type === "progress") {
								     $(this).append('<input type="number"  name="quantity" min="0" max="100">');
								 }
							 }
						 });
						 
						 
                    });
					
					
                    $(ft.table).on('click', '.icon-pencil', function (e) {
						var curRow = $(this).closest('tr');
						var oldValue = new Array();
						 
						 
						 $.data(w.footable, tId + '_oldRowValue', oldValue);
						 
						 
						 $(curRow).find('td').each(function(index) {
							 
							 oldValue.push($(this).html());
							 
							 if ($(this).find('button').length <= 0) {
								 var data_type = $(ft.table).find('th').eq(index).attr('data-type');
								 
								 //如果该列可编辑
								 if (data_type !== "uneditable") {
								 
								 if ($(this).find('div').length <=0)
							         var value = $(this).text();
							     else if (data_type === "progress") {
								     var val = $(this).text().trim();
									 var value =val.substr(0,val.length-1);
								 }
								 else
							         var value = $(this).text().trim();
									 
							     $(this).empty();
								 
							     if (data_type === undefined) {
								     $(this).append('<input type="text" value="'+ value + '">');
							     }
							     else if (data_type === "integer") {
									 $(this).append('<input type="number" value="'+ value + '">');
								 }
							     else if (data_type === "date") {
									 $(this).append('<input type="date" value="'+ value + '">');
								 }
							     else if (data_type === "option") {
									 var newTd = '<select class ="td_option" name="status">';
									 var option=$(ft.table).find('th').eq(index).attr('data-option').split(":"); 
									 for (i=0;i<option.length ;i++ ){
										 newTd +='<option value="' + option[i] +'">' + option[i] +'</option>';
									 }
									 newTd += '</select>';
									 $(this).append(newTd);
								 }
							     else if (data_type === "progress") {
								     $(this).append('<input type="number"  name="quantity" min="0" max="100" value="'+ value + '">');
								 }
							 }
							 }
						 });
						 
						 $(curRow).addClass('fooEditRow');
						 switchButtons(e.target);
						 
                    });

                    
					$(ft.table).on('click', '.icon-trash', function (e) {
						var curRow = $(this).closest('tr');
						
						addCssToRow(curRow);
						$(curRow).addClass('fooDeleteRow');
						switchButtons(e.target);
                    });
					
					
					$(ft.table).on('click', 'button[value="Ok"]', function (e) {
						var curRow = $(this).closest('tr');
						
						//确定删除行
						if ($(curRow).hasClass('fooDeleteRow')) {
							processCommand(e.target, 'Delete');
						}
						
						//确定添加or修改行
						else {
							//从input状态转为确定输入
							$(curRow).find('td').each(function(index) {
							 
								var data_type = $(ft.table).find('th').eq(index).attr('data-type');
								if (data_type === "option") {
									var value = $('.td_option option:selected').val();
									$(this).text(value);
								}
							    else if (data_type === "progress") {
									var value = $(this).find("INPUT").val();
									var newTd = '<div class="progress progress-success progress-striped"><div class="bar" style="width: ' + value + '%">' + value + '%</div></div>';
									$(this).html(newTd);
									
								}
								else {
									var value = $(this).find("INPUT").val();
									$(this).text(value);
								}
								
							});
							
							//确定添加行
							if ($(curRow).hasClass('fooNewRow')) {
								processCommand(e.target, 'Add');
								$(curRow).removeClass('fooNewRow');
							}
							
							//确定修改行
							else if ($(curRow).hasClass('fooEditRow')) {
								processCommand(e.target, 'Update');
								$(curRow).removeClass('fooEditRow');
							}
							
							switchButtons(e.target);
						}
                    });
					
					
					$(ft.table).on('click', 'button[value="Cancel"]', function (e) {
						var curRow = $(this).closest('tr');
						
						//取消行新建
						if ($(curRow).hasClass('fooNewRow')) {
							processCommand(e.target, 'Delete');
						}
						
						//取消行修改
						else if ($(curRow).hasClass('fooEditRow')) {
							$(curRow).removeClass('fooEditRow');
							
							var setValue = $.data(w.footable, tId + '_oldRowValue');
							$(curRow).find('td').each(function(i) {
								//alert('td_value is: ' + setValue[i]);
								$(this).html(setValue[i]);
							});
						}
						
						//取消行删除
						else if ($(curRow).hasClass('fooDeleteRow')) {
							deleteCssToRow(curRow);
							$(curRow).removeClass('fooDeleteRow');
							switchButtons(e.target);
						}
                    });
					
                } //footable_initialized
            }); //ft.table bind

            addFooRowButtons(ft.table);
			
            //AutoLoad sends a load command each time a Footable is created. 
			if($.data(w.footable, $(ft.table).attr('id') + '_autoLoad')) {
				processCommand(ft.table, 'Load');
			}


        } //p.init

    } // Editable()

    w.footable.plugins.register(new Editable(), defaults);

})(jQuery, window);


