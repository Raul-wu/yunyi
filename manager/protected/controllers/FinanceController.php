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

    //有限合伙/公司信息统计表
    public function actionCooperateList()
    {
        $this->menuId = 5005;

        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $conditions['name'] = trim(Yii::app()->request->getParam('name', ''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));

        $infoRes = LACooperateService::getAll($conditions, $conditions['page']);

        $this->render('cooperateList', array(
            'name' => $conditions['name'],
            'cooperateAll' => $infoRes['cooperateAll'],
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
        $objPhpExcel->getActiveSheet()->setCellValue('A1', '项目名称');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '项目方名称');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '部门');
        $objPhpExcel->getActiveSheet()->setCellValue('D1', '项目经理');
        $objPhpExcel->getActiveSheet()->setCellValue('E1', '托管人');
        $objPhpExcel->getActiveSheet()->setCellValue('F1', '分期');//TODO 不理解数据哪里来
        $objPhpExcel->getActiveSheet()->setCellValue('G1', '存续/清盘');
        $objPhpExcel->getActiveSheet()->setCellValue('H1', '募集规模/万元');
        $objPhpExcel->getActiveSheet()->setCellValue('I1', '清算规模/万元');//TODO 不理解数据哪里来
        $objPhpExcel->getActiveSheet()->setCellValue('J1', '存续规模/万元');//默认值  -  TODO 不理解数据哪里来
        $objPhpExcel->getActiveSheet()->setCellValue('K1', '存续时间');
        $objPhpExcel->getActiveSheet()->setCellValue('L1', '利息兑付规则');//默认值 自然季度分配、每满三个月的收益分配一次、第一，第三季度末月20号
        $objPhpExcel->getActiveSheet()->setCellValue('M1', '业绩比较基准');//TODO 不理解数据哪里来

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
        $objPhpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);

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

            $product_detail = LAPProductDetailService::getByPPid($product['ppid']);

            $objPhpExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $product_detail['project_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $product_detail['parent_finance_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('C' . $i, $product_detail['department'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('D' . $i, $product_detail['project_manager'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('E' . $i, $product_detail['trustee'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('F' . $i, '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('G' . $i, $product['status'] == LAPProductModel::STATUS_DURATION ? '存续' : $product['status'] == LAPProductModel::STATUS_FINISH ? '清盘' : '-', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('H' . $i, LAPProductService::getHasScaleByPPid($product['ppid']) / LConstService::E4 , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('I' . $i, '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('I' . $i, '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('I' . $i, LAPProductModel::$arrMode[$product['mode']] , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('I' . $i, '', PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $filename = "固定收益付息明细".date("ymdHis");
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
        $objPhpExcel->getActiveSheet()->setCellValue('A1', '序号');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '基金代码');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '是否存续');
        $objPhpExcel->getActiveSheet()->setCellValue('D1', '管理人');
        $objPhpExcel->getActiveSheet()->setCellValue('E1', '项目名称');
        $objPhpExcel->getActiveSheet()->setCellValue('F1', '团队负责人');
        $objPhpExcel->getActiveSheet()->setCellValue('G1', '项目经理');
        $objPhpExcel->getActiveSheet()->setCellValue('H1', '托管人');
        $objPhpExcel->getActiveSheet()->setCellValue('I1', '项目类型');
        $objPhpExcel->getActiveSheet()->setCellValue('J1', '收益类型');
        $objPhpExcel->getActiveSheet()->setCellValue('K1', '认购费');
        $objPhpExcel->getActiveSheet()->setCellValue('L1', '批次');
        $objPhpExcel->getActiveSheet()->setCellValue('M1', '成立时间');
        $objPhpExcel->getActiveSheet()->setCellValue('N1', '募集规模（万元）');
        $objPhpExcel->getActiveSheet()->setCellValue('O1', '存续期限');
        $objPhpExcel->getActiveSheet()->setCellValue('P1', '投资期限');
        $objPhpExcel->getActiveSheet()->setCellValue('Q1', '到期日');
        $objPhpExcel->getActiveSheet()->setCellValue('R1', '到期日2（如延期）');
        $objPhpExcel->getActiveSheet()->setCellValue('S1', '赎回总金额（万元）');
        $objPhpExcel->getActiveSheet()->setCellValue('T1', '投资金额（万元）');
        $objPhpExcel->getActiveSheet()->setCellValue('U1', 'A管理费');
        $objPhpExcel->getActiveSheet()->setCellValue('V1', 'B托管费');
        $objPhpExcel->getActiveSheet()->setCellValue('W1', 'C外包费');
        $objPhpExcel->getActiveSheet()->setCellValue('X1', 'D客户/销售服务费');
        $objPhpExcel->getActiveSheet()->setCellValue('Y1', 'E投资/财务顾问费');
        $objPhpExcel->getActiveSheet()->setCellValue('Z1', '付费规则');
        $objPhpExcel->getActiveSheet()->setCellValue('AA1', '委贷利率');
        $objPhpExcel->getActiveSheet()->setCellValue('AB1', '托管户余额（元）');

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
        $objPhpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20);

        $i = $j = 1;
        foreach ($infoRes['productAll'] as $product) {
            $i++;
            $objPhpExcel->getActiveSheet()->getStyle("A$i:P$i")->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );

            $product_detail = LAPProductDetailService::getByPPid($product['ppid']);

            $objPhpExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $j, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $product['fund_code'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('C' . $i, $product['status'] == LAPProductModel::STATUS_DURATION ? '存续' : $product['status'] == LAPProductModel::STATUS_FINISH ? '清盘' : '-', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('D' . $i, $product_detail['manager'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('E' . $i, $product_detail['project_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('F' . $i, $product_detail['team_leader'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('G' . $i, $product_detail['project_manager'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('H' . $i, $product_detail['trustee'] , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('I' . $i, $product_detail['project_type'] , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('J' . $i, LAPProductModel::$arrType[$product['type']]  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('K' . $i, $product['buy_rate_E6'] / LConstService::E6  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('L' . $i, $product['batch']  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('M' . $i, date('Y-m-d',$product['establish'])  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('N' . $i, $product['scale']  / LConstService::E4  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('O' . $i, $product['duration_data']  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('P' . $i, $product['investment_term']  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('Q' . $i, date('Y-m-d',$product['expected_date'])  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('R' . $i, ''  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('S' . $i, ''  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('T' . $i, LAPProductService::getHasScaleByPPid($product['ppid']) / LConstService::E4  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('U' . $i, $product['management_E6'] / LConstService::E6  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('V' . $i, $product['trusteeship_E6'] / LConstService::E6  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('W' . $i, $product['epiboly_E6'] / LConstService::E6  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('X' . $i, $product['service_fees_E6'] / LConstService::E6  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('Y' . $i, $product['adviser_fees_E6'] / LConstService::E6  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('Z' . $i, $product['pay_rule'] / LConstService::E6  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('AA' . $i, '' / LConstService::E6  , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('AB' . $i, '' / LConstService::E6  , PHPExcel_Cell_DataType::TYPE_STRING);

            $j++;
        }

        $filename = "契约型基金要素".date("ymdHis");
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

    public function actionCooperateListExport()
    {
        $this->menuId = 5007;

        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $conditions['name'] = trim(Yii::app()->request->getParam('name', ''));
        $infoRes = LACooperateService::getAll($conditions, 1, 999999999);

        Yii::$enableIncludePath = false;
        Yii::import('extensions.PHPExcelSuite.*', true);
        $objPhpExcel = new PHPExcel();
        //设值
        $objPhpExcel->getProperties()->setCreator("")
            ->setLastModifiedBy("")
            ->setTitle('有限合伙公司信息统计表')
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
        $objPhpExcel->getActiveSheet()->setTitle('有限合伙公司信息统计表');
        //设置表格头部
        $objPhpExcel->getActiveSheet()->setCellValue('A1', '名称');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '企业性质');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '注册地');
        $objPhpExcel->getActiveSheet()->setCellValue('D1', '委派代表');
        $objPhpExcel->getActiveSheet()->setCellValue('E1', '项目经理');
        $objPhpExcel->getActiveSheet()->setCellValue('F1', '部门');
        $objPhpExcel->getActiveSheet()->setCellValue('G1', '团队负责人');
        $objPhpExcel->getActiveSheet()->setCellValue('H1', '核税情况');
        $objPhpExcel->getActiveSheet()->setCellValue('I1', '代理情况');
        $objPhpExcel->getActiveSheet()->setCellValue('J1', '账户类型');

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
        $objPhpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

        $i = 1;
        foreach ($infoRes['cooperateAll'] as $cooperate) {
            $i++;
            $objPhpExcel->getActiveSheet()->getStyle("A$i:P$i")->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );

            $objPhpExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $cooperate['name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('B' . $i, isset(LACooperateModel::$arrNature[$cooperate['nature']]) ? LACooperateModel::$arrNature[$cooperate['nature']] : '', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('C' . $i, $cooperate['location'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('D' . $i, $cooperate['delegate'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('E' . $i, $cooperate['project_manager'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('F' . $i, $cooperate['department'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('G' . $i, $cooperate['team_leader'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('H' . $i, isset(LACooperateModel::$arrTax[$cooperate['tax']]) ? LACooperateModel::$arrTax[$cooperate['tax']] : '' , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('I' . $i, $cooperate['team_leader'] , PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('J' . $i, isset(LACooperateModel::$arrAccountType[$cooperate['account_type']]) ? LACooperateModel::$arrAccountType[$cooperate['account_type']] : '' , PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $filename = "有限合伙公司信息统计表".date("ymdHis");
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