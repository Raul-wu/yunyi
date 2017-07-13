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

        $ppid   = Yii::app()->request->getParam('ppid','');
        $opType = Yii::app()->request->getParam('opType','');
        $arrData = array(
            'type'          => trim(Yii::app()->request->getParam('type','')),
            'name'          => trim(Yii::app()->request->getParam('name','')),
            'bank_account'       => trim(Yii::app()->request->getParam('bank_account','')),
            'bank_address'      => trim(Yii::app()->request->getParam('bank_address','')),
            'handler'       => trim(Yii::app()->request->getParam('handler','')),
            'status'    => Yii::app()->request->getParam('status','')
        );
        if( $opType == LAAccountModel::OP_TYPE_ADD)
        {
            $scenario = SpvEditFormModel::SPV_NEW;
            $msg = '新增操作';
        }
        else
        {
            $scenario = SpvEditFormModel::SPV_EDIT;
            $msg = '修改操作';
        }

        $request = new SpvEditFormModel();
        $request->setScenario($scenario);
        $request->setAttributes($arrData);
        $request->validate();
        if ($errors = $request->getErrors())
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, '数据不能为空');
        }

        if( $opType == LAAccountModel::OP_TYPE_ADD)
        {
            if(LAAccountService::Create($arrData))
            {
                $msg .= ',成功';
                $code = LError::SUCCESS;
            }
            else
            {
                $msg .= ',失败';
                $code = LError::INTERNAL_ERROR;
            }
        }
        else
        {
            if(LAAccountService::Update($id, $arrData))
            {
                $msg .= ',成功';
                $code = LError::SUCCESS;
            }
            else
            {
                $msg .= ',失败';
                $code = LError::INTERNAL_ERROR;
            }
        }

        $this->ajaxReturn($code, $msg, array("url" => Yii::app()->createUrl("account/list")));
    }
}

class PProductEditFormModel extends AdminBaseFormModel
{
    const PPRODUCT_NEW           = 'pproduct_new';
    const PPRODUCT_EDIT          = 'pproduct_edit';

    public $ppid;
    public $fund_code;
    public $name;
    public $goods_type;
    public $struct;
    public $project_type;
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
            array('und_code, name, goods_type, struct, project_type, mode, scale, remain, income_rate_E6, buy_rate_E6,
            establish, value_date, duration_data, expected_date, interest_principle, management, trusteeship, epiboly, 
            service_fees, adviser_fees, lending_rate_E6, investment_term, is_exchang, is_dely, pay_rule, create_time, update_time', 'safe'),
        );
    }
}

class PProductDetailFormModel extends AdminBaseController
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
}