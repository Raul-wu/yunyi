<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 9/11/17
 * Time: 22:37
 */
class LATaRecordsCMBService
{
    const LOG_PREFIX = 'admin.services.LATaRecordsCMBService.';

    public static function create($data)
    {
        if(empty($data))
        {
            Yii::log("data is empty",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            return false;
        }

        foreach ($data as $key => $value)
        {
            $data[$key]['create_time'] = $data[$key]['update_time'] = date('Y-m-d H:i:s', time());

            $objCMB = new LATaRecordsCMBModel();
            $objCMB->setAttributes($data[$key], false);
            if (!$objCMB->save())
            {
                Yii::log(sprintf("Create ta Fail, Params:[%s]",serialize($data)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                return  false;
            }
        }
        return true;
    }

    public static function deleteByTid($tid)
    {
        return LATaRecordsCMBModel::model()->deleteAll("tid =:tid", array(':tid' => $tid));
    }

    public static function getListByTid($tid)
    {
        $criteria = new CDbCriteria();

        $criteria->compare('tid', $tid, false);

        return LATaRecordsCMBModel::model()->findAll($criteria);
    }

    public static function generateExcelByTid($tid)
    {
        $cmb = self::getListByTid($tid);
        if(!$cmb )
        {
            return false;
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

        $objActSheet->getColumnDimension('A')->setWidth(30);
        $objActSheet->getColumnDimension('B')->setWidth(30);
        $objActSheet->getColumnDimension('C')->setWidth(30);
        $objActSheet->getColumnDimension('D')->setWidth(30);
        $objActSheet->getColumnDimension('E')->setWidth(30);
        $objActSheet->getColumnDimension('F')->setWidth(30);
        $objActSheet->getColumnDimension('G')->setWidth(30);
        $objActSheet->getColumnDimension('H')->setWidth(30);
        $objActSheet->getColumnDimension('I')->setWidth(30);
        $objActSheet->getColumnDimension('J')->setWidth(30);
        $objActSheet->getColumnDimension('K')->setWidth(30);
        $objActSheet->getColumnDimension('L')->setWidth(30);
        $objActSheet->getColumnDimension('M')->setWidth(30);
        $objActSheet->getColumnDimension('N')->setWidth(30);
        $objActSheet->getColumnDimension('O')->setWidth(30);
        $objActSheet->getColumnDimension('P')->setWidth(30);
        $objActSheet->getColumnDimension('Q')->setWidth(30);
        $objActSheet->getColumnDimension('R')->setWidth(30);
        $objActSheet->getColumnDimension('S')->setWidth(30);
        $objActSheet->getColumnDimension('T')->setWidth(30);
        $objActSheet->getColumnDimension('U')->setWidth(30);
        $objActSheet->getColumnDimension('V')->setWidth(30);
        $objActSheet->getColumnDimension('W')->setWidth(30);

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
        $objPhpExcel->getActiveSheet()->setCellValue('L1', '确认金额(万元)');
        $objPhpExcel->getActiveSheet()->setCellValue('M1', '确认份额');
        $objPhpExcel->getActiveSheet()->setCellValue('N1', '持有份额');
        $objPhpExcel->getActiveSheet()->setCellValue('O1', '销售渠道');
        $objPhpExcel->getActiveSheet()->setCellValue('P1', '销售渠道代码');
        $objPhpExcel->getActiveSheet()->setCellValue('Q1', '分红方式');
        $objPhpExcel->getActiveSheet()->setCellValue('R1', '起息日');
        $objPhpExcel->getActiveSheet()->setCellValue('S1', '截止日');
        $objPhpExcel->getActiveSheet()->setCellValue('T1', '天数');
        $objPhpExcel->getActiveSheet()->setCellValue('U1', '利率%');
        $objPhpExcel->getActiveSheet()->setCellValue('V1', '利息(万元)');
        $objPhpExcel->getActiveSheet()->setCellValue('W1', '本息合计(万元)');
        $i = 1;
        foreach($cmb as $k => $v )
        {
            $i++;
            $objPhpExcel->getActiveSheet()->setCellValue('A' . $i, isset($v['manager']) ? $v['manager'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('B' . $i, isset($v['fund_code']) ? $v['fund_code'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('C' . $i, isset($v['pproduct_name']) ? $v['pproduct_name'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('D' . $i, isset($v['qid']) ? $v['qid'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('E' . $i, isset($v['bank_account']) ? $v['bank_account'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('F' . $i, isset($v['name']) ? $v['name'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('G' . $i, isset($v['type']) ? $v['type'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('H' . $i, isset($v['id_type']) ? $v['id_type'] : '');
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('I' . $i, isset($v['id_content']) ? $v['id_content'] : '' , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValue('J' . $i, isset($v['conformation_date']) ? $v['conformation_date'] : '');
//            $objPhpExcel->getActiveSheet()->setCellValue('K' . $i, isset($v['']);
            $objPhpExcel->getActiveSheet()->setCellValue('L' . $i, isset($v['conformation_amount']) ? $v['conformation_amount'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('M' . $i, $v['conformation_amount'] * LConstService::E4);
            $objPhpExcel->getActiveSheet()->setCellValue('N' . $i, $v['has_quotient'] * LConstService::E4);
//            $objPhpExcel->getActiveSheet()->setCellValue('O' . $i, isset($v['']);
//            $objPhpExcel->getActiveSheet()->setCellValue('P' . $i, isset($v['']);
//            $objPhpExcel->getActiveSheet()->setCellValue('Q' . $i, isset($v['']);
            $objPhpExcel->getActiveSheet()->setCellValue('R' . $i, isset($v['value_date']) ? $v['value_date'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('S' . $i, isset($v['expected_date']) ? $v['expected_date'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('T' . $i, (strtotime($v['expected_date']) - strtotime($v['value_date'])) / 86400);
            $objPhpExcel->getActiveSheet()->setCellValue('U' . $i, isset($v['income_rate_E6']) ? $v['income_rate_E6'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('V' . $i, $v['total'] - $v['amount']);
            $objPhpExcel->getActiveSheet()->setCellValue('W' . $i, isset($v['total']) ? $v['total'] : '');
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