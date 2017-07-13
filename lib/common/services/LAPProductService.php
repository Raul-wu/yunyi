<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/11/17
 * Time: 20:57
 */
class LAPProductService
{
    const LOG_PREFIX = 'admin.services.LAPProductService.';

    public static function getAll($arrCondition = array(), $page = 1, $perPage = 10, $order = '')
    {
        $criteria = new CDbCriteria();
        $strUrl = '?';

        if(isset($arrCondition['fund_code']) && !empty($arrCondition['fund_code']))
        {
            $criteria->compare('fund_code', $arrCondition['fund_code'], true);
            $strUrl .= "&fund_code={$arrCondition['fund_code']}";
        }

        $criteria->order = $order ? $order : 'ppid desc ';
        $count = LAPProductModel::model()->count($criteria);

        $criteria->limit  = $perPage;
        $criteria->offset = ($perPage * ($page - 1));
        $pageBar = LAdminPager::getPages($count, $page, $perPage, $strUrl);

        $objPProduct = LAPProductModel::model()->findAll($criteria);

        $arrPProduct = array_map(function($list){
            return $list->attributes;
        }, $objPProduct);

        return array('productAll' => $arrPProduct,'pageBar' => $pageBar,'count' => $count);
    }

    public static function create($pproductData, $pproductDetailData)
    {
        if(empty($pproductData))
        {
            Yii::log("pproduct data empty",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            return false;
        }

        $transaction = Yii::app()->yuyinDB->beginTransaction();
        try
        {
            $pproduct = self::create($pproductData);

            if(!isset($pproduct->ppid))
            {
                throw new Exception("create pproduct failed");
            }

            LAPProductDetailService::create($pproduct->ppid, $pproductDetailData);

            $transaction->commit();

            return true;
        }
        catch(Exception $e)
        {
            Yii::log("create pproduct exception {$e->getMessage()}",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            $transaction->rollBack();
            return false;
        }
    }

    public static function update($ppid, $pproductData, $pproductDetailData)
    {
        if(empty($ppid) || empty($pproductData))
        {
            Yii::log("ppid or pproduct data empty",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            return false;
        }

        $transaction = Yii::app()->yuyinDB->beginTransaction();
        try
        {
            if(self::update($ppid, $pproductData))
            {
                if(LAPProductDetailService::update($ppid, $pproductDetailData))
                {
                    $transaction->commit();
                    return true;
                }
            }

            $transaction->rollBack();
            return false;
        }
        catch(Exception $e)
        {
            Yii::log("update pproduct exception {$e->getMessage()}",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            $transaction->rollBack();
            return false;
        }
    }


    public static function CreatePProduct($data)
    {
        if(empty($data))
        {
            Yii::log(sprintf("Create pproduct Fail, Create Params Empty"), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $data['create_time'] = $data['update_time'] = time();

        $objPProduct = new LAPProductModel();
        $objPProduct->setAttributes($data, false);
        if ($objPProduct->save())
        {
            Yii::log(sprintf("Create pproduct success, ID[%s]", $objPProduct->ppid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objPProduct;
        }
        else
        {
            Yii::log(sprintf("Create pproduct Fail, Params:[%s]",serialize($data)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function UpdatePProduct($ppid, $data)
    {
        if(empty($ppid))
        {
            Yii::log(sprintf("Update pproduct Fail, Update id Empty"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $objPProduct = self::getByID($ppid);

        if(!$objPProduct)
        {
            Yii::log(sprintf("Update pproduct Fail, Get Empty accountInfo, id:[%s]", $ppid),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $data['update_time'] = time();

        $objPProduct->setAttributes($data, false);
        if ($objPProduct->save())
        {
            Yii::log(sprintf("Update pproduct success, id[%s]", $objPProduct->ppid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objPProduct;
        }
        else
        {
            Yii::log(sprintf("Update pproduct Fail, ID:[%s] Params:[%s]", array($ppid, serialize($data))), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function getById($ppid)
    {
        return LAPProductModel::model()->findByPk($ppid);
    }

    public static function getPProductAndPProductDetail($ppid)
    {
        $pproduct = self::getById($ppid);
        $pproductDetail = LAPProductDetailService::getByID($ppid);

        $pproductById = array();
        foreach($pproduct as $key => $value)
        {
            $pproductById[$key] = $value;
        }

        foreach($pproductDetail as $key => $value)
        {
            $pproductById[$key] = $value;
        }

        return $pproductById;
    }
}