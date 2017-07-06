define(['jquery'], function($) {

  (function($) {
    var privateFunction = function() {
        // 执行代码
    }
    //初始化，
    var methods = {
 
        init: function(options) {
 
            // 在每个元素上执行方法
            this.each(function() {
                var $this = $(this);
                
               
                // 尝试去获取settings，如果不存在，则返回“undefined”
                var settings = $this.data('settings');
      
     
                // 如果获取settings失败，则根据options和default创建它
                if(typeof(settings) == 'undefined') {
 
                    var defaults = {
                        width: '399px'
                    }
 
                    settings = $.extend({}, defaults, options);
 
                    // 保存我们新创建的settings
                    $this.data('settings', settings);
            
                  
                } else {
                    // 如果我们获取了settings，则将它和options进行合并（这不是必须的，你可以选择不这样做）

                    settings = $.extend({}, settings, options);
 
                    // 如果你想每次都保存options，可以添加下面代码：
                   
                    $this.data('settings', settings);
    
                }

                var a = $this.data('settings');

                // 执行代码     
            });
        },

        elCreate : function(item){
          companyArea = $(this);
          settings = $(this).data('settings');

          if(settings.dataArea)
          {
             var dataArea = $(settings.dataArea).width(settings.width);
          }
          companyCancel = dataArea.width(settings.width).find("#cancal").click(function(){
            isExpend = false;
            dataArea.hide();
          });

          selectCompany = $('<input placeholder="全部" type="text"class="companyInfo"  style="outline: none; padding-right: 20px;  background: none;" id="selectCompany">').width(settings.width).focus(function(){
              var offset = $(this).offset(),
              left = offset.left,
              top = offset.top+36;
              isExpend = true;
              dataArea.css({'left':left,'top':top});
              dataArea.show();
              $(this).blur(); 
          });

          //全选
          dataArea.find("#selectAll").click(function(){
    
            dataArea.find(":checkbox").each(function(){
                  this.checked = true;
              });
          });
          //反选
          dataArea.find("#revAll").click(function(){

            dataArea.find(":checkbox").each(function(){

                if (this.checked) {
                    this.checked = false; 
                }
                else {
                    this.checked = true;
                    
                }
            });
          });

          dataArea.find('#confirm').click(function(){
                
                //检查是否没选
                var num = dataArea.find(":checked").size();
                var total = dataArea.find(":checkbox").size();

                if(num<=0)
                {
                  companyCancel.trigger("click");
                  return false;
                }

                if(num==total)
                {
                  companyCancel.trigger("click");
                  return false;
                }
                companyStr = '';
                companySelect = new Array();

                dataArea.find(":checked").each(function(){
                     
                        var name = $(this).attr('companyName');
                        var id = $(this).attr('_id');
                        var companyLable = "<lable name='companyLable' _id="+id+">"+name+"&nbsp;&nbsp;</lable>";    
                        
                      companyStr += companyLable;   

                      companySelect.push(id);       
                      
                });
                  
                companyArea.empty().append(companyStr);

                $("[name=companyLable]").hover(
                function () {
                  $(this).addClass("company-lable-hover");
                },
                function () {
                  $(this).removeClass("company-lable-hover");
                }
              ).click(function(){
                  $(this).remove();
                  var id = $(this).attr('_id');

                  var index = companySelect.indexOf(id);

                  delete companySelect[index];
                  
                  if(companyArea.text() == '')
                  {
                   
                    companyArea.append(selectCompany.focus(function(){
                      var offset = $(this).offset(),
                      left = offset.left,
                      top = offset.top+36;
                      isExpend = true;
                      dataArea.css({'left':left,'top':top});
                      dataArea.show();
                      $(this).blur(); 
                    }));

                    dataArea.find(":checkbox").each(function(){
                          this.checked = true;
                    });

                    companyCancel.trigger("click");
                  }
                });
                companyCancel.trigger("click");
              });
          $(document).bind("click",function(e){
              var target  = $(e.target);
              if(target.closest(".companyInfo").length == 0){
                  companyCancel.trigger("click");
              }
          });

         
          if(settings._data)
          {

              $(this).append(selectCompany);
              dataArea.find('#confirm').trigger('click');
          }
          else
          {
             $(this).append(selectCompany);
          }
        }
    };
 
    $.fn.multiSelect = function() {
        var method = arguments[0];

        if(methods[method]) {
            method = methods[method];
            arguments = Array.prototype.slice.call(arguments, 1);
        } else if( typeof(method) == 'object' || !method ) {
            init = methods.init;
            elCreate = methods.elCreate;     
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.multiSelect' );
            return this;
        }
        
         //methoda.apply(this, arguments);
        init.apply(this, arguments);
        //绑定
        elCreate.apply(this, arguments);
 
    }
 
})(jQuery);

/**
 * 绑定事件
 */
  
})
