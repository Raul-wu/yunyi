<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/10/17
 * Time: 21:24
 */

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tables.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/skins/black.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/jquery.artDialog.source.js?v=" . STATIC_VER);

Yii::app()->clientScript->registerScript("url", 'window.url='.json_encode($url).';', CClientScript::POS_END);

Yii::app()->clientScript->registerScript("addSpvPermission", 'window.addSpvPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2006102).'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("changeStateSpvPermission", 'window.changeStateSpvPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2006101).'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("listSpvPermission", 'window.listSpvPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 9999).'";', CClientScript::POS_END);
?>

<div id="main">
    <div class="quick_action">
        <div class="action_mod">
            <form class="pure-form" action="<?= Yii::app()->createUrl("/account/list/")?>">
                <input type="hidden" id="tkName" tkName="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>">
                <div class="pure-g">
                    <div class="pure-u-2-3">
                        <input type="text"  placeholder="账名" class="pure-input-1-1"  value="<?= $name ?>" name="name" id="name" />
                        <button type="submit" class="pure-button pure-button-primary">筛选</button>
                        <button type="button" id="reset" class="pure-button">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="quick_action fix_panel">
        <div class="action_mod">
            <?php
            if(LAPermissionService::selectMenuPermission($this->menuId, 2006102))
            {
                ?>
                <a type="submit" class="pure-button pure-button-primary" id="buttonHolder"
                   href="<?= Yii::app()->createUrl("account/add") ?>">新增</a>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="content">
        <div class="table_mod">
            <table class="pure-table">
                <colgroup>
                    <col class="w_20" />
                    <col class="w_100" />
                    <col class="w_100" />
                    <col class="w_100" />
                    <col class="w_120" />
                    <col class="w_120" />
                    <col class="w_120" />
                    <col class="w_80" />
                </colgroup>
                <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>账户性质</th>
                    <th>户名</th>
                    <th>银行账号</th>
                    <th>开户行</th>
                    <th>经办人</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(!empty($accounts))
                {
                    foreach ($accounts as $key => $account)
                    {
                        ?>
                        <tr class="<?= $key % 2 ? "" : "pure-table-odd" ?> pure-table-tr">
                            <td><input type="checkbox" value="<?= $account['id'] ?>" class="check" data-id="<?= $account['id'] ?>"/></td>
                            <td class="tl"><?= $account['type'] ?></td>
                            <td class="tl"><?= $account['name'] ?></td>
                            <td class="tl"><?= $account['bank_account'] ?></td>
                            <td class="tl"><?= $account['bank_address'] ?></td>
                            <td class="tl"><?= $account['handler'] ?></td>
                            <td class="tc"><?= $account['status'] == LAAccountModel::STATUS_OPEN ? '正常' : '停用' ?></td>
                            <td class="tc">
                                <a href="<?= Yii::app()->createUrl('account/edit/', array('id' => $account['id'])) ?>">编辑</a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="screening_result fl">
            <div class="fl">筛选到<?= $count ?>个产品</div>
        </div>
        <?= $pageBar ?>
    </div>
</div>