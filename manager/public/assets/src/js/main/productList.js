/**
 * Created by hexi on 14-10-13.
 */
require([
    'jquery',
    'widgets/SingleMultiBtn',
    'widgets/ConfirmBtn',
    'art-dialog',
], function ($, SingleMultiBtn, ConfirmBtn, placeholder) {
    $('#corps').on('change', function () {
        $('#corpForm').submit();
    });

    var widget = new SingleMultiBtn("#buttonHolder", {
        singleBtns: [{
            name: "编辑子产品",
            click: function(id) {
                url = urls.editUrl + '?pid='+id;
                window.location.href= url;
            }
        },
            //{
            //    name: "复制子产品",
            //    click: function(id) {
            //        url = urls.copyUrl + '?pid='+id + '&type=copy';
            //        window.location.href= url;
            //    }
            //},
        ],
        commonBtns: [
            {
                name: "删除",
                click: function(id) {
                    confirm("确定删除所选子产品吗？", delProduct);
                },
                canShowFunc: function() {
                    return delProductPermission;
                }
            }
            ,
            {
                name: "下架",
                click: function(id) {
                    confirm("确定下架所选子产品吗？", offOnlineProduct);
                },
                canShowFunc: function() {
                    return offlinePermission;
                }
            }
        ]
    });

    widget.listen();

    //快速发布子产品
    function fastPublish() {
        var chk_ids=[];
        $(":checkbox:checked").each(function(){
            chk_ids.push($(this).attr('data-id'));
        });

        var params={};
        params['pid'] = chk_ids.join(',');
        params[$("#tkName").attr('tkName')] = $("#tkName").val();

        $.ajax({
            type:"POST",
            url :window.fastPublish,
            data:params,
            dataType:'json',
            success:function(data){
                if(!data.retCode){
                    msgDialog(data.retMsg, 'reload');
                }else{
                    msgDialog(data.retMsg);
                }
            }
        });
    }

    //下架子产品
    function offOnlineProduct() {
        var chk_ids=[];
        $(":checkbox:checked").each(function(){
            chk_ids.push($(this).attr('data-id'));
        });

        var params={};
        params['pids'] = chk_ids.join(',');
        params[$("#tkName").attr('tkName')] = $("#tkName").val();

        $.ajax({
            'url': offlineProduct,
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

    //删除子产品
    function delProduct() {
        var chk_ids=[];
        $(":checkbox:checked").each(function(){
            chk_ids.push($(this).attr('data-id'));
        });

        var params={};
        params['pids'] = chk_ids.join(',');
        params[$("#tkName").attr('tkName')] = $("#tkName").val();

        $.ajax({
            'url': deleteProduct,
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

    $(".check:checked").trigger('click');

    $('.check').on('click', function() {
        var index = $(this).index('.check');
        this.checked ? $('.pure-table-tr:eq('+ index +')').addClass(' pure-table-selected') :
            $('.pure-table-tr:eq('+ index +')').removeClass(' pure-table-selected');
    });

    $('.pure-table').delegate('tr', 'mouseover', function() {
        $(this).addClass('pure-table-hover');
    }).delegate('tr', 'mouseout', function() {
        $(this).removeClass('pure-table-hover');
    });
});

//自动生成子产品
function fastSaveProduct() {
    var chk_ids=[];
    $(":checkbox:checked").each(function(){
        chk_ids.push($(this).attr('data-id'));
    });

    var params = {};
    params[$("#tkName").attr('tkName')] = $("#tkName").val();
    params['pid'] = chk_ids;
    $.ajax({
        type:"get",
        url :window.fastEdit,
        data:params,
        dataType:'json',
        success:function(data){
            if(!data.retCode){
                msgDialog(data.retMsg, 'reload');
            }else{
                msgDialog(data.retMsg);
            }
        }
    });
}

function setOrderBy(orderBy, direction) {
    $('#orderBy').val(orderBy);
    $('#direction').val(direction);
    $('.pure-form').submit();
}

//子产品置顶
function setTop(pid,action){
    var params = {};
    params[$("#tkName").attr('tkName')] = $("#tkName").val();
    params['pid'] = pid;
    params['act'] = action;
    $.ajax({
        type:"get",
        url :'/product/p/SetTop',
        data:params,
        dataType:'json',
        success:function(data){
            if(!data.retCode){
                msgDialog(data.retMsg, 'reload');
            }else{
                msgDialog(data.retMsg);
            }
        }
    });
}

//批量生成子产品草稿
function batch_product(product,pids) {
    var content_val = '';
    for(var i = 0;i < product.length;i++){
        var totalShare = '';
        if(product[i]['totalShare']==0){
            totalShare = '<span style="color:red">' +  product[i]['totalShare'] + '</span>';
        }else{
            totalShare = product[i]['totalShare'];
        }

        content_val += '<tr>' +
        '<td style="text-align: left;">' + product[i]['shortName'] + '</td>' +
        '<td>' + totalShare + '万' + '</td>' +
        '<td>' + product[i]['sellBeginTime'] + '</td>' +
        '</tr>'
    }

    tabContent = '<table class="pure-table">' +
        '<thead>' +
        '<tr>' +
        '<th style="width:500px;">产品名称</th>' +
        '<th class="w_120">产品额度</th>' +
        '<th class="w_200">开售时间</th>' +
        '</tr>' +
        '</thead>' +
        content_val;

    confirm(tabContent, fastSaveProduct, '' ,true);
}

//批量生成子产品草稿
function getProductByPid(pids){
    var params = {};
    params[$("#tkName").attr('tkName')] = $("#tkName").val();
    params['pid'] = pids;
    $.ajax({
        type:"get",
        url : window.getProductByPids,
        data:params,
        dataType:'json',
        success:function(data){
            if( !data.retCode ){
                batch_product(data.retData,pids);
            }else {
                msgDialog(data.retMsg);
            }
        }
    });
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
