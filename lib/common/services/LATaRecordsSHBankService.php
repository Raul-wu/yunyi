<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 9/11/17
 * Time: 22:38
 */
class LATaRecordsSHBankService
{
    const LOG_PREFIX = 'admin.services.LATaRecordsSHBankService.';

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

            $objSHBand = new LATaRecordsSHBankModel();
            $objSHBand->setAttributes($data[$key], false);
            if (!$objSHBand->save())
            {
                Yii::log(sprintf("Create ta Fail, Params:[%s]",serialize($data)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                return  false;
            }
        }
        return true;
    }

    public static function deleteByTid($tid)
    {
        return LATaRecordsSHBankModel::model()->deleteAll("tid =:tid", array(':tid' => $tid));
    }

    public static function getListByTid($tid)
    {
        $criteria = new CDbCriteria();

        $criteria->compare('tid', $tid, false);

        return LATaRecordsSHBankModel::model()->findAll($criteria);
    }

    public static function generateExcelByTid($tid)
    {
        $shanghaiBank = self::getListByTid($tid);
        if(!$shanghaiBank )
        {
            return false;
        }

        Yii::$enableIncludePath = false;
        Yii::import('extensions.PHPExcelSuite.*', true);

        $objPhpExcel = null;
        $objPhpExcel = new PHPExcel();
        $fileName    = '上海银行版_' . date('YmdHis', time()) . '.xlsx';

        $objPhpExcel->getProperties()->setCreator("TA")
            ->setLastModifiedBy("TA")
            ->setTitle('yunyi')
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

        $objPhpExcel->getActiveSheet()->setCellValue('A1', '投资者名称');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '投资者类型');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '证件类型');
        $objPhpExcel->getActiveSheet()->setCellValue('D1', '证件号');
        $objPhpExcel->getActiveSheet()->setCellValue('E1', '投资者账号');
        $objPhpExcel->getActiveSheet()->setCellValue('F1', '开户行名称');
        $objPhpExcel->getActiveSheet()->setCellValue('G1', '类别');
        $objPhpExcel->getActiveSheet()->setCellValue('H1', '持有份额');
        $objPhpExcel->getActiveSheet()->setCellValue('I1', '份额确认日');
        $objPhpExcel->getActiveSheet()->setCellValue('J1', '起息日');
        $objPhpExcel->getActiveSheet()->setCellValue('K1', '截止日');
        $objPhpExcel->getActiveSheet()->setCellValue('L1', '天数');
        $objPhpExcel->getActiveSheet()->setCellValue('M1', '利率%');
        $objPhpExcel->getActiveSheet()->setCellValue('N1', '利息(万元)');
        $objPhpExcel->getActiveSheet()->setCellValue('O1', '本息合计(万元)');
        $i = 1;
        foreach($shanghaiBank as $k => $v )
        {
            $i++;
            $objPhpExcel->getActiveSheet()->setCellValue('A' . $i, isset($v['name']) ? $v['name'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('B' . $i, isset($v['type']) ? $v['type'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('C' . $i, isset($v['id_type']) ? $v['id_type'] : '');
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('D' . $i, isset($v['id_content']) ? $v['id_content'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('E' . $i, isset($v['bank_account']) ? $v['bank_account'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValue('F' . $i, isset($v['bank_address']) ? $v['bank_address'] : '');
//            $objPhpExcel->getActiveSheet()->setCellValue('G' . $i, $v['']);
            $objPhpExcel->getActiveSheet()->setCellValue('H' . $i, isset($v['amount']) ? $v['amount'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('I' . $i, isset($v['conformation_date']) ? $v['conformation_date'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('J' . $i, isset($v['value_date']) ? $v['value_date'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('K' . $i, isset($v['expected_date']) ? $v['expected_date'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('I' . $i, ($v['expected_date'] - $v['value_date']) / 86400);
            $objPhpExcel->getActiveSheet()->setCellValue('M' . $i, isset($v['income_rate_E6']) ? $v['income_rate_E6'] : '');
            $objPhpExcel->getActiveSheet()->setCellValue('N' . $i, $v['total'] - $v['amount']);
            $objPhpExcel->getActiveSheet()->setCellValue('O' . $i, isset($v['total']) ? $v['total'] : '');
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