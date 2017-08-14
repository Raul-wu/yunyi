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

        <!--表格数据-->
        <div class="table_mod">
            <table class="pure-table">
                <colgroup>
                    <col class="w_60" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_100" />
                    <col class="w_80" />
                    <col class="w_120" />
                    <col class="w_120" />
                    <col class="w_200" />
                    <col class="w_80" />
                    <col class="w_150" />
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>角色名称</th>
                    <th>排序号</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                <?php
                foreach ($roleArr as $row)
                {
                ?>
                    <tr class="pure-table-odd">
                        <td class="tl"><?= $row["_id"] ?></td>
                        <td class="tl"><?= CHtml::encode($row["name"]) ?></td>
                        <td class="tl"><?= $row["sort"] ?></td>
                        <td>
                            <a href="<?= Yii::app()->createUrl('role/Permission', array('roleId' => $row["_id"])) ?>" class="pure-button">权限</a>
                            <a href="<?= Yii::app()->createUrl('role/edit', array('_id' => $row["_id"])) ?>" class="pure-button">编辑</a>
                            <a href="javascript:if(confirm('确定删除吗？')){location.href = '/role/del?_id=<?php echo $row["_id"];?>';}" class="pure-button">删除</a>
                            <a href="<?= Yii::app()->createUrl('manager/index', array('roleId' => $row["_id"])) ?>" class="pure-button">查看</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
                <form class="pure-form" action="<?= Yii::app()->createUrl('role/insert')?>"  method="post" id="postForm">
                <tr class="pure-table-odd">
                    <td class="tl">&nbsp;</td>
                    <td class="tl"><input format="number_scientific" id="name" name="name" value="" type="text" class="pure-input-1-3"></td>
                    <td class="tl"><input format="number_scientific" id="sort" name="sort" value="50" type="text" class="pure-input-1-3" style="width: 50px;"></td>
                    <td>
                        <input type="hidden" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>"/>
                        <button type="submit" class="pure-button pure-button-primary" id="save">新增角色</button>
                    </td>
                </tr>
                </form>

                </tbody>
            </table>
        </div>
        <!--表格数据-->

    </div>
</div>