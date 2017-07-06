<?php

/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 */
class ManagerController extends AdminBaseController
{
    public $menuId = 1001;

    public function actionTest()
    {
        $this->setJsMain('permission');
        if(!empty($_POST))
        {
            $subject = "test";
            $titleArr = array('size'=>11,'text'=>'','bold'=>false,'align'=>'L','indent'=>0,'left'=>0);
            $titleArr['text'] = "test";
            $titleArr['size'] = 18;
            $titleArr['align'] = 'C';
            $segmentArr = array($titleArr);

            $segmentArr[] = array('html'=>$_POST["word"]);
            $stamp1 = '@' . "";
            $stamp2 = '@' . "";
            $pdf = LTransferAgree::init($subject)->pdfRaw($segmentArr,
                $stamp1, $stamp2);
            ob_end_clean();
            ob_start();

            $fileName = md5($subject).'.pdf';
            header('Content-Type: application/pdf');
            header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
            header('Pragma: public');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Content-Disposition: inline; filename="'.$fileName.'"');

            $contentLength = strlen($pdf);
            if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
                // the content length may vary if the server is using compression
                header('Content-Length: '.$contentLength);
            }
            echo $pdf;
            exit;
        }
        $this->render("test", array());
    }
    /**
     * 管理员列表
     */
    public function actionIndex()
    {
        //日志记录
        $this->log("查看管理员列表");
        //分頁
        $num = 12;
        $page = Yii::app()->request->getQuery("page");
        //獲取用戶數據
        $params = array();
        $name = Yii::app()->request->getQuery("managerName", '');
        if ($name) {
            $params['name'] = $name;
        }
        $managerArr = LAManagerService::getAll($params, $page ? $page : 1, $num);
        //檢索數據
        $manager = array();
        foreach ($managerArr["managerArr"] as $key => $val) {
            $manager[$key] = (array)$val;
            if (!empty($val->role)) {
                $roleInfo = array();
                foreach ($val->role as $role) {
                    $roleRes = LANewRoleService::detail($role);
                    if (empty($roleRes) || empty($roleRes->state)) {
                        continue;
                    }
                    $roleInfo[$role] = $roleRes->roleName;
                }
                $manager[$key]['roleInfo'] = $roleInfo;
            }
        }
        $this->setJsMain("managerList");
        $this->render("list", array(
            "managerArr" => $manager,
            "page" => LAdminPager::getPages($managerArr['count'], $page, $num, '')
        ));
    }

    /**
     * 新增管理员
     */
    public function actionAdd()
    {
        $this->setJsMain('permission');
        //日志记录
        $this->log("新增管理员页面");
        $roleInfo = LANewRoleService::getAllRole();
        $pass = LAManagerService::random(6);
        $this->render("edit", array(
            "roleInfo" => $roleInfo,
            "pass" => $pass,
            "title" => "新增管理员",
            "postUrl" => "manager/insert"
        ));
    }

    /**
     * 新增管理员入库
     */
    public function actionInsert()
    {
        $params = array(
            "email" => Yii::app()->request->getParam("email"),
            "name" => Yii::app()->request->getParam("name"),
            "role" => !empty($_POST['role']) ? $_POST['role'] : '',
            "whiteIp" => Yii::app()->request->getParam("whiteIp"),
            'isAdmin' => Yii::app()->request->getParam('is_admin')
        );
        if (empty($params["email"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入帐号！");
        }
        $managerRow = LAManagerService::getRow(array("email" => $params["email"]));
        if (!empty($managerRow)) {
            $this->ajaxReturn(LError::PARAM_ERROR, "帐号已经存在！");
        }
        if (empty($params["role"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请选择用户的角色！");
        }
        $password = "123456";
        $ret = LAManagerService::insert(array(
            "email" => strtolower($params['email']),
            "password" => md5($password),
            "name" => $params['name'],
            "corpId" => "",
            "whiteIp" => $params["whiteIp"],
            "role" => $params['role'],
            "lastLogin" => time(),
            "lastActive" => time(),
            'isAdmin' => $params['isAdmin'],
            "lastIp" => LAManagerService::getIp(),
            "state" => 1
        ));
        if ($ret) {
            //日志记录
            $this->log("新增管理员成功");
            $this->ajaxReturn(LError::SUCCESS, "保存成功");
        }
        $this->ajaxReturn(LError::INTERNAL_ERROR, "保存失败！");
    }

    /**
     * 编辑管理员
     */
    public function actionEdit()
    {
        //日志记录
        $this->log("编辑管理员页面");
        $this->setJsMain('permission');
        $_id = Yii::app()->request->getQuery("_id");
        if (empty($_id)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("manager/index"));
        }
        $managerRow = LAManagerService::getRow(array("_id" => intval($_id)));
        if (empty($managerRow)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("manager/index"));
        }
        $roleInfo = LANewRoleService::getAllRole();
        $this->render("edit", array(
            "roleInfo" => $roleInfo,
            "manager" => $managerRow,
            "pass" => "",
            "title" => "编辑管理员",
            "postUrl" => "manager/update"
        ));
    }

    /**
     * 管理员更新
     */
    public function actionUpdate()
    {
        $params = array(
            "_id" => Yii::app()->request->getParam("_id"),
            "name" => Yii::app()->request->getParam("name"),
            "whiteIp" => Yii::app()->request->getParam("whiteIp"),
            "role" => !empty($_POST['role']) ? $_POST['role'] : '',
            'isAdmin' => Yii::app()->request->getParam('is_admin')
        );
        if (empty($params["role"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请选择用户的角色！");
        }
        $ret = LAManagerService::update(array(
            "_id" => intval($params['_id']),
            "name" => $params['name'],
            "whiteIp" => $params["whiteIp"],
            "role" => $params["role"],
            'isAdmin' => $params["isAdmin"]
        ));
        if ($ret) {
            //日志记录
            $this->log("更新管理员成功");
            $this->ajaxReturn(LError::SUCCESS, "保存成功");
        }
        $this->ajaxReturn(LError::INTERNAL_ERROR, "保存失败！");
    }

    /**
     * 重置密码
     */
    public function actionResetPass()
    {
        //日志记录
        $this->log("重置管理员密码");
        $_id = Yii::app()->request->getQuery("_id");
        if (empty($_id)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("manager/index"));
        }
        $managerRow = LAManagerService::getRow(array("_id" => intval($_id)));
        if (empty($managerRow)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("manager/index"));
        }
        LAManagerService::update(array(
            "_id" => intval($_id),
            "password" => md5("123456"),
        ));
        Yii::app()->request->redirect(Yii::app()->createUrl("manager/index"));
    }

    /**
     * 禁止用户
     */
    public function actionDisable()
    {
        //日志记录
        $this->log("禁用管理员");
        $_id = Yii::app()->request->getQuery("_id");
        if (empty($_id)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("manager/index"));
        }
        $managerRow = LAManagerService::getRow(array("_id" => intval($_id)));
        if (empty($managerRow)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("manager/index"));
        }
        LAManagerService::update(array(
            "_id" => intval($_id),
            "state" => $managerRow["state"] == 1 ? 0 : 1,
        ));
        Yii::app()->request->redirect(Yii::app()->createUrl("manager/index"));
    }


    /**
     * 修改密码
     */
    public function actionEditPass()
    {
        //日志记录
        $this->log("管理员修改密码页面");
        $this->setJsMain('permission');
        $_id = LAManagerService::getUserId();
        if (empty($_id)) {
            Yii::app()->request->redirect("/");
        }
        $managerRow = LAManagerService::getRow(array("_id" => intval($_id)));
        if (empty($managerRow)) {
            Yii::app()->request->redirect("/");
        }
        $this->render("pass", array(
            "manager" => $managerRow,
        ));
    }

    /**
     * 更新密码
     */
    public function actionUpdatePass()
    {
        $params = array(
            "password" => Yii::app()->request->getParam("password"),
            "password1" => Yii::app()->request->getParam("password1"),
            "password2" => Yii::app()->request->getParam("password2")
        );
        if (empty($params["password"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入旧密码！");
        }
        if (empty($params["password1"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入新密码！");
        }
        if (empty($params["password2"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入确认密码！");
        }
        if ($params["password1"] != $params["password2"]) {
            $this->ajaxReturn(LError::PARAM_ERROR, "新密码与确认密码不一致！");
        }
        if(!LUtil::pwdStrength($params["password1"]))
        {
            $this->ajaxReturn(LError::PARAM_ERROR, "新密码长度为6-20位,请重新输入！");
        }
        $_id = LAManagerService::getUserId();
        if (empty($_id)) {
            Yii::app()->request->redirect("/");
        }
        $managerRow = LAManagerService::getRow(array("_id" => intval($_id)));
        if (empty($managerRow)) {
            Yii::app()->request->redirect("/");
        }
        if (md5($params["password"]) != $managerRow["password"]) {
            $this->ajaxReturn(LError::PARAM_ERROR, "旧密码输入错误！");
        }
        $ret = LAManagerService::update(array(
            "_id" => intval($managerRow['_id']),
            "password" => md5($params["password1"])
        ));
        if ($ret) {
            $this->ajaxReturn(LError::SUCCESS, "保存成功");
        }
        LUtil::
        $this->ajaxReturn(LError::INTERNAL_ERROR, "保存失败！");
    }


    /**
     * 管理员权限分配
     */
    public function actionPermission()
    {
        header("Content-type: text/html; charset=utf-8");
        //日志记录
        $this->log("管理员权限分配页面");
        $this->setJsMain('permission');
        $userId = Yii::app()->request->getQuery("uid");
        if (empty($userId)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("manager/index"));
        }
        $userInfo = LAManagerService::getRow(array('_id' => intval($userId)));
        if (empty($userInfo)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("role/index"));
        }
        $permissionArr = array();
        $menuObj = new LAMenuService();
        $menuArr = $menuObj->getMenuList();
        foreach ($menuArr as $key => $val) {
            $menuPermissionArr = LAMenuService::getMenuPermissionAll(array("menuId" => intval($val["_id"])));
            $menuArr[$key]["permission"] = $menuPermissionArr;
            foreach ($menuPermissionArr as $valPer) {
                $perRow = LAPermissionService::getPersonPermission(array(
                    "uid" => intval($userId),
                    "menuId" => intval($val["_id"]),
                    "permission" => intval($valPer["perId"]),
                ));
                $permissionArr[$userId][$val["_id"]][$valPer["perId"]] = empty($perRow) ? 0 : 1;
            }
        }

        $permissionRole = LANewRoleService::getRoleIdByAuthority($userInfo["role"]);
        $this->render("permission", array(
            'userInfo' => $userInfo,
            "menuArr" => $menuArr,
            "permissionArr" => $permissionArr,
            "permissionRole" => $permissionRole
        ));
    }

    /**
     * 角色权限更新
     */
    public function actionSavePermission()
    {
        $userId = Yii::app()->request->getParam("uid");
        if (empty($userId)) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "权限分配失败！");
        }
        $userInfo = LAManagerService::getRow(array("_id" => intval($userId)));
        if (empty($userInfo)) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "权限分配失败！");
        }
        $perArr = !empty($_POST["listid"]) ? $_POST["listid"] : array();
        $ret = LAPermissionService::SavePersonalPermission($userId, $perArr);
        if ($ret) {
            //日志记录
            $this->log("更新权限分配页面");
            $this->ajaxReturn(LError::SUCCESS, "权限分配成功");
        }
        $this->ajaxReturn(LError::INTERNAL_ERROR, "权限分配失败！");
    }

    public function actionGetauthorizename()
    {
        $keyword = Yii::app()->request->getParam("keyword");
        if (empty($keyword)) {
            $this->ajaxReturn(LError::PARAM_ERROR, "数据违规,保存失败");
        }
        $res = LAManagerService::getManagerListByName($keyword);
        $list = array();
        foreach ($res as $key => $val) {
            $list[] = array(
                'id' => $val->email,
                'title' => $val->name,
            );
        }
        echo json_encode(array("data" => $list));
    }

}