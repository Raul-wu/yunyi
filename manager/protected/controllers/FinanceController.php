<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/7/17
 * Time: 22:07
 */
class FinanceController extends AdminBaseController
{
    public $menuId = 5001;

    //契约型基金-项目基本要素
    public function actionPProductList()
    {
        $this->menuId = 5002;

        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $conditions['status'] = trim(Yii::app()->request->getParam('status', ''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));

        $infoRes = LAPProductService::getAll($conditions, $conditions['page']);

        $this->render('pproductList', array(
            'status' => $conditions['status'],
            'pproducts' => $infoRes['productAll'],
            'count' => $infoRes['count'],
            'pageBar'   => $infoRes['pageBar'],
        ));
    }

    //客户份额表
    public function actionQuotientList()
    {
        $this->menuId = 5003;

        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));

        $infoRes = LAPProductService::getAll($conditions, $conditions['page']);

        $this->render('quotientList', array(
            'pproducts' => $infoRes['productAll'],
            'count' => $infoRes['count'],
            'pageBar'   => $infoRes['pageBar'],
        ));
    }

    //固定收益类产品付息明细
    public function actionPProductDetailList()
    {
        $this->menuId = 5004;

        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $conditions['status'] = trim(Yii::app()->request->getParam('status', ''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));

        $infoRes = LAPProductService::getAll($conditions, $conditions['page']);

        $this->render('pproductDetailList', array(
            'status' => $conditions['status'],
            'pproducts' => $infoRes['productAll'],
            'count' => $infoRes['count'],
            'pageBar'   => $infoRes['pageBar'],
        ));
    }
}