/**
 * Created by rwu on 8/14/17.
 */
require([
    'jquery',
    'jquery-form',
    'datepicker-config',
    'widgets/ConfirmBtn',
    'widgets/ImgUpload',
    'Handlebars',
    'text!templates/mgrTeam.html',
    'text!templates/durationInfo.html',
    'text!templates/attachment-item.html',
    'widgets/jquery.combo.select',
    'art-dialog',
], function($, placeholder, placeholder, ConfirmBtn, ImgUpload, Handlebars, mgrTeamTpl, durationInfo, attItem, placeholder) {

    var $form = $("#form");
    var saveAction = $form.attr('saveAction');

    $("#save").on('click', function(e) {

        e.preventDefault();

        $form.attr('action', $form.attr('saveAction'));

        $form.ajaxSubmit(function(data) {
            if (!data.retCode) {
                msgDialog(data.retMsg, data.retData.url);
            } else {
                if (data.retData.errors) {
                    $.each(data.retData.errors, function(i) {
                        $form.find('[name=' + i + ']').addClass('error');
                        $form.find('[name=' + i + ']').attr('placeholder','必填');
                    });
                }
                msgDialog(data.retMsg);
            }
        });
    });

    // 收到的收益/(本金*天数/365)
    $("input[name='fact_income_rate_E6']").focus(function(){
        var fact_principal = $("input[name='fact_principal']").val();
        var fact_income = $("input[name='fact_income']").val();
        var value_date = $("input[name='value_date']").val();
        var stringTime = $("input[name='fact_end_date']").val();


        if(fact_principal == 0 || fact_income == 0) {
            msgDialog("请输入到期本金和到期收益");
        } else {
            var timestamp2 = Date.parse(new Date(stringTime));
            var days = parseInt((timestamp2 / 1000 - value_date) / 86400);
            var rate = fact_income / (fact_principal * days / 365) * 100;

            $("input[name='fact_income_rate_E6']").val(rate.toFixed(2));
        }
    });

    function fomatFloat(src,pos){
        return Math.round(src*Math.pow(10, pos))/Math.pow(10, pos);
    }

    //消息提示框
    function msgDialog(content,url)
    {
        var msgDialog = art.dialog({
            'title':'消息提示',
            'width': "300px",
            'height':"50px",
            'ok': function()
            {
                if(url && url =='reload')
                {
                    location.reload();
                }
                else if(url && url !='reload')
                {
                    location.href= url;
                }

            },
            'content' : content,
            'lock' :true
        });
    }

});
