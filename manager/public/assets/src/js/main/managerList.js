require(['jquery', 'jquery-form', 'widgets/Grid', 'widgets/Dialog', 'zTree'],
function($, placeholder, Grid, dialog, tree) {

/* 列表页的js start，没特殊添加可以不用动 */
    var g_tk = $("[name=g_tk]").val();
    var param = {
        'page': 1,
        'pageSize': 30,
        'g_tk': g_tk
    };

    var $findRole = $(".showRole");
    var $managerRole = $(".managerRole");

    var setting = {
        data: {
            simpleData: {
                enable: true
            }
        }
    };

    /* 列表页的js end*/

    $(function() {
        //初始化的一些js
        $findRole.click(function(){
            param.id = $(this).attr('_id');
            var name = $(this).text();

            $.post(menuUrl, param,
                function(data) {

                    var node = [];
                    $.each(data.retData, function(i,n){
                        node.push(n);
                    });
                    dialog.open({
                        title: '查看权限-' + name,
                        template: $('#showMenu').html()
                    });
                    $.fn.zTree.init($(".J_template #treeDemo"), setting, node);
                });

        });

        $managerRole.click(function(){

            var id = $(this).attr('_id');
            if($("#"+id).is(":hidden"))
            {
                $("#"+id).show();
            }
            else
            {
                $("#"+id).hide();
            }
        });






    });
    

});