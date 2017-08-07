/**
 * Created by Raul on 2015/8/14.
 */
require([
    'jquery',
    'jquery-form',
    'numberTip',
    'widgets/ConfirmBtn',
    'Handlebars',
    'art-dialog',
    'widgets/excelUpload',
], function($, placeholder,  placeholder, ConfirmBtn, Handlebars,placeholder, ExcelUpload) {

    var $form = $("#form");
    var saveAction = $form.attr('saveAction');

    function isAllow(lastName) {
        if(lastName == ".xls" || lastName == ".xlsx") {
            return true;
        }
        return false;
    }

    $("#save").on('click', function(e) {

        e.preventDefault();

        $form.attr('action', $form.attr('saveAction'));

        var file = document.getElementById("quotients").files;
        var fileName = file[0].name;

        var index = fileName.lastIndexOf(".");
        var lastname = fileName.substring(index,fileName.length);
        if(!isAllow(lastname)) {
            msgDialog("请上传excel后缀的文件");
            return false;
        }

        if(file[0].size > (5 * 1024 * 1024)) {
            msgDialog("上传文件不能超过5M");
            return false;
        }

        confirm("确定导入'" + fileName + "'份额表？");

    });

    function confirm(content,flag)
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
            },
            cancel: true
        });
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