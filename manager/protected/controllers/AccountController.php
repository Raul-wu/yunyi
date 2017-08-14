<?php
/**
 * Created by PhpStorm.
 * User: Raul
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

        $conditions['fund_code'] = trim(Yii::app()->request->getParam('fund_code',''));
        $conditions['name'] = trim(Yii::app()->request->getParam('name',''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));
        $arrAccount = LAAccountService::getAll($conditions, $conditions['page']);

        $this->render('list',array(
            'accounts'  => $arrAccount['accountAll'],
            'pageBar'   => $arrAccount['pageBar'],
            'count'     => $arrAccount['count'],
            'name'      => $conditions['name'],
            'fund_code'      => $conditions['fund_code'],
            'url'       => array('editUrl'=>Yii::app()->createUrl("account/edit/")),
        ));
    }

    public function actionAdd()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006102);

        $this->setJsMain('accountEdit');

        $ppid = Yii::app()->request->getParam('ppid','');
        $pproduct = LAPProductService::getById($ppid);

        $this->render('edit',array(
            'opType'            => 'add',
            'chk_state'         => 'checked',
            'fund_code' => $pproduct->fund_code,
            'ppid' => $ppid,
        ));
    }

    public function actionEdit()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006103);

        $this->setJsMain('accountEdit');

        $id = trim(Yii::app()->request->getParam('id',''));
        $objAccount = LAAccountService::getByID($id);

        $pproduct = LAPProductService::getById($objAccount->ppid);

        $this->render('edit',array(
            'opType'    => 'edit',
            'id'     => $id,
            'fund_code'  => $objAccount->fund_code,
            'type'  => $objAccount->type,
            'name'   => $objAccount->name,
            'bank_account'   => $objAccount->bank_account,
            'bank_address'  => $objAccount->bank_address,
            'handler'   => $objAccount->handler,
            'status'      => $objAccount->status,
            'ppid'   => $pproduct->ppid,
        ));
    }

    public function actionSave()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        $ppid = Yii::app()->request->getParam('ppid','');
        $id   = Yii::app()->request->getParam('id','');
        $opType = Yii::app()->request->getParam('opType','');

        $pproduct = LAPProductService::getById($ppid);

        $arrData = array(
            'ppid'              => $ppid,
            'fund_code'          => $pproduct->fund_code,
            'type'          => trim(Yii::app()->request->getParam('type','')),
            'name'          => trim(Yii::app()->request->getParam('name','')),
            'bank_account'       => trim(Yii::app()->request->getParam('bank_account','')),
            'bank_address'      => trim(Yii::app()->request->getParam('bank_address','')),
            'handler'       => trim(Yii::app()->request->getParam('handler','')),
            'status'    => Yii::app()->request->getParam('status','')
        );
        if( $opType == LAAccountModel::OP_TYPE_ADD)
        {
            $scenario = AccountEditFormModel::ACCOUNT_NEW;
            $msg = '新增操作';
        }
        else
        {
            $scenario = AccountEditFormModel::ACCOUNT_EDIT;
            $msg = '修改操作';
        }

        $request = new AccountEditFormModel();
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

    public function actionImport()
    {
        $conditions['fund_code'] = trim(Yii::app()->request->getParam('fund_code',''));
        $conditions['name'] = trim(Yii::app()->request->getParam('name',''));
        $arrAccount = LAAccountService::getAll($conditions, 0);

        Yii::$enableIncludePath = false;
        Yii::import('extensions.PHPExcelSuite.*', true);
        $objPhpExcel = new PHPExcel();
        //设值
        $objPhpExcel->getProperties()->setCreator("")
            ->setLastModifiedBy("")
            ->setTitle('资金账户列表')
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
        $objPhpExcel->getActiveSheet()->setTitle('资金账户列表');
        //设置表格头部
        $objPhpExcel->getActiveSheet()->setCellValue('A1', '基金代码');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '账户性质');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '户名');
        $objPhpExcel->getActiveSheet()->setCellValue('D1', '银行账号');
        $objPhpExcel->getActiveSheet()->setCellValue('E1', '开户行');
        $objPhpExcel->getActiveSheet()->setCellValue('F1', '经办人');
        $objPhpExcel->getActiveSheet()->setCellValue('G1', '状态');

        //设置列宽
        $objPhpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPhpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPhpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

        $i = 1;
        foreach ($arrAccount['accountAll'] as $account) {
            $i++;
            $objPhpExcel->getActiveSheet()->getStyle("A$i:P$i")->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );

            $objPhpExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $account['fund_code'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $account['type'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('C' . $i, $account['name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('D' . $i, $account['bank_account'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('E' . $i, $account['bank_address'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('F' . $i, $account['handler'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('G' . $i, $account['status'] == LAAccountModel::STATUS_OPEN ? '正常' : '停用', PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $filename = "资金账户列表".date("ymd");
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



class AccountEditFormModel extends AdminBaseFormModel
{
    const ACCOUNT_NEW           = 'account_new';
    const ACCOUNT_EDIT          = 'account_edit';

    public $id;
    public $ppid;
    public $fund_code;
    public $type;
    public $name;
    public $bank_account;
    public $bank_address;
    public $handler;
    public $status;

    public function rules()
    {
        return array(
            array('ppid, fund_code, type, name, bank_account, bank_address, handler, status, create_time, update_time', 'safe'),

            array('type, name, bank_account, bank_address, handler, status', 'required', 'on' => array(self::ACCOUNT_NEW, self::ACCOUNT_EDIT)),
        );
    }
}