<?php

/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 */
class MenuController extends AdminBaseController
{
    public $menuId = 1002;

    public function actionIndex()
    {
        $this->setJsMain('menu');
        $menuObj = new LAMenuService();
        $menuArr = $menuObj->getMenuList();
        $this->render("index", array(
            "menuArr" => $menuArr
        ));
    }

    public function actionAdd()
    {
        $this->setJsMain('menu');
        $menuObj = new LAMenuService();
        $menuArr = $menuObj->getMenuList();
        $this->render("edit", array(
            "title" => "新增菜单",
            "postUrl" => "/menu/insert",
            "menuArr" => $menuArr
        ));
    }

    public function actionInsert()
    {
        $params = array(
            "_id" => Yii::app()->request->getParam("menuId"),
            "name" => Yii::app()->request->getParam("name"),
            "route" => Yii::app()->request->getParam("route"),
            "parentId" => Yii::app()->request->getParam("parentId"),
            "className" => Yii::app()->request->getParam("className"),
            "sort" => Yii::app()->request->getParam("sort"),
        );
        if (empty($params["_id"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入菜单ID！");
        }
        if (empty($params["name"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入菜单名称！");
        }
        if (empty($params["route"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入菜单URL！");
        }
        $menuRow = LAMenuService::getRow($params["_id"]);
        if (!empty($menuRow)) {
            $this->ajaxReturn(LError::PARAM_ERROR, "菜单已经存在！");
        }
        $ret = LAMenuService::insert($params);
        if ($ret) {
            LAMenuService::insertMenuPermission(array(
                "perId" => $params["_id"],
                "name" => "查看权限",
                "route" => $params["route"],
                "menuId" => intval($ret)
            ));
            $this->ajaxReturn(LError::SUCCESS, "保存成功");
        }
        $this->ajaxReturn(LError::INTERNAL_ERROR, "保存失败！");
    }

    public function actionEdit()
    {
        $this->setJsMain('menu');
        $menuId = Yii::app()->request->getParam("menuId");
        if (empty($menuId)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("menu/index"));
        }
        $menu = LAMenuService::getMenuRow($menuId);
        if (empty($menu)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("menu/index"));
        }
        $menuObj = new LAMenuService();
        $menuArr = $menuObj->getMenuList();
        $this->render("edit", array(
            "title" => "编辑菜单",
            "postUrl" => "/menu/update",
            "menuArr" => $menuArr,
            "menu" => $menu,
        ));
    }

    public function actionUpdate()
    {
        $params = array(
            "_id" => Yii::app()->request->getParam("_id"),
            "name" => Yii::app()->request->getParam("name"),
            "route" => Yii::app()->request->getParam("route"),
            "parentId" => Yii::app()->request->getParam("parentId"),
            "className" => Yii::app()->request->getParam("className"),
            "sort" => Yii::app()->request->getParam("sort"),
        );
        if (empty($params["name"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入菜单名称！");
        }
        if (empty($params["route"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入菜单URL！");
        }
        $menuRow = LAMenuService::getMenuRow($params["_id"]);
        if (empty($menuRow)) {
            $this->ajaxReturn(LError::PARAM_ERROR, "菜单不在存在！");
        }
        $ret = LAMenuService::update($params);
        if ($ret) {
            $this->ajaxReturn(LError::SUCCESS, "保存成功");
        }
        $this->ajaxReturn(LError::INTERNAL_ERROR, "保存失败！");
    }

    public function actionDel()
    {
        $menuId = Yii::app()->request->getParam("menuId");
        if (empty($menuId)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("menu/index"));
        }
        LAMenuService::delete($menuId);
        LAMenuService::deleteMenuIdPermission($menuId);
        Yii::app()->request->redirect(Yii::app()->createUrl("menu/index"));
    }

    public function actionSaveSort()
    {
        $menuIdArr = !empty($_POST["listId"]) ? $_POST["listId"] : "";
        $sortArr = !empty($_POST["sort"]) ? $_POST["sort"] : "";
        if (empty($menuIdArr)) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "请选择要更新的菜单！");
        }

        foreach ($menuIdArr as $key => $val) {
            if (!empty($val) && isset($sortArr[$val])) {
                $ret = LAMenuService::update(array(
                    "_id" => $val,
                    "sort" => $sortArr[$val]
                ));
            }
        }
        $this->ajaxReturn(LError::SUCCESS, "保存成功");
    }
}