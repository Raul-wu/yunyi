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

?>

<div id="main">
    <div class="content">

        <form class="pure-form" action="<?= Yii::app()->createUrl($postUrl)?>"  method="post" id="postForm">
            <div class="editor_box">
                <h1><?= $title ?></h1>
                <div class="editor_form">
                    <div class="pure-g">

                        <div class="pure-u-1"><label>父级菜单</label>
                            <select name="parentId" id="parentId">
                                <option value="0">顶级菜单</option>
                                <?php
                                foreach ($menuArr as $val)
                                {
                                    ?>
                                    <option value="<?= $val["_id"] ?>"<?php if (!empty($menu["parentId"]) && $menu["parentId"] == $val["_id"]) {?> selected<?php }?>><?= $val["fixStr"] ?><?= $val["name"] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="pure-u-1"><label>菜单ID</label> <?php if (empty($menu["_id"])) {?><input format="number_scientific" id="menuId" name="menuId" value="" type="text" class="pure-input-1-3"><?php }else{ ?><?= $menu["_id"] ?><?php }?> </div>
                        <div class="pure-u-1"><label>菜单名称</label> <input format="number_scientific" id="name" name="name" value="<?= !empty($menu["name"]) ? $menu["name"] : "" ?>" type="text" class="pure-input-1-3"> </div>
                        <div class="pure-u-1"><label>菜单URL</label> <input format="number_scientific" id="route" name="route" value="<?= !empty($menu["route"]) ? $menu["route"] : "#" ?>" type="text" class="pure-input-1-3"> </div>
                        <div class="pure-u-1"><label>菜单样式</label> <input format="number_scientific" id="className" name="className" value="<?= !empty($menu["className"]) ? $menu["className"] : "" ?>" type="text" class="pure-input-1-3"> </div>
                        <div class="pure-u-1"><label>排序号</label> <input format="number_scientific" id="sort" name="sort" value="<?= isset($menu["sort"]) ? $menu["sort"] : 50 ?>" type="text" style="width: 80px;"> </div>
                    </div>

                </div>
            </div>

            <div class="form_action">
                <input type="hidden" name="_id" value="<?= !empty($menu["_id"]) ? $menu["_id"] : 0 ?>"/>
                <input type="hidden" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>"/>
                <button type="submit" class="pure-button pure-button-primary" id="save">提交保存</button>
                <a href="<?= Yii::app()->createUrl('menu/index') ?>" class="pure-button pure-button-primary">返回列表</a>
            </div>

        </form>

    </div>
</div>