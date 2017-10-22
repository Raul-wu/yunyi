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
        $conditions['status'] = trim(Yii::app()->request->getParam('status',''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));
        $conditions['fund_name'] = trim(Yii::app()->request->getParam('fund_name', ''));
        $conditions['quotient_name'] = trim(Yii::app()->request->getParam('quotient_name', ''));
        $conditions['id_card'] = trim(Yii::app()->request->getParam('id_card', ''));
        $arrQuotient = LAQuotientService::getAll($conditions, $conditions['page']);

        $this->render('list',array(
            'quotients'  => $arrQuotient['quotientAll'],
            'pageBar'   => $arrQuotient['pageBar'],
            'count'     => $arrQuotient['count'],
            'name'     => $conditions['name'],
            'fund_name'     => $conditions['fund_name'],
            'quotient_name'     => $conditions['quotient_name'],
            'id_card'     => $conditions['id_card'],
            'status'     => $conditions['status'],
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

        if(empty($_POST['buy_date']))
        {
            $this->ajaxReturn(LError::SUCCESS, '请输入购买日');
        }

        $buy_date = strtotime($_POST['buy_date']);

        $result = LAQuotientService::analysisServiceExcel($pid, $filePath, $buy_date);
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

    public function actionSelect()
    {
        $qid = Yii::app()->request->getParam('qid');
        $quotient = LAQuotientService::getByID($qid);



        $quotientChange = LAQuotientService::getAllByChangeId($qid);
        $this->render('select',array(
            'qid' =>$qid,
            'quotient' => $quotient,
            'quotientChange' => $quotientChange
        ));
    }

    public function actionChange()
    {
        $this->setJsMain('quotientChange');

        $qid = Yii::app()->request->getParam('qid');
        $quotient = LAQuotientService::getByID($qid);
        $this->render('change',array(
            'qid' =>$qid,
            'quotient' => $quotient
        ));
    }

    public function actionSaveChange()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        $pid = Yii::app()->request->getParam('pid');
        $product = LAProductService::getById($pid);
        $total = LAQuotientService::getTotalAmountByPid($pid);


        if($product->status != LAProductModel::STATUS_DURATION)
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "您当前变更的客户份额不在存续中,不能变更!");
        }
        if($product->per_user_by_limit)
        {
            $total_amount = LAQuotientService::getUsersTotalAmountByIDCard($_POST['id_content']);
            if($product->per_user_by_limit < ($total_amount + (intval($_POST['amount'])  * LConstService::E4)))
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！已达单用户限购额度");
            }
        }
        if($product->max_buy && ( (intval($_POST['amount'])  * LConstService::E4) > $product->max_buy))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！已达单笔最大金额");
        }
        if($product->min_buy && ( (intval($_POST['amount'])  * LConstService::E4 ) < $product->min_buy ))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！已达单笔最小金额");
        }
        if ($qid = Yii::app()->request->getParam('qid'))
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
            if($ret = LAQuotientService::create($pid, $quotientData))
            {
                LAQuotientService::update($qid, array(
                    "changeQid" => $ret->qid,
                    "status" => LAQuotientModel::STATUS_DEL,
                ));

                $criteria = new CDbCriteria();
                $criteria->compare("changeQid", $qid);
                $updateOption = array(
                    "changeQid" => $ret->qid
                );
                LAQuotientModel::model()->updateAll($updateOption, $criteria);

                $this->ajaxReturn(LError::SUCCESS, "变更客户份额成功！", array("url" => Yii::app()->createUrl("quotient/list?pid=". $pid)));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "变更客户份额失败！");
            }
        }
        $this->ajaxReturn(LError::INTERNAL_ERROR, "变更客户份额失败！");

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

        if($product->status != LAProductModel::STATUS_ESTABLISH)
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "您当前编辑的客户份额不在成立中,不能编辑!");
        }

        if($product->total_count < $total + (intval($_POST['amount']) * LConstService::E4))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！已达子产品限购额度上限");
        }
        if($product->per_user_by_limit)
        {
            $total_amount = LAQuotientService::getUsersTotalAmountByIDCard($_POST['id_content']);
            if($product->per_user_by_limit < ($total_amount + (intval($_POST['amount'])  * LConstService::E4)))
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！已达单用户限购额度");
            }
        }
        if($product->max_buy && ( (intval($_POST['amount'])  * LConstService::E4) > $product->max_buy))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "创建客户份额失败！已达单笔最大金额");
        }
        if($product->min_buy && ( (intval($_POST['amount'])  * LConstService::E4 ) < $product->min_buy ))
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
            unset($quotientData["status"]);
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

    public function actionExport()
    {
        $qids = Yii::app()->request->getParam('qids','');
        $pids = Yii::app()->request->getParam('pids', '');

        $conditions = array(
            'qid' => $qids != 'null' ? $qids : "",
            'pid' => $pids != 'null' ? $pids : "",
        );

        $arrQuotient = LAQuotientService::getAll($conditions, 0);

        Yii::$enableIncludePath = false;
        Yii::import('extensions.PHPExcelSuite.*', true);

        $objPhpExcel = null;
        $objPhpExcel = new PHPExcel();
        $fileName    = '客户份额列表_' . date('YmdHis', time()) . '.xlsx';

        $objPhpExcel->getProperties()->setCreator("TA")
            ->setLastModifiedBy("TA")
            ->setTitle('yunyin')
            ->setSubject("")
            ->setDescription("")
            ->setKeywords("")
            ->setCategory("");

        $objActSheet = $objPhpExcel->getActiveSheet();
        $objStyleA1  = $objActSheet->getStyle('A1');
        $objAlignA1  = $objStyleA1->getAlignment();
        $objAlignA1->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPhpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

        $objPhpExcel->getActiveSheet()->setTitle('客户份额列表');
        $objPhpExcel->getActiveSheet()->setCellValue('A1', '子产品名称');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '投资人姓名');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '交易金额（万元）');
        $objPhpExcel->getActiveSheet()->setCellValue('D1', '投资类型');
        $objPhpExcel->getActiveSheet()->setCellValue('E1', '证件类别');
        $objPhpExcel->getActiveSheet()->setCellValue('F1', '证件号码');
        $objPhpExcel->getActiveSheet()->setCellValue('G1', '经办人姓名');
        $objPhpExcel->getActiveSheet()->setCellValue('H1', '状态');

        $i = 1;
        foreach ($arrQuotient['quotientAll'] as $quotient)
        {
            $i++;

            $objPhpExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $quotient['product']['name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $quotient['name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('C' . $i, $quotient['amount'] / LConstService::E4, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('D' . $i, isset(LAQuotientModel::$arrType[$quotient['type']]) ? LAQuotientModel::$arrType[$quotient['type']] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('E' . $i, isset(LAQuotientModel::$arrIdType[$quotient['id_type']]) ? LAQuotientModel::$arrIdType[$quotient['id_type']] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('F' . $i, $quotient['id_content'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('G' . $i, $quotient['handler_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('H' . $i, isset(LAProductModel::$arrStatus[$quotient['product']['status']]) ? LAProductModel::$arrStatus[$quotient['product']['status']] : '', PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type: application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header("Content-Disposition:attachment;filename=" . $fileName);
        header("Content-Transfer-Encoding:binary");
        $objWriter->save("php://output");
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
    public $buy_date;

    public function rules()
    {
        return array(
            array('pid, name, amount, type, id_type, id_content, handler_name, delegate_name, bank_account, bank_name, bank_address, bank_province, bank_city, status, buy_date, create_time, update_time', 'safe'),

            array('pid, name, amount, id_content, buy_date', 'required', 'on' => array(self::QUOTIENT_NEW_ONE, self::QUOTIENT_EDIT_ONE)),
        );
    }

    public function getData()
    {
        $data = $this->attributes;

        $data['amount'] = $data['amount'] * LConstService::E4;
        $data['buy_date'] = strtotime($this->buy_date);

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