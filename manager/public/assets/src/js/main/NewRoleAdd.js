require([
    'jquery',
    'zTree',
    'zTreeCheck'
], function($, tree, treeCheck) {


    var $save = $("#save");

    var $submitInput = $(".submit");
    var setting = {
        check: {
            enable: true
        },
        data: {
            simpleData: {
                enable: true
            }
        }
    };

    var zNodes = allAuthority;


    function setCheck() {
        var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
        type = { "Y":"ps", "N":"s"};
        zTree.setting.check.chkboxType = type;
    }

    var tree = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
    setCheck();

    $save.click(function() {

        var authority = [];

       var nodes = tree.getCheckedNodes(true);

        for (var i=0, l=nodes.length; i<l; i++) {
            authority.push(nodes[i].permissionId);
        }

        var g_tk = $("[name=g_tk]").val();
        var param = {
            'g_tk': g_tk
        };

        $submitInput.map(function(){
            var key = $(this).attr('name');
            var value = $(this).val();
            param[key] = value;
        });
        param.state = $(":radio:checked").val();
        param.authority = authority;

        //异步请求保存结果
        $.post(saveUrl, param,
        function(data) {

            $(".error").removeClass("error");
            if (data.retCode == 0) {
                alert(data.retMsg);
                location.href = document.referrer;
            } else {
                if (data.retData) {
                    var alertMsg = '';
                    $.each(data.retData,
                        function (i, v) {
                            $("[name=" + i + "]").addClass('error');
                            alertMsg += v + '\n\n';
                        });
                    alert(alertMsg);
                }
            }
        });
    });
});