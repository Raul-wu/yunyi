define([
	'jquery',
	'widgets/common/EventEmitter',
	'widgets/common/inherit'
], function($, EventEmitter, inherit) {
	var _tpl = '<div class="masklayer J_dialog" style="position: fixed;">'
		+ '		<div class="hd">'
		+ '			<span class="close J_s_close"></span><span class="J_title"></span>'
		+ '		</div>'
		+ '		<div class="J_template" style="overflow-y: scroll;overflow-x: auto;max-height: 560px">'
		+ '		</div>'
		+ '</div>';

	var _queue = [];
	var _dialogs = {};
	var _mask = $('#mask');

	var _bind = function(func, context) {
		if (!$.isFunction(func)) {
			return func;
		}

		return function() {
			func.apply(context, arguments);
		}
	};

	var DialogBase = inherit(function(options, caching) {
		this.options = $.extend(true, {}, DialogBase.defaults, options);
		this.caching = caching ? true : false;

		this.init();
	}, EventEmitter);

	DialogBase.defaults = {
		title: '&nbsp;',
		template: '',
		'class': '',
        'width' : '',
		events: {
			'click .J_s_close': 'close',
			'click .J_ok': 'close',
			'click .J_close': 'close'
		}
	};

	$.extend(DialogBase.prototype, {
		layer: $(null),
		showTimes: 0,

		init: function() {
			var layer = $(_tpl);
			var events = this.options.events;

			for (var event in events) {
				if (events.hasOwnProperty(event)) {
					var func = events[event];
					var el = event.split(' ');
					if (el.length === 2) {
						if (typeof func === 'string' && $.isFunction(this[func])) {
							func = this[func];
						}
						if ($.isFunction(func)) {
							layer.on(el[0], el[1], _bind(func, this));
						}
					}
				}
			}

			layer.find('.J_template').html(this.options.template);
			layer.find('.J_title').html(this.options.title);
			layer.addClass(this.options['class']);
            if(this.options['width'])
            {
                layer.css('width', this.options['width']);
            }


			this.layer = layer.hide();
			this.layer.appendTo(document.body);

		},

		show: function() {
			if (this.showTimes === 0) {
				this.trigger('beforeFirstShow');
			}
            //让弹出层居中
            var layerWidth = this.layer.css('width');
            var layerHeight = this.layer.css('height');
            var windowsWidth = $(window).width();
            var windowsHeight = $(window).height();


            layerWidth = layerWidth.replace('px','');
            layerHeight = layerHeight.replace('px','');



            var offsetLeft = ((windowsWidth - layerWidth)/2) + layerWidth/2, offsetTop = (windowsHeight - layerHeight)/2;




			this.trigger('beforeShow');
            this.layer.css('top', offsetTop+'px');
            this.layer.css('left', offsetLeft+'px');

			//this.layer.css('margin-top', -parseInt(this.layer.height() / 2));
			this.layer.show();
			this.showTimes ++;
			this.trigger('afterShow');
		},

		close: function() {
			this.trigger('beforeClose');
			if (this.caching) {
				this.layer.hide();
			} else {
				this.layer.remove();
			}
			this.trigger('afterClose');
		}
	});
	
	var Dialog = function(){
		this.Enum = {
			ICON_WARN		:	'result_warn',
			ICON_SUCCESS	:	'result_success',
			ICON_IMPORTANT	:	'result_important'
		};

		var _dfr = null;
		var _open = function(options, key) {

			if (_mask.length === 0) {
				_mask = $('<div class="mask" id="mask"></div>');
				_mask.hide();
				_mask.appendTo(document.body);
			}

			var dialog;
			if (key && _dialogs.hasOwnProperty(key) && _dialogs[key] instanceof DialogBase) {
				dialog = _dialogs[key];
			} else {
				dialog = new DialogBase(options, key)
					.on('beforeShow', function() {
						_mask.show();
					})
					.on('afterClose', function() {
						_queue.shift();
						if (_queue.length === 0) {
							_mask.hide();
						} else {
							setTimeout(function() {
								_queue[0].show();
							}, 0);
						}
					});

				if (key) {
					_dialogs[key] = dialog;
				}
			}

			if (_queue.length === 0) {
				setTimeout(function() {
					dialog.show();
				}, 0);
			}
			_queue.push(dialog);

			return dialog;
		};

		this.open = function(options, key) {
			return _open(options, key);
		};

		this.alert = function(title, content, close, icon, desc) {

			title = title || undefined;
			content = content || '';
			close = close || '确认';
			icon = icon || this.Enum.ICON_WARN;
			desc = desc || '';

			var alertTpl ='<div class="' + icon + '">'
				+ '			<span class="icon"></span><div class="sub_tit">' + content + '</div>'
				+ '		</div>'
				+ '		<div class="result_details">' + desc + '</div>'
				+ '		<div class="ft">'
				+ '			<a href="javascript:void(0);" style="    float:right;" class="pure-button pure-button-primary J_close">' + close + '</a>'
				+ '		</div>';
			_dfr = $.Deferred();

			this.open({
				title: title,
				template: alertTpl,

				events: {
					'click .J_close': function() {
						_dfr.resolve();
						this.close();
					}
				}
			}).on('beforeClose', function () {
				_dfr.resolve();
			});
			return _dfr.promise();
		};

		this.confirm = function(title, content, ok, close, icon, desc) {
			title = title || undefined;
			content = content || '';
			ok = ok || '确认';
			close = close || '取消';
			icon = icon || this.Enum.ICON_WARN;
			desc = desc || '';

			var confirmTpl ='<div class="' + icon + '">'
				+ '			<span class="icon"></span><div class="sub_tit">' + content + '</div>'
				+ '		</div>'
				+ '		<div class="result_details">' + desc + '</div>'
				+ '		<div class="ft">'
				+ '			<a href="javascript:void(0);" class="btn J_ok">' + ok + '</a>'
				+ '			<a href="javascript:void(0);" class="btn btn_silver J_close">' + close + '</a>'
				+ '		</div>';
			_dfr = $.Deferred();
			this.open({
				title: title,
				template: confirmTpl,

				events: {
					'click .J_close': function() {
						_dfr.reject();
						this.close();
					},
					'click .J_ok': function() {
						_dfr.resolve();
						this.close();
					}
				}
			}).on('beforeClose', function () {
				_dfr.reject();
			});

			return _dfr.promise();
		};
	};

	return new Dialog();
});