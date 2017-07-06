<?php

/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 */
class MenuPermissionController extends AdminBaseController
{
    public $menuId = 1002;

    public function actionIndex()
    {
        $menuId = Yii::app()->request->getParam("menuId");
        if (empty($menuId)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("menu/index"));
        }
        $menu = LAMenuService::getMenuRow($menuId);
        if (empty($menu)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("menu/index"));
        }
        $menuPermissionArr = LAMenuService::getMenuPermissionAll(array("menuId" => intval($menuId)));
        $this->render("index", array(
            "menuPermissionArr" => $menuPermissionArr,
            "menu" => $menu
        ));
    }

    public function actionAdd()
    {
        $this->setJsMain('menu');
        $menuId = Yii::app()->request->getParam("menuId");
        if (empty($menuId))
        {
            Yii::app()->request->redirect(Yii::app()->createUrl("menu/index"));
        }

        $menu = LAMenuService::getMenuRow($menuId);
        if (empty($menu))
        {
            Yii::app()->request->redirect(Yii::app()->createUrl("menu/index"));
        }

        $menuPer = LAMenuService::getPerId(array("menuId" => intval($menuId)), 1, 1);
        //var_dump($menuPer);die;
        $menuPerId = $menuId."101";
        if (!empty($menuPer))
        {
            $menuPerId = $menuPer["perId"]+1;
        }

        $this->render("edit", array(
            "postUrl" => "/menuPermission/insert",
            "menu" => $menu,
            "menuPerId" => $menuPerId,
        ));
    }

    public function actionInsert()
    {
        $params = array(
            "perId" => Yii::app()->request->getParam("menuPerId"),
            "name" => Yii::app()->request->getParam("name"),
            "menuId" => Yii::app()->request->getParam("menuId"),
            "route" => Yii::app()->request->getParam("route")
        );

        if (empty($params["perId"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入权限ID！");
        }

        if (empty($params["name"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入权限名称！");
        }

        if (empty($params["route"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入权限URL！");
        }

        $menuRow = LAMenuService::getRow(array("_id" => $params["menuId"]));
        if (!empty($menuRow)) {
            $this->ajaxReturn(LError::PARAM_ERROR, "菜单不存在！");
        }

        $ret = LAMenuService::insertMenuPermission($params);
        if ($ret) {
            $this->ajaxReturn(LError::SUCCESS, "保存成功");
        }
        $this->ajaxReturn(LError::INTERNAL_ERROR, "保存失败！");
    }

    public function actionDel()
    {
        $_id = Yii::app()->request->getParam("_id");
        if (empty($_id)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("menu/index"));
        }
        LAMenuService::deleteMenuPermission($_id);
        Yii::app()->request->redirect(Yii::app()->createUrl("menu/index"));
    }
}