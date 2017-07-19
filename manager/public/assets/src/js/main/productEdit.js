require([
    'jquery',
    'jquery-form',
    'numberTip',
    'widgets/ConfirmBtn',
    'Handlebars',
    'art-dialog'
], function($, placeholder,  placeholder, ConfirmBtn, Handlebars,placeholder) {

    var $form = $("#form");
    var saveAction = $form.attr('saveAction');

    //保存
    $("#save").on('click', function(e) {

        e.preventDefault();
        var $this = $(this);

        if (!$this.data('lock')) {
            $this.data('lock', 1);
        } else {
            return;
        }

        $form.attr('action', $form.attr('saveAction'));

        $form.ajaxSubmit(function(data) {
            //var turnError = true;
            if (!data.retCode) {
                msgDialog(data.retMsg, data.retData.url);
            } else {
                msgDialog(data.retMsg);
            }

            $this.data('lock', 0);
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
