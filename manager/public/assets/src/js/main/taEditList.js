require([
    'jquery',
    'art-dialog',
    'widgets/SingleMultiBtn',
    'widgets/ConfirmBtn',
], function($, placeholder, SingleMultiBtn) {

    var widget = new SingleMultiBtn("#buttonHolder", {
        singleBtns: [
            // {
            //     name: "修改",
            //     click: function(id) {
            //         window.location.href= edit + '?tid='+id;
            //     }
            // }
        ],
        commonBtns: [
            {
                name: "删除",
                click: function() {
                    confirm("是否确认删除收益分配信息？", deleteTas);
                },
            }]
    });

    widget.listen();

    $('#selectAll').on('click', function() {
        if (this.checked) {
            $('.check').each(function() {
                this.checked = false;
            });

            $('.check').trigger('click');
        } else {
            $('.check').each(function() {
                this.checked = true;
            });

            $('.check,.close').trigger('click');
        }
    });

    function deleteTas() {
        var chk_ids=[];
        $(":checkbox:checked").each(function(){
            chk_ids.push($(this).attr('data-id'));
        });

        var params={};
        params['tids'] = chk_ids.join(',');
        params[$("#tkName").attr('tkName')] = $("#tkName").val();

        $.ajax({
            'url': deleteTa,
            'data': params,
            'dataType': 'json',
            'type': 'POST',
            'success': function (data) {
                if(!data.retCode){
                    msgDialog(data.retMsg, 'reload');
                }else{
                    msgDialog(data.retMsg);
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
