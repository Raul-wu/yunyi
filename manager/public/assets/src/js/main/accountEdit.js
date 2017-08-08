/**
 * Created by Raul on 2017/7/14.
 */
require([
    'jquery',
    'art-dialog',
    'widgets/ConfirmBtn',
], function($, placeholder) {

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

    $("#edit").on('click', function() {
        var strData = chkParams();
        $.ajax({
            'url': window.saveUrl ,
            'data': strData + '&id=' + $("#id").val(),
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

        return 'ppid=' + $("#ppid").val() + '&name=' + $("#name").val() + '&type=' + $("#type").val() + '&bank_account=' + $("#bank_account").val()
            + '&bank_address=' + $("#bank_address").val() + '&handler=' + $("#handler").val() + '&status=' + $("input[name='status']:checked").val()
            + '&opType=' + $("#opType").val() + '&g_tk=' + $("input[tkname=g_tk]").val();
    }

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