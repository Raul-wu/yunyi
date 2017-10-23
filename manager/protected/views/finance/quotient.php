<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/8/17
 * Time: 00:20
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
                        <input type="text"  placeholder="客户姓名" class="pure-input-1-1" id="quotient_name" value="<?=isset($name) ? $name : ''?>" id="name" name="name" >
                        <input type="text"  placeholder="证件号" class="pure-input-1-1" id="id_content" value="<?=isset($id_content) ? $id_content : ''?>" id="id_content" name="id_content" >
                        <button type="submit" class="pure-button pure-button-primary">筛选</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php
    if(!empty($quotient) || (!isset($name) && !isset($id_content))) {
        ?>
        <div class="content">
            <div class="editor_box">
                <h1>基本信息</h1>

                <div class="editor_form">
                    <div class="pure-g">
                        <div class="pure-u-1"><label>客户姓名</label>
                            <?= $quotient['name'] ?>
                        </div>

                        <div class="pure-u-1"><label>客户身份证</label>
                            <?= $quotient['id_content'] ?>
                        </div>

                        <div class="pure-u-1"><label>类型</label>
                            <?= isset(LAQuotientModel::$arrIdType[$quotient['id_type']]) ? LAQuotientModel::$arrIdType[$quotient['id_type']] : '' ?>
                        </div>


                    </div>
                </div>
            </div>
            <div class="editor_box table_mod">
                <h1>份额信息</h1>
                <table class="pure-table">
                    <colgroup>
                        <col class="w_60"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>产品名称</th>
                        <th>预计到期日</th>
                        <th>金额(万元)</th>
                        <th>状态</th>
                        <th>基金代码</th>
                        <th>银行账号</th>
                        <th>银行户名</th>
                        <th>开行名称</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($products as $key => $product) {
                        ?>
                        <tr class="<?= $key % 2 ? "" : "pure-table-odd" ?>  pure-table-tr"
                            id="tr<?= $quotient['qid'] ?>">
                            <td><?= $key + 1 ?></td>
                            <td><?= $product['name'] ?></td>
                            <td><?= $product['expected_date'] ?></td>
                            <td><?= $product['amount'] ?></td>
                            <td><?= $product['status'] ?></td>
                            <td><?= $product['fund_code'] ?></td>
                            <td><?= $product['bank_account'] ?></td>
                            <td><?= $product['bank_name'] ?></td>
                            <td><?= $product['bank_address'] ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
    }
        ?>

</div>