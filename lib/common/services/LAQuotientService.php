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

        if(isset($arrCondition['pid']) && !empty($arrCondition['pid']))
        {
            $criteria->compare('product.pid', $arrCondition['pid'], true);
            $strUrl .= "&pid={$arrCondition['pid']}";
        }

        $criteria->order = $order ? $order : 'qid desc ';
        $count = LAQuotientModel::model()->with('product')->count($criteria);

        $criteria->limit  = $perPage;
        $criteria->offset = ($perPage * ($page - 1));
        $pageBar = LAdminPager::getPages($count, $page, $perPage, $strUrl);

        $objQuotient = LAQuotientModel::model()->with('product')->findAll($criteria);

        $arrQuotient = array_map(function($list){
            return $list->attributes;
        }, $objQuotient);

        return array('quotientAll' => $arrQuotient,'pageBar' => $pageBar,'count' => $count);
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

    public static function analysisServiceExcel($filePath)
    {
        $data = self::commonExcel($filePath);

        echo '<pre>';var_dump($data);echo '</pre>';exit;
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