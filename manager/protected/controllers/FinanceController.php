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

        $conditions['name'] = trim(Yii::app()->request->getParam('name', ''));
        $conditions['id_content'] = trim(Yii::app()->request->getParam('id_content', ''));

        $quotientAll = LAQuotientService::getListForQuotientList($conditions);

        $pids = array();
        foreach($quotientAll as $quotient)
        {
            $pids[] = array(
                'qid' => $quotient['qid'],
                'pid' => $quotient['pid']
            )
            ;
        }

        $products = array();
        foreach($pids as $key => $value)
        {
            $quotient = LAQuotientService::getByID($value['qid']);
            $product = LAProductService::getById($value['pid']);
            $pproduct = LAPProductService::getById($product->ppid);

            $products[$key] = array(
                'name' => $product->name,
                'expected_date' => date('Y-m-d', $pproduct->expected_date),
                'amount' => $quotient->amount / LConstService::E4,
                'status' => LAProductModel::$arrStatus[$product->status],
                'fund_code' => $pproduct->fund_code,
                'bank_account' =>$quotient->bank_account,
                'bank_name' => $quotient->bank_name,
                'bank_address' => $quotient->bank_address
            );
        }

        $this->render('quotientList', array(
            'quotients'  => $quotientAll,
            'name'     => $conditions['name'],
            'id_content'     => $conditions['id_content'],
            'products' => $products
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