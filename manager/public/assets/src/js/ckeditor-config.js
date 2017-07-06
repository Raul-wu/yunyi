define([
    'jquery',
    'qiniu'
], function ($, Qiniu) {


    var options = {
        'tbl':{
            toolbar :[
                ['Bold'],
                ['NumberedList'],
                ['Table'],
                ['Maximize']
            ]
        }
    };

    $.extend(CKEDITOR.config, {
        customConfig: null,
        language: 'zh-cn',
        toolbarGroups: [
            { name: 'styles' },
            { name: 'colors'},
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
            { name: 'links' },
            { name: 'insert' },
            { name: 'others' },
            { name: 'paragraph',   groups: [ 'align', 'list', 'indent' ] },
            { name: 'multiimg' },
            { name: 'tools' },
            { name: 'document', groups: [ 'mode' ] }
        ],

//        toolbarGroups: [
//            { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
//            { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
//            { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
//            { name: 'forms' },
//            '/',
//            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
//            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
//            { name: 'links' },
//            { name: 'insert' },
//            '/',
//            { name: 'styles' },
//            { name: 'colors' },
//            { name: 'tools' },
//            { name: 'others' },
//            { name: 'about' }
//        ],

        // Remove some buttons provided by the standard plugins, which are
        // not needed in the Standard(s) toolbar.
        removeButtons: 'Underline,Subscript,Superscript,Cut,Copy,Paste,Styles,Anchor,Flash,PageBreak,Iframe,Font,FontSize,Smiley,SpecialChar,ShowBlocks,Save,NewPage,Preview,Print',

        // Set the most common block elements.
        format_tags: 'p;h1;h2;h3;pre',

        // Simplify the dialog windows.
        removeDialogTabs: 'image:advanced;link:advanced',

        //filebrowserBrowseUrl: '/product/browse',
        //filebrowserImageBrowseUrl: '/product/browse',
        //filebrowserUploadUrl: '/product/upload',

        removePlugins: 'elementspath',//移除掉底部dom路径
        resize_enabled: false,

        extraPlugins: 'autogrow',
        autoGrow_onStartup: true,
        autoGrow_maxHeight: 500
    });

    CKEDITOR.on('dialogDefinition', function(e) {

        // Take the dialog name and its definition from the event
        // data.
        var dialogName = e.data.name;
        var dialogDefinition = e.data.definition;

        // Check if the definition is from the dialog we're
        // interested on (the "Link" dialog).
        if ( dialogName == 'image' ) {
            dialogDefinition.minWidth = 600;
            // Add a new tab to the "Link" dialog.
            dialogDefinition.addContents({
                id : 'imgUploader',
                label : '图片上传',
                accessKey : 'M',
                elements : [
                    {
                        type: 'button',
                        id: 'selectFileBtn',
                        label: '选择文件',
                        title: '点击选中需要上传的文件'
                    }
                ]
            }, 'info');

            var old = dialogDefinition.onLoad;
            //初始化
            dialogDefinition.onLoad = function () {
                //alert(333);
                //console.log(dialogDefinition);

                old.apply(this, arguments);

                imgUploaderInit($('#' + this.getContentElement("imgUploader", "selectFileBtn").domId), this);
                this.inited = true;
            };
        }
    });

    //富文本编辑器
    $('textarea[richtext=ckeditor]').each(function(i, h) {
        var editorType = $(h).attr('editorType');
        var setting = editorType ? (options[editorType] ? options[editorType] : {}) :{};

        var editor = CKEDITOR.replace(h, setting);

        $(this).data('editor', editor);
    });

    var imgUploaderInit = function($btn, dialog) {

        var $tip = $('<span id="' + $btn.attr('id') + '_tip" style="display:none">上传中...</span>');
        $tip.insertAfter($btn);

        (new QiniuJsSDK()).uploader({
            runtimes: 'html5,flash,html4',    //上传模式,依次退化
            multi_selection: false,
            qiniu_upurl: 'https://02.up.e-neway.com/',
            browse_button: $btn[0],       //上传选择的点选按钮，**必需**
            uptoken_url: '/uptoken/index',
            //Ajax请求upToken的Url，**强烈建议设置**（服务端提供）
            //uptoken : '<Your upload token>',
            //若未指定uptoken_url,则必须指定 uptoken ,uptoken由其他程序生成
            unique_names: false,
            // 默认 false，key为文件名。若开启该选项，SDK会为每个文件自动生成key（文件名）
            save_key: false,
            // 默认 false。若在服务端生成uptoken的上传策略中指定了 `sava_key`，则开启，SDK在前端将不对key进行任何处理
            domain: "https://02.up.e-neway.com/",
            //bucket 域名，下载资源时用到，**必需**
            container: $btn.parent()[0],           //上传区域DOM ID，默认是browser_button的父元素，
            max_file_size: '200kb',           //最大文件体积限制
            flash_swf_url: 'js/plupload/Moxie.swf',  //引入flash,相对路径
            max_retries: 3,                   //上传失败最大重试次数
            dragdrop: false,                   //开启可拖曳上传
            drop_element: 'container',        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
            chunk_size: '4mb',                //分块上传时，每片的体积
            auto_start: true,                 //选择文件后自动上传，若关闭需要自己绑定事件触发上传
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,gif,png,bmp,jpeg" }
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
                    console.log("BeforeUpload");
                    console.log(up, file);

                    $tip.show();
                }, this),
                'UploadProgress': $.proxy(function(up, file) {
                    // 每个文件上传时,处理相关的事情
                }, this),
                'FileUploaded': $.proxy(function(up, file, info) {
                    console.log("BeforeUpload");
                    console.log(up, file);

                    $tip.hide();

                    console.log("qiniu响应：");
                    console.log(info);

                    var result = $.parseJSON(info);
                    //console.log(result);

                    var txtUrl = dialog.getContentElement("info", "txtUrl");
                    txtUrl.setValue("https://02.up.e-neway.com/" + result.url);
                    txtUrl.focus();

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
                    console.log("Error");
                    console.log(up, err, errTip);

                    $tip.hide();
                    alert("上传出错，请重传");

                    //上传出错时,处理相关的事情
                }, this),
                'UploadComplete': function() {
                    //队列文件处理完毕后,处理相关的事情
                },
                'Key': function(up, file) {
                    var ext, name;

                    ext = Qiniu.getFileExtension(file.name);
                    name = ext ? file.id + '.' + ext : file.id;
                    var $dom = $('[name=' + CKEDITOR.currentInstance.name + ']'), imgPrefix = 'product/';
                    if ($dom.length > 0 && $dom.attr('imgPrefix')) {
                        imgPrefix = $dom.attr('imgPrefix');
                    }
                    console.log(CKEDITOR.currentInstance);
                    return imgPrefix + name;

                    // do something with key here
                }
            }
        });//end Qiniu.uploader

    };

});