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
        if (LAPermissionService::checkRoleAuthority(1003102))
        {
        ?>
        <div class="screening_result">
            <div class="fr"><a href="<?= Yii::app()->createUrl('menuPermission/add', array('menuId' => $menu["_id"])) ?>" class="pure-button pure-button-primary">新增 <?= $menu["name"] ?> 菜单权限</a></div>
        </div>
            <?php
        }
        ?>
        <!--表格数据-->
        <div class="table_mod">
            <table class="pure-table">
                <colgroup>
                    <col />
                    <col />
                    <col />
                    <col />
                    <col />
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>权限名称</th>
                    <th>权限URL</th>
                    <th>操作代码</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($menuPermissionArr as $row)
                    {
                        ?>
                        <tr class="pure-table-odd">
                            <td><?= $row["perId"] ?></td>
                            <td><?= CHtml::encode($row["name"]) ?></td>
                            <td><?= CHtml::encode($row["route"]) ?></td>
                            <td>LAPermissionService::checkRoleAuthority(<?= !empty($row["perId"]) ? $row["perId"] : "" ?>)</td>
                            <td>
                                <a href="javascript:if(confirm('您确定要删除<?= CHtml::encode($row["name"]) ?>菜单么？请谨慎操作！')){location.href = '/menuPermission/del?_id=<?php echo $row["_id"];?>';}" class="pure-button">删除</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!--表格数据-->

    </div>
</div>