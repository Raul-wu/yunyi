/**
 * Created by rwu on 10/12/17.
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
                name: "编辑",
                click: function(id) {
                    window.location.href= editUrl + '?cid='+id;
                }
            },
        ],
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
        $("#name").val("");
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
});
