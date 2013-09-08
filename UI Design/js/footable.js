/*!
 * FooTable - Awesome Responsive Tables
 * Version : 0.5
 * http://themergency.com/footable
 *
 * Requires jQuery - http://jquery.com/
 *
 * Copyright 2012 Steven Usher & Brad Vincent
 * Released under the MIT license
 * You are free to use FooTable in commercial projects as long as this copyright header is left intact.
 *
 * Date: 22 Apr 2013
 */
(function ($, w, undefined) {
    w.footable = {
        options: {
            delay: 100, // The number of millseconds to wait before triggering the react event
            breakpoints: { // The different screen resolution breakpoints
                phone: 480,
                tablet: 1024
            },
            parsers: {  // The default parser to parse the value out of a cell (values are used in building up row detail)
                alpha: function (cell) {
                    return $(cell).data('value') || $.trim($(cell).text());
                },
				///////////////numeric is changed to date
                date: function (cell) {
                    var val = $(cell).data('value') || $(cell).text().replace(/[^0-9.\-]/g, '');
                    val = parseFloat(val);
                    if (isNaN(val)) val = 0;
                    return val;
                }
            },
            calculateWidthAndHeightOverride: null,
            toggleSelector: ' > tbody > tr:not(.footable-row-detail)', //the selector to show/hide the detail row
            columnDataSelector: '> thead > tr:last-child > th, > thead > tr:last-child > td', //the selector used to find the column data in the thead
            detailSeparator: ':', //the seperator character used when building up the detail row
            createGroupedDetail: function (data) {
                var groups = { '_none': { 'name': null, 'data': [] } };
                for (var i = 0; i < data.length; i++) {
                    var groupid = data[i].group;
                    if (groupid !== null) {
                        if (!(groupid in groups))
                            groups[groupid] = { 'name': data[i].groupName || data[i].group, 'data': [] };

                        groups[groupid].data.push(data[i]);
                    } else {
                        groups._none.data.push(data[i]);
                    }
                }
                return groups;
            },
            createDetail: function (element, data, createGroupedDetail, separatorChar, classes) {
                /// <summary>This function is used by FooTable to generate the detail view seen when expanding a collapsed row.</summary>
                /// <param name="element">This is the div that contains all the detail row information, anything could be added to it.</param>
                /// <param name="data">
                ///  This is an array of objects containing the cell information for the current row.
                ///  These objects look like the below:
                ///    obj = {
                ///      'name': String, // The name of the column
                ///      'value': Object, // The value parsed from the cell using the parsers. This could be a string, a number or whatever the parser outputs.
                ///      'display': String, // This is the actual HTML from the cell, so if you have images etc you want moved this is the one to use and is the default value used.
                ///      'group': String, // This is the identifier used in the data-group attribute of the column.
                ///      'groupName': String // This is the actual name of the group the column belongs to.
                ///    }
                /// </param>
                /// <param name="createGroupedDetail">The grouping function to group the data</param>
                /// <param name="separatorChar">The separator charactor used</param>
                /// <param name="classes">The array of class names used to build up the detail row</param>

                var groups = createGroupedDetail(data);
                for (var group in groups) {
                    if (groups[group].data.length === 0) continue;
                    if (group !== '_none') element.append('<div class="' + classes.detailInnerGroup + '">' + groups[group].name + '</div>');

                    for (var j = 0; j < groups[group].data.length; j++) {
                        var separator = (groups[group].data[j].name) ? separatorChar : '';
                        element.append('<div class="' + classes.detailInnerRow + '"><div class="' + classes.detailInnerName + '">' + groups[group].data[j].name + separator + '</div><div class="' + classes.detailInnerValue + '">' + groups[group].data[j].display + '</div></div>');
                    }
                }
            },
            classes: {
                main: 'footable',
                loading: 'footable-loading',
                loaded: 'footable-loaded',
                toggle: 'footable-toggle',
                detail: 'footable-row-detail',
                detailCell: 'footable-row-detail-cell',
                detailInner: 'footable-row-detail-inner',
                detailInnerRow: 'footable-row-detail-row',
                detailInnerGroup: 'footable-row-detail-group',
                detailInnerName: 'footable-row-detail-name',
                detailInnerValue: 'footable-row-detail-value',
                detailShow: 'footable-detail-show'
            },
            triggers: {
                initialize: 'footable_initialize',                      //trigger this event to force FooTable to reinitialize
                resize: 'footable_resize',                              //trigger this event to force FooTable to resize
                toggleRow: 'footable_toggle_row',                       //trigger this event to force FooTable to toggle a row
                expandFirstRow: 'footable_expand_first_row'             //trigger this event to force FooTable to expand the first row
            },
            events: {
                alreadyInitialized: 'footable_already_initialized',     //fires when the FooTable has already been initialized
                initializing: 'footable_initializing',                  //fires before FooTable starts initializing
                initialized: 'footable_initialized',                    //fires after FooTable has finished initializing
                resizing: 'footable_resizing',                          //fires before FooTable resizes
                resized: 'footable_resized',                            //fires after FooTable has resized
                breakpoint: 'footable_breakpoint',                      //fires inside the resize function, when a breakpoint is hit
                columnData: 'footable_column_data',                     //fires when setting up column data. Plugins should use this event to capture their own info about a column
                rowDetailUpdating: 'footable_row_detail_updating',      //fires before a detail row is updated
                rowDetailUpdated: 'footable_row_detail_updated',        //fires when a detail row is being updated
                rowCollapsed: 'footable_row_collapsed',                 //fires when a row is collapsed
                rowExpanded: 'footable_row_expanded'                    //fires when a row is expanded
            },
            debug: false, // Whether or not to log information to the console.
            log : null
        },

        version: {
            major: 0, minor: 5,
            toString: function () {
                return w.footable.version.major + '.' + w.footable.version.minor;
            },
            parse: function (str) {
                version = /(\d+)\.?(\d+)?\.?(\d+)?/.exec(str);
                return {
                    major: parseInt(version[1], 10) || 0,
                    minor: parseInt(version[2], 10) || 0,
                    patch: parseInt(version[3], 10) || 0
                };
            }
        },

        plugins: {
            _validate: function (plugin) {
                ///<summary>Simple validation of the <paramref name="plugin"/> to make sure any members called by Foobox actually exist.</summary>
                ///<param name="plugin">The object defining the plugin, this should implement a string property called "name" and a function called "init".</param>

                if (typeof plugin['name'] !== 'string') {
                    if (w.footable.options.debug === true) console.error('Validation failed, plugin does not implement a string property called "name".', plugin);
                    return false;
                }
                if (!$.isFunction(plugin['init'])) {
                    if (w.footable.options.debug === true) console.error('Validation failed, plugin "' + plugin['name'] + '" does not implement a function called "init".', plugin);
                    return false;
                }
                if (w.footable.options.debug === true) console.log('Validation succeeded for plugin "' + plugin['name'] + '".', plugin);
                return true;
            },
            registered: [], // An array containing all registered plugins.
            register: function (plugin, options) {
                ///<summary>Registers a <paramref name="plugin"/> and its default <paramref name="options"/> with Foobox.</summary>
                ///<param name="plugin">The plugin that should implement a string property called "name" and a function called "init".</param>
                ///<param name="options">The default options to merge with the Foobox's base options.</param>

                if (w.footable.plugins._validate(plugin)) {
                    w.footable.plugins.registered.push(plugin);
                    if (options !== undefined && typeof options === 'object') $.extend(true, w.footable.options, options);
                    if (w.footable.options.debug === true) console.log('Plugin "' + plugin['name'] + '" has been registered with the Foobox.', plugin);
                }
            },
            init: function (instance) {
                ///<summary>Loops through all registered plugins and calls the "init" method supplying the current <paramref name="instance"/> of the Foobox as the first parameter.</summary>
                ///<param name="instance">The current instance of the Foobox that the plugin is being initialized for.</param>

                for (var i = 0; i < w.footable.plugins.registered.length; i++) {
                    try {
                        w.footable.plugins.registered[i]['init'](instance);
                    } catch (err) {
                        if (w.footable.options.debug === true) console.error(err);
                    }
                }
            }
        }
    };

    var instanceCount = 0;

    $.fn.footable = function (options) {
        ///<summary>The main constructor call to initialize the plugin using the supplied <paramref name="options"/>.</summary>
        ///<param name="options">
        ///<para>A JSON object containing user defined options for the plugin to use. Any options not supplied will have a default value assigned.</para>
        ///<para>Check the documentation or the default options object above for more information on available options.</para>
        ///</param>

        options = options || {};
        var o = $.extend(true, {}, w.footable.options, options); //merge user and default options
        return this.each(function () {
            instanceCount++;
            this.footable = new Footable(this, o, instanceCount);
        });
    };

    //helper for using timeouts
    function Timer() {
        ///<summary>Simple timer object created around a timeout.</summary>
        var t = this;
        t.id = null;
        t.busy = false;
        t.start = function (code, milliseconds) {
            ///<summary>Starts the timer and waits the specified amount of <paramref name="milliseconds"/> before executing the supplied <paramref name="code"/>.</summary>
            ///<param name="code">The code to execute once the timer runs out.</param>
            ///<param name="milliseconds">The time in milliseconds to wait before executing the supplied <paramref name="code"/>.</param>

            if (t.busy) {
                return;
            }
            t.stop();
            t.id = setTimeout(function () {
                code();
                t.id = null;
                t.busy = false;
            }, milliseconds);
            t.busy = true;
        };
        t.stop = function () {
            ///<summary>Stops the timer if its runnning and resets it back to its starting state.</summary>

            if (t.id !== null) {
                clearTimeout(t.id);
                t.id = null;
                t.busy = false;
            }
        };
    }

    function Footable(t, o, id) {
        ///<summary>Inits a new instance of the plugin.</summary>
        ///<param name="t">The main table element to apply this plugin to.</param>
        ///<param name="o">The options supplied to the plugin. Check the defaults object to see all available options.</param>
        ///<param name="id">The id to assign to this instance of the plugin.</param>

        var ft = this;
        ft.id = id;
        ft.table = t;
        ft.options = o;
        ft.breakpoints = [];
        ft.breakpointNames = '';
        ft.columns = {};

        var opt = ft.options,
            cls = opt.classes,
            evt = opt.events,
            trg = opt.triggers,
            indexOffset = 0;

        // This object simply houses all the timers used in the FooTable.
        ft.timers = {
            resize: new Timer(),
            register: function (name) {
                ft.timers[name] = new Timer();
                return ft.timers[name];
            }
        };

        w.footable.plugins.init(ft);

        ft.init = function () {
            var $window = $(w), $table = $(ft.table);

            if ($table.hasClass(cls.loaded)) {
                //already loaded FooTable for the table, so don't init again
                ft.raise(evt.alreadyInitialized);
                return;
            }

            //raise the initializing event
            ft.raise(evt.initializing);

            $table.addClass(cls.loading);

            // Get the column data once for the life time of the plugin
            $table.find(opt.columnDataSelector).each(function () {
                var data = ft.getColumnData(this);
                ft.columns[data.index] = data;
            });

            // Create a nice friendly array to work with out of the breakpoints object.
            for (var name in opt.breakpoints) {
                ft.breakpoints.push({ 'name': name, 'width': opt.breakpoints[name] });
                ft.breakpointNames += (name + ' ');
            }

            // Sort the breakpoints so the smallest is checked first
            ft.breakpoints.sort(function (a, b) {
                return a['width'] - b['width'];
            });

            $table
                //bind to FooTable initialize trigger
                .bind(trg.initialize, function () {
                    //remove previous "state" (to "force" a resize)
                    $table.removeData('footable_info');
                    $table.data('breakpoint', '');

                    //add the toggler to each row
                    ft.addRowToggle();

                    //bind the toggle selector click events
                    ft.bindToggleSelectors();

                    //set any cell classes defined for the columns
                    ft.setColumnClasses();

                    //remove the loading class
                    $table.removeClass(cls.loading);

                    //add the FooTable and loaded class
                    $table.addClass(cls.loaded).addClass(cls.main);

                    //trigger the FooTable resize
                    $table.trigger(trg.resize);

                    //raise the initialized event
                    ft.raise(evt.initialized);
                })
                //bind to FooTable resize trigger
                .bind(trg.resize, function () {
                    ft.resize();
                })
                //bind to FooTable expandFirstRow trigger
                .bind(trg.expandFirstRow, function() {
                    $table.find(opt.toggleSelector).first().not('.' + cls.detailShow).trigger(trg.toggleRow);
                });

            //trigger a FooTable initialize
            $table.trigger(trg.initialize);

            //bind to window resize
            $window
                .bind('resize.footable', function () {
                    ft.timers.resize.stop();
                    ft.timers.resize.start(function () {
                        ft.raise(trg.resize);
                    }, opt.delay);
                });
        };

        ft.addRowToggle = function () {
            var $table = $(ft.table),
                hasToggleColumn = false;

            //first remove all toggle spans
            $table.find('span.' + cls.toggle).remove();

            for (var c in ft.columns) {
                var col = ft.columns[c];
                if (col.toggle) {
                    hasToggleColumn = true;
                    var selector = '> tbody > tr:not(.' + cls.detail + ') > td:nth-child(' + (parseInt(col.index, 10) + 1) + ')';
                    $table.find(selector).not('.' + cls.detailCell).prepend($('<span />').addClass(cls.toggle));
                    return;
                }
            }
            //check if we have an toggle column. If not then add it to the first column just to be safe
            if (!hasToggleColumn) {
                $table
                    .find('> tbody > tr:not(.' + cls.detail + ') > td:first-child')
                    .not('.' + cls.detailCell)
                    .prepend($('<span />').addClass(cls.toggle));
            }
        };

        ft.setColumnClasses = function () {
            $table = $(ft.table);
            for (var c in ft.columns) {
                var col = ft.columns[c];
                if (col.className !== null) {
                    var selector = '', first = true;
                    $.each(col.matches, function (m, match) { //support for colspans
                        if (!first) selector += ', ';
                        selector += '> tbody > tr:not(.' + cls.detail + ') > td:nth-child(' + (parseInt(match, 10) + 1) + ')';
                        first = false;
                    });
                    //add the className to the cells specified by data-class="blah"
                    $table.find(selector).not('.' + cls.detailCell).addClass(col.className);
                }
            }
        };

        //moved this out into it's own function so that it can be called from other add-ons
        ft.bindToggleSelectors = function () {
            var $table = $(ft.table);
            $table.find(opt.toggleSelector).unbind(trg.toggleRow).bind(trg.toggleRow, function (e) {
                var $row = $(this).is('tr') ? $(this) : $(this).parents('tr:first');
                ft.toggleDetail($row.get(0));
            });

            $table.find(opt.toggleSelector).unbind('click.footable').bind('click.footable', function (e) {
                if ($table.is('.breakpoint') && $(e.target).is('td,.footable-toggle')) {
                    $(this).trigger(trg.toggleRow);
                }
            });
        };

        ft.parse = function (cell, column) {
            var parser = opt.parsers[column.type] || opt.parsers.alpha;
            return parser(cell);
        };

        ft.getColumnData = function (th) {
            var $th = $(th), hide = $th.data('hide'), index = $th.index();
            hide = hide || '';
            hide = hide.split(',');
            var data = {
                'index': index,
                'hide': { },
                'type': $th.data('type') || 'alpha',
                'name': $th.data('name') || $.trim($th.text()),
                'ignore': $th.data('ignore') || false,
                'toggle': $th.data('toggle') || false,
                'className': $th.data('class') || null,
                'matches': [],
                'names': { },
                'group': $th.data('group') || null,
                'groupName': null
            };

            if (data.group !== null) {
                var $group = $(ft.table).find('> thead > tr.footable-group-row > th[data-group="' + data.group + '"], > thead > tr.footable-group-row > td[data-group="' + data.group + '"]').first();
                data.groupName = ft.parse($group, { 'type': 'alpha' });
            }

            var pcolspan = parseInt($th.prev().attr('colspan') || 0, 10);
            indexOffset += pcolspan > 1 ? pcolspan - 1 : 0;
            var colspan = parseInt($th.attr('colspan') || 0, 10), curindex = data.index + indexOffset;
            if (colspan > 1) {
                var names = $th.data('names');
                names = names || '';
                names = names.split(',');
                for (var i = 0; i < colspan; i++) {
                    data.matches.push(i + curindex);
                    if (i < names.length) data.names[i + curindex] = names[i];
                }
            } else {
                data.matches.push(curindex);
            }

            data.hide['default'] = ($th.data('hide') === "all") || ($.inArray('default', hide) >= 0);

            for (var name in opt.breakpoints) {
                data.hide[name] = ($th.data('hide') === "all") || ($.inArray(name, hide) >= 0);
            }
            var e = ft.raise(evt.columnData, { 'column': { 'data': data, 'th': th } });
            return e.column.data;
        };

        ft.getViewportWidth = function () {
            return window.innerWidth || (document.body ? document.body.offsetWidth : 0);
        };

        ft.getViewportHeight = function () {
            return window.innerHeight || (document.body ? document.body.offsetHeight : 0);
        };

        ft.calculateWidthAndHeight = function ($table, info) {
            if (jQuery.isFunction(opt.calculateWidthAndHeightOverride)) {
                return opt.calculateWidthAndHeightOverride($table, info);
            }
            if (info.viewportWidth < info.width) info.width = info.viewportWidth;
            if (info.viewportHeight < info.height) info.height = info.viewportHeight;

            return info;
        };

        ft.hasBreakpointColumn = function (breakpoint) {
            for (var c in ft.columns) {
                if (ft.columns[c].hide[breakpoint]) {
                    if (ft.columns[c].ignore) continue;
                    return true;
                }
            }
            return false;
        };

        ft.resize = function () {
            var $table = $(ft.table);

            if (!$table.is(':visible')) { return; } //we only care about FooTables that are visible

            var info = {
                'width': $table.width(),                  //the table width
                'height': $table.height(),                //the table height
                'viewportWidth': ft.getViewportWidth(),   //the width of the viewport
                'viewportHeight': ft.getViewportHeight(), //the width of the viewport
                'orientation': null
            };

            info.orientation = info.viewportWidth > info.viewportHeight ? 'landscape' : 'portrait';

            info = ft.calculateWidthAndHeight($table, info);

            var pinfo = $table.data('footable_info');
            $table.data('footable_info', info);
            ft.raise(evt.resizing, { 'old': pinfo, 'info': info });

            // This (if) statement is here purely to make sure events aren't raised twice as mobile safari seems to do
            if (!pinfo || ((pinfo && pinfo.width && pinfo.width !== info.width) || (pinfo && pinfo.height && pinfo.height !== info.height))) {
                var current = null, breakpoint;
                for (var i = 0; i < ft.breakpoints.length; i++) {
                    breakpoint = ft.breakpoints[i];
                    if (breakpoint && breakpoint.width && info.width <= breakpoint.width) {
                        current = breakpoint;
                        break;
                    }
                }

                var breakpointName = (current === null ? 'default' : current['name']),
                    hasBreakpointFired = ft.hasBreakpointColumn(breakpointName),
                    previousBreakpoint = $table.data('breakpoint');

                $table.data('breakpoint', breakpointName);

                //only do something if the breakpoint has changed
                if ( breakpointName !== previousBreakpoint ) {
                    $table
                        .find('> tbody > tr:not(.' + cls.detail + ')').data('detail_created', false).end()
                        .removeClass('default breakpoint').removeClass(ft.breakpointNames)
                        .addClass(breakpointName + (hasBreakpointFired ? ' breakpoint' : ''))
                        .find('> thead > tr:last-child > th')
                        .each(function () {
                            var data = ft.columns[$(this).index()], selector = '', first = true;
                            $.each(data.matches, function (m, match) {
                                if (!first) {
                                    selector += ', ';
                                }
                                var count = match + 1;
                                selector += '> tbody > tr:not(.' + cls.detail + ') > td:nth-child(' + count + ')';
                                selector += ', > tfoot > tr:not(.' + cls.detail + ') > td:nth-child(' + count + ')';
                                selector += ', > colgroup > col:nth-child(' + count + ')';
                                first = false;
                            });

                            selector += ', > thead > tr[data-group-row="true"] > th[data-group="' + data.group + '"]';
                            var $column = $table.find(selector).add(this);
                            if (data.hide[breakpointName] === false) $column.show();
                            else $column.hide();

                            if ($table.find('> thead > tr.footable-group-row').length === 1) {
                                var $groupcols = $table.find('> thead > tr:last-child > th[data-group="' + data.group + '"]:visible, > thead > tr:last-child > th[data-group="' + data.group + '"]:visible'),
                                    $group = $table.find('> thead > tr.footable-group-row > th[data-group="' + data.group + '"], > thead > tr.footable-group-row > td[data-group="' + data.group + '"]'),
                                    groupspan = 0;

                                $.each($groupcols, function () {
                                    groupspan += parseInt($(this).attr('colspan') || 1, 10);
                                });

                                if (groupspan > 0) $group.attr('colspan', groupspan).show();
                                else $group.hide();
                            }
                        })
                        .end()
                        .find('> tbody > tr.' + cls.detailShow).each(function () {
                            ft.createOrUpdateDetailRow(this);
                        });

                    $table.find('> tbody > tr.' + cls.detailShow + ':visible').each(function () {
                        var $next = $(this).next();
                        if ($next.hasClass(cls.detail)) {
                            if (breakpointName === 'default' && !hasBreakpointFired) $next.hide();
                            else $next.show();
                        }
                    });

                    // adding .footable-first-column and .footable-last-column to the first and last th and td of each row in order to allow
                    // for styling if the first or last column is hidden (which won't work using :first-child or :last-child)
                    $table.find('> thead > tr > th.footable-last-column, > tbody > tr > td.footable-last-column').removeClass('footable-last-column');
                    $table.find('> thead > tr > th.footable-first-column, > tbody > tr > td.footable-first-column').removeClass('footable-first-column');
                    $table.find('> thead > tr, > tbody > tr')
                        .find('> th:visible:last, > td:visible:last')
                        .addClass('footable-last-column')
                        .end()
                        .find('> th:visible:first, > td:visible:first')
                        .addClass('footable-first-column');

                    ft.raise(evt.breakpoint, { 'breakpoint': breakpointName, 'info': info });
                }
            }

            ft.raise(evt.resized, { 'old': pinfo, 'info': info });
        };

        ft.toggleDetail = function (actualRow) {
            var $row = $(actualRow),
                $next = $row.next();

            //check if the row is already expanded
            if ($row.hasClass(cls.detailShow)) {
                $row.removeClass(cls.detailShow);

                //only hide the next row if it's a detail row
                if ($next.hasClass(cls.detail)) $next.hide();

                ft.raise(evt.rowCollapsed, { 'row': actualRow });

            } else {
                var created = ft.createOrUpdateDetailRow(actualRow);
                $row.addClass(cls.detailShow);
                $next.show();

                ft.raise(evt.rowExpanded, { 'row': actualRow });
            }
        };

        ft.getColumnFromTdIndex = function (index) {
            /// <summary>Returns the correct column data for the supplied index taking into account colspans.</summary>
            /// <param name="index">The index to retrieve the column data for.</param>
            /// <returns type="json">A JSON object containing the column data for the supplied index.</returns>
            var result = null;
            for (var column in ft.columns) {
                if ($.inArray(index, ft.columns[column].matches) >= 0) {
                    result = ft.columns[column];
                    break;
                }
            }
            return result;
        };

        ft.createOrUpdateDetailRow = function (actualRow) {
            var $row = $(actualRow), $next = $row.next(), $detail, values = [];
            if ($row.data('detail_created') === true) return true;

            if ($row.is(':hidden')) return false; //if the row is hidden for some reason (perhaps filtered) then get out of here
            ft.raise(evt.rowDetailUpdating, { 'row': $row, 'detail': $next });
            $row.find('> td:hidden').each(function () {
                var index = $(this).index(), column = ft.getColumnFromTdIndex(index), name = column.name;
                if (column.ignore === true) return true;

                if (index in column.names) name = column.names[index];
                values.push({ 'name': name, 'value': ft.parse(this, column), 'display': $.trim($(this).html()), 'group': column.group, 'groupName': column.groupName });
                return true;
            });
            if (values.length === 0) return false; //return if we don't have any data to show
            var colspan = $row.find('> td:visible').length;
            var exists = $next.hasClass(cls.detail);
            if (!exists) { // Create
                $next = $('<tr class="' + cls.detail + '"><td class="' + cls.detailCell + '"><div class="' + cls.detailInner + '"></div></td></tr>');
                $row.after($next);
            }
            $next.find('> td:first').attr('colspan', colspan);
            $detail = $next.find('.' + cls.detailInner).empty();
            opt.createDetail($detail, values, opt.createGroupedDetail, opt.detailSeparator, cls);
            $row.data('detail_created', true);
            ft.raise(evt.rowDetailUpdated, { 'row': $row, 'detail': $next });
            return !exists;
        };

        ft.raise = function (eventName, args) {

            if (ft.options.debug === true && $.isFunction(ft.options.log)) ft.options.log(eventName, 'event');

            args = args || { };
            var def = { 'ft': ft };
            $.extend(true, def, args);
            var e = $.Event(eventName, def);
            if (!e.ft) {
                $.extend(true, e, def);
            } //pre jQuery 1.6 which did not allow data to be passed to event object constructor
            $(ft.table).trigger(e);
            return e;
        };

        ft.init();
        return ft;
    }
})(jQuery, window);


/*--------------------------------------------------------------------
footable.additional.js
---------------------------------------------------------------------*/
/* ------ footable sorting ------ */
(function ($, w, undefined) {
    if (w.footable === undefined || w.footable === null)
        throw new Error('Please check and make sure footable.js is included in the page and is loaded prior to this script.');

    var defaults = {
        sort: true,
        sorters: {
            alpha: function (a, b) {
                if (a === b) return 0;
                if (a < b) return -1;
                return 1;
            },
            numeric: function (a, b) {
                return a - b;
            }
        },
        classes: {
            sort: {
                sortable: 'footable-sortable',
                sorted: 'footable-sorted',
                descending: 'footable-sorted-desc',
                indicator: 'footable-sort-indicator'
            }
        },
        events: {
            sort: {
                sorting: 'footable_sorting',
                sorted: 'footable_sorted'
            }
        }
    };

    function Sort() {
        var p = this;
        p.name = 'Footable Sortable';
        p.init = function (ft) {
            if (ft.options.sort === true) {
                $(ft.table).bind({
                    'footable_initialized': function (e) {
                        var cls = ft.options.classes.sort, evt = ft.options.events.sort, column;

                        var $table = $(ft.table), $tbody = $table.find('> tbody'), $th;

                        if ($table.data('sort') === false) return;

                        $table.find('> thead > tr:last-child > th, > thead > tr:last-child > td').each(function (ec) {
                            $th = $(this), column = ft.columns[$th.index()];
                            if (column.sort.ignore !== true && !$th.hasClass(cls.sortable)) {
                                $th.addClass(cls.sortable);
                                $('<span />').addClass(cls.indicator).appendTo($th);
                            }
                        });

                        $table.find('> thead > tr:last-child > th.' + cls.sortable + ', > thead > tr:last-child > td.' + cls.sortable).unbind('click.footable').bind('click.footable', function (ec) {
                            $th = $(this), column = ft.columns[$th.index()];
                            if (column.sort.ignore === true) return true;

                            $table.data('sorted', column.index);

                            ec.preventDefault();

                            $table.find('> thead > tr:last-child > th, > thead > tr:last-child > td').not($th).removeClass(cls.sorted + ' ' + cls.descending);

                            var sort = true;
                            var ascending = true;

                            if ($th.hasClass(cls.sorted)) {
                                sort = false;
                                ascending = false;
                            } else if ($th.hasClass(cls.descending)) {
                                sort = false;
                            }

                            if (ascending) {
                                $th.removeClass(cls.descending).addClass(cls.sorted);
                            } else {
                                $th.removeClass(cls.sorted).addClass(cls.descending);
                            }

                            //raise a pre-sorting event so that we can cancel the sorting if needed
                            var event = ft.raise(evt.sorting, { column: column, direction: ascending ? 'ASC' : 'DESC' });
                            if (event && event.result === false) return;

                            if (sort) {
                                p.sort(ft, $tbody, column);
                            } else {
                                p.reverse(ft, $tbody);
                            }

                            ft.bindToggleSelectors();
                            ft.raise(evt.sorted, { column: column, ascending: ascending });
                            return false;
                        });

                        var didSomeSorting = false;
                        for (var c in ft.columns) {
                            column = ft.columns[c];
                            if (column.sort.initial) {
                                p.sort(ft, $tbody, column);
                                didSomeSorting = true;
                                $th = $table.find('> thead > tr:last-child > th:eq(' + c + '), > thead > tr:last-child > td:eq(' + c + ')');

                                if (column.sort.initial === 'descending') {
                                    p.reverse(ft, $tbody);
                                    $th.addClass(cls.descending);
                                } else {
                                    $th.addClass(cls.sorted);
                                }

                                break;
                            }
                        }
                        if (didSomeSorting) {
                            ft.bindToggleSelectors();
                        }
                    },
                    'footable_column_data': function (e) {
                        var $th = $(e.column.th);
                        e.column.data.sort = e.column.data.sort || {};
                        e.column.data.sort.initial = $th.data('sort-initial') || false;
                        e.column.data.sort.ignore = $th.data('sort-ignore') || false;
                        e.column.data.sort.selector = $th.data('sort-selector') || null;

                        var match = $th.data('sort-match') || 0;
                        if (match >= e.column.data.matches.length) match = 0;
                        e.column.data.sort.match = e.column.data.matches[match];
                    }
                });
            }
        };

        p.rows = function (ft, tbody, column) {
            var rows = [];
            tbody.find('> tr').each(function () {
                var $row = $(this), $next = null;
                if ($row.hasClass(ft.options.classes.detail)) return true;
                if ($row.next().hasClass(ft.options.classes.detail)) {
                    $next = $row.next().get(0);
                }
                var row = { 'row': $row, 'detail': $next };
                if (column !== undefined) {
                    row.value = ft.parse(this.cells[column.sort.match], column);
                }
                rows.push(row);
                return true;
            }).detach();
            return rows;
        };

        p.sort = function (ft, tbody, column) {
            var rows = p.rows(ft, tbody, column);
            var sorter = ft.options.sorters[column.type] || ft.options.sorters.alpha;
            rows.sort(function (a, b) {
                return sorter(a.value, b.value);
            });
            for (var j = 0; j < rows.length; j++) {
                tbody.append(rows[j].row);
                if (rows[j].detail !== null) {
                    tbody.append(rows[j].detail);
                }
            }
        };

        p.reverse = function (ft, tbody) {
            var rows = p.rows(ft, tbody);
            for (var i = rows.length - 1; i >= 0; i--) {
                tbody.append(rows[i].row);
                if (rows[i].detail !== null) {
                    tbody.append(rows[i].detail);
                }
            }
        };
    }

    w.footable.plugins.register(new Sort(), defaults);

})(jQuery, window);


/* ------ footable filter ------ */
(function ($, w, undefined) {
    if (w.footable === undefined || w.footable === null)
        throw new Error('Please check and make sure footable.js is included in the page and is loaded prior to this script.');

    var jQversion = w.footable.version.parse($.fn.jquery);
    if (jQversion.major === 1 && jQversion.minor < 8) { // For older versions of jQuery, anything below 1.8
        $.expr[':'].ftcontains = function (a, i, m) {
            return $(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
        };
    } else { // For jQuery 1.8 and above
        $.expr[':'].ftcontains = $.expr.createPseudo(function (arg) {
            return function (elem) {
                var text = $(elem).find('td').text();
                var data = $(elem).find('td[data-value]').each(function () {
                    text += $(this).data('value');
                });
                return text.toUpperCase().indexOf(arg.toUpperCase()) >= 0;
            };
        });
    }

    var defaults = {
        filter: {
            enabled: true,
            input: '.footable-filter',
            timeout: 300,
            minimum: 2,
            disableEnter: false
        }
    };

    function Filter() {
        var p = this;
        p.name = 'Footable Filter';
        p.init = function (ft) {
            if (ft.options.filter.enabled === true) {
                ft.timers.register('filter');
                $(ft.table).bind({
                    'footable_initialized': function (e) {
                        var $table = $(ft.table);
                        var data = {
                            'input': $table.data('filter') || ft.options.filter.input,
                            'timeout': $table.data('filter-timeout') || ft.options.filter.timeout,
                            'minimum': $table.data('filter-minimum') || ft.options.filter.minimum,
                            'disableEnter': $table.data('filter-disable-enter') || ft.options.filter.disableEnter
                        };
                        if (data.disableEnter) {
                            $(data.input).keypress(function (event) {
                                if (window.event)
                                    return (window.event.keyCode !== 13);
                                else
                                    return (event.which !== 13);
                            });
                        }
                        $table.bind('footable_clear_filter', function () {
                            $(data.input).val('');
                            p.clearFilter(ft);
                        });
                        $table.bind('footable_filter', function (event, args) {
                            p.filter(ft, args.filter);
                        });
                        $(data.input).keyup(function (eve) {
                            ft.timers.filter.stop();
                            if (eve.which === 27) {
                                $(data.input).val('');
                            }
                            ft.timers.filter.start(function () {
                                var val = $(data.input).val() || '';
                                p.filter(ft, val);
                            }, data.timeout);
                        });
                    }
                });
            }
        };

        p.filter = function (ft, filterString) {
            var $table = $(ft.table);
            var minimum = $table.data('filter-minimum') || ft.options.filter.minimum;
            var clear = !filterString || filterString.length < minimum;

            //raise a pre-filter event so that we can cancel the filtering if needed
            var event = ft.raise('footable_filtering', { filter: filterString, clear: clear });
            if (event && event.result === false) return;

            if (clear) {
                p.clearFilter(ft);
            } else {
                var filters = filterString.split(' ');

                $table.find('> tbody > tr').hide().addClass('footable-filtered');
                var rows = $table.find('> tbody > tr:not(.footable-row-detail)');
                $.each(filters, function (i, f) {
                    if (f && f.length)
                        rows = rows.filter('*:ftcontains("' + f + '")');
                });
                rows.each(function () {
                    p.showRow(this, ft);
                    $(this).removeClass('footable-filtered');
                });
                ft.raise('footable_filtered', { filter: filterString });
            }
        };

        p.clearFilter = function (ft) {
            $(ft.table).find('> tbody > tr:not(.footable-row-detail)').removeClass('footable-filtered').each(function () {
                p.showRow(this, ft);
            });
            ft.raise('footable_filtered', { cleared: true });
        };

        p.showRow = function (row, ft) {
            var $row = $(row), $next = $row.next(), $table = $(ft.table);
            if ($row.is(':visible')) return; //already visible - do nothing
            if ($table.hasClass('breakpoint') && $row.hasClass('footable-detail-show') && $next.hasClass('footable-row-detail')) {
                $row.add($next).show();
                ft.createOrUpdateDetailRow(row);
            }
            else $row.show();
        };
    }

    w.footable.plugins.register(new Filter(), defaults);

})(jQuery, window);


/* ------ footable paging ------ */
(function ($, w, undefined) {
    if (w.footable === undefined || w.footable === null)
        throw new Error('Please check and make sure footable.js is included in the page and is loaded prior to this script.');

    var defaults = {
        paginate: true,
        pageSize: 10,
        pageNavigation: '.pagination',
        firstText: '&laquo;',
		previousText: '&lsaquo;',
		nextText: '&rsaquo;',
        lastText: '&raquo;'
    };

    function pageInfo(ft) {
        var $table = $(ft.table), $tbody = $table.find('> tbody');
        this.pageNavigation = $table.data('page-navigation') || ft.options.pageNavigation;
        this.pageSize = $table.data('page-size') || ft.options.pageSize;
        this.firstText = $table.data('page-first-text') || ft.options.firstText;
		this.previousText = $table.data('page-previous-text') || ft.options.previousText;
		this.nextText = $table.data('page-next-text') || ft.options.nextText;
        this.lastText = $table.data('page-last-text') || ft.options.lastText;
        this.currentPage = 0;
        this.pages = [];
        this.control = false;
    }

    function Paginate() {
        var p = this;
        p.name = 'Footable Paginate';

        p.init = function (ft) {
            if (ft.options.paginate === true) {
                $(ft.table).bind({
                    'footable_initialized': function () {
                        ft.pageInfo = new pageInfo(ft);
						ft.raise('footable_setup_paging');
                    },
                    'footable_sorted footable_filtered footable_setup_paging': function () {
                        p.setupPaging(ft);
                    }
                });
            }
        };
		
		p.setupPaging = function(ft) {
			var $tbody = $(ft.table).find('> tbody');
			p.createPages(ft, $tbody);
			p.createNavigation(ft, $tbody);
			p.fillPage(ft, $tbody, ft.pageInfo.currentPage);
		};

        p.createPages = function (ft, tbody) {
            var pages = 1;
            var info = ft.pageInfo;
            var pageCount = pages * info.pageSize;
            var page = [];
            var lastPage = [];
            info.pages = [];
            var rows = tbody.find('> tr:not(.footable-filtered,.footable-row-detail)');
            rows.each(function (i, row) {
                page.push(row);
                if (i === pageCount - 1) {
                    info.pages.push(page);
                    pages++;
                    pageCount = pages * info.pageSize;
                    page = [];
                } else if (i >= rows.length - (rows.length % info.pageSize)) {
                    lastPage.push(row);
                }
            });
            if (lastPage.length > 0) info.pages.push(lastPage);
            if (info.currentPage >= info.pages.length) info.currentPage = info.pages.length - 1;
            if (info.currentPage < 0) info.currentPage = 0;
        };

        p.createNavigation = function (ft, tbody) {
            var $nav = $(ft.table).find(ft.pageInfo.pageNavigation);
			//if we cannot find the navigation control within the table, then try find it outside
			if ($nav.length === 0) {
				$nav = $(ft.pageInfo.pageNavigation);
				//if the navigation control is inside another table, then get out
				if ($nav.parents('table:first') !== $(ft.table)) return;
				//if we found more than one navigation control, write error to console
				if ($nav.length > 1 && ft.options.debug === true) console.error('More than one pagination control was found!');
			}
			//if we still cannot find the control, then don't do anything
            if ($nav.length === 0) return;
			//if the nav is not a UL, then find or create a UL
			if (!$nav.is('ul')) { 
				if ($nav.find('ul:first').length === 0) { $nav.append('<ul />'); }
				$nav = $nav.find('ul');
			}
            $nav.find('li').remove();
            var info = ft.pageInfo;
            info.control = $nav;
            if (info.pages.length > 0) {
                $nav.append('<li class="footable-page-arrow"><a data-page="first" href="#first">'+ft.pageInfo.firstText+'</a>');
                $nav.append('<li class="footable-page-arrow"><a data-page="prev" href="#prev">'+ft.pageInfo.previousText+'</a></li>');
                $.each(info.pages, function (i, page) {
                    if (page.length > 0) {
                        $nav.append('<li class="footable-page"><a data-page="' + i + '" href="#">' + (i + 1) + '</a></li>');
                    }
                });
                $nav.append('<li class="footable-page-arrow"><a data-page="next" href="#next">'+ft.pageInfo.nextText+'</a></li>');
                $nav.append('<li class="footable-page-arrow"><a data-page="last" href="#last">'+ft.pageInfo.lastText+'</a></li>');
            }
            $nav.find('a').click(function (e) {
                e.preventDefault();
                var page = $(this).data('page');
                var newPage = info.currentPage;
                if (page === 'first') {
                    newPage = 0;
                } else if (page === 'prev') {
                    if (newPage > 0) newPage--;
                } else if (page === 'next') {
                    if (newPage < info.pages.length - 1) newPage++;
                } else if (page === 'last') {
                    newPage = info.pages.length - 1;
                } else {
                    newPage = page;
                }
                p.paginate(ft, newPage);
            });
			p.setPagingClasses($nav, info.currentPage, info.pages.length);
        };

        p.paginate = function (ft, newPage) {
            var info = ft.pageInfo;
            if (info.currentPage !== newPage) {
                var $tbody = $(ft.table).find('> tbody');

                //raise a pre-pagin event so that we can cancel the paging if needed
                var event = ft.raise('footable_paging', { page: newPage, size: info.pageSize });
                if (event && event.result === false) return;

                p.fillPage(ft, $tbody, newPage);
                info.control.find('li').removeClass('active disabled');
                p.setPagingClasses(info.control, info.currentPage, info.pages.length);
            }
        };
		
		p.setPagingClasses = function(nav, currentPage, pageCount) {
            nav.find('li.footable-page > a[data-page=' + currentPage + ']').parent().addClass('active');
			if (currentPage >= pageCount - 1) {
				nav.find('li.footable-page-arrow > a[data-page="next"]').parent().addClass('disabled');
                nav.find('li.footable-page-arrow > a[data-page="last"]').parent().addClass('disabled');
			}
			if (currentPage < 1) {
                nav.find('li.footable-page-arrow > a[data-page="first"]').parent().addClass('disabled');
				nav.find('li.footable-page-arrow > a[data-page="prev"]').parent().addClass('disabled');
			}
		};

        p.fillPage = function (ft, tbody, pageNumber) {
            ft.pageInfo.currentPage = pageNumber;
            tbody.find('> tr').hide();
            $(ft.pageInfo.pages[pageNumber]).each(function () {
                p.showRow(this, ft);
            });
        };

        p.showRow = function (row, ft) {
            var $row = $(row), $next = $row.next(), $table = $(ft.table);
            if ($table.hasClass('breakpoint') && $row.hasClass('footable-detail-show') && $next.hasClass('footable-row-detail')) {
                $row.add($next).show();
                ft.createOrUpdateDetailRow(row);
            }
            else $row.show();
        };
    }

    w.footable.plugins.register(new Paginate(), defaults);

})(jQuery, window);


