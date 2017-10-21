<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/24/17
 * Time: 20:24
 */
class LAQuotientService
{
    const LOG_PREFIX = 'admin.services.LAQuotientService.';

    public static function getListForQuotientList($arrCondition = array(), $order = 'qid desc ')
    {
        $criteria = new CDbCriteria();

        if(isset($arrCondition['name']) && !empty($arrCondition['name']))
        {
            $criteria->compare('name', $arrCondition['name'], false);
        }

        if(isset($arrCondition['id_content']) && !empty($arrCondition['id_content']))
        {
            $criteria->compare('id_content', $arrCondition['id_content'], false);
        }

        $criteria->order = $order;
        return LAQuotientModel::model()->findAll($criteria);
    }

    public static function getAll($arrCondition = array(), $page = 1, $perPage = 10, $order = '')
    {
        $criteria = new CDbCriteria();
        $strUrl = '?';

        if(isset($arrCondition['name']) && !empty($arrCondition['name']))
        {
            $criteria->compare('product.name', $arrCondition['name'], true);
            $strUrl .= "&name={$arrCondition['name']}";
        }

        if(isset($arrCondition['fund_name']) && !empty($arrCondition['fund_name']))
        {
            $criteria->compare('pproduct.name', $arrCondition['fund_name'], true);
            $strUrl .= "&fund_name={$arrCondition['fund_name']}";
        }

        if(isset($arrCondition['pid']) && !empty($arrCondition['pid']))
        {
            $criteria->addInCondition('product.pid', explode(',', $arrCondition['pid']));
            $strUrl .= "&pid={$arrCondition['pid']}";
        }

        if(isset($arrCondition['qid']) && !empty($arrCondition['qid']))
        {
            $criteria->addInCondition('qid', explode(',', $arrCondition['qid']));
            $strUrl .= "&qid={$arrCondition['qid']}";
        }

        if(isset($arrCondition['quotient_name']) && !empty($arrCondition['quotient_name']))
        {
            $criteria->compare('t.name', $arrCondition['quotient_name'], true);
            $strUrl .= "&quotient_name={$arrCondition['quotient_name']}";
        }

        if(isset($arrCondition['id_card']) && !empty($arrCondition['id_card']))
        {
            $criteria->compare('id_content', $arrCondition['id_card'], true);
            $strUrl .= "&id_card={$arrCondition['id_card']}";
        }

        $criteria->order = $order ? $order : 'qid desc ';
        $count = LAQuotientModel::model()->with('product')->with('pproduct')->count($criteria);

        $pageBar = "";
        if ($page > 0)
        {
            $criteria->limit  = $perPage;
            $criteria->offset = ($perPage * ($page - 1));
            $pageBar = LAdminPager::getPages($count, $page, $perPage, $strUrl);
        }


        $quotient = LAQuotientModel::model()->with('product')->with('pproduct')->findAll($criteria);

        return array('quotientAll' => $quotient,'pageBar' => $pageBar,'count' => $count);
    }

    public static function getByID($id)
    {
        return LAQuotientModel::model()->findByPk($id);
    }

    public static function Create($pid, $data)
    {
        if(empty($pid))
        {
            Yii::log(sprintf("Create Quotient Fail, Create Params Empty(pid)"), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        if(empty($data))
        {
            Yii::log(sprintf("Create Quotient Fail, Create Params Empty"), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        if(!isset($data['create_time']))
        {
            $data['create_time'] = date('Y-m-d H:i:s', time());
        }

        if(!isset($data['update_time']))
        {
            $data['update_time'] = date('Y-m-d H:i:s', time());
        }

        $data['status'] = LAQuotientModel::STATUS_OPEN;

        $objQuotient = new LAQuotientModel();
        $objQuotient->setAttributes($data, false);
        if ($objQuotient->save())
        {
            Yii::log(sprintf("Create Quotient success, ID[%s]", $objQuotient->qid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objQuotient;
        }
        else
        {
            Yii::log(sprintf("Create Quotient Fail, Params:[%s]",serialize($data)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function Update($qid, $data)
    {
        if(empty($qid))
        {
            Yii::log(sprintf("Update Quotient Fail, Update id Empty"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $objQuotient = self::getByID($qid);

        if(!$objQuotient)
        {
            Yii::log(sprintf("Update Quotient Fail, Get Empty accountInfo, id:[%s]", $qid),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        if(!isset($data['update_time']))
        {
            $data['update_time'] = date('Y-m-d H:i:s', time());
        }

        $objQuotient->setAttributes($data, false);
        if ($objQuotient->save())
        {
            Yii::log(sprintf("Update Quotient success, id[%s]", $objQuotient->qid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objQuotient;
        }
        else
        {
            Yii::log(sprintf("Update Quotient Fail, ID:[%s] Params:[%s]", array($qid, serialize($data))), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function updateQuotientStatus($quotient, $status)
    {
        $quotient->status = $status;
        $quotient->update_time = date('Y-m-d H:i:s');
        if(!$quotient->save())
        {
            Yii::log(sprintf("Update Quotient status Fail"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
        return true;
    }

    public static function deleteQuotientStatusByPid($pids)
    {
        $params = array(':pids'=>$pids);
        if(LAQuotientModel::model()->deleteAll("pid in (:pids)", $params))
        {
            Yii::log(sprintf("Delete Quotient Succ"),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return true;
        }
        else
        {
            Yii::log(sprintf("Delete Quotient Fail"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
    }

    public static function deleteQuotientStatusByQid($qid)
    {
        return LAQuotientModel::model()->deleteByPk(intval($qid));
    }

    public static function analysisServiceExcel($pid, $filePath)
    {
        $product = LAProductService::getById($pid);
        $total = LAQuotientService::getTotalAmountByPid($pid);

        $data = self::commonExcel($filePath);
        unset($data['head']);

        $sql = "insert into quotient(pid,name,amount,type,id_type,id_content,handler_name,delegate_name,bank_account,bank_name,bank_address,bank_province,bank_city,create_time,update_time) values ";

        $time =  date('Y-m-d H:i:s', time());

        $sql_insert = "";
        $err_msg_detail = '';
        foreach($data as $key => $arr)
        {
            foreach($arr as $value)
            {
                if($product->total_count < $total + ($value[2] * LConstService::E4))
                {
                    $err_msg_detail .= "序号:{$value[0]} 姓名:{$value[1]}; ";
                    continue;
                }

                if($product->per_user_by_limit)
                {
                    $total_amount = self::getUsersTotalAmountByIDCard($value[5]);
                    if($product->per_user_by_limit < ($total_amount + ($value[2]* LConstService::E4)))
                    {
                        $err_msg_detail .= "序号:{$value[1]} 姓名:{$value[1]}; ";
                        continue;
                    }
                }

                if($product->max_buy && (($value[2]* LConstService::E4) > $product->max_buy))
                {
                    $err_msg_detail .= "序号:{$value[0]} 姓名:{$value[1]}; ";
                    continue;
                }

                if($product->min_buy && (($value[2] * LConstService::E4) < $product->min_buy ))
                {
                    $err_msg_detail .= "序号:{$value[0]} 姓名:{$value[1]}; ";
                    continue;
                }

                $name = isset($value[1]) && !empty($value[1]) ? $value[1] : '';
                $amount = isset($value[2]) && !empty($value[2]) ? $value[2] * LConstService::E4 : '';
                $type = isset(LAQuotientModel::$arrTypeReversal[$value[3]]) ? LAQuotientModel::$arrTypeReversal[$value[3]] : LAQuotientModel::TYPE_SELF;
                $id_type = isset(LAQuotientModel::$arrIdTypeReversal[$value[4]]) ? LAQuotientModel::$arrIdTypeReversal[$value[4]] : LAQuotientModel::ID_TYPE_SELF;
                $id_content = isset($value[5]) && !empty($value[5]) ? $value[5] : '';
                $handler_name = isset($value[6]) && !empty($value[6]) ? $value[6] : '';
                $delegate_name = isset($value[7]) && !empty($value[7]) ? $value[7] : '';
                $bank_account = isset($value[8]) && !empty($value[8]) ? $value[8] : '';
                $bank_name = isset($value[9]) && !empty($value[9]) ? $value[9] : '';
                $bank_address = isset($value[10]) && !empty($value[10]) ? $value[10] : '';
                $bank_province = isset($value[11]) && !empty($value[11]) ? $value[11] : '';
                $bank_city = isset($value[12]) && !empty($value[12]) ? $value[12] : '';

                $sql_insert .= "($pid, '$name',$amount, $type, $id_type, '$id_content', '$handler_name', '$delegate_name', '$bank_account', '$bank_name', '$bank_address', '$bank_province', '$bank_city', '$time', '$time' ),";
            }
        }

        if($err_msg_detail)
        {
            $err_msg_detail = "以下用户'交易金额'未通过子产品购买限额校验：" . $err_msg_detail;
        }

        if($sql_insert)
        {
            $sql_insert = substr($sql_insert, 0, -1) . ';';
            $sql = $sql . $sql_insert;
        }
        else
        {
            return array('msg' => "导入客户份额失败". ", {$err_msg_detail}", 'res' => false);
        }

        try
        {
            if (Yii::app()->yuyinDB->createCommand($sql)->execute())
            {
                Yii::log("import client quotient success ", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);

                return array("msg" => "导入客户份额成功" . ", {$err_msg_detail}", 'res' => true);
            }
            else
            {
                Yii::log("import client quotient failed ", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                return array('msg' => "导入客户份额失败", 'res' => false);
            }
        }
        catch (Exception $e)
        {
            Yii::log("import client quotient failed message {$e->getMessage()};", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return array('msg' => $e->getMessage(), 'res' => false);
        }
    }

    public static function storeUploadFile($data)
    {
        $path = '/tmp/upload/quotient';
        if (!file_exists($path))
        {
            if (!mkdir( $path , 0775 ,  true ))
            {
                Yii::log("Failed to create folders path[$path] ", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                return false;
            }
        }

        $name = explode('.', $data['quotients']['name']);
        $file_name = $name[0] . '_' . time(). $name[1];
        move_uploaded_file($data["quotients"]["tmp_name"], $path . $file_name);
        return  $path . $file_name;
    }

    public static function commonExcel($filePath)
    {
        Yii::import('extensions.PHPExcelSuite.*', true);
        $objPhpExcel = new PHPExcel();
        $phpExcelReader = new PHPExcel_Reader_Excel2007();
        if (!$phpExcelReader->canRead($filePath))
        {
            $phpExcelReader = new PHPExcel_Reader_Excel5();
            if (!$phpExcelReader->canRead($filePath))
            {
                Yii::log(sprintf("NO EXCEL FILE"), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                return false;
            }
        }
        $objPhpExcel = $phpExcelReader->load($filePath);
        $currentSheet = $objPhpExcel->getActiveSheet();
        $allColumn = $currentSheet->getHighestColumn();
        $highestRow = $currentSheet->getHighestRow();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($allColumn); //总列数
        $first_row = array();
        $head = array();
        for ($col = 0; $col < $highestColumnIndex; $col++)
        {
            $head[$col] = $currentSheet->getCellByColumnAndRow($col, 1)->getValue();
            for ($row = 2; $row <= $highestRow; $row++)
            {
                if ($currentSheet->getCellByColumnAndRow($col, $row)->getValue() != null)
                {
                    $first_row[$row][$col] = $currentSheet->getCellByColumnAndRow($col, $row)->getValue();
                }
            }
        }
        return array('data'=>$first_row,'head'=>$head);
    }

    public static function getUsersTotalAmountByIDCard($id_card)
    {
        if(empty($id_card))
        {
            Yii::log(sprintf("id card is empty"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'amount';
        $criteria->compare('id_content', $id_card, false);
        $criteria->compare('status', LAQuotientModel::STATUS_OPEN);
        $quotients = LAQuotientModel::model()->findAll($criteria);

        $total_amount = 0;
        foreach($quotients as $quotient)
        {
            $total_amount += $quotient['amount'];
        }
        return $total_amount;
    }

    public static function getTotalAmountByPid($pid)
    {
        if(empty($pid))
        {
            Yii::log(sprintf("pid is empty"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'amount';
        $criteria->compare('pid', $pid, false);
        $criteria->addInCondition('status', array(LAQuotientModel::STATUS_OPEN, LAQuotientModel::STATUS_DURATION,LAQuotientModel::STATUS_FINISH));
        $quotients = LAQuotientModel::model()->findAll($criteria);

        $total_amount = 0;
        foreach($quotients as $quotient)
        {
            $total_amount += $quotient['amount'];
        }
        return $total_amount;
    }

    public static function getAllByPids($pids)
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('pid', explode(',', $pids));
        $criteria->addNotInCondition('status', array(LAQuotientModel::STATUS_DEL, LAQuotientModel::STATUS_FINISH));
        return LAQuotientModel::model()->findAll($criteria);
    }

    public static function updateQuotientStatusByPPid($ppid, $status)
    {
        $pids = LAProductService::getPidByPPid($ppid);

        return LAQuotientModel::model()->updateAll(array('status'=>$status, 'update_time'=>date('Y-m-d H:i:s')), "pid in ({$pids}) and status != " . LAQuotientModel::STATUS_DEL);


    }
}