<?php

/**
 * Created by PhpStorm.
 * User: Raul
 * Date: 7/24/17
 * Time: 20:28
 */
class QuotientController extends AdminBaseController
{
    public $menuId = 2003;

    public function actionList()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $this->setJsMain('quotientList');

        $conditions['pid'] = trim(Yii::app()->request->getParam('pid',''));
        $conditions['name'] = trim(Yii::app()->request->getParam('name',''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));
        $conditions['fund_name'] = trim(Yii::app()->request->getParam('fund_name', ''));
        $arrQuotient = LAQuotientService::getAll($conditions, $conditions['page']);

        $this->render('list',array(
            'quotients'  => $arrQuotient['quotientAll'],
            'pageBar'   => $arrQuotient['pageBar'],
            'count'     => $arrQuotient['count'],
            'name'     => $conditions['name'],
            'fund_name'     => $conditions['fund_name'],
        ));
    }

    public function actionAdd()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006102);

        $this->setJsMain('quotientEdit');

        $pid = Yii::app()->request->getParam('pid','');

        $product = LAProductService::getById($pid);

        $this->render('edit',array(
            'pid' => $pid,
            'product' => $product
        ));
    }

    public function actionSave()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        $pid = Yii::app()->request->getParam('pid');
        if(!$pid)
        {
            throw new CHttpException(404,'缺少必要参数');
            Yii::app()->end();
        }

        if ($_FILES["quotients"]["error"] > 0)
        {
            $this->ajaxReturn(LError::SUCCESS, '上传文件失败');
        }

        $name = explode('.', $_FILES['quotients']['name']);
        if(!in_array($name[1], array('xls', 'xlsx')))
        {
            $this->ajaxReturn(LError::SUCCESS, '请上传excel后缀的文件');
        }

        if($_FILES['quotients']['size'] > (5 * 1024 * 1024))
        {
            $this->ajaxReturn(LError::SUCCESS, '上传文件不能超过5M');
        }

        $filePath = LAQuotientService::storeUploadFile($_FILES);
        if($filePath === false)
        {
            $this->ajaxReturn(LError::SUCCESS, '读取文件失败');
        }

        $result = LAQuotientService::analysisServiceExcel($pid, $filePath);
        if($result && $result['res'])
        {
            $this->ajaxReturn(LError::SUCCESS,$result['msg'], array("url" => Yii::app()->createUrl("quotient/list?pid=". $pid)));
        }
        else
        {
            $this->ajaxReturn(LError::PARAM_ERROR,$result['msg']);
        }
    }
}

class QuotientEditFormModel extends AdminBaseFormModel
{
    const QUOTIENT_NEW           = 'quotient_new';

    public $id;
    public $type;
    public $name;
    public $bank_account;
    public $bank_address;
    public $handler;
    public $status;

    public function rules()
    {
        return array(
            array('type, name, bank_account, bank_address, handler, status, create_time, update_time', 'safe'),

            array('type, name, bank_account, bank_address, handler, status', 'required', 'on' => array(self::QUOTIENT_NEW)),
        );
    }
}