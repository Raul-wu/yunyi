/**
 * @author: deliliu liuwenjie@e-neway.com
 * @since: 7/10/14 9:34
 */

define([
    'jquery',
    'Handlebars',
    'text!templates/confirmBtn.html'
], function ($, Handlebars, tpl) {

    var ConfirmBtn = function() {
        this.init.apply(this, arguments);
    };
    ConfirmBtn.count = 0;

    ConfirmBtn.prototype = {
        init: function(placeholder, confirmClick, options) {

            var $placeholder = $(placeholder);

            if (!$placeholder.length) return;

            //var events = $._data($placeholder[0], 'events');

            options = options || {};

            confirmClick = confirmClick || function() {};//点击按钮后的回调函数

            this.wrapId = ConfirmBtn.count++;
            var data = {
                wrapId: this.wrapId,
                confirmText: options.confirmText || $placeholder.attr('confirmText'),//二次确认按钮文字
                text: options.text || $placeholder.html(),//默认按钮文字
                confirmBtnType: options.confirmBtnType || $placeholder.attr('confirmBtnType') || 'button'//二次确认按钮button类型
            };
            var html = Handlebars.compile(tpl)(data);
            $placeholder.replaceWith(html);

            //var events = $._data($placeholder[0], 'events');

            this.$wrap = $('#confirmBtn_' + this.wrapId);

            var self = this;

            //点击默认按钮
            $('.j-btn', this.$wrap).click(function(e) {
                self.$wrap.addClass("go_ani");

                e.preventDefault();
            });

            //离开wrap
            this.$wrap.mouseleave(function() {
                self.$wrap.removeClass("go_ani");
            });

            //点击二次确认按钮
            $('.j-confirmBtn', this.$wrap).click(function(e) {
                console.log('confirmBtn');
                confirmClick(e);
            });

        }
    };


    return ConfirmBtn;
});