require([
    'jquery',
    'art-dialog',
    'widgets/ConfirmBtn',
], function($, placeholder) {
    var isSubmit = false;
    var globalAjaxData = {
        'ajax': '1'
    };
    var csrf = window.csrf;
    if (csrf) {
        globalAjaxData[csrf.name] = csrf.value;
    }
    $.ajaxSetup({
        type: 'POST',
        data: globalAjaxData
    });
    $('#edit').click(function () {
        var id = $('input[name="client_id"]:checked').val();
        if (typeof id == 'undefined') {
            showMsg('请选择要编辑的行');
        } else {
            location.href = '/customer/edit?id=' + id;
        }
    });
    $('#editStatus').click(function () {
        var id = $('input[name="client_id"]:checked').val();
        if (typeof id == 'undefined') {
            showMsg('请选择要更改状态的行');
        } else {
            var status = $('input[name="client_id"]:checked').data('status');
            var html = status == 1 ? '状态&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="status" checked id="status_on" value="1"><label for="status_on" style="width:20px;">&nbsp;&nbsp;正常</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="status" id="status_off"  value="2"><label for="status_off" style="width:20px;">&nbsp;&nbsp;作废</label>' : '状态&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="status" id="status_on" value="1"><label for="status_on" style="width:20px;">&nbsp;&nbsp;正常</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="status" id="status_off"  checked value="2"><label for="status_off" style="width:20px;">&nbsp;&nbsp;作废</label>'
            art.dialog({
                'title': '消息',
                'width': "300px",
                'height': "100px",
                'content': html,
                'lock': true,
                ok: function () {
                    var status = $('input[name="status"]:checked').val();
                    $.ajax({
                        type: 'post',
                        url: '/customer/changeStatus',
                        data: {
                            'clientId': id,
                            'status': status,
                            'type': type
                        },
                        success: function (data) {
                            showMsg(data.retMsg);
                            if (data.retCode == 0) {
                                location.reload();
                            }
                        }
                    });

                }
            });
        }
    });
    function showMsg(content) {
        var msgDialog = art.dialog({
            'title': '消息',
            'width': "300px",
            'height': "100px",
            'content': content,
            'lock': true
        });
    }

    $('button[type=reset]').on('click', function () {
        $('.pure-form').find('input[type=text]').attr('value', "");
        $('.pure-form').find('select').find('option:selected').attr("selected", false);
    })
});