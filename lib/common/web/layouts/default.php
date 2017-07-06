<?php
/**
 * @var $this AdminBaseController
 */
$this->beginContent('common.web.layouts.base'); ?>

<div id="layout">
    <div id="side_menu">
        <div class="user_mod">
            <div class="user_avatar"><img src="/assets/src/img/pro_detail_pm.png" ></div>
            <div class="user_status">

                <p > <?= LAManagerService::getName() ?><br><a href="/manager/EditPass">修改密码</a> <a href="/user/signout">登出</a></p>
            </div>
        </div>

        <?php
        if (!empty($this->sonMenu))
        {
        ?>
        <div class="pure-menu pure-menu-open">
            <ul>
                <?php
                $parentId = 0;
                foreach ($this->sonMenu as $val)
                {
                    $className = "";
                    //被选中
                    if ($this->menuId == $val["_id"])
                    {
                        $className = "pure-menu-selected";
                    }

                    //三级菜单被选中
                    if ($this->menuId == $val["_id"] && $val["leftFix"] == 2)
                    {
                        $className = "pure-menu-children pure-menu-children-selected";
                    }

                    //三级菜单没有被选中
                    if ($this->menuId != $val["_id"] && $val["leftFix"] == 2)
                    {
                        $className = "pure-menu-children";
                    }

                    //三级菜单被选中自动选中上级菜单
                    if (!empty($this->sonMenu[$this->menuId]["parentId"]) && $this->sonMenu[$this->menuId]["parentId"] == $val["_id"])
                    {
                        $parentId = $this->sonMenu[$this->menuId]["parentId"];
                        $className = "pure-menu-open pure-menu-selected";
                    }

                    if ($val["leftFix"] == 2 && $parentId != $val["parentId"])
                    {
                        $className = "pure-menu-children-display";
                    }
                    ?>
                    <?php
                    if($val["_id"] == 	200203){
                        ?>
                        <li class="<?= $className ?>" ><a href="<?= $val["route"] ?>"><?= $val["name"] ?><span style="color: red"><?= !empty(LARemindService::getRole()) ? "(".LARemindService::getRole().")" : '' ?></span></a></li>

                        <?php
                    }else if($val["_id"] == 	2008){
                        ?>
                        <li class="<?= $className ?>" ><a href="<?= $val["route"] ?>"><?= $val["name"] ?><span style="color: red"><?= !empty(LARemindService::getRoleGps()) ? "(".LARemindService::getRoleGps().")" : '' ?></span></a></li>
                    <?php
                    }
                    else{
                        ?>

                        <li class="<?= $className ?>" ><a href="<?= $val["route"] ?>"><?= $val["name"] ?></a></li>
                        <?php
                    }

                }
                ?>
            </ul>
        </div>
        <?php
        }
        ?>
    </div>
    <div id="menu" class="pure-menu pure-menu-open pure-menu-horizontal">
        <ul>
            <?php
            foreach ($this->topMenu as $val)
            {

                ?>
                <li class="<?= $this->parentMenuId == $val["_id"] ? 'pure-menu-selected' : '' ?>">
                    <a href="<?= $val["route"] ?>">
                        <span class="icon <?= !empty($val["className"]) ? $val["className"] : "icon_05" ?>"></span>
                        <?= $val["name"] ?>
                    </a>
                </li>
            <?php
            }
            ?>
        </ul>
    </div>


		<?= $content ?>
	</div>
<?php $this->endContent(); ?>
