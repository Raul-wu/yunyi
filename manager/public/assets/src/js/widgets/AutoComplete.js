define(function() {

    String.prototype.format = function(config, reserve) {

        return this.replace(/\{([^}]+)\}/g, (typeof config == 'object') ?
        function(m, i) {
            var ret = config[i];
            if (ret == null && reserve) {
                return m;
            }
            return ret;
        }: config);
    };

    var plus1 = function(v) {
        return v - ( - 1);
    },
    minus1 = function(v) {
        return v - 1;
    },
    last = function(page_size, total) {

        return Math.ceil(total / page_size);
    };

    var Grid = function() {
        this.init.apply(this, arguments);
    };

    var normalizeColumns = function(columns) {

        var newColumns = {};

        $.each(columns,
        function(i, column) {
            if (!column.type) {
                column.type = 'text';
            }

        });

    };

    var renderers = {
        decimal: function(v) {
            return v.decimal(2);
        },
        integer: function(v) {
            return v.decimal();
        },
        dic: function(v) {
            return this[v];
        },

        plain: function(text) {
            return text;
        },

        checkbox: function(v) {
            return '<input type="checkbox" value="{value}" style="height:18px;margin-top:1px;" />'.format(v)
        },

        input: function(v) { 
            return '<input class="grid_input" style="width:36px;" type="text" value="{value}" />'.format(v)
        },
        icon: function(v) {
            return map(this + '',
            function(icon) {
                return '<span class="icon-only icon-{icon}" _value={value}></span>'.format({
                    value: v,
                    icon: icon
                });
            }).join('');
        },
        rate: function(c, v) {
            if (v == 0) {
                return v.decimal(3) + '%';
            } else {
                return (100 * c / v).decimal(3) + '%';
            }
        }
    };

    var column_types = {
        'text': {
            renderer: renderers.plain,
            align: 'left'
        },

        'decimal': {
            renderer: renderers.decimal,
            align: 'right'
        },
        'rate': {
            renderer: renderers.rate,
            align: 'right'
        },
        'integer': {
            renderer: renderers.integer,
            align: 'right'
        },
        'input': {
            renderer: renderers.input,
            align: 'right'
        },
        'checkbox': {
            title: '<input type="checkbox">',
            renderer: renderers.checkbox,
            align: 'center',
            width: 40
        }
    };

    var getColumnHTML = function(column, item) {

        if (column.mapping == '') {
            return '<td class="grid-col grid-col-spacing"></td>';
        }

        var args_mapping = column.mapping.map(function(mapping) {
            return item[mapping]
        });

        return '<td style="{style}" class="grid-col grid-col-{column-name}">'.format({
            'style': 'text-align:' + column.align,

            'column-name': column.mapping
        }) + column.renderer.apply(null, args_mapping) + '</td>';
    };

    Grid.prototype = {

        init: function(btnPh, options) {
            this.options = {
                thead: $(btnPh).find('thead'),
                tbody: $(btnPh).find('tbody')
            };

            $.extend(this.options, options);

            this.options.normalCol = normalizeColumns(this.options.columns);
            this.renderHeader(this.options.columns, this.options.thead);
            this.page_index = 1;
            this.display_page_number = 7;
            this.total = 0;
            this.page_size = 30;
            this.btnPh = btnPh;

            this._initElPaginator();

        },

        renderers: renderers,

        renderHeader: function(columns, thead) {
            thead.empty();
            var grid = thead.parent();
            var tr = $('<tr/>').append(columns.map(function(column) {

                var width = (column.colspan > 1) ? null: column.width ? typeof column.width == 'number' ? column.width + 'px': column.width: null,
                cursor = (column.sortable) ? 'pointer': 'default';

                return '<th style="{style}" class="{class}" _field="{field}">'.format({
                    'field': column.mapping,
                    'class': column.sortable ? 'sortable sort-unvisible asc': '',
                    'style': 'width:' + width + '; cursor: ' + cursor,
                    //todo
                }) + column.title + (column.sortable ? '<span class="grid-order-field"></span>': '') + '</th>';
            }).join('')).appendTo(thead);
            this.sortable();

        },

        renderBody: function(items) {;
            var tbody = this.options.tbody,
            columns = this.options.columns;
            tbody.empty();
            /*var el = tbody.get(0);
            while (el.firstChild) {
                el.removeChild(el.firstChild);
            }*/
            var index = 1;
            $.each(items,
            function(i, item) {
                var trClass = (index++%2 == 0) ? '': 'pure-table-odd';

                $('<tr/>').append(columns.map(function(column) {
                    return getColumnHTML(column, item);
                }).join('')).addClass(trClass).appendTo(tbody);
            });

        },

        sortable: function() {
            var self = this;
            //todo 增加样式之类
            if (self.options.sortEvent) {
                this.options.thead.delegate('.sortable', 'click',
                function() {
                    var _field = $(this).attr('_field');

                    var el = $(this);
                    var last_sort_column = $('.sortable');
                    last_sort_column.addClass('sort-unvisible');
                    last_sort_column = el;

                    if (el.hasClass('sort-unvisible')) {
                        el.removeClass('sort-unvisible');
                    }
                    if (el.hasClass('asc')) {
                        el.removeClass('asc').addClass('desc');
                        sort_asc = 'desc';
                    } else {
                        el.removeClass('desc').addClass('asc');
                        sort_asc = 'asc';
                    }

                    sort_field = el.attr('_field').split(' ')[0];

                    self.options.sortEvent(sort_field, sort_asc);

                });

            }
        },

        pageTotal: function(total) {

            var _self = this;
            
            if (total == 0) {
                this.total = total;
                var trNum = this.options.columns.length;
                this.options.tbody.html('<tr><td  colspan="' + trNum + '" >没有相关记录</td></tr>');
                this.table.hide();     
                return false;

            }
            if (total != this.total) {
                this.total = total;
                this.page_index = 1;
                this._refreshPaginator();

            }
        },

        pageChange: function() {

            var page_index = this.page_index;

            this.options.pageChange(page_index);

        },

        _initElPaginator: function() {

            var _self = this;
            this.el = $('<ul/>').addClass('pure-paginator').append(this.el_prev = $('<li><a class="pure-button" style="margin-right:0">«</a></li>').bind('click',
            function() {
                if (!$(this).hasClass('disabled')) {
                    _self.page_index =   minus1(_self.page_index);
                    _self._refreshPaginator();
                    _self.pageChange();
                }
            })).append(this.el_pages = $('<li></li>').delegate('a:not(.pure-button-active)', 'click',
            function() {
                _self.page_index = $(this).html() - 0;
                _self._refreshPaginator();
                _self.pageChange();

            })

            ).append(this.el_next = $('<li><a class="pure-button" style="margin-right:0">»</a></li>').bind('click',
            function() {
                if (!$(this).hasClass('disabled')) {
                    _self.page_index = plus1(_self.page_index);
                    _self._refreshPaginator();
                    _self.pageChange();
                }
            }))

            this.table = $('<div style="background-color: #e9eef5;padding-top:20px;display:none"/>').addClass('paginator_mod').append(this.el_page_size_info = $('<span class="page-size-info" style="float:left" >共' + _self.total + '条记录</p>')).append(this.el);

            //console.log(_self.btnPh);
            $(this.btnPh).after(this.table);
        },

        _refreshPaginator: function() {

            var self = this;
            //self.table.show();  
            var display_page_number = self.display_page_number,
            page_size = self.page_size,
            total = self.total,
            page_index = self.page_index,
            page_number = Math.ceil(total / page_size);

            if (page_size != null && total != null && page_index != null) {

                var half = Math.ceil(display_page_number / 2);

                var start = Math.max(Math.min(page_number - display_page_number + 1, page_index - half), 1);

                var end = Math.min(display_page_number, page_number);

                var html = '';

                for (var i = 0; i < end; i++) {
                    html += '<li><a class="pure-button " style="margin-right:0">{index}</a></li>'.format({
                        index: i + start
                    });
                }

                this.el_pages.html(html).find('a.pure-button').eq(page_index - start).addClass('pure-button-active');

                this.el.find('li').removeClass('disabled');
                $('.page-size-info').text('共' + total + '条记录');

                if (page_index == 1) {
                   
                    this.el_prev.addClass('disabled');
                }

                if (page_index >= page_number) {
                    this.el_next.addClass('disabled');

                }
               
            }
            
            this.table.show();
        },
    }
    return Grid;   
});