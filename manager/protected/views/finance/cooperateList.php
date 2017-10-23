<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 10/12/17
 * Time: 23:26
 */
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/skins/black.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/jquery.artDialog.source.js?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tables.css?v=" . STATIC_VER);

?>

<div id="main">
    <div class="quick_action">
        <div class="action_mod">
            <form class="pure-form">
                <div class="pure-g">
                    <div class="pure-u-2-3">
                        <input type="text"  placeholder="名称" class="pure-input-1-1" id="name" value="<?=isset($name) ? $name : ''?>" id="name" name="name" >
                        <button type="submit" class="pure-button pure-button-primary">筛选</button>
                        <a href="<?= Yii::app()->createUrl('finance/CooperateListExport?name='.$name) ?>" class="pure-button pure-button-primary">导出</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="content">
        <input type="hidden" id="tkName" tkName="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>">
        <div class="table_mod">

            <table class="pure-table">
                <colgroup>
                    <col class="w_60" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                </colgroup>
                <thead>
                <tr>
                    <th>名称</th>
                    <th>企业性质</th>
                    <th>注册地</th>
                    <th>委派代表</th>
                    <th>项目经理</th>
                    <th>部门</th>
                    <th>团队负责人</th>
                    <th>核税情况</th>
                    <th>代理情况</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($cooperateAll as $key => $cooperate)
                {
                    ?>
                    <tr class="<?= $key % 2 ? "" : "pure-table-odd"?>  pure-table-tr" id="tr<?= $cooperate['cid'] ?>">
                        <td><?= $cooperate['name'] ?></td>
                        <td><?= isset(LACooperateModel::$arrNature[$cooperate['nature']]) ? LACooperateModel::$arrNature[$cooperate['nature']] : '' ?></td>
                        <td><?= $cooperate['location']?></td>
                        <td><?= $cooperate['delegate'] ?></td>
                        <td><?= $cooperate['project_manager'] ?></td>
                        <td><?= $cooperate['department'] ?></td>
                        <td><?= $cooperate['team_leader'] ?></td>
                        <td><?= isset(LACooperateModel::$arrTax[$cooperate['tax']]) ? LACooperateModel::$arrTax[$cooperate['tax']] : '' ?></td>
                        <td><?= $cooperate['team_leader'] ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <div>
            <div class="">
                <div class="fl">共筛选到<?= $count ?>个数据</div>

                <div class="loadHolder"></div>

            </div>

            <?= $pageBar ?>
        </div>
    </div>
</div>