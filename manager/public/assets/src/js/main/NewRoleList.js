require(['jquery', 'jquery-form', 'widgets/Grid', 'widgets/Dialog', 'zTree'],
function($, placeholder, Grid, dialog, tree) {

/* 列表页的js start，没特殊添加可以不用动 */
    var g_tk = $("[name=g_tk]").val();
    var param = {
        'page': 1,
        'pageSize': 30,
        'g_tk': g_tk
    };
    var $editNews = $("#editNews");

    var $findRole = $("#findRole");

    var $reset = $("#reset");

    var setting = {
        data: {
            simpleData: {
                enable: true
            }
        }
    };


    var Grid = new Grid("#gridTable", {
        columns : header,
        /*definedRender tbody中没有特殊的展示，可以不用动*/
        definedRender: {
            0: {
                renderer: function (_id) {

                    return '<input type="checkbox" value="{value}" style="height:18px;margin-top:1px;" />'.format(_id)
                }
            },
            7: {
                renderer: function (_id) {
                    return '<a _id="{_id}" href="javascript:;" class="roleRight">查看权限</a>'.format({
                        _id: _id
                    });
                }
            }
        },
        listUrl :window.listUrl,
       /* 绑定grid中的事件，只有展示功能的话，不用动*/
       events: {
           'click :checkbox': function(){
               if($(":checked").size()== 1)
               {
                   $editNews.removeClass('pure-button-disabled').addClass('pure-button-primary');
               }
               else
               {
                   $editNews.removeClass('pure-button-primary').addClass('pure-button-disabled');
               }
           },
           'click .roleRight': function(event){

               param.id = $(event.toElement).attr('_id');

               $.post(menuUrl, param,
                  function(data) {

                   var node = [];
                   $.each(data.retData, function(i,n){
                       node.push(n);
                   });
                  dialog.open({
                      title: '查看权限',
                      template: $('#showMenu').html()
                  });
                   $.fn.zTree.init($(".J_template #treeDemo"), setting, node);



               });
               //先AJAX获取数据
             //

           }
        },
        sortEvent: function(field, asc) {
            param.orderBy = field;
            param.asc = asc;
            Grid.getData(param);
        },
        pageChange: function(page) {
            param.page = page;
            Grid.getData(param);
        }
    });
    /* 列表页的js end*/

    $(function() {
        //初始化的一些js


        Grid.getData(param);
        $editNews.click(function(){
            if($(this).hasClass('pure-button-disabled'))
            {
                return false;
            }
            var id = $(":checked").val();
            window.location.href = editUrl + '?id=' + id;
        });

        $findRole.click(function(){
            param.roleName = $("#roleName").val();
            Grid.getData(param);

        });

        $reset.click(function(){
            $("#roleName").val('');
            param.roleName = '';
        });






    });
    

});