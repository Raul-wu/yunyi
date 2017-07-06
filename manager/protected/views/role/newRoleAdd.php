<?php

$this->setBodyClass('jqui');

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css?v=" . STATIC_VER);
//Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/zTreeStyle/demo.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/zTreeStyle/zTreeStyle.css?v=" . STATIC_VER);


if (!empty($registerScript)) {
    foreach ($registerScript as $key => $value) {
        if (empty($key) || empty($value)) {
            continue;
        }
        Yii::app()->clientScript->registerScript($key, "window." . $key . "='" . $value . "';", CClientScript::POS_END);
    }
}
Yii::app()->clientScript->registerScript("allAuthority", "window.allAuthority=" . $allAuthority . ";", CClientScript::POS_END);
//var_dump($allAuthority);die;
?>





<div id="main">
    <div class="content">

            <div class="editor_box">

                <h1><?= $title ?></h1>

                <div class="editor_form">
                    <div class="pure-g">

                        <input type="hidden" key='' token" class="submit"
                        name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>"
                        csrfInput/>
                        <input type="hidden" class="submit" name="id"
                               value="<?php echo !empty($roleInfo) ? $roleInfo->_id : '' ?>"/>


                        <div class="pure-u-md-1-2"><label>角色名称</label>

                            <input type="text" class="pure-input-1-3  submit" placeholder="请输入角色名称"
                                   value="<?php echo !empty($roleInfo) ? $roleInfo->roleName : '' ?>"
                                   name="roleName"><span class="needAdd">*</span>
                        </div>
                        <div class="pure-u-md-1-2"><label style="float: left">设置权限</label>
                            <div style="float: left;margin-left:150px;position: absolute;margin-top: 10px;border: 1px solid #617775;background: #f0f6e4;width:220px;height:360px;overflow-y:scroll;overflow-x:auto;">
                                <ul id="treeDemo" class="ztree"></ul>
                                </div>
                         </div>

                        <div class="pure-u-md-1"><label>角色说明</label>

                            <textarea class="pure-input-1-3  submit" style="margin: 0px; height: 256px; width: 377px;" name="roleContent" imgPrefix="mail/"
                                      placeholder="请输入角色说明" ><?= !empty($roleInfo['roleContent']) ? $roleInfo['roleContent'] : '' ?></textarea>

                        </div>
                        <div class="pure-u-md-1-2">


                        </div>
                        <div class="pure-u-md-1" id="linkUrl"><label>排序</label>

                            <input type="text" class="pure-input-1-3 submit" placeholder="请输入排序"
                                   value="<?php echo !empty($roleInfo) ? $roleInfo->roleSort : '' ?>"
                                   name="roleSort"><span class="needAdd">*</span>
                        </div>
                        <div class="pure-u-md-1-2">


                        </div>
                        <div class="pure-u-md-1" id="linkUrl"><label>状态</label>
                            <input type="radio" name="state" value="1" id="stateAble"  <?php echo !empty($roleInfo->state) ? 'checked="checked"' : '' ?>"><label style="text-align: left"
                                                                                             for="stateAble">有效</label>
                            <input name="state" type="radio" value="0" id="stateDisAble" " <?php echo empty($roleInfo->state) ? 'checked="checked"' : '' ?> "><label style="text-align: left"
                                                                                                for="stateDisAble">无效</label>
                        </div>
                        <div class="pure-u-md-1-2">


                        </div>


                    </div>
                </div>


                <div class="form_action pure-form">

                    <button type="button" class="pure-button pure-button-primary" id="save">保存</button>


                </div>