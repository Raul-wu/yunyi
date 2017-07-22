<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/11/17
 * Time: 20:47
 */
class PProductController extends AdminBaseController
{
    public $menuId = 2001;

    public function actionList()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $this->setJsMain('pproductList');

        $conditions['fund_code'] = trim(Yii::app()->request->getParam('fund_code', ''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));

        $infoRes = LAPProductService::getAll($conditions, $conditions['page']);

        $this->render('list', array(
            'pproducts' => $infoRes['productAll'],
            'count' => $infoRes['count'],
            'fund_code' => $conditions['fund_code'],
            'pageBar'   => $infoRes['pageBar'],
        ));
    }

    public function actionAdd()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2001102);

        $this->setJsMain('pproductEdit');

        $this->render('edit',array(
            'opType'            => 'add',
            'chk_state'         => 'checked',
        ));
    }

    public function actionEdit()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2001103);

        $this->setJsMain('pproductEdit');

        $ppid = trim(Yii::app()->request->getParam('ppid',''));
        $arrPProduct = LAPProductService::getPProductAndPProductDetail($ppid);

        $this->render('edit',array(
            'opType'    => 'edit',
            'ppid'     => $ppid,
            'pproduct'  => $arrPProduct,
        ));
    }

    public function actionSave()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        if (!$ppid = Yii::app()->request->getParam('ppid'))
        {
            $pproduct = new PProductFormModel();
            $pproduct->setScenario(PProductFormModel::PPRODUCT_NEW);
            $pproduct->setAttributes($_POST);
            $pproduct->validate();
            if ($errors = $pproduct->getErrors())
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, '数据不能为空');
            }
            $pproductData = $pproduct->getData();

            $pproductDetail = new PProductDetailFormModel();
            $pproductDetail->setAttributes($_POST);
            $pproductDetail->validate();

            $pproductDetailData = $pproductDetail->getData();

            if(LAPProductService::create($pproductData, $pproductDetailData))
            {
                $this->ajaxReturn(LError::SUCCESS, "创建母产品成功！", array("url" => Yii::app()->createUrl("PProduct/list" )));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "创建母产品失败！");
            }
        }
        else
        {
            $pproduct = new PProductFormModel();
            $pproduct->setScenario(PProductFormModel::PPRODUCT_EDIT);
            $pproduct->setAttributes($_POST);
            $pproduct->validate();
            if ($errors = $pproduct->getErrors())
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, '数据不能为空');
            }
            $pproductData = $pproduct->getData();

            $pproductDetail = new PProductDetailFormModel();
            $pproductDetail->setAttributes($_POST);
            $pproductDetail->validate();
            $pproductDetailData = $pproductDetail->getData();

            if(LAPProductService::update($ppid, $pproductData, $pproductDetailData))
            {
                $this->ajaxReturn(LError::SUCCESS, "更新母产品成功！", array("url" => Yii::app()->createUrl("PProduct/list" )));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "更新母产品失败！");
            }
        }
    }

    public function actionDelete()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2001104);

        if (!$ppids = Yii::app()->request->getParam('ppids'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        $arrPPids = explode(',', $ppids);
        $succID = $failID = '';
        foreach($arrPPids as $ppid)
        {
            $pproduct = LAPProductService::getById($ppid);

            if (LAPProductService::updatePProductStatus($pproduct, LAPProductModel::STATUS_DELETE))
            {
                $succID .= empty($succID) ? $ppid : ',' . $ppid;
            }
            else
            {
                $failID .= empty($failID) ? $ppid : ',' . $ppid;
            }
        }

        if(!$failID)
        {
            $this->ajaxReturn(LError::SUCCESS, "母产品ID:{$succID}删除成功");
        }
        else
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "母产品ID:{$succID}删除成功;母产品ID:{$failID}删除失败");
        }
    }

    public function actionDuration()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2001105);

        if (!$ppids = Yii::app()->request->getParam('ppids'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        $arrPPids = explode(',', $ppids);
        $succID = $failID = '';
        foreach($arrPPids as $ppid)
        {
            $pproduct = LAPProductService::getById($ppid);

            if (LAPProductService::updatePProductStatus($pproduct, LAPProductModel::STATUS_DURATION))
            {
                $succID .= empty($succID) ? $ppid : ',' . $ppid;
            }
            else
            {
                $failID .= empty($failID) ? $ppid : ',' . $ppid;
            }
        }

        if(!$failID)
        {
            $this->ajaxReturn(LError::SUCCESS, "母产品ID:{$succID}转存续成功");
        }
        else
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "母产品ID:{$succID}转存续成功;母产品ID:{$failID}转存续失败");
        }
    }

}

class PProductFormModel extends AdminBaseFormModel
{
    const PPRODUCT_NEW           = 'pproduct_new';
    const PPRODUCT_EDIT          = 'pproduct_edit';

    public $fund_code;
    public $name;
    public $goods_type;
    public $struct;
    public $type;
    public $mode;
    public $scale;
    public $remain;
    public $income_rate_E6;
    public $buy_rate_E6;
    public $establish;
    public $value_date;
    public $duration_data;
    public $expected_date;
    public $interest_principle;
    public $management;
    public $trusteeship;
    public $epiboly;
    public $service_fees;
    public $adviser_fees;
    public $lending_rate_E6;
    public $investment_term;
    public $is_exchange;
    public $is_dely;
    public $pay_rule;

    public function rules()
    {
        return array(
            array('fund_code, name, goods_type, struct, type, mode, scale, remain, income_rate_E6, buy_rate_E6,
            establish, value_date, duration_data, expected_date, interest_principle, management, trusteeship, epiboly, 
            service_fees, adviser_fees, lending_rate_E6, investment_term, is_exchange, is_dely, pay_rule, create_time, update_time', 'safe'),

            array('fund_code, name, scale, income_rate_E6, value_date, expected_date', 'required', 'on' => array(self::PPRODUCT_NEW, self::PPRODUCT_EDIT))
        );
    }

    public function getData()
    {
        $data = $this->attributes;
        $data['income_rate_E6'] = $this->income_rate_E6 * LConstService::E4;
        $data['buy_rate_E6'] = $this->buy_rate_E6 * LConstService::E4;
        $data['lending_rate_E6'] = $this->lending_rate_E6 * LConstService::E4;
        $data['establish'] = strtotime($this->establish);
        $data['value_date'] = strtotime($this->value_date);
        $data['expected_date'] = strtotime($this->expected_date);

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

class PProductDetailFormModel extends AdminBaseFormModel
{
    const PPRODUCT_DETAIL_NEW           = 'pproduct_detail_new';
    const PPRODUCT_DETAIL_EDIT          = 'pproduct_detail_edit';

    public $ppid;
    public $finance_name;
    public $project_name;
    public $parent_finance_name;
    public $money_use;
    public $payment_source;
    public $risk_control;
    public $project_city;
    public $project_address;
    public $project_address_img;
    public $project_address_explain;
    public $project_summary;
    public $project_detail;
    public $manager;
    public $team_leader;
    public $project_manager;
    public $trustee;
    public $project_type;
    public $department;
    public $risk_level;
    public $legal_structure;
    public $publishing_organization;

    public function rules()
    {
        return array(
            array('ppid, finance_name, project_name, parent_finance_name, money_use, payment_source, risk_control, project_city,
            project_address, project_address_img, project_address_explain, project_summary, project_detail, manager, team_leader,
            project_manager, trustee, project_type, department, risk_level, legal_structure, publishing_organization',  'safe'),
        );
    }

    public function getData()
    {
        $data = $this->attributes;

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