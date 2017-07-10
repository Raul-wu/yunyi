/**
 * Created by Raul on 2015/8/14.
 */
require([
    'jquery',
    'art-dialog',
    'widgets/SingleMultiBtn',
    'widgets/ConfirmBtn',
], function($, placeholder, SingleMultiBtn) {

    var widget = new SingleMultiBtn("#buttonHolder", {
        singleBtns: [{
            name: "编辑",
            click: function(id) {
                window.location.href= url.editUrl + '?id='+id;
            },
            canShowFunc: function() {
                return listSpvPermission;
            }
        },
        ],
        commonBtns: [
            {
                name: "启用",
                click: function(ids) {
                    changeState(ids,window.stateNoraml);
                },
                canShowFunc: function() {
                    return changeStateSpvPermission;
                }
            },
            {
                name: "禁用",
                click: function(ids) {
                    changeState(ids,window.stateDelete);
                },
                canShowFunc: function() {
                    return changeStateSpvPermission;
                }
            },
        ]
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

    $('#reset').on("click",function(){
        $("#showName").val("");
        $("#spvName").val("");
        $("#account").val("");
    });

    function changeState(ids,state)
    {
        var token = $("input[tkname=g_tk]").val();
        if(ids.length<=0){
            msgDialog('请选择要操作的数据');
        }

        return art.dialog({
            id: 'Confirm',
            icon: 'question',
            fixed: true,
            lock: true,
            opacity: .8,
            content: '共' + ids.length + '条记录,确定要更新状态吗？',
            ok: function(){
                $.ajax({
                    'url': "/product/spv/ChangeState" ,
                    'data': {'spvIDs':ids.join(','), 'isBeneAccount':state, g_tk: token },
                    'dataType': 'json',
                    'type': 'POST',
                    'success': function(data) {
                        if(!data.retCode){
                            msgDialog(data.retMsg, 'reload');
                        }else{
                            msgDialog(data.retMsg);
                        }
                    }
                });
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



