/**
 * Created by ge on 14-8-5.
 */

require([
	'jquery',
	'jquery-form',
	'datepicker-config',
	'widgets/ConfirmBtn',
	'widgets/ImgUpload',
	'Handlebars',
	'text!templates/mgrTeam.html',
	'text!templates/durationInfo.html',
	'text!templates/attachment-item.html',
    'widgets/jquery.combo.select',
    'art-dialog',
], function($, placeholder, placeholder, ConfirmBtn, ImgUpload, Handlebars, mgrTeamTpl, durationInfo, attItem, placeholder) {

    var $form = $("#form");
    var saveAction = $form.attr('saveAction');

    //保存
	$("#save").on('click', function(e) {

        //保存时取消disable
        $(".pure-u-md-1-2 > input").each(function(){
            $(this).removeAttr("disabled");
        });
        $(".pure-u-md-1-2 > select").each(function(){
            $(this).removeAttr("disabled");
        });

		e.preventDefault();
		var $this = $(this);

		if (!$this.data('lock')) {
			$this.data('lock', 1);
		} else {
			return;
		}

		$('textarea[richtext=ckeditor]').each(function(i, h) {
			h.value = $(h).data('editor').getData();

		});

		$form.attr('action', $form.attr('saveAction'));

		$form.ajaxSubmit(function(data) {
			$(".error").removeClass("error");
            $('#imgUpload1').next('span').next('div').remove();
console.log("aaaa");
            //var turnError = true;
			if (!data.retCode) {
                msgDialog(data.retMsg, data.retData.url);
			} else {
                msgDialog(data.retMsg);

                for (var i in data.retData) {
                    //if (turnError)
                    //{
                    //    $("[name="+i+"]").focus().blur();
                    //}
                    if(i=='spvId')
                    {
                        $('.combo-select').find('input').addClass('error');
                    }
                    if(i=='videoImg')
                    {
                       $('#imgUpload1').next('span').after('<div class="tip-twitter tip-arrow-top" >'+data.retData[i]+'</div>');
                    }
                    $("[name="+i+"]").addClass('error').focus(function() {
                        $(this).removeClass('error');
                        $(this).next('span').next('div').remove();

                    });
                    $("[name="+i+"]").next('span').next('div').remove();
                    $("[name="+i+"]").next('span').after('<div class="tip-twitter tip-arrow-top" >'+data.retData[i]+'</div>');
                    $("."+i+"").next('span').next('div').remove();
                    $("."+i+"").next('span').after('<div class="tip-twitter tip-arrow-top" >'+data.retData[i][0]+'</div>');
                    //turnError = false;

                }
            }

			$this.data('lock', 0);
		});

	});


    $(document).ready(function(){

    });


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
