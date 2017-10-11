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
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('O' . $i, $v['total'] + $v['amount'], PHPExcel_Cell_DataType::TYPE_STRING);
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