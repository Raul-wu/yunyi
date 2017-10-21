<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/14/17
 * Time: 22:12
 */
class LATaService
{
    const LOG_PREFIX = 'admin.services.LATaService.';

    public static function getById($tid)
    {
        return LATaModel::model()->findByPk($tid);
    }

    public static function getTaListByPPid($ppid)
    {
        $criteria = new CDbCriteria();

        $criteria->compare('ppid', $ppid, false);

        return LATaModel::model()->findAll($criteria);
    }

    public static function getTaByPPid($ppid)
    {
        $criteria = new CDbCriteria();

        $criteria->compare('ppid', $ppid, false);
        $criteria->order = 'create_time desc';

        return LATaModel::model()->find($criteria);
    }

    public static function create($ppid, $data)
    {
        if(empty($ppid))
        {
            Yii::log("ppid empty",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            return false;
        }

        $data['create_time'] = $data['update_time'] = date('Y-m-d H:i:s', time());
        $data['ppid'] = $ppid;

        $objTa = new LATaModel();
        $objTa->setAttributes($data, false);
        if ($objTa->save())
        {
            $arr = LATaService::generateTaResult($objTa->tid);
            LATaRecordsCMBService::create($arr['cmb']);
            LATaRecordsSHBankService::create($arr['shBank']);

            Yii::log(sprintf("Create ta success, ID[%s]", $objTa->tid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objTa;
        }
        else
        {
            Yii::log(sprintf("Create ta Fail, Params:[%s]",serialize($data)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function update($tid, $data)
    {
        if(empty($tid) )
        {
            Yii::log("ppid data empty",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            return false;
        }

        $objTa = self::getByID($tid);

        if(!$objTa)
        {
            Yii::log(sprintf("Update ta Fail, Get Empty ta info, id:[%s]", $tid),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $data['update_time'] = date('Y-m-d H:i:s', time());

        $objTa->setAttributes($data, false);
        if ($objTa->save())
        {
            Yii::log(sprintf("Update ta success, id[%s]", $objTa->tid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objTa;
        }
        else
        {
            Yii::log(sprintf("Update ta Fail, ID:[%s] Params:[%s]", array($tid, serialize($data))), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function getTaCountByPPid($ppid)
    {
        $criteria = new CDbCriteria();

        $criteria->compare('ppid', $ppid, false);

        return LATaModel::model()->count($criteria);
    }

    public static function deleteTaRecordByPPid($ppid)
    {
        return LATaModel::model()->deleteAll("ppid =:ppid", array(':ppid' => $ppid));
    }

    public static function getTaList($arrCondition = array(), $page = 1, $perPage = 5, $order = '')
    {
        $criteria = new CDbCriteria();
        $strUrl = '?';

        if(isset($arrCondition['ppid']) && !empty($arrCondition['ppid']))
        {
            $criteria->compare('t.ppid', $arrCondition['ppid'], true);
            $strUrl .= "&ppid={$arrCondition['ppid']}";
        }

        $criteria->order = $order ? $order : 't.create_time desc ';
        $count = LATaModel::model()->with('pproduct')->count($criteria);

        $criteria->limit  = $perPage;
        $criteria->offset = ($perPage * ($page - 1));
        $pageBar = LAdminPager::getPages($count, $page, $perPage, $strUrl);

        $objTa = LATaModel::model()->with('pproduct')->findAll($criteria);


        return array('taAll' => $objTa,'pageBar' => $pageBar,'count' => $count);
    }

    public static function deleteTa($tids)
    {
        if(LATaModel::model()->deleteAll("tid in (" . trim($tids, ',') . ")"))
        {
            LATaRecordsCMBModel::model()->deleteAll("tid in (" . trim($tids, ',') . ")");
            LATaRecordsSHBankModel::model()->deleteAll("tid in (" . trim($tids, ',') . ")");

            Yii::log(sprintf("Delete Ta Succ"),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return true;
        }
        else
        {
            Yii::log(sprintf("Delete Ta Fail"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
    }

    public static function generateTaResult($tid)
    {
        $objTa = self::getById($tid);
        $objPProduct = LAPProductService::getById($objTa->ppid);
        $pids = LAProductService::getPidByPPid($objTa->ppid);
        $arrQuotients = LAQuotientService::getAllByPids($pids);
        $objPProductDetail = LAPProductDetailService::getByPPid($objTa->ppid);

        $shBank = $cmb = array();

        foreach($arrQuotients as $arrQuotient)
        {
            $shBank[] = array(
                'ppid' => $objTa->ppid,
                'qid'  => $arrQuotient->qid,
                'pid'  => $arrQuotient->pid,
                'tid'  => $tid,
                'name' => $arrQuotient->name,
                'type' => LAQuotientModel::$arrType[$arrQuotient->type],
                'id_type' => LAQuotientModel::$arrIdType[$arrQuotient->id_type],
                'id_content' => $arrQuotient->id_content,
                'bank_account' => $arrQuotient->bank_account,
                'bank_address' => $arrQuotient->bank_address,
                'amount' => $arrQuotient->amount,
                'conformation_date' => $arrQuotient->create_time,
                'value_date' => date('Y-m-d', $objPProduct->value_date),
                'expected_date' => date('Y-m-d', $objPProduct->expected_date),
                'income_rate_E6' => $objPProduct->income_rate_E6 / LConstService::E4 ,
                'total' => round((($arrQuotient->amount) * ($objPProduct->income_rate_E6 / LConstService::E2) * ((($objPProduct->expected_date - $objPProduct->value_date) / 86400)) / 365), 2)
            );

            $cmb[] = array(
                'ppid' => $objTa->ppid,
                'qid'  => $arrQuotient->qid,
                'pid'  => $arrQuotient->pid,
                'tid'  => $tid,
                'manager' => $objPProductDetail['manager'],
                'fund_code'=>$objPProduct->fund_code,
                'pproduct_name'=>$objPProduct->name,
                'bank_account' => $arrQuotient->bank_account,
                'name' => $arrQuotient->name,
                'type' => LAQuotientModel::$arrType[$arrQuotient->type],
                'id_type' => LAQuotientModel::$arrIdType[$arrQuotient->id_type],
                'id_content' => $arrQuotient->id_content,
                'conformation_date' => $arrQuotient->create_time,
                'conformation_amount' => $arrQuotient->amount,
                'conformation_quotient' => $arrQuotient->amount,
                'has_quotient' => $arrQuotient->amount,
                'value_date' => date('Y-m-d', $objPProduct->value_date),
                'expected_date' => date('Y-m-d', $objPProduct->expected_date),
                'income_rate_E6' => $objTa->fact_income_rate_E6 / LConstService::E4 ,
                'total' => round((($arrQuotient->amount) * ($objTa->fact_income_rate_E6 / LConstService::E2) * ((($objTa->fact_end_date - $objPProduct->value_date) / 86400)) / 365), 2)
            );
        }

        return array('shBank' => $shBank, 'cmb' => $cmb);
    }
}