require([
    'jquery',
    'art-dialog',
    'widgets/SingleMultiBtn',
    'widgets/ConfirmBtn',
], function($, placeholder, SingleMultiBtn) {

    var widget = new SingleMultiBtn("#buttonHolder", {
        singleBtns: [
            {
                name: "查看详情",
                click: function(id) {
                    window.location.href= detail + '?ppid='+id;
                }
            }
        ]
    });

    widget.listen();

    $('#reset').on("click",function(){
        $("#fund_code").val("");
    });

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
});
