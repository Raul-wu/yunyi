define([
    'jquery',
    'widgets/SingleMultiBtn',
    'art-dialog',
    'widgets/ConfirmBtn',
], function($, SingleMultiBtn, placeholder, ConfirmBtn) {

    var widget = new SingleMultiBtn("#btnPh", {
        singleBtns: [
            {
                name: "编辑基金",
                click: function(ppid) {
                    window.location.href = url + "?ppid=" + ppid;
                }
            },
            {
                name: "创建子产品",
                click: function(ppid) {
                    checkHasScale(ppid);
                },
                // canShowFunc: function() {
                //     if(!createProductPermission)
                //         return false;
                //     else
                //         return true;
                // }
            },
            {
                name: "查看子产品",
                click: function(ppid) {
                    url = subShow + '?ppid=' + ppid;
                    window.location.href = url;
                },
                // canShowFunc: function(){
                //     return listProductPermission;
                // }
            }
        ],
        commonBtns: [
            {
                name: "转存续",
                click: function() {
                    confirm("确定将所选的基金转存续吗？", turnDuration);
                },
                // canShowFunc: function(ids) {
                    // if(!DurPProductPermission) {
                    //     return false;
                    // }
                    // else
                    // {
                        // var canShow = true;
                        // $.each(ids, function(i, h) {
                        //     if ($.inArray(parseInt(h), canTurnPPids) == -1) {
                        //         canShow = false;
                        //     }
                        // });
                        //
                        // if(canShow && DurPProductPermission){
                        //     return true;
                        // }
                        //
                        // return canShow;
                    // }
                // }
            },
            {
                name: "删除",
                click: function() {
                    confirm("确定删除所选基金吗？", delPProduct);
                },
                // canShowFunc: function(){
                //     return delPProductPermission;
                // }
            }
        ]
    });
    widget.listen();

    function checkHasScale(ppid) {
        var params={};
        params['ppid'] = ppid;
        params[$("#tkName").attr('tkName')] = $("#tkName").val();

        $.ajax({
            'url': checkScale,
            'data': params,
            'dataType': 'json',
            'type': 'POST',
            'success': function (data) {
                if(data.retCode){
                    msgDialog(data.retMsg);
                }else{
                    url = subCreate + '?ppid=' + ppid;
                    window.location.href = url;
                }
            }
        });
    }


    function delPProduct() {
        var chk_ids=[];
        $(":checkbox:checked").each(function(){
            chk_ids.push($(this).attr('data-id'));
        });

        var params={};
        params['ppids'] = chk_ids.join(',');
        params[$("#tkName").attr('tkName')] = $("#tkName").val();
        $.ajax({
            'url': deletePProduct,
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

    function turnDuration() {
        var chk_ids=[];
        $(":checkbox:checked").each(function(){
            chk_ids.push($(this).attr('data-id'));
        });

        var params={};
        params['ppids'] = chk_ids.join(',');
        params['g_tk']  = $("#tkName").val();

        $.ajax({
            'url': durationUrl,
            'data': params,
            'dataType': 'json',
            'type': 'POST',
            'success': function (data) {
                msgDialog(data.retMsg, 'reload');
            }
        });
    }

    $(".check:checked").trigger('click');


    $('.pure-table').delegate('tr', 'mouseover', function() {
        $(this).addClass('pure-table-hover');
    }).delegate('tr', 'mouseout', function() {
        $(this).removeClass('pure-table-hover');
    });

    $('#selectAll').on('click', function() {
        if ($(this)[0].checked)
        {
            $('.check').each(function() {
                this.checked = false;
            });

            $('.check').trigger('click');
        }
        else
        {
            $('.check').each(function() {
                this.checked = true;
            });

            $('.check').trigger('click');
        }
    });

    $('.check').on('click', function() {
        var index = $(this).index('.check');
        this.checked ? $('.pure-table-tr:eq('+ index +')').addClass(' pure-table-selected') :
            $('.pure-table-tr:eq('+ index +')').removeClass(' pure-table-selected');
    });

    $('#reset').on("click",function(){
        $("#fund_code").val("");
    });

    function confirm(content,callback,params)
    {
        return art.dialog({
            id: 'Confirm',
            icon: 'question',
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