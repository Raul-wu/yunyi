<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-18
 * Time: 下午3:41
 */
$this->setBodyClass('jqui');

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-timepicker.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerScriptFile("/assets/src/js/lib/ckeditor/ckeditor.js", CClientScript::POS_BEGIN);
?>

<div id="main">
    <div class="content">
        <?php
        if (LAPermissionService::checkRoleAuthority(1002102)) {
        ?>
        <div class="screening_result">
            <div class="fr"><a href="<?= Yii::app()->createUrl('menu/add') ?>" class="pure-button pure-button-primary">新增菜单</a></div>
        </div>
            <?php
        }
        ?>
        <!--表格数据-->
        <div class="table_mod">
            <table class="pure-table">
                <colgroup>
                    <col class="w_20" />
                    <col class="w_80" />
                    <col class="w_300" />
                    <col class="w_120" />
                    <col class="w_40" />
                    <col class="w_250" />
                    <col />
                </colgroup>
                <thead>
                <tr>
                    <th>选</th>
                    <th>菜单ID</th>
                    <th>菜单名称</th>
                    <th>菜单URL</th>
                    <th>排序号</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <form class="pure-form" action="<?= Yii::app()->createUrl("menu/SaveSort")?>"  method="post" id="postForm">
                    <?php
                    foreach ($menuArr as $key=>$row)
                    {
                    ?>
                        <tr class="<?= $key % 2 == 0 ? 'pure-table-odd' : ''; ?>">
                            <td class="tl"><input name='listId[]' type='checkbox' value='<?= $row["_id"] ?>'></td>
                            <td class="tl"><?= $row["_id"] ?></td>
                            <td class="tl"><?= $row["fixStr"] ?><?= CHtml::encode($row["name"]) ?></td>
                            <td class="tl"><?= $row["route"] ?></td>
                            <td class="tl"><input name='sort[<?= $row["_id"] ?>]' type='text' value='<?= $row["sort"] ?>' style="width: 40px;" /></td>
                            <td class="tl">
                                <a href="<?= Yii::app()->createUrl('menuPermission/index', array('menuId' => $row["_id"])) ?>" class="pure-button">查看权限</a>
                                <a href="<?= Yii::app()->createUrl('menu/edit', array('menuId' => $row["_id"])) ?>" class="pure-button">编辑菜单</a>
                                <a href="javascript:if(confirm('您确定要删除 <?= CHtml::encode($row["name"]) ?> 菜单么？请谨慎操作！')){location.href = '/menu/del?menuId=<?php echo $row["_id"];?>';}" class="pure-button">删除菜单</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr class="noborder">
                        <th colspan="6" style="text-align: left; padding-left: 10px;">
                            <a href="javascript:$('input[type=checkbox]').attr('checked', true);">全选</a>/<a href="javascript:$('input[type=checkbox]').attr('checked', false);">取消</a>
                            <input type="hidden" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>"/>
                            <button type="submit" class="pure-button pure-button-primary" id="save">更新排序</button>
                        </th>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
        <!--表格数据-->

    </div>
</div>