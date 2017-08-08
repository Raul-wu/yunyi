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

        if(isset($arrCondition['ppid']) && !empty($arrCondition['ppid']))
        {
            $criteria->compare('pproduct.ppid', $arrCondition['ppid'], true);
            $strUrl .= "&ppid={$arrCondition['ppid']}";
        }

        $criteria->addCondition('t.status != ' . LAProductModel::STATUS_DELETE);
        $criteria->order = $order ? $order : 'pid desc ';

        $count = LAProductModel::model()->with('pproduct')->count($criteria);

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
        $data['status'] = LAProductModel::STATUS_ESTABLISH;
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
        $data['status'] = $objProduct->status;

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

    public static function updateProductStatus($product, $status)
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

    public static function updateProductStatusByPPid($ppid, $status)
    {
        return LAProductModel::model()->updateAll(array('status'=>$status, 'update_time'=>date('Y-m-d H:i:s')), "ppid = {$ppid} and status != " . LAProductModel::STATUS_DELETE);
    }

    public static function getById($pid)
    {
        return LAProductModel::model()->findByPk($pid);
    }

    public static function getPidByPPid($ppid)
    {
        $criteria = new CDbCriteria();

        $criteria->select = 'pid';
        $criteria->compare('ppid', $ppid, false);
        $criteria->addCondition('status !=' . LAProductModel::STATUS_DELETE);

        $objProducts = LAProductModel::model()->findAll($criteria);

        $products = array_map(function($list){
            return $list->attributes;
        }, $objProducts);

        $pids = '';
        foreach($products as $product)
        {
            $pids .= empty($pids) ? $product['pid'] : ',' . $product['pid'];
        }
        return $pids;
    }

    public static function getProductForQuotientList($pids, $order = 'pid desc')
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('t.pid ', $pids);
        $criteria->addCondition('t.status != ' . LAProductModel::STATUS_DELETE);
        $criteria->order = $order ;

        $product = LAProductModel::model()->with('pproduct')->findAll($criteria);

        return array('productAll' => $product,);
    }
}