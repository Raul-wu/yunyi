<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/14/17
 * Time: 21:16
 */
class TaController extends AdminBaseController
{
    public $menuId = 3002;

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

        if(strtotime($_POST['fact_end_date']) < $_POST['value_date'] )
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "实际到期日不能小于起息日！");
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
            if($objta = LATaService::create($ppid, $taData))
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

        $this->setJsMain('taExecEdit');

        $ppid = Yii::app()->request->getParam('ppid','');

        $pproduct = LAPProductService::getById($ppid);
        $ta = LATaService::getTaByPPid($ppid);

        $this->render('exec',array(
            'ppid' => $ppid,
            'pproduct' => $pproduct,
            'ta' => $ta,
            'tid' => isset($ta->tid) ? $ta->tid : 0
        ));
    }

    public function actionDoExec()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006102);

        if (!$ppid = Yii::app()->request->getParam('ppid'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        $pproduct = LAPProductService::getById($ppid);
        if(LAPProductService::updatePProductStatus($pproduct, LAPProductModel::STATUS_FINISH))
        {
            LAProductService::updateProductStatusByPPid($ppid, LAProductModel::STATUS_FINISH);

            LAQuotientService::updateQuotientStatusByPPid($ppid, LAQuotientModel::STATUS_FINISH);

            $this->ajaxReturn(LError::SUCCESS, "基金ID:{$ppid}清算成功", array("url" => Yii::app()->createUrl("history/detail?ppid={$ppid}" )));
        }
        else
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "基金ID:{$ppid}清算失败");
        }
    }

    public function actionCmbExcel()
    {
        if (!$tid = Yii::app()->request->getParam('tid'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        $cmb = LATaRecordsCMBService::getListByTid($tid);
        if(!$cmb )
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "不存在客户份额！");
        }

        Yii::$enableIncludePath = false;
        Yii::import('extensions.PHPExcelSuite.*', true);

        $objPhpExcel = null;
        $objPhpExcel = new PHPExcel();
        $fileName    = '招商银行版_' . date('YmdHis', time()) . '.xlsx';

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

        $objPhpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('S')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('T')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('U')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('W')->setWidth(30);

        $objPhpExcel->getActiveSheet()->setTitle('招商银行版');
        $objPhpExcel->getActiveSheet()->setCellValue('A1', '管理人名称');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '基金代码');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '基金名称');
        $objPhpExcel->getActiveSheet()->setCellValue('D1', '客户编号');
        $objPhpExcel->getActiveSheet()->setCellValue('E1', '登记账号');
        $objPhpExcel->getActiveSheet()->setCellValue('F1', '客户名称');
        $objPhpExcel->getActiveSheet()->setCellValue('G1', '客户类型');
        $objPhpExcel->getActiveSheet()->setCellValue('H1', '证件类型');
        $objPhpExcel->getActiveSheet()->setCellValue('I1', '证件号码');
        $objPhpExcel->getActiveSheet()->setCellValue('J1', '确认日期');
        $objPhpExcel->getActiveSheet()->setCellValue('K1', '业务类型');
        $objPhpExcel->getActiveSheet()->setCellValue('L1', '确认金额(元)');
        $objPhpExcel->getActiveSheet()->setCellValue('M1', '确认份额(份)');
        $objPhpExcel->getActiveSheet()->setCellValue('N1', '持有份额(份)');
        $objPhpExcel->getActiveSheet()->setCellValue('O1', '销售渠道');
        $objPhpExcel->getActiveSheet()->setCellValue('P1', '销售渠道代码');
        $objPhpExcel->getActiveSheet()->setCellValue('Q1', '分红方式');
        $objPhpExcel->getActiveSheet()->setCellValue('R1', '起息日');
        $objPhpExcel->getActiveSheet()->setCellValue('S1', '截止日');
        $objPhpExcel->getActiveSheet()->setCellValue('T1', '天数');
        $objPhpExcel->getActiveSheet()->setCellValue('U1', '利率%');
        $objPhpExcel->getActiveSheet()->setCellValue('V1', '利息(元)');
        $objPhpExcel->getActiveSheet()->setCellValue('W1', '本息合计(元)');
        $i = 1;
        foreach($cmb as $k => $v )
        {
            $i++;
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('A' . $i, isset($v['manager']) ? $v['manager'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('B' . $i, isset($v['fund_code']) ? $v['fund_code'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('C' . $i, isset($v['pproduct_name']) ? $v['pproduct_name'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('D' . $i, isset($v['qid']) ? $v['qid'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('E' . $i, isset($v['bank_account']) ? $v['bank_account'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('F' . $i, isset($v['name']) ? $v['name'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('G' . $i, isset($v['type']) ? $v['type'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('H' . $i, isset($v['id_type']) ? $v['id_type'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('I' . $i, isset($v['id_content']) ? $v['id_content'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('J' . $i, isset($v['conformation_date']) ? date('Y-m-d', strtotime($v['conformation_date'])) : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('K' . $i, '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('L' . $i, isset($v['conformation_amount']) ? round($v['conformation_amount'], 2) : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('M' . $i, $v['conformation_amount'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('N' . $i, $v['has_quotient'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('O' . $i, '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('P' . $i, '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('Q' . $i, '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('R' . $i, isset($v['value_date']) ? $v['value_date'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('S' . $i, isset($v['expected_date']) ? $v['expected_date'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('T' . $i, (strtotime($v['expected_date']) - strtotime($v['value_date'])) / 86400, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('U' . $i, isset($v['income_rate_E6']) ? $v['income_rate_E6'] . '%' : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('V' . $i, isset($v['total']) ? $v['total'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $total_amount = $v['total'] +  $v['conformation_amount'];
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('W' . $i, round($total_amount, 2), PHPExcel_Cell_DataType::TYPE_STRING);
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

    public function actionSHBankExcel()
    {
        if (!$tid = Yii::app()->request->getParam('tid'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        $shanghaiBank = LATaRecordsSHBankService::getListByTid($tid);
        if(!$shanghaiBank )
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "不存在客户份额！");
        }

        Yii::$enableIncludePath = false;
        Yii::import('extensions.PHPExcelSuite.*', true);

        $objPhpExcel = null;
        $objPhpExcel = new PHPExcel();
        $fileName    = '上海银行版_' . date('YmdHis', time()) . '.xlsx';

        $objPhpExcel->getProperties()->setCreator("TA")
            ->setLastModifiedBy("TA")
            ->setTitle('yunyin$objPhpExcel->getActiveSheet()->getColumnDimension')
            ->setSubject("")
            ->setDescription("")
            ->setKeywords("")
            ->setCategory("");

        $objActSheet = $objPhpExcel->getActiveSheet();
        $objStyleA1  = $objActSheet->getStyle('A1');
        $objAlignA1  = $objStyleA1->getAlignment();
        $objAlignA1->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPhpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
        $objPhpExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);

        $objPhpExcel->getActiveSheet()->setTitle('上海银行版');
        $objPhpExcel->getActiveSheet()->setCellValue('A1', '投资者名称');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '投资者类型');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '证件类型');
        $objPhpExcel->getActiveSheet()->setCellValue('D1', '证件号');
        $objPhpExcel->getActiveSheet()->setCellValue('E1', '投资者账号');
        $objPhpExcel->getActiveSheet()->setCellValue('F1', '开户行名称');
        $objPhpExcel->getActiveSheet()->setCellValue('G1', '类别');
        $objPhpExcel->getActiveSheet()->setCellValue('H1', '持有份额(份)');
        $objPhpExcel->getActiveSheet()->setCellValue('I1', '份额确认日');
        $objPhpExcel->getActiveSheet()->setCellValue('J1', '起息日');
        $objPhpExcel->getActiveSheet()->setCellValue('K1', '截止日');
        $objPhpExcel->getActiveSheet()->setCellValue('L1', '天数');
        $objPhpExcel->getActiveSheet()->setCellValue('M1', '利率%');
        $objPhpExcel->getActiveSheet()->setCellValue('N1', '利息(元)');
        $objPhpExcel->getActiveSheet()->setCellValue('O1', '本息合计(元)');
        $i = 1;
        foreach($shanghaiBank as $k => $v )
        {
            $i++;
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('A' . $i, isset($v['name']) ? $v['name'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('B' . $i, isset($v['type']) ? $v['type'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('C' . $i, isset($v['id_type']) ? $v['id_type'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('D' . $i, isset($v['id_content']) ? $v['id_content'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('E' . $i, isset($v['bank_account']) ? $v['bank_account'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('F' . $i, isset($v['bank_address']) ? $v['bank_address'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('G' . $i, '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('H' . $i, isset($v['amount']) ? $v['amount'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('I' . $i, isset($v['conformation_date']) ? date('Y-m-d', strtotime($v['conformation_date'])) : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('J' . $i, isset($v['value_date']) ? $v['value_date'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('K' . $i, isset($v['expected_date']) ? $v['expected_date'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('L' . $i, (strtotime($v['expected_date']) - strtotime($v['value_date'])) / 86400, PHPExcel_Cell_DataType::TYPE_STRING) ;
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('M' . $i, isset($v['income_rate_E6']) ? $v['income_rate_E6'] . '%' : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('N' . $i, $v['total'], PHPExcel_Cell_DataType::TYPE_STRING);
            $total_amound = $v['total'] + $v['amount'];
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('O' . $i, round($total_amound, 2), PHPExcel_Cell_DataType::TYPE_STRING);
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

    public function actionCheckHasQuotient()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006102);

        if (!$ppid = Yii::app()->request->getParam('ppid'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        $pids = LAProductService::getPidByPPid($ppid);
        $quotient = LAQuotientService::getAllByPids($pids);

        if(count($quotient) < 1)
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "没有客户份额不可以录入分配信息");
        }
        else
        {
            $this->ajaxReturn(LError::SUCCESS, "");
        }
    }
}

class TaFormModel extends AdminBaseFormModel
{
    const TA_NEW           = 'ta_new';
    const TA_EDIT          = 'ta_edit';

    public $term;
    public $ta_value_date;
    public $fact_end_date;
    public $fact_principal;
    public $fact_income;
    public $fact_income_rate_E6;
    public $file_path;

    public function rules()
    {
        return array(
            array('term, ta_value_date, fact_end_date, fact_principal, fact_income, fact_income_rate_E6, file_path, create_time, update_time', 'safe'),

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
        $data['ta_value_date'] = strtotime($this->ta_value_date);

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