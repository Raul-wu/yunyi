/**
 * Created by lwj5577 on 2014/12/4.
 */
define(function () {

    var SingleMultiBtn = function() {
        this.init.apply(this, arguments);
    };

    SingleMultiBtn.prototype = {
        btnIdIncr: 0,
        btnShow: [],
        init: function(btnPh, options) {
            this.options = {
                beforBtn: '<a id="{id}" href="{url}" class="pure-button pure-button-primary">',
                afterBtn: '</a>',
                singleBtns: [],
                commonBtns: [],
                multiBtns: []
            };

            $.extend(this.options, options);

            this.options.checkBoxSeltor = options.checkBoxSeltor || ".check";

            this.options.dataAttrName = options.dataAttrName || "data-id";

            this.insertBtns(this.options.singleBtns, this.options.commonBtns, this.options.multiBtns, this.options.beforBtn, this.options.afterBtn, this, btnPh);
        },
        insertBtn: function(btnType, btnData, beforBtn, afterBtn, context, $insertAfter) {

            beforBtn = btnData.beforBtn || beforBtn;
            afterBtn = btnData.afterBtn || afterBtn;

            var btnStr = beforBtn + btnData.name + afterBtn + " ";

            btnStr = btnStr.replace("{url}", btnData.url || "javascript:;");
            var id = "btn_" + this.btnIdIncr++;
            var $btn = $(btnStr)
                .attr("id", id)
                .addClass(btnType)
                .hide()
                .click(function(e) {
                    if (btnData.click) {
                        btnData.click(btnType == "singleBtn" ? context.getSelected()[0] : context.getSelected());
                    }
                    if (btnData.url) {
                        e.preventDefault();
                    }
                });

            $btn.insertAfter($insertAfter);
            if (btnData.canShowFunc) {
                this.btnShow.push({id: id, canShowFunc: btnData.canShowFunc});
            }

            return $btn;
        },
        insertBtns: function(singleBtns, commonBtns, multiBtns, beforBtn, afterBtn, context, btnPh) {
            var $lastDom = $(btnPh);
            var _this = this;
            $.each(singleBtns, function(i, h) {
                $lastDom = _this.insertBtn("singleBtn", h, beforBtn, afterBtn, context, $lastDom);
            });
            $.each(commonBtns, function(i, h) {
                $lastDom = _this.insertBtn("commonBtn", h, beforBtn, afterBtn, context, $lastDom);
            });
            $.each(multiBtns, function(i, h) {
                $lastDom = _this.insertBtn("multiBtn", h, beforBtn, afterBtn, context, $lastDom);
            });
        },
        listen: function(selector) {
            selector = selector || this.options.checkBoxSeltor;
            var self = this;

            $(selector).click(function() {
                var selected = self.getSelected();
                $.each(self.btnShow, function(i, h) {
                    if(h.canShowFunc(selected)) {
                        $("#" + h.id).removeAttr("noShow");
                    } else {
                        $("#" + h.id).attr('noShow','noShow');
                    }
                });

                if (selected.length > 1) {
                    self.multi();
                } else if (selected.length == 1) {
                    self.single();
                } else {
                    self.hide();
                }
            });
        },
        getSelected: function() {
            var selected = [];

            var self = this;
            $(this.options.checkBoxSeltor).each(function (i, h) {
                var id;
                if (h.checked && (id = $(h).attr(self.options.dataAttrName))) {
                    selected.push(id);
                }
            });
            return selected;
        },
        single: function() {
            this.hide();
            $(".singleBtn:not([noShow])").show();
            $(".commonBtn:not([noShow])").show();
        },
        multi: function() {
            this.hide();
            $(".commonBtn:not([noShow])").show();
            $(".multiBtn:not([noShow])").show();
        },
        hide: function() {
            $(".singleBtn").hide();
            $(".commonBtn").hide();
            $(".multiBtn").hide();
        }
    };

    return SingleMultiBtn;
});