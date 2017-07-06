/**
 * Created by hexi on 14-10-30.
 */

define(['jquery', 'Handlebars', 'text!templates/loadingEasy.html'], function ($, handlebars, tpl) {
    var tplEngine = handlebars.compile(tpl);
    var LoadingEasy = function (selector) {
        this.init(selector);
    }

    LoadingEasy.count = 0;

    LoadingEasy.prototype = {
        init : function (selector) {
            this.placeHolder = $(selector);
            this.ids = [];
            var _this = this;

            this.placeHolder.each(function (i ,ele) {
                var data = {
                    lid : 'lid' + LoadingEasy.count
                };

                _this.ids.push('#lid' + LoadingEasy.count);
                var html = tplEngine(data);
                $(ele).replaceWith(html);
                LoadingEasy.count++;
                console.log(LoadingEasy.count);
            });
        },

        hide : function() {
            for(var i = 0; i < this.ids.length; i++) {
                $(this.ids[i]).hide();
            }

            return this;
        },

        show : function () {
            for(var i = 0; i < this.ids.length; i++) {
                $(this.ids[i]).show();
            }

            return this;
        }
    };

    return LoadingEasy;
});