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

    public function actionDelete()
    {
        if (!$qids = Yii::app()->request->getParam('qids'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        $arrQids = explode(',', $qids);
        $succID = $failID = '';
        foreach($arrQids as $qid)
        {
            if (LAQuotientService::deleteQuotientStatusByQid($qid))
            {
                $succID .= empty($succID) ? $qid : ',' . $qid;
            }
            else
            {
                $failID .= empty($failID) ? $qid : ',' . $qid;
            }
        }

        if(!$failID)
        {
            $this->ajaxReturn(LError::SUCCESS, "客户份额ID:{$succID}删除成功");
        }
        else
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "客户份额ID:{$succID}删除成功;客户份额ID:{$failID}删除失败");
        }
    }

    public function actionAddOne()
    {
        $this->setJsMain('quotientEditOne');

        $pid = Yii::app()->request->getParam('pid');

        $this->render('editOne',array(
            'pid'            => $pid,
        ));
    }

    public function actionEditOne()
    {
        $this->setJsMain('quotientEditOne');

        $qid = Yii::app()->request->getParam('qid');
        $quotient = LAQuotientService::getByID($qid);
        $this->render('editOne',array(
            'qid' =>$qid,
            'quotient' => $quotient
        ));
    }

    public function actionSaveOne()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        $pid = Yii::app()->request->getParam('pid');
        $product = LAProductService::getById($pid);
        $total = LAQuotientService::getTotalAmountByPid($pid);
        if($product->total_count < $total+intval($_POST['amount']))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！已达子产品限购额度上限");
        }
        if($product->per_user_by_limit)
        {
            $total_amount = LAQuotientService::getUsersTotalAmountByIDCard($_POST['id_content']);
            if($product->per_user_by_limit < ($total_amount + intval($_POST['amount'])))
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！已达单用户限购额度");
            }
        }
        if($product->max_buy && ( intval($_POST['amount']) > $product->max_buy))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！已达单笔最大金额");
        }
        if($product->min_buy && ( intval($_POST['amount']) < $product->min_buy ))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！已达单笔最小金额");
        }

        if (!$qid = Yii::app()->request->getParam('qid'))
        {
            $quotient = new QuotientEditFormModel();
            $quotient->setAttributes($_POST);
            $quotient->setScenario(QuotientEditFormModel::QUOTIENT_NEW_ONE);
            $quotient->validate();
            if ($errors = $quotient->getErrors())
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, '数据不能为空');
            }

            $quotientData = $quotient->getData();
            if(LAQuotientService::create($pid, $quotientData))
            {
                $this->ajaxReturn(LError::SUCCESS, "创建客户份额成功！", array("url" => Yii::app()->createUrl("quotient/list?pid=". $pid)));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！");
            }
        }
        else
        {
            $quotient = new QuotientEditFormModel();
            $quotient->setAttributes($_POST);
            $quotient->setScenario(QuotientEditFormModel::QUOTIENT_EDIT_ONE);
            $quotient->validate();
            if ($errors = $quotient->getErrors())
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, '数据不能为空');
            }

            $quotientData = $quotient->getData();
            if(LAQuotientService::update($qid, $quotientData))
            {
                $this->ajaxReturn(LError::SUCCESS, "更新客户份额成功！", array("url" => Yii::app()->createUrl("quotient/list?pid=". $pid)));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "更新客户份额失败！");
            }
        }
    }
}

class QuotientEditFormModel extends AdminBaseFormModel
{
    const QUOTIENT_NEW_ONE          = 'quotient_new_one';
    const QUOTIENT_EDIT_ONE         = 'quotient_edit_one';

    public $pid;
    public $name;
    public $type;
    public $amount;
    public $id_type;
    public $id_content;
    public $handler_name;
    public $delegate_name;
    public $bank_account;
    public $bank_name;
    public $bank_address;
    public $bank_province;
    public $bank_city;
    public $status;

    public function rules()
    {
        return array(
            array('pid, name, amount, type, id_type, id_content, handler_name, delegate_name, bank_account, bank_name, bank_address, bank_province, bank_city, status, create_time, update_time', 'safe'),

            array('pid, name, amount, id_content', 'required', 'on' => array(self::QUOTIENT_NEW_ONE, self::QUOTIENT_EDIT_ONE)),
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