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
