define([
    'jquery',
    'Handlebars',
    'qiniu',
    'text!templates/imgUpload.html',
    'text!templates/imgUpload-item.html',
    'text!templates/pdfUpload-item.html',
    'text!templates/excelUpload-item.html',
    'text!templates/imgUpload-loading.html'
], function ($, Handlebars, Qiniu, tpl, itemTpl, pdfItemTpl, excelItemTpl, loadingTpl) {
    var ExcelUpload = function() {
        this.init.apply(this, arguments);
    };

    ExcelUpload.count = 0;
    ExcelUpload.prototype = {
        init: function(placeholder, options) {
            var _self = this;

            this.id = ExcelUpload.count++;
            var $placeholder = $(placeholder);
            if (!$placeholder.length) return;

            options = options || {};

            options.pathPrefix = options.pathPrefix || "";

            this.options = {
                maxUpload: 1,//0不限
                inputName: $placeholder.attr('inputName') || options.inputName || "", //是否需要input[type=hidden]
                files: ($placeholder.attr('url') && $placeholder.attr('url').split('|')) || options.files || [], //已上传的文件url
                fileUrlPrefix: options.fileUrlPrefix || 'https://02.up.e-neway.com/',
                getPath: options.getPath || function() { return options.pathPrefix; },
                uploaded: options.uploaded || function() { return options.uploaded; },
                isbatch: $placeholder.attr('isbatch') || options.isbatch || false,
                isorgiName: options.isorgiName || false,
                token_url: options.token_url || '/uptoken/index',
                linkUrl: options.linkUrl || 'javascript:;',
                externalParams: options.externalParams || {},
                max_file_size : options.max_file_size || '100mb'
            };

            this.options = $.extend(options, this.options);

            var html = Handlebars.compile(tpl)({id: this.id});
            this.itemTE = Handlebars.compile(itemTpl);
            this.loadingTE = Handlebars.compile(loadingTpl);
            this.pdfItemTE = Handlebars.compile(pdfItemTpl);
            this.excelItemTE = Handlebars.compile(excelItemTpl);

            $placeholder.replaceWith(html);
            this.$wrap = $('#imgUpload' + this.id);
            this.$btn = $('#uploadBtn' + this.id);

            this.itemCount = 0;
            this.itemId = 0;
            this.loadingCount = 0;
            this.errorTip = '';

            $.each(this.options.files, $.proxy(function(i, file) {
                if (!file) return;

                this.addItem(file);

            }, this));

            this.$wrap.delegate('.j_del', 'click', $.proxy(function(e) {
                e.preventDefault();

                var $target = $(e.currentTarget);

                this.removeItem($target.data('id'));
            }, this));


            (new QiniuJsSDK()).uploader({
                runtimes: 'html5,flash,html4',    //上传模式,依次退化
                multi_selection: _self.options.isbatch,
                qiniu_upurl: 'https://up.qbox.me/' ,
                browse_button: this.$btn[0],       //上传选择的点选按钮，**必需**
                uptoken_url: this.options.token_url,
                //Ajax请求upToken的Url，**强烈建议设置**（服务端提供）
                //uptoken : '<Your upload token>',
                //若未指定uptoken_url,则必须指定 uptoken ,uptoken由其他程序生成
                unique_names: false,
                // 默认 false，key为文件名。若开启该选项，SDK会为每个文件自动生成key（文件名）
                save_key: false,
                // 默认 false。若在服务端生成uptoken的上传策略中指定了 `save_key`，则开启，SDK在前端将不对key进行任何处理
                domain: this.options.fileUrlPrefix,
                //bucket 域名，下载资源时用到，**必需**
                container: this.$wrap[0],           //上传区域DOM ID，默认是browser_button的父元素，
                //max_file_size: '100mb',           //最大文件体积限制
                max_file_size: this.options.max_file_size,           //最大文件体积限制
                flash_swf_url: 'js/plupload/Moxie.swf',  //引入flash,相对路径
                max_retries: 3,                   //上传失败最大重试次数
                dragdrop: false,                   //开启可拖曳上传
                drop_element: 'container',        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
                chunk_size: '4mb',                //分块上传时，每片的体积
                auto_start: true,                 //选择文件后自动上传，若关闭需要自己绑定事件触发上传
                filters: {
                    mime_types : [
                        { title : "Image files", extensions : this.options.mimeTypes ? this.options.mimeTypes : "jpg,gif,png,bmp,jpeg" }
                    ]
                },
                init: {
                    'FilesAdded': $.proxy(function(up, files) {
                        plupload.each(files, function(file) {
                            // 文件添加进队列后,处理相关的事情
                        });
                    }, this),
                    'BeforeUpload': $.proxy(function(up, file) {
                        // 每个文件上传前,处理相关的事情
                        _self.options.originName = file.name;
                        if(_self.options.isorgiName)
                        {
                            var path = _self.options.getPath();
                            var keyUp = path + file.name;
                            this.getToken(up,keyUp);
                        }

                        this.addLoading(file.id);
                    }, this),
                    'UploadProgress': $.proxy(function(up, file) {
                        // 每个文件上传时,处理相关的事情
                    }, this),
                    'FileUploaded': $.proxy(function(up, file, info) {
                        this.removeLoading(file.id);
                        var result = $.parseJSON(info);
                        imgSrc = this.options.fileUrlPrefix + result.url;
                        _self.options.uploaded(imgSrc, _self);
                        this.addItem(this.options.fileUrlPrefix + result.url);
                        // 每个文件上传成功后,处理相关的事情
                        // 其中 info 是文件上传成功后，服务端返回的json，形式如
                        // {
                        //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
                        //    "key": "gogopher.jpg"
                        //  }
                        // 参考http://developer.qiniu.com/docs/v6/api/overview/up/response/simple-response.html
                        // var domain = up.getOption('domain');
                        // var res = parseJSON(info);
                        // var sourceLink = domain + res.key; 获取上传成功后的文件的Url
                    }, this),
                    'Error': $.proxy(function(up, err, errTip) {
                        //上传出错时,处理相关的事情
                        $(".imgError").css('color','red');
                        $(".imgError").html(errTip);
                    }, this),
                    'UploadComplete': function() {
                        //队列文件处理完毕后,处理相关的事情
                    },
                    'Key': function(up, file) {
                        var path = _self.options.getPath(), ext, name;

                        // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
                        // 该配置必须要在 unique_names: false , save_key: false 时才生效
                        if (_self.options.isorgiName) {
                            name = file.name;
                        } else {
                            ext = Qiniu.getFileExtension(file.name);
                            name = ext ? file.id + '.' + ext : file.id;
                        }

                        var key = path + name;

                        return key;
                    }
                }
            });

        },
        getToken: function(up, keyUp) {
            var params = {key:keyUp};
            if(up.runtime == 'html4') {
                params = {
                    redirect:1
                };
            }
            params[csrf.name] =csrf.value;

            $.ajax({
                url:this.options.token_url,
                type:'POST',
                data:params,
                async:false
            }).success(function(data) {
                var data = eval('('+data+')');
                up.settings.multipart_params.token =  data.uptoken;
            }).error(function($errMsg) {
                $.showInfoTab($errMsg);
            });
        },
        addLoading: function(id) {
            this.loadingCount++;

            $(this.loadingTE({
                wrapId: this.id,
                id: id
            })).insertBefore(this.$btn);

            if (this.options.maxUpload > 0 && (this.itemCount + this.loadingCount) >= this.options.maxUpload && !this.options.isbatch) {
                this.hideSelectFile();
            }
        },
        removeLoading: function(id) {
            this.loadingCount--;

            $('#loading' + this.id + '-' + id, this.$wrap).remove();
        },
        addItem: function(fileUrl) {
            var itemId = this.itemId++;

            this.itemCount++;
            if (!this.files) this.files = {};
            this.files[itemId] = fileUrl;
            if (this.options.uploadType == 'pdf' ) {
                $(this.pdfItemTE({
                    wrapId: this.id,
                    itemId: itemId,
                    href: this.options.linkUrl + encodeURIComponent(fileUrl),
                    attachmentUrl: fileUrl
                })).insertBefore(this.$btn);
                this.options.uploadedFunc ? this.options.uploadedFunc(this.options.originName, fileUrl) : '';
            } else if (this.options.uploadType == 'excel') {
                $(this.excelItemTE({
                    wrapId: this.id,
                    itemId: itemId,
                    href: this.options.linkUrl + encodeURIComponent(fileUrl),
                    attachmentUrl: fileUrl
                })).insertBefore(this.$btn);
                this.options.uploadedFunc ? this.options.uploadedFunc(this.options.originName, fileUrl) : '';
            }
            else {
                $(this.itemTE({
                    wrapId: this.id,
                    itemId: itemId,
                    inputName: this.options.inputName,
                    fileUrl: fileUrl
                })).insertBefore(this.$btn);
            }

            if (this.options.maxUpload > 0 && (this.itemCount + this.loadingCount) >= this.options.maxUpload && !this.options.isbatch) {
                this.hideSelectFile();
            }
        },
        removeItem: function(id) {


            $('#item' + this.id + '-' + id).remove();
            this.options.deleteFunc ? this.options.deleteFunc(this.options.externalParams) : '';

            if (this.options.maxUpload > 0 && (this.options.maxUpload > (this.itemCount - 1))) {
                this.showSelectFile();
            }

            this.itemCount--;
            delete this.files[id];
        },

        removeAllItems: function() {
            for (var i in this.files)
            {
                this.removeItem(i);
            }
        },

        showSelectFile: function() {
            this.$btn.show();
        },

        hideSelectFile: function() {
            this.$btn.hide();
        }
    };

    return ExcelUpload;
});