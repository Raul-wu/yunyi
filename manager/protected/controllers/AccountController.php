<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/10/17
 * Time: 21:19
 */
class AccountController extends AdminBaseController
{
    public $menuId = 2004;

    public function actionList()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $this->setJsMain('accountList');

        $conditions['name'] = trim(Yii::app()->request->getParam('name',''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));
        $arrAccount = LAAccountService::getAll($conditions, $conditions['page']);

        $this->render('list',array(
            'accounts'  => $arrAccount['accountAll'],
            'pageBar'   => $arrAccount['pageBar'],
            'count'     => $arrAccount['count'],
            'name'      => $conditions['name'],
            'url'       => array('editUrl'=>Yii::app()->createUrl("account/edit/")),
        ));
    }

    public function actionAdd()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006102);

        $this->setJsMain('accountEdit');

        $this->render('edit',array(
            'opType'            => 'add',
            'chk_state'         => 'checked',
        ));
    }

    public function actionEdit()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006103);

        $this->setJsMain('accountEdit');

        $id = trim(Yii::app()->request->getParam('id',''));
        $objAccount = LAAccountService::getByID($id);

        $this->render('edit',array(
            'opType'    => 'edit',
            'id'     => $id,
            'type'  => $objAccount->type,
            'name'   => $objAccount->name,
            'bank_account'   => $objAccount->bank_account,
            'bank_address'  => $objAccount->bank_address,
            'handler'   => $objAccount->handler,
            'status'      => $objAccount->status,
        ));
    }

    /**
     * 保存spv新增/修改
     */
    public function actionSave()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        $params  = $this->getParams();
        $spvId   = $params['spvId'];
        $arrData = array(
            'name'          => $params['name'],
            'account'       => $params['account'],
            'address'       => $params['address'],
            'stamp'         => LASPVService::http_get_data($params['imgPath']),
            'isBeneAccount' => $params['isBeneAccount'],
            'state'         => LMerchantInfoModel::STATE_NORMAL,
            'showName'      => $params['showName'],
            'payName'       => $params['payName'],
            'settlement'    => $params['settlement'],
            'imgPath'       => $params['imgPath'],
            'remarks'       => $params['remarks'],
            'spvName'       => $params['spvName'],
        );
        if( $params['opType'] == 'add')
        {
            $scenario = SpvEditFormModel::SPV_NEW;
            $msg = '新增spv操作';
        }
        else
        {
            $scenario = SpvEditFormModel::SPV_EDIT;
            $msg = '修改spv操作';
        }

        $request = new SpvEditFormModel();
        $request->setScenario($scenario);
        $request->setAttributes($params);
        $request->validate();
        if ($errors = $request->getErrors())
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, '数据不能为空');
        }

        if( $params['opType'] == 'add')
        {
            if(LASPVService::Create($arrData))
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
            if(LASPVService::Update($spvId, $arrData))
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

        $this->ajaxReturn($code, $msg, array("url" => Yii::app()->createUrl("spv/list")));
    }

    /**
     * spv启用/禁用
     */
    public function actionChangeState()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006101);

        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        $params = $this->getParams();
        if(empty($params['spvIDs']))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, 'spvID不能为空!');
        }
//        if(empty($params['isBeneAccount']))
//        {
//            $this->ajaxReturn(LError::INTERNAL_ERROR, '参数错误，缺少更新状态值!');
//        }
        if(!in_array($params['isBeneAccount'],array(LMerchantInfoModel::IS_BENEACCOUNT,LMerchantInfoModel::NOT_BENEACCOUNT)))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, '参数错误，更新状态值异常!');
        }

        $arrSpvIDs = explode(',',$params['spvIDs']);
        $count = count($arrSpvIDs);
        $strID = '';
        $updateData = array(
            'isBeneAccount' => $params['isBeneAccount'],
            'updateTime'    => time()
        );
        foreach($arrSpvIDs as $spvID)
        {
            if(!LASPVService::Update($spvID, $updateData))
            {
                $strID .= !empty($strID) ? ',' . $spvID : $spvID;
            }
        }

        $strMsg = "共{$count}个Spv正在执行" . LMerchantInfoModel::$arrBeneAccount[$params['isBeneAccount']] . "操作";
        if(!empty($strID))
        {
            $strMsg .= ",SpvID：{$strID}操作失败，请关注！";
        }
        $this->ajaxReturn(LError::SUCCESS, "{$strMsg}");
    }

    private function getParams()
    {
        $showName   = trim(Yii::app()->request->getParam('showName',''));
        $name       = trim(Yii::app()->request->getParam('name', ''));
        $account    = trim(Yii::app()->request->getParam('account', ''));
        $page       = trim(Yii::app()->request->getParam('page', 1));
        $spvIDs     = trim(Yii::app()->request->getParam('spvIDs', 0));
        $state      = trim(Yii::app()->request->getParam('state', 1));
        $spvId      = trim(Yii::app()->request->getParam('spvId', 0));
        $isBeneAccount = trim(Yii::app()->request->getParam('isBeneAccount', 1));

        //新增、修改表单参数
        $spvName    = trim(Yii::app()->request->getParam('spvName', ''));
        $payName    = trim(Yii::app()->request->getParam('payName', ''));
        $address    = trim(Yii::app()->request->getParam('address', ''));
        $imgPath    = trim(Yii::app()->request->getParam('imgPath', ''));
        $settlement = trim(Yii::app()->request->getParam('settlement'));
        $remarks    = trim(Yii::app()->request->getParam('remarks', ''));
        $opType     = trim(Yii::app()->request->getParam('opType', 'add'));

        return array(
            'showName'  => $showName,
            'name'      => $name,
            'account'   => $account,
            'url'       => Yii::app()->createUrl('/spv/list?'),
            'page'      => $page,
            'spvIDs'    => $spvIDs,        //批量 启用/禁用 功能用ID
            'state'     => $state,         //C++用于打款状态
            'spvId'     => $spvId,         //新增、编辑 功能用ID
            'opType'    => $opType,
            'spvName'   => $spvName,
            'payName'   => $payName,
            'address'   => $address,
            'imgPath'   => $imgPath,
            'settlement'=> $settlement,
            'remarks'   => $remarks,
            'isBeneAccount'   => $isBeneAccount,//启用、禁用spv用的状态
        );
    }
}



class SpvEditFormModel extends AdminBaseFormModel
{
    const SPV_NEW           = 'spv_new';
    const SPV_EDIT          = 'spv_edit';
    const SPV_CHANGE_STATE  = 'spv_change_state';

    public $spvId;          //spvId, 主键
    public $spvName;        //spv名称
    public $name;           //合同用的名称（合同上的乙方）
    public $account;        //商户号（金通打款帐号）
    public $address;        //spv地址
    public $stamp;          //spv合同章, 二进制形式保存在数据中
    public $isBeneAccount;  //是否是收款账户
    public $state;          //spv状态
    public $showName;       //后台显示名
    public $payName;        //商户名
    public $createUserId;   //创建spv的后台用户ID
    public $settlement;     //结算方式
    public $imgPath;        //合同章路径, 用于列表页展示
    public $remarks;        //备注

    public function rules()
    {
        return array(
            array('name, spvName, account, account, address, stamp, isBeneAccount, state, showName, payName, createUserId, settlement, imgPath, remarks, createTime, updateTime', 'safe'),
            //新增spv校验规则
            array('spvId, name, spvName, account, account, address, state, showName, payName, settlement, imgPath', 'required', 'on' => self::SPV_NEW),
            //更新spv校验规则
            array('name, spvName, account, account, address, state, showName, payName, settlement, imgPath', 'required', 'on' => self::SPV_EDIT),
        );
    }
}