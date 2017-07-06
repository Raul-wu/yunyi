<?php
$this->setBodyClass('jqui');

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-timepicker.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerScriptFile("/assets/src/js/lib/ckeditor/ckeditor.js", CClientScript::POS_BEGIN);

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/zTreeStyle/zTreeStyle.css?v=" . STATIC_VER);

if (!empty($registerScript))
{

    foreach ($registerScript as $key => $value)
    {
        if(empty($key) || empty($value))
        {
            continue;
        }
        Yii::app()->clientScript->registerScript($key, "window." . $key . "='" . $value . "';", CClientScript::POS_END);
    }

}

Yii::app()->clientScript->registerScript("header", "window.header=".$header.";", CClientScript::POS_END);

?>
<div id="main">
    <div class="quick_action">
        <div class="action_mod" style="height: 40px">
            <form class="pure-form">
                <div class="pure-g">

                    <div class="pure-u-1-1">
                        <div style="float: right">
                            <button type="button" class="pure-button pure-button-primary"  id="findRole">查询</button>
                            <button type="button" class="pure-button"  id="reset">重置</button>
                        </div>
                        <div>
                            <input type="text"  placeholder="角色名称" CLASS="pure-input-1-5"  value="" id="roleName" >


                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="quick_action fix_panel">
        <div class="action_mod"  >

            <a id="btnPh" href="<?=$registerScript['addUrl']?>"  class="pure-button pure-button-primary  btnPh" style="">创建角色</a>
            <a id="editNews"  class="pure-button pure-button-disabled btnPh" style="">编辑</a>

        </div>
    </div>
    <div id="main">
        <input type="hidden" key="token" id="tkName" class="submit" name="<?= Yii::app()->request->csrfTokenName ?>"  tkName="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>" />
        <div class="content">
            <div class="table_mod">
                <table id="gridTable" class="pure-table">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<div id="showMenu" style="display:none">

    <ul id="treeDemo" class="ztree"></ul>
</div>
