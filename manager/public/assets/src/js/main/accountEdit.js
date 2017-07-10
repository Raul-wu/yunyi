/**
 * Created by Raul on 2015/8/14.
 */
require([
    'jquery',
    'art-dialog',
    'widgets/ImgUpload',
    'widgets/ConfirmBtn',
], function($, placeholder, ImgUpload) {

    var $form = $("#accountForm");

    $("#back").on('click', function(){
        window.location.href = '/account/list';
    });

    $("#add").on('click', function() {
        var strData = chkParams();

        $.ajax({
            'url': window.saveUrl ,
            'data': strData,
            'dataType': 'json',
            'type': 'POST',
            'success': function(data) {
                if(!data.retCode) {
                    msgDialog(data.retMsg, data.retData.url);
                }else{
                    msgDialog(data.retMsg);
                }
            }
        });
    });

    $("#edit").on('click', function() {
        var strData = chkParams();

        $.ajax({
            'url': window.saveUrl ,
            'data': strData + '&spvId=' + $("#spvId").val(),
            'dataType': 'json',
            'type': 'POST',
            'success': function(data) {
                if(!data.retCode) {
                    msgDialog(data.retMsg, data.retData.url);
                }else{
                    if (data.retData.errors) {
                        $.each(data.retData.errors, function(i) {
                            $form.find('[name=' + i + ']').addClass('error');
                            $form.find('[name=' + i + ']').attr('placeholder','必填');
                        });
                    }
                    msgDialog(data.retMsg);
                }
            }
        });
    });

    function chkParams(){

        return 'showName=' + $("#showName").val() + '&spvName=' + $("#spvName").val() + '&account=' + $("#account").val()
            + '&payName=' + $("#payName").val() + '&name=' + $("#name").val() + '&address=' +$("#address").val()
            + '&imgPath=' + $("#imgPath").val() + '&settlement=' + $("input[name='settlement']:checked").val()
            + '&isBeneAccount=' + $("input[name='isBeneAccount']:checked").val() + '&remarks=' + $("#remarks").val()
            + '&opType=' + $("#opType").val() + '&g_tk=' + $("input[tkname=g_tk]").val();
    }

    //共用确认弹框
    function confirm(content,callback,params,flag)
    {
        var icon = (flag == "" || flag == undefined || flag == null) ? 'question' : '';
        return art.dialog({
            id: 'Confirm',
            icon: icon,
            fixed: true,
            lock: true,
            opacity: .8,
            content: content,
            ok: function(){
                callback(params);
            },
            cancel: true
        });
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