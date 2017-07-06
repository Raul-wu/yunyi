/**
 * Created by 9020MT on 14-11-13.
 */
define(['jquery-org'], function () {
    var _width;
    $(document).ready(function(){

        _width = $(window).width() - 200;
        $(window).resize(function() {
            _width = $(window).width() - 200;
        });

        $(window).scroll(function() {
            var _top = $(document).scrollTop();
            if(_top > 70){
                $(".fix_panel").addClass("quick_action_fix").css("width",_width);
            }
            else{
                $(".fix_panel").removeClass("quick_action_fix");
            }
        }).trigger("scroll");

        $(".user_status .more").click(function() {
            $(".user_status p").toggle()
        });
    });
    return $;
});