<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/16/17
 * Time: 21:11
 */
class HistoryController extends AdminBaseController
{
    public $menuId = 3003;

    public function actionList()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $this->setJsMain('historyList');

        $conditions['fund_code'] = trim(Yii::app()->request->getParam('fund_code', ''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));
        $conditions['status'] = LAPProductModel::STATUS_FINISH;

        $infoRes = LAPProductService::getAll($conditions, $conditions['page']);

        $this->render('list', array(
            'pproducts' => $infoRes['productAll'],
            'count' => $infoRes['count'],
            'pageBar' => $infoRes['pageBar'],
            'fund_code' => $conditions['fund_code']
        ));
    }

    public function actionDetail()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006102);

        $ppid = Yii::app()->request->getParam('ppid','');

        $pproduct = LAPProductService::getById($ppid);
        $ta = LATaService::getTaListByPPid($ppid);

        $this->render('detail',array(
            'ppid' => $ppid,
            'pproduct' => $pproduct,
            'taList' => $ta
        ));
    }
}