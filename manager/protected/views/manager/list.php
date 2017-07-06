<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-18
 * Time: 下午3:41
 */

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/zTreeStyle/zTreeStyle.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerScript('menuUrl', "window.menuUrl='" . Yii::app()->createUrl("/role/roleAuthority") . "';", CClientScript::POS_END);
?>



<div id="main">
    <div class="quick_action">

        <div class="action_mod" style="height: 40px">
            <form class="pure-form">
                <div class="pure-g">

                    <div class="pure-u-1-1">
                        <div style="float: right">
                            <button type="submit" class="pure-button pure-button-primary">筛选</button>
                            <button type="reset" class="pure-button">重置</button>

                        </div>
                        <div>
                            <input type="text"  placeholder="管理员名称" CLASS="pure-input-1-5"  value="" name="managerName" >


                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
    if (LAPermissionService::checkRoleAuthority(1001102)) {
    ?>
    <div class="quick_action fix_panel">
        <div class="action_mod"  >

            <div >
                    <a href="<?= Yii::app()->createUrl('manager/add') ?>"
                       class="pure-button pure-button-primary">新增管理员</a>
            </div>
        </div>
    </div>
        <?php
    }
    ?>
    <div class="content">
        <!--表格数据-->
        <div class="table_mod">
            <table class="pure-table">
                <colgroup>
                    <col class="w_40">
                    <col class="w_80">
                    <col class="w_80">
                    <col class="w_200">
                    <col class="w_200">
                    <col class="w_200">
                    <col class="w_80">
                    <col class="w_250">
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>账号</th>
                    <th>姓名</th>
                    <th>上次登录时间</th>
                    <th>最后活动时间</th>
                    <th>最后一次登录IP</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                <?php
                foreach ($managerArr as $key=>$row)
                {
                ?>
                    <tr class="<?= $key % 2 == 0 ? 'pure-table-odd' : ''; ?>">
                        <td class="tl"><?= $row["_id"] ?></td>
                        <td class="tl"><a href="javascript:;" class="managerRole" _id="<?= $row["_id"] ?>"><?= CHtml::encode($row["email"]) ?></a></td>
                        <td class="tl"><?= CHtml::encode($row["name"]) ?></td>
                        <td class="tl"><?= date("Y-m-d H:i:s", $row["lastLogin"]) ?></td>
                        <td class="tl"><?= date("Y-m-d H:i:s", $row["lastActive"]) ?></td>
                        <td class="tl"><?= CHtml::encode($row["lastIp"]) ?></td>
                        <td class="tl"><?= $row["state"]==1 ? "正常" : "<font color=red>禁止</font>" ?></td>
                        <td>
                            <a href="javascript:if(confirm('您确定要重置密码么？密码重置后默认为：123456，请尽快修改密码！')){location.href = '/manager/resetpass?_id=<?php echo $row["_id"];?>';}" class="pure-button">重置密码</a>
                            <a href="<?= Yii::app()->createUrl('manager/edit', array('_id' => $row["_id"])) ?>" class="pure-button">编辑</a>
                            <?php
                            if ($row["_id"] != LAManagerService::getUserId())
                            {
                            if ($row["state"] == 1)
                            {
                            ?>
                            <a href="javascript:if(confirm('您确定要禁用<?= CHtml::encode($row["email"]) ?>帐号么？禁用后无法登录，请谨慎操作！')){location.href = '/manager/disable?_id=<?php echo $row["_id"];?>';}" class="pure-button">禁用</a>
                            <?php
                            }
                            else
                            {
                            ?>
                            <a href="javascript:if(confirm('您确定要启用<?= CHtml::encode($row["email"]) ?>帐号么？启用后可以正常登录，请谨慎操作！')){location.href = '/manager/disable?_id=<?php echo $row["_id"];?>';}" class="pure-button">启用</a>
                            <?php
                            }
                            }
                            ?>
                          <a href="<?= Yii::app()->createUrl('manager/Permission', array('uid' => $row["_id"])) ?>" class="pure-button">设置权限</a>
                        </td>
                    </tr>
                    <?php
                    if(!empty($row['roleInfo']))
                    {
                        ?>
                    <tr id="<?=$row["_id"] ?>" style="display: none">

                      <td colspan="2" style="text-align: right;">
                            用户角色:
                      </td>
                        <td colspan="6" style="text-align: left;">
                            <?php
                                foreach ($row['roleInfo'] as $roleId => $roleName)
                                {
                                    echo '<a href="javascript:;" style="margin-left:20px" _id="'.$roleId.'" class="showRole">'.$roleName.'</a>' ;
                                }
                            ?>
                        </td>
                    </tr>
                <?php
                    }
                }
                ?>

                </tbody>
            </table>
        </div>
        <!--表格数据-->
        <?= $page ?>

    </div>
</div>

<input type="hidden" key="token" id="tkName" class="submit" name="<?= Yii::app()->request->csrfTokenName ?>"  tkName="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>" />

<div id="showMenu" style="display:none">

    <ul id="treeDemo" class="ztree"></ul>
</div>