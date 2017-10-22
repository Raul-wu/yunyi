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
        singleBtns: [
        {
            name: "查看",
            click: function(id) {
                window.location.href= selectUrl + '?qid='+id;
            }
        },
         {
            name: "编辑",
            click: function(id) {
                window.location.href= editUrl + '?qid='+id;
            }
        },
        {
            name: "变更",
            click: function(id) {
                window.location.href= changeUrl + '?qid='+id;
            }
        },
        ],
        commonBtns: [
        {
            name: "删除",
            click: function() {
                confirm("是否确认删除客户份额？", deleteQuotients);
            },
        }
    ]
    });

    function deleteQuotients() {
        var chk_ids=[];
        $(":checkbox:checked").each(function(){
            chk_ids.push($(this).attr('data-id'));
        });

        var params={};
        params['qids'] = chk_ids.join(',');
        params[$("#tkName").attr('tkName')] = $("#tkName").val();

        $.ajax({
            'url': delQuotient,
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
        $("#name").val("");
        $("#fund_name").val("");
        $("#quotient_name").val("");
        $("#id_card").val("");
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

    $('#export').on("click",function(){
        var chk_ids=[];
        $(":checkbox:checked").each(function(){
            chk_ids.push($(this).attr('data-id'));
        });

        var params={};
        params['qids'] = chk_ids.join(',');

        var pids = GetQueryString("pid");

        if(!params['qids'] && !pids) {
            msgDialog("请选择需要导出的客户份额");
            return false;
        }

        location.href= exportUrl + "?qids=" + params['qids'] + "&pids=" + pids;
    });

    function GetQueryString(name)
    {
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    }
});



