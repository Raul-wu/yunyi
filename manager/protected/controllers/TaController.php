<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/14/17
 * Time: 21:16
 */
class TaController extends AdminBaseController
{
    public $menuId = 3001;

    public function actionList()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $this->setJsMain('taList');

        $conditions['fund_code'] = trim(Yii::app()->request->getParam('fund_code', ''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));
        $conditions['status'] = LAPProductModel::STATUS_DURATION . ',' . LAPProductModel::STATUS_WAIT;

        $infoRes = LAPProductService::getAll($conditions, $conditions['page']);

        $this->render('list', array(
            'pproducts' => $infoRes['productAll'],
            'count' => $infoRes['count'],
            'pageBar'   => $infoRes['pageBar'],
            'fund_code' => $conditions['fund_code']
        ));
    }

    public function actionEditList()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $this->setJsMain('taEditList');

        $conditions['ppid'] = trim(Yii::app()->request->getParam('ppid', ''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));

        $infoRes = LATaService::getTaList($conditions, $conditions['page']);

        $this->render('editList', array(
            'tas' => $infoRes['taAll'],
            'count' => $infoRes['count'],
            'pageBar'   => $infoRes['pageBar'],
        ));
    }


    public function actionDelete()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2001104);

        if (!$tids = Yii::app()->request->getParam('tids'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        if(LATaService::deleteTa($tids))
        {
            $this->ajaxReturn(LError::SUCCESS, "删除成功");
        }
        else
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "删除失败");
        }
    }

    public function actionAdd()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006102);

        $this->setJsMain('taEdit');

        $ppid = Yii::app()->request->getParam('ppid','');

        $pproduct = LAPProductService::getById($ppid);

        $this->render('edit',array(
            'ppid' => $ppid,
            'pproduct' => $pproduct,
        ));
    }

    public function actionEdit()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006102);

        $this->setJsMain('taModify');

        $tid = Yii::app()->request->getParam('tid','');

        $ta = LATaService::getById($tid);
        $pproduct = LAPProductService::getById($ta->ppid);

        $this->render('edit',array(
            'pproduct' => $pproduct,
            'ta' => $ta,
            'tid' => $tid,
            'ppid' => $ta->ppid
        ));
    }

    public function actionSave()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        $ppid = Yii::app()->request->getParam('ppid');
        if(!$ppid)
        {
            throw new CHttpException(404,'缺少必要参数');
            Yii::app()->end();
        }

        $tid = Yii::app()->request->getParam('tid');
        if (!$tid)
        {
            $ta = new TaFormModel();
            $ta->setAttributes($_POST);
            $ta->setScenario(TaFormModel::TA_NEW);
            $ta->validate();
            if ($errors = $ta->getErrors())
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, '数据不能为空');
            }

            $taData = $ta->getData();
            if(LATaService::create($ppid, $taData))
            {
                $this->ajaxReturn(LError::SUCCESS, "录入收益分配信息成功！", array("url" => Yii::app()->createUrl("ta/list")));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "录入收益分配信息失败！");
            }
        }
        else
        {
            $ta = new TaFormModel();
            $ta->setAttributes($_POST);
            $ta->setScenario(TaFormModel::TA_EDIT);
            $ta->validate();
            if ($errors = $ta->getErrors())
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, '数据不能为空');
            }

            $taData = $ta->getData();
            if(LATaService::update($tid, $taData))
            {
                $this->ajaxReturn(LError::SUCCESS, "更新收益分配信息成功！", array("url" => Yii::app()->createUrl("ta/list" )));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "更新收益分配信息失败！");
            }
        }
    }

    public function actionExec()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006102);

        $this->setJsMain('taEdit');

        $ppid = Yii::app()->request->getParam('ppid','');

        $pproduct = LAPProductService::getById($ppid);
        $ta = LATaService::getTaByPPid($ppid);

        $this->render('exec',array(
            'ppid' => $ppid,
            'pproduct' => $pproduct,
            'ta' => $ta
        ));
    }

    public function actionDoExec()
    {

    }
}

class TaFormModel extends AdminBaseFormModel
{
    const TA_NEW           = 'ta_new';
    const TA_EDIT          = 'ta_edit';

    public $term;
    public $fact_end_date;
    public $fact_principal;
    public $fact_income;
    public $fact_income_rate_E6;
    public $file_path;

    public function rules()
    {
        return array(
            array('term, fact_end_date, fact_principal, fact_income, fact_income_rate_E6, file_path, create_time, update_time', 'safe'),

            array('term, fact_end_date, fact_principal, fact_income, fact_income_rate_E6', 'required', 'on' => array(self::TA_NEW, self::TA_EDIT))
        );
    }

    public function getData()
    {
        $data = $this->attributes;
        $data['fact_principal'] = $this->fact_principal * LConstService::E4;
        $data['fact_income'] = $this->fact_income * LConstService::E4;
        $data['fact_income_rate_E6'] = $this->fact_income_rate_E6 * LConstService::E4;
        $data['fact_end_date'] = strtotime($this->fact_end_date);

        return $this->trimData($data);
    }

    public function trimData($data)
    {
        foreach ($data as $key =>  $val)
        {
            if (!is_array($val))
            {
                $data[$key] = trim($val);
            }
        }

        return $data;
    }
}