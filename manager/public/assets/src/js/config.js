

require.config({

    urlArgs: Date.parse(new Date()),
    waitSeconds: 0,
    baseUrl: "/assets/src/js/",
    shim: {
        'ckeditor-config': {
        //'deps': ['ckeditor']
        },
        'jquery-ui': {
            deps: ['jquery']
        },
        'jquery-number' : {
            deps :['jquery']
        },
        'numberTip' : {
            deps :['jquery']
        },
        'jquery-form': {
            exports: 'jquery-form',
            deps: ['jquery']
        },
        'art-dialog':{
            exports:'art-dialog',
            deps:['jquery']
        },
        'jquery-autosize': {
            deps: ['jquery']
        },
        'timepicker': {
            deps: ['jquery-ui']
        },
        'datepicker-config': {
            deps: ['timepicker']
        },
        'Handlebars': {
            exports: 'Handlebars'
        },
        'plupload': {
            exports: 'plupload'
        },
        'qiniu': {
            exports: 'Qiniu',
            deps: ['plupload']
        },
        'QiniuSDK': {
            exports: 'QiniuSDK',
            deps: ['plupload']
        },
        'loadings':{
            exports: 'loading',
            deps:['jquery-ui']
        },
        'zTree': {
            exports: 'zTree',
            deps :['jquery']
        },
        'zTreeCheck': {
            exports: 'zTreeCheck',
            deps :['jquery','zTree']
        },
        'pagination':{
            exports: 'pagination',
            deps:['jquery-ui']
        },
        'fullScreen': {
            exports: 'fullScreen',
            deps: ['jquery']
        },
        'highcharts' : {
            exports: 'highcharts',
            deps: ['jquery']
        },
        'bootstrap-datetimepicker':{
            exports: 'bootstrap-datetimepicker',
            deps:['jquery']
        }
    },


    paths: {
        'text': '../../bower_components/requirejs-text/text',
        'jquery-org': '../../bower_components/jquery/dist/jquery.min',
        'jquery': 'jquery',
        'jquery-form': '../../bower_components/jquery-form/jquery.form',

        'art-dialog': 'lib/art_dialog/jquery.artDialog.source',

        'jquery-ui': 'lib/jquery-ui-1.11.0.custom/jquery-ui.min',
        'jquery-number' : '../../bower_components/jquery-number/jquery.number.min',
        'jquery-autosize': '../../bower_components/jquery-autosize/jquery.autosize',
        'timepicker': 'lib/jquery-ui-timepicker-addon',
        'Handlebars': 'lib/handlebars-v1.3.0',
        'plupload': '../../bower_components/plupload/js/plupload.full.min',
        'numberTip' : './widgets/NumberTip',
        'qiniu': 'lib/qiniu/qiniu',
        'QiniuSDK': 'lib/qiniu-sdk/qiniu.min',
        'zTree': 'widgets/zTree/ztree.core',
        'zTreeCheck': 'widgets/zTree/ztree.excheck',
        'bigAutocomplete': 'widgets/bigAutocomplete',
        'loadings': 'lib/loadings.min',
        'pagination': 'lib/jquery.pagination',
        'fullScreen' : 'widgets/jquery.fullscreen',
        'highcharts' : 'widgets/highcharts.min',
        'bootstrap-datetimepicke' : 'widgets/bootstrap-datetimepicker'
    }
});