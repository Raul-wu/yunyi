/**
 * Created by 9020MT on 14-9-19.
 */
require([
    'jquery',
    'jquery-form',
    'datepicker-config',
    'ckeditor-config',
    'widgets/ConfirmBtn',
    'widgets/ImgUpload',
    'Handlebars',
    'text!templates/mgrTeam.html'
], function($, placeholder, placeholder, placeholder, ConfirmBtn, ImgUpload) {

    //保存
    $('#save').click(function(e) {
        e.preventDefault();

        $("#postForm").ajaxSubmit(function(data) {
            if (data.retCode) {
                if (data.retMsg) {
                    alert(data.retMsg);
                    location.href = document.referrer;
                } else {
                    alert("服务器超时，请重新再试");
                }
            } else {
                alert( data.retMsg );
                location.href = document.referrer;
            }

        });
    });
});