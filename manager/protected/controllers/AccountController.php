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

    public function actionSave()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        $id   = Yii::app()->request->getParam('id','');
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



class SpvEditFormModel extends AdminBaseFormModel
{
    const SPV_NEW           = 'account_new';
    const SPV_EDIT          = 'account_edit';

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
        );
    }
}