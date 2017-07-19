<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/18/17
 * Time: 09:09
 */
class LAProductService
{
    const LOG_PREFIX = 'admin.services.LAProductService.';

    public static function getAll($arrCondition = array(), $page = 1, $perPage = 5, $order = '')
    {
        $criteria = new CDbCriteria();
        $strUrl = '?';

        if(isset($arrCondition['fund_code']) && !empty($arrCondition['fund_code']))
        {
            $criteria->compare('fund_code', $arrCondition['fund_code'], true);
            $strUrl .= "&fund_code={$arrCondition['fund_code']}";
        }

//        $criteria->select = 't.pid, t.ppid, t.total_count, t.expected_income_rate_E6,pp.fund_code,name,value_date,expected_date,mode';
        $criteria->order = $order ? $order : 'pid desc ';
//        $criteria->join = 'LEFT JOIN pproduct pp ON t.ppid = pp.ppid ';

        $count = LAProductModel::model()->count($criteria);

        $criteria->limit  = $perPage;
        $criteria->offset = ($perPage * ($page - 1));
        $pageBar = LAdminPager::getPages($count, $page, $perPage, $strUrl);

        $product = LAProductModel::model()->with('pproduct')->findAll($criteria);

        return array('productAll' => $product,'pageBar' => $pageBar,'count' => $count);
    }

    public static function create($ppid, $data)
    {
        if(empty($ppid))
        {
            Yii::log("ppid empty",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            return false;
        }

        $data['create_time'] = $data['update_time'] = date('Y-m-d H:i:s', time());
        $data['status'] = LAProductModel::STATUS_DRAFT;
        $data['ppid'] = $ppid;

        $objProduct = new LAProductModel();
        $objProduct->setAttributes($data, false);
        if ($objProduct->save())
        {
            Yii::log(sprintf("Create product success, ID[%s]", $objProduct->ppid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objProduct;
        }
        else
        {
            Yii::log(sprintf("Create product Fail, Params:[%s]",serialize($data)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function update($pid, $data)
    {
        if(empty($pid) )
        {
            Yii::log("pid data empty",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            return false;
        }

        $objProduct = self::getByID($pid);

        if(!$objProduct)
        {
            Yii::log(sprintf("Update product Fail, Get Empty product info, id:[%s]", $pid),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $data['update_time'] = date('Y-m-d H:i:s', time());

        $objProduct->setAttributes($data, false);
        if ($objProduct->save())
        {
            Yii::log(sprintf("Update product success, id[%s]", $objProduct->pid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objProduct;
        }
        else
        {
            Yii::log(sprintf("Update pproduct Fail, ID:[%s] Params:[%s]", array($pid, serialize($data))), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function updatePProductStatus($product, $status)
    {
        $product->status = $status;
        $product->update_time = date('Y-m-d H:i:s');
        if(!$product->save())
        {
            Yii::log(sprintf("Update product status Fail"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
        return true;
    }

    public static function getById($pid)
    {
        return LAProductModel::model()->findByPk($pid);
    }

    public static function getProductByPPid($ppid)
    {
        $criteria = new CDbCriteria();

        $criteria->select = 'ppid,pid,total_count,expected_income_rate_E6,status';
        $criteria->order = 'pid desc ';
        $criteria->compare('ppid', $ppid, false);

        $objProduct = LAPProductModel::model()->findAll($criteria);

        return array_map(function($list){
            return $list->attributes;
        }, $objProduct);
    }
}