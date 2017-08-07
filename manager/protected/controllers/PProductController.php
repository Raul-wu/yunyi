<?php

/**
 * Created by PhpStorm.
 * User: Raul
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
            'remain'    => LAPProductService::getProductTotalCountByPPid($ppid)
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
                $this->ajaxReturn(LError::SUCCESS, "创建基金成功！", array("url" => Yii::app()->createUrl("PProduct/list" )));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "创建基金失败！");
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
                $this->ajaxReturn(LError::SUCCESS, "更新基金成功！", array("url" => Yii::app()->createUrl("PProduct/list" )));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "更新基金失败！");
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

        $hasPids = LAProductService::getPidByPPid($ppids);
        if($hasPids)
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "请先删除子产品后再删除基金！");
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

        if(LAProductService::updateProductStatusByPPid($succID, LAProductModel::STATUS_DELETE))
        {
            $pids = LAProductService::getPidByPPid($succID);
            LAQuotientService::deleteQuotientStatusByPid($pids);
        }

        if(!$failID)
        {
            $this->ajaxReturn(LError::SUCCESS, "基金ID:{$succID}删除成功");
        }
        else
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "基金ID:{$succID}删除成功;基金ID:{$failID}删除失败");
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

        LAProductService::updateProductStatusByPPid($succID, LAProductModel::STATUS_DURATION);

        if(!$failID)
        {
            $this->ajaxReturn(LError::SUCCESS, "基金ID:{$succID}转存续成功");
        }
        else
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "基金ID:{$succID}转存续成功;基金ID:{$failID}转存续失败");
        }
    }

    public function actionCheckHasScale()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2001106);

        if (!$ppid = Yii::app()->request->getParam('ppid'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        $pproduct = LAPProductService::getById($ppid);//echo '<pre>';var_dump($pproduct->scale);echo '</pre>';exit;
        if($pproduct->scale <= 0)
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "募集规模为0时不可创建子产品");
        }
        else
        {
            $this->ajaxReturn(LError::SUCCESS, "");
        }
    }

}

class PProductFormModel extends AdminBaseFormModel
{
    const PPRODUCT_NEW           = 'pproduct_new';
    const PPRODUCT_EDIT          = 'pproduct_edit';

    public $fund_code;
    public $name;
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
    public $management_E6;
    public $trusteeship_E6;
    public $epiboly_E6;
    public $service_fees_E6;
    public $adviser_fees_E6;
    public $lending_rate_E6;
    public $investment_term;
    public $pay_rule;

    public function rules()
    {
        return array(
            array('fund_code, name, struct, type, mode, scale, remain, income_rate_E6, buy_rate_E6,
            establish, value_date, duration_data, expected_date, interest_principle, management_E6, trusteeship_E6, epiboly_E6, 
            service_fees_E6, adviser_fees_E6, lending_rate_E6, investment_term, pay_rule, create_time, update_time', 'safe'),

            array('fund_code, name, income_rate_E6, value_date, expected_date', 'required', 'on' => array(self::PPRODUCT_NEW, self::PPRODUCT_EDIT))
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

        $data['management_E6'] = $this->management_E6 * LConstService::E4;
        $data['trusteeship_E6'] = $this->trusteeship_E6 * LConstService::E4;
        $data['epiboly_E6'] = $this->epiboly_E6 * LConstService::E4;
        $data['service_fees_E6'] = $this->service_fees_E6 * LConstService::E4;
        $data['adviser_fees_E6'] = $this->adviser_fees_E6 * LConstService::E4;

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