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

        $criteria->order = $order ? $order : 'qid desc ';
        $count = LAQuotientModel::model()->with('product')->with('pproduct')->count($criteria);

        $criteria->limit  = $perPage;
        $criteria->offset = ($perPage * ($page - 1));
        $pageBar = LAdminPager::getPages($count, $page, $perPage, $strUrl);

        $quotient = LAQuotientModel::model()->with('product')->with('pproduct')->findAll($criteria);

        return array('quotientAll' => $quotient,'pageBar' => $pageBar,'count' => $count);
    }

    public static function getByID($id)
    {
        return LAQuotientModel::model()->findByPk($id);
    }

    public static function Create($data)
    {
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

    public static function analysisServiceExcel($pid, $filePath)
    {
        $data = self::commonExcel($filePath);
        unset($data['head']);

        $sql = "insert into quotient(pid,name,amount,type,id_type,id_content,handler_name,delegate_name,bank_account,bank_name,bank_address,bank_province,bank_city,create_time,update_time) values ";

        $time =  date('Y-m-d H:i:s', time());

        $sql_insert ="";
        foreach($data as $key => $arr)
        {
            foreach($arr as $value)
            {
                $name = isset($value[1]) && !empty($value[1]) ? $value[1] : '';
                $amount = isset($value[2]) && !empty($value[2]) ? $value[2] : '';
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

        if($sql_insert)
        {
            $sql_insert = substr($sql_insert, 0, -1) . ';';
            $sql = $sql . $sql_insert;
        }

        try
        {
            if (Yii::app()->yuyinDB->createCommand($sql)->execute())
            {
                Yii::log("import client quotient success ", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);

                return array("msg" => "导入客户份额成功", 'res' => true);
            }
            else
            {
                Yii::log("import client quotient failed ", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                return array('msg' => '导入客户份额失败', 'res' => false);
            }
        }
        catch (Exception $e)
        {
            Yii::log("import client quotient failed message {$e->getMessage()};", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
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
}