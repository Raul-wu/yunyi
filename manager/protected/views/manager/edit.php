<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-19
 * Time: 下午1:20
 */
$this->setBodyClass('jqui');

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-timepicker.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/combo.select.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tip-twitter.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/skins/black.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/jquery.artDialog.source.js?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tables.css?v=" . STATIC_VER);

Yii::app()->clientScript->registerScript("roleUrl", "window.roleUrl='" . Yii::app()->createUrl('/manager/index') . "';", CClientScript::POS_END);
?>
<div id="main">
    <div class="content">

        <form class="pure-form" action="<?= Yii::app()->createUrl($postUrl)?>"  method="post" id="postForm">
            <div class="editor_box">
                <h1><?= $title ?></h1>
                <div class="editor_form">
                    <div class="pure-g">

                        <?php
                        if ($postUrl == "manager/insert")
                        {
                        ?>
                        <div class="pure-u-1-2"><label>账号</label> <input format="number_scientific" id="email" name="email" value="" type="text" class="pure-input-1-3"> *</div>
                        <div class="pure-u-md-1-2"><label style="float: left">角色选择</label>
                            <div style="float: left;margin-left:150px;position: absolute;margin-top: 10px;border: 1px solid #617775;background: #f0f6e4;width:220px;height:260px;overflow-y:scroll;overflow-x:auto;">
                                <?php
                                if(!empty($roleInfo))
                                {

                                    foreach ($roleInfo as $key => $value)
                                    {
                                        echo ' <input name=role[] type="checkbox" id="role' . $key . '" value="' . $key . '"><label style="text-align:left;color:#000000" for="role' . $key . '">' . $value . '</label>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="pure-u-1"><label>姓名</label> <input format="number_scientific" id="name" name="name" value="" type="text" class="pure-input-1-3"> *</div>
                        <?php
                        }
                        else
                        {
                        ?>
                        <div class="pure-u-1-2"><label>邮箱</label> <?= $manager["email"] ?></div>
                        <div class="pure-u-md-1-2"><label style="float: left">角色选择</label>
                            <div style="float: left;margin-left:150px;position: absolute;margin-top: 10px;border: 1px solid #617775;background: #f0f6e4;width:220px;height:260px;overflow-y:scroll;overflow-x:auto;">
                                <?php
                                if(!empty($roleInfo) )
                                {

                                    foreach ($roleInfo as $key => $value)
                                    {

                                        $role = !empty($manager['role']) ? $manager['role'] : array();
                                        if(in_array($key, $role))
                                        {
                                            $checked = 'checked';
                                        }
                                        else
                                        {
                                            $checked = '';
                                        }
                                        echo ' <input name=role[] type="checkbox" ' . $checked . ' id="role' . $key . '" value="' . $key . '"><label style="text-align:left;color:#000000" for="role' . $key . '">' . $value . '</label>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="pure-u-1"><label>姓名</label>
                            <input format="number_scientific" id="name" name="name" value="<?= $manager["name"] ?>" type="text" class="pure-input-1-3"> *</div>
                        <?php
                        }
                        ?>

                        <div class="pure-u-1"><label>超级管理员</label>
                            <input type="radio" name="is_admin" id="admin_true" <?= (!empty($manager) && $manager['isAdmin'] == 1) ? 'checked' : ''; ?> value="1"/>
                            <label for="admin_true" style="width:20px;">是</label>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" <?= (!empty($manager) && $manager['isAdmin'] == 2) ? 'checked' : (empty($manager['isAdmin']) ?  'checked' : ''); ?> name="is_admin" id="admin_false"  value="2"/>
                            <label  style="width:20px;" for="admin_false">否</label>
                        </div>
                        <div class="pure-u-1"><label>ip白命单</label>
                            <textarea  placeholder="可不填写,多个请用英文,分隔" name="whiteIp" style="margin: 0px; width: 378px; height: 189px;">
                                <?= !empty($manager["whiteIp"]) ? $manager["whiteIp"] : "" ?>
                            </textarea>
                        </div>
                    </div>

                </div>
            </div>

            <div class="form_action">
                <input type="hidden" name="_id" value="<?= !empty($manager["_id"]) ? $manager["_id"] : 0 ?>"/>
                <input type="hidden" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>"/>
                <button type="submit" class="pure-button pure-button-primary" id="save">提交保存</button>
                <a href="<?= Yii::app()->createUrl('manager/index') ?>" class="pure-button pure-button-primary">返回列表</a>
            </div>

        </form>

    </div>
</div>
