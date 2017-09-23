/**
 * Created by Raul on 2017/7/13.
 */
require([
    'jquery',
    'art-dialog',
    'widgets/SingleMultiBtn',
    'widgets/ConfirmBtn',
], function($, placeholder, SingleMultiBtn) {

    var widget = new SingleMultiBtn("#buttonHolder", {
        singleBtns: [
            {
                name: "编辑子产品",
                click: function(id) {
                    window.location.href= editProduct + '?pid='+id;
                },
            },
            {
                name: "导入客户份额",
                click: function(id) {
                    // window.location.href= quotient + '?pid='+id;
                    checkProductIsEstablish(id, 1);
                },
            },
            {
                name: "添加单个客户份额",
                click: function(id) {
                    // window.location.href= addQuotient + '?pid='+id;
                    checkProductIsEstablish(id, 2);
                },
            }
        ],
        commonBtns: [
            {
                name: "查看客户份额",
                click: function() {
                    var chk_ids=[];
                    $(":checkbox:checked").each(function(){
                        chk_ids.push($(this).attr('data-id'));
                    });

                    window.location.href= quotientList + '?pid=' + chk_ids.join(',');
                },
                // canShowFunc: function(){
                //     return delPProductPermission;
                // }
            },
            {
                name: "删除",
                click: function() {
                    confirm("是否确认删除子产品(同时会删除该子产品下的客户份额！)", deleteProducts);
                },
            }
        ]
    });

    widget.listen();

    function checkProductIsEstablish(pid, type) {
        var params={};
        params['pid'] = pid;
        params[$("#tkName").attr('tkName')] = $("#tkName").val();

        $.ajax({
            'url': checkProductEstablish,
            'data': params,
            'dataType': 'json',
            'type': 'POST',
            'success': function (data) {
                if(data.retCode){
                    msgDialog(data.retMsg);
                }else{
                    if(type == 1){
                        window.location.href= quotient + '?pid='+pid;
                    }else if(type == 2){
                        window.location.href= addQuotient + '?pid='+pid;
                    }
                }
            }
        });
    }

    function deleteProducts() {
        var chk_ids=[];
        $(":checkbox:checked").each(function(){
            chk_ids.push($(this).attr('data-id'));
        });

        var params={};
        params['pids'] = chk_ids.join(',');
        params[$("#tkName").attr('tkName')] = $("#tkName").val();

        $.ajax({
            'url': delProduct,
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
        $("#fund_code").val("");
    });

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