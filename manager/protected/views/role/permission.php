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
?>

<div id="main">
    <div class="content">
        <div class="screening_result">
            <h3><?= $roleRow["name"] ?> 权限编辑</h3>
        </div>

        <!--表格数据-->
        <div class="table_mod">
            <table class="pure-table">
                <colgroup>
                    <col class="w_20" />
                    <col class="w_300" />
                    <col />
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>菜单名称</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <form class="pure-form" action="<?= Yii::app()->createUrl("role/SavePermission")?>"  method="post" id="postForm">
                    <?php
                    foreach ($menuArr as $row)
                    {
                        ?>
                        <tr class="pure-table-odd">
                            <td class="tl"><?= $row["_id"] ?></td>
                            <td class="tl"><?= $row["fixStr"] ?><?= CHtml::encode($row["name"]) ?></td>
                            <td class="tl">
                                <?php
                                foreach ($row["permission"] as $keyPer => $varPer)
                                {
                                    ?>
                                    <label><input name='listid[<?= $row["_id"] ?>][<?= $varPer["perId"] ?>]' type='checkbox' value='<?= $varPer["perId"] ?>'<?php if(!empty($permissionArr[$roleRow["_id"]][$row["_id"]][$varPer["perId"]])) { ?> checked<?php } ?>> <?= $varPer["name"] ?></label>
                                <?php
                                }
                                ?>
                            </td>

                        </tr>
                    <?php
                    }
                    ?>
                    <tr class="noborder"><th colspan="3">
                            <a href="###" onclick="javascript:$('input[type=checkbox]').attr('checked', true);">全选</a>/<a href="###" onclick="javascript:$('input[type=checkbox]').attr('checked', false);">取消</a>
                            <input type="hidden" name="roleId" value="<?= $roleRow["_id"] ?>"/>
                            <input type="hidden" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>"/>
                            <button type="submit" class="pure-button pure-button-primary" id="save">提交保存</button>
                        </th>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
        <!--表格数据-->

    </div>
</div>