/**
 * @author: deliliu liuwenjie@e-neway.com
 * @since: 7/9/14 0:55
 */

define([
    'jquery',
    'jquery-ui',
    'timepicker'
], function ($, placeholder, placeholder) {
    var holidays = ["2015-01-01", "2015-01-02", "2015-01-03", "2015-02-18", "2015-02-19", "2015-02-20", "2015-02-21", "2015-02-22", "2015-02-23", "2015-02-24", "2015-04-04", "2015-04-05", "2015-04-06", "2015-05-01", "2015-05-02", "2015-05-03", "2015-06-20", "2015-06-21", "2015-06-22", "2015-09-26", "2015-09-27", "2015-10-01", "2015-10-02", "2015-10-03", "2015-10-04", "2015-10-05", "2015-10-06", "2015-10-07"];

    $.timepicker.setDefaults({
        dateFormat: 'yy-mm-dd',
        //controlType: 'select',
        timeFormat: 'HH:mm',
        timeText: '时间',
        hourText: '时',
        minuteText: '分',
        secondText: '秒',
        monthNames: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
        dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
        dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
        dayNamesMin: ['日','一','二','三','四','五','六']
    });

    $.datepicker.setDefaults({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        monthNamesShort: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
        dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
        dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
        dayNamesMin: ['日','一','二','三','四','五','六'],
        beforeShowDay: function(date) {
            var month = date.getMonth() + 1 > 9 ? date.getMonth() + 1 : '0' + (date.getMonth() + 1);
            var day = date.getDate() > 9 ? date.getDate() : '0' + date.getDate();
            var date = date.getFullYear() + '-' + month + '-' + day;
            return [true, $.inArray(date, holidays) == -1 ? "" : "ui-state-disabled",''];
        }

    });


    $('[datepicker=datepicker]').datepicker();
    $('[timepicker=timepicker]').datetimepicker();

});