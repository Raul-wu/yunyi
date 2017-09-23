require([
    'jquery',
    'art-dialog',
    'widgets/SingleMultiBtn',
    'widgets/ConfirmBtn',
], function($, placeholder, SingleMultiBtn) {

    var widget = new SingleMultiBtn("#buttonHolder", {
        singleBtns: [
            {
                name: "录入分配信息",
                click: function(id) {
                    checkHasQuotient(id);
                }
            },
            {
                name: "查看分配信息列表",
                click: function(id) {
                    window.location.href= editList + '?ppid='+id;
                }
            },
            {
                name: "执行清算",
                click: function(id) {
                    window.location.href= exec + '?ppid='+id;
                }
            },
        ]
    });

    widget.listen();

    $('#reset').on("click",function(){
        $("#fund_code").val("");
    });

    function checkHasQuotient(ppid) {
        var params={};
        params['ppid'] = ppid;
        params[$("#tkName").attr('tkName')] = $("#tkName").val();

        $.ajax({
            'url': checkQuotient,
            'data': params,
            'dataType': 'json',
            'type': 'POST',
            'success': function (data) {
                if(data.retCode){
                    msgDialog(data.retMsg);
                }else{
                    window.location.href= add + '?ppid='+ppid;
                }
            }
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