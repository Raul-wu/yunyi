<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/7/17
 * Time: 22:07
 */
class FinanceController extends AdminBaseController
{
    public $menuId = 5001;

    //契约型基金-项目基本要素
    public function actionPProductList()
    {
        $this->menuId = 5002;

        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $conditions['status'] = trim(Yii::app()->request->getParam('status', ''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));

        $infoRes = LAPProductService::getAll($conditions, $conditions['page']);

        $this->render('pproductList', array(
            'status' => $conditions['status'],
            'pproducts' => $infoRes['productAll'],
            'count' => $infoRes['count'],
            'pageBar'   => $infoRes['pageBar'],
        ));
    }

    //客户份额表
    public function actionQuotientList()
    {
        $this->menuId = 5003;

        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $conditions['name'] = trim(Yii::app()->request->getParam('name', ''));
        $conditions['id_content'] = trim(Yii::app()->request->getParam('id_content', ''));

        $quotientAll = LAQuotientService::getListForQuotientList($conditions);

        $pids = array();
        foreach($quotientAll as $quotient)
        {
            $pids[] = array(
                'qid' => $quotient['qid'],
                'pid' => $quotient['pid']
            )
            ;
        }

        $products = array();
        foreach($pids as $key => $value)
        {
            $quotient = LAQuotientService::getByID($value['qid']);
            $product = LAProductService::getById($value['pid']);
            $pproduct = LAPProductService::getById($product->ppid);

            $products[$key] = array(
                'name' => $product->name,
                'expected_date' => date('Y-m-d', $pproduct->expected_date),
                'amount' => $quotient->amount / LConstService::E4,
                'status' => LAProductModel::$arrStatus[$product->status],
                'fund_code' => $pproduct->fund_code,
                'bank_account' =>$quotient->bank_account,
                'bank_name' => $quotient->bank_name,
                'bank_address' => $quotient->bank_address
            );
        }

        $this->render('quotientList', array(
            'quotients'  => $quotientAll,
            'name'     => $conditions['name'],
            'id_content'     => $conditions['id_content'],
            'products' => $products
        ));
    }

    //固定收益类产品付息明细
    public function actionPProductDetailList()
    {
        $this->menuId = 5004;

        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $conditions['status'] = trim(Yii::app()->request->getParam('status', ''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));
        $conditions['type'] = LAPProductModel::TYPE_FI;

        $infoRes = LAPProductService::getAll($conditions, $conditions['page']);

        $this->render('pproductDetailList', array(
            'status' => $conditions['status'],
            'pproducts' => $infoRes['productAll'],
            'count' => $infoRes['count'],
            'pageBar'   => $infoRes['pageBar'],
        ));
    }

    public function actionPProductDetailListExport()
    {
        $this->menuId = 5005;

        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $conditions['status'] = trim(Yii::app()->request->getParam('status', ''));
        $conditions['type'] = LAPProductModel::TYPE_FI;
        $infoRes = LAPProductService::getAll($conditions, 1, 999999999);

        Yii::$enableIncludePath = false;
        Yii::import('extensions.PHPExcelSuite.*', true);
        $objPhpExcel = new PHPExcel();
        //设值
        $objPhpExcel->getProperties()->setCreator("")
            ->setLastModifiedBy("")
            ->setTitle('固定收益付息明细')
            ->setSubject("")
            ->setDescription("")
            ->setKeywords("")
            ->setCategory("");
        //设置表格头部加粗居中
        $objPhpExcel->getActiveSheet()->getStyle('A1:U1')->applyFromArray(
            array(
                'font' => array (
                    'bold' => true
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )
            )
        );
        //设置工作簿的名称
        $objPhpExcel->getActiveSheet()->setTitle('固定收益付息明细');
        //设置表格头部
        $objPhpExcel->getActiveSheet()->setCellValue('A1', '基金ID');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '基金代码');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '基金名称');
        $objPhpExcel->getActiveSheet()->setCellValue('D1', '额度(元)');
        $objPhpExcel->getActiveSheet()->setCellValue('E1', '预期收益');
        $objPhpExcel->getActiveSheet()->setCellValue('F1', '起息日');
        $objPhpExcel->getActiveSheet()->setCellValue('G1', '到期日');
        $objPhpExcel->getActiveSheet()->setCellValue('H1', '分配方式');
        $objPhpExcel->getActiveSheet()->setCellValue('I1', '状态');

        //设置列宽
        $objPhpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);

        $i = 1;
        foreach ($infoRes['productAll'] as $product) {
            $i++;
            $objPhpExcel->getActiveSheet()->getStyle("A$i:P$i")->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );

            $objPhpExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $product['ppid'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $product['fund_code'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('C' . $i, $product['name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('D' . $i, $product['scale'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('E' . $i, $product['income_rate_E6'] / LConstService::E4, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('F' . $i, date('Y-m-d', $product['value_date']), PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('G' . $i, date('Y-m-d', $product['expected_date']), PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('H' . $i, LAPProductModel::$arrMode[$product['mode']] , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('I' . $i, LAPProductModel::$arrStatus[$product['status']], PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $filename = "固定收益付息明细".date("ymd");
        $objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel5');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    public function actionPProductListExport()
    {
        $this->menuId = 5006;

        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $conditions['status'] = trim(Yii::app()->request->getParam('status', ''));
        $infoRes = LAPProductService::getAll($conditions, 1, 999999999);

        Yii::$enableIncludePath = false;
        Yii::import('extensions.PHPExcelSuite.*', true);
        $objPhpExcel = new PHPExcel();
        //设值
        $objPhpExcel->getProperties()->setCreator("")
            ->setLastModifiedBy("")
            ->setTitle('契约型基金要素')
            ->setSubject("")
            ->setDescription("")
            ->setKeywords("")
            ->setCategory("");
        //设置表格头部加粗居中
        $objPhpExcel->getActiveSheet()->getStyle('A1:U1')->applyFromArray(
            array(
                'font' => array (
                    'bold' => true
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )
            )
        );
        //设置工作簿的名称
        $objPhpExcel->getActiveSheet()->setTitle('契约型基金要素');
        //设置表格头部
        $objPhpExcel->getActiveSheet()->setCellValue('A1', '基金代码');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '基金名称');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '收益类型');
        $objPhpExcel->getActiveSheet()->setCellValue('D1', '募集规模（万元）');
        $objPhpExcel->getActiveSheet()->setCellValue('E1', '清算规模（万元）');
        $objPhpExcel->getActiveSheet()->setCellValue('F1', '预计到期');
        $objPhpExcel->getActiveSheet()->setCellValue('G1', '分配方式');
        $objPhpExcel->getActiveSheet()->setCellValue('H1', '状态');

        //设置列宽
        $objPhpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

        $i = 1;
        foreach ($infoRes['productAll'] as $product) {
            $i++;
            $objPhpExcel->getActiveSheet()->getStyle("A$i:P$i")->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );

            $objPhpExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $product['fund_code'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $product['name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('C' . $i, LAPProductModel::$arrType[$product['type']], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('D' . $i, $product['scale'] / LConstService::E4, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('E' . $i, ($product['scale'] - LAPProductService::getProductTotalCountByPPid($product['ppid']))  / LConstService::E4, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('F' . $i, date('Y-m-d',$product['expected_date']), PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('G' . $i, LAPProductModel::$arrMode[$product['mode']], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('H' . $i, LAPProductModel::$arrStatus[$product['status']] , PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $filename = "契约型基金要素".date("ymd");
        $objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel5');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }
}