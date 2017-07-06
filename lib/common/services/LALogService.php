<?php

/**
 * 目前只用到了 addNew，别的备用
 */
class LALogService
{
    const LOG_PREFIX = 'admin.services.LALogService.';
    const LOG_SERVICE = 'admin_log';


    public static function addNewRecord($collectName, $oldData, $newData)
    {

        if(empty($collectName))
        {
            Yii::log(sprintf("insert log fail ,collectName is empty"),
                CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $newLog = new LALogModel();

        $newLog->operationType = 1;
        if (empty($oldData))
        {
            $newLog->operationType = 0;
        }
        foreach ($newData as $key => $value)
        {
            $oldDetail     = !empty($oldData[$key]) ? $oldData[$key] : 0;
            $newDetail     = !empty($value) ? $value : 0;
            $deatail[$key] = array(
                'org' => $oldDetail,
                'new' => $newDetail
            );
        }

       // $operator = LAManagerService::getUserId();
        $newLog->collectName = $collectName;
        $newLog->operator    = !empty($operator) ? $operator : 0;
        $newLog->detail      = $deatail;

        if(!$newLog->insert())
        {
            Yii::log(sprintf("insert log fail ,database is worry"), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
        }

        return true;
    }
    /**
     * @param $page
     * @param $pageSize
     * @param $orderBy
     * @param $asc
     * @param $platForm
     * @return array
     */
    public static function getLists($page, $pageSize, $orderBy, $asc, $param)
    {

        $start = ($page - 1) * $pageSize;

        $conditions = array();

        $conditions['cancelRefuse'] = array('notEq' => LALogModel::IS_CANCEL_REFUSE);

        if(!empty($param))
        {
            foreach ($param as $key => $value)
            {
                if(!empty($value))
                {
                    $conditions[$key] = array('==' => $value);
                }
            }
        }



        $criteriaCount = new EMongoCriteria(array(
            'conditions' => $conditions,
        ));

        $count = LALogModel::model()->count($criteriaCount);

        $criteria = new EMongoCriteria(array(
            'conditions' => $conditions,
            'sort'       => array($orderBy => ($asc == 'desc') ? EMongoCriteria::SORT_DESC : EMongoCriteria::SORT_ASC, 'createTime' => EMongoCriteria::SORT_DESC),
            'limit'      => $pageSize,
            'offset'     => $start
        ));

        $listInfoRes = LALogModel::model()->findAll($criteria);
        $refuseType = LALogModel::model()->getRefuseType();
        $smsType = LALogModel::model()->getSmsType();
        $listInfo = array_map(function($info) use ($refuseType, $smsType){
            $tmp['_id'] = $info['_id'];
            $tmp['mobile'] = $info['mobile'];
            $tmp['refuseType'] = $info['refuseType'];
            $tmp['smsType'] = $info['smsType'];
            $tmp['refuseOperator'] = $info['refuseOperator'];
            $tmp['refuseTime'] = date('Y-m-d H:i:s', $info['refuseTime']);
            $tmp['refuseTypeName'] = $refuseType[$info['refuseType']];
            $tmp['smsTypeName'] = $smsType[$info['smsType']];
            return $tmp;
        },$listInfoRes);


        return array(
            'checkInfo' => $listInfo,
            'count'     => $count
        );
    }


    /**
     * @param $id
     * @return bool
     */
    public static function recordIsExist($mobile, $smsType = 0)
    {
        if(empty($mobile))
        {
            return false;
        }
        $criteria = new EMongoCriteria();
        $criteria->addCond('mobile', '==', $mobile);
        $criteria->addCond('smsType', '==', $smsType);
        $criteria->addCond('cancelRefuse', '!=', LALogModel::IS_CANCEL_REFUSE);
        $newBlackRecord = LALogModel::model()->find($criteria);

        if(!$newBlackRecord)
        {
            return false;
        }

        return true;
    }





    public static function changeState($ids)
    {
        foreach ($ids as $id)
        {
            $modifier = new EMongoModifier();
            $modifier->addModifier('cancelRefuse', 'set', LALogModel::IS_CANCEL_REFUSE);
            $modifier->addModifier('cancelTime', 'set', time());
            $modifier->addModifier('cancelOperator', 'set', LAManagerService::getName() );
            $criteria = new EMongoCriteria();
            $criteria->addCond('_id', '==', new MongoId($id));

            try{
                LALogModel::model()->updateAll($modifier, $criteria);
                Yii::log(sprintf("success to cancel refuse sms, id[%s]", $id),
                    CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            }
            catch(EMongoException $e)
            {
                Yii::log(sprintf("fail to cancel refuse sms, id[%s]", $id),
                    CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
                return false;
            }
        }

        return true;
    }


    public function getRefuseMobile($type)
    {
        $criteria = new EMongoCriteria();
        $criteria->addCond('smsType', 'in', array(0, $type));
        $criteria->addCond('cancelRefuse', '!=', LALogModel::IS_CANCEL_REFUSE);
        $refuseRes = LALogModel::model()->findAll($criteria);

        return array_unique(array_map(function ($res) {
            return $res->$mobile;
        }, $refuseRes));
    }



    public static function decodeHexStr($dataCoding,$hexStr,$encode="UTF-8"){
        // only hex numbers is allowed
        if (strlen($hexStr) % 2 != 0 || preg_match("/[^\da-fA-F]/",$hexStr)) return FALSE;

        $buffer=array();
        if ($dataCoding == 15) {//GBK
            for($i=0;$i<strlen($hexStr);$i+=2){
                if(hexdec(substr($hexStr,$i,2))>=0xa1){//0xa1-0xfe
                    $buffer[]=iconv("GBK",$encode,pack("H4",substr($hexStr,$i,4)));
                    $i+=2;
                }else{
                    $buffer[]=iconv("ISO8859-1",$encode,pack("H2",substr($hexStr,$i,2)));
                }
            }
        } elseif (($dataCoding & 0x0C) == 8) {//UCS-2BE
            for($i=0;$i<strlen($hexStr);$i+=4){
                $buffer[]=iconv("UCS-2BE",$encode,pack("H4",substr($hexStr,$i,4)));
            }
        } else {//ISO8859-1
            for($i=0;$i<strlen($hexStr);$i+=2){
                $buffer[]=iconv("ASCII",$encode,pack("H2",substr($hexStr,$i,2)));
            }
        }
        return join("",$buffer);
    }
    protected function log($tpl, $params, $category, $level = CLogger::LEVEL_TRACE)
    {
        Yii::log(call_user_func_array("sprintf", array_merge(array($tpl), $params)), $level, self::LOG_PREFIX . $category);
    }




}