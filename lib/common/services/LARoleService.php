<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-19
 * Time: 下午5:38
 */
class LARoleService
{
    const LOG_PREFIX = 'admin.services.LARoleService.';

    /**
     * 查询数据
     * @param $params
     * @param int $page
     * @param int $num
     * @param array $order
     * @return array
     */
    public static function getAll($params = array(), $page=1, $num=30, $order = array())
    {
        $criteriaArr = array(
            "limit" => $num,
            "offset" => ($page - 1) * $num,
            "sort" => $order
        );
        $criteria = new EMongoCriteria($criteriaArr);
        foreach ($params as $field => $param)
        {
            $criteria->addCond($field, '==', $param);
        }
        //总条数
        $count = LARoleModel::model()->count($criteria);

        //数据
        $roleArr = LARoleModel::model()->findAll($criteria);

        return array(
            "roleArr" => $roleArr,
            "count" => $count
        );
    }

    /**
     * 插入数据
     * @param $params
     * @return bool
     */
    public static function insert($params)
    {
        /**
         * 获取最大_id
         * 如果无数据默认为1
         */
        $criteria = new EMongoCriteria();
        $criteria->sort("_id", EMongoCriteria::SORT_DESC);
        $criteria->limit(1);
        $row = LARoleModel::model()->findAll($criteria);
        $params["_id"] = !empty($row[0]->_id) ? $row[0]->_id + 1 : 1;

        $roleModel = new LARoleModel();
        $roleModel->setAttributes($params,false);
        if ($roleModel->save())
        {
            Yii::log(sprintf("succeeded to insert _id[%s] into LARoleModel", $roleModel->_id),
                CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return $roleModel->_id;
        }
        else
        {
            Yii::log(sprintf("failed to insert _id[%s] into LARoleModel", $roleModel->_id),
                CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
    }

    public static function getRow($params)
    {
        //获取数据
        $criteria = new EMongoCriteria();
        foreach($params as $name=>$value)
        {
            $criteria->$name('==', $value);
        }
        return LARoleModel::model()->find($criteria);
    }

    public static function update($params)
    {
        if(!isset($params["_id"]) || !$params["_id"])
        {
            return false;
        }

        if($roleRow=self::getRow(array("_id" => intval($params["_id"]))))
        {
            $params["_id"] = intval($params["_id"]);
            $roleRow->setAttributes($params, false);
            if ($roleRow->save())
            {
                Yii::log(sprintf("succeeded to update moban pid[%s] in LARoleModel ", $roleRow->_id),
                    CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
                return true;
            }
            else
            {
                Yii::log(sprintf("failed to update moban pid[%s] in LARoleModel ", $roleRow->_id),
                    CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                return false;
            }
        }
        return false;
    }

    public static function delete($_id)
    {
        return LARoleModel::model()->deleteByPk(intval($_id));
    }
}