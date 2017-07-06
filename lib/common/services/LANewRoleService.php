<?php

/**
 */
class LANewRoleService
{
    const LOG_PREFIX = 'common.services.LANewRoleService.';

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
        $criteria = new EMongoCriteria();
        if(!empty($param['roleName']))
        {
            $criteria->addCond("roleName", "==", new MongoRegex("/.*" . $param['roleName'] . ".*/i"));
        }

        $asc = ($asc == 'asc') ? 1 : -1;
        $count = LANewRoleModel::model()->count($criteria);

        $criteria->sort($orderBy, $asc);
        if ($page > 0)
        {
            $criteria->limit($pageSize);
            $criteria->offset(($page - 1) * $pageSize);
        }

        $pushRes = LANewRoleModel::model()->findAll($criteria);
        $res = array_map(function ($v) {

            $tmp =$v->attributes;
            $tmp['createTime'] = date('Y-m-d H:i:s', $v->createTime);
            $tmp['stateName'] = !empty($v->state) ? '有效' : '无效';
            return $tmp;
        }, $pushRes);

        return array(
            'checkInfo' => $res,
            'count'     => $count
        );
    }



    /**
     * 添加退订
     * @param $mobile
     * @param int $smsType
     * @param int $from
     * @return bool
     */
    public static function addNewRecord($data)
    {

        if(!empty($data['id']))
        {
            $roleRecord = LANewRoleModel::model()->findByPk(intval($data['id']));
            unset($data['id']);
            $data['updateTime'] = time();
        }
        else
        {
            $roleRecord = new LANewRoleModel();

            $criteria = new EMongoCriteria();
            $criteria->sort("_id", EMongoCriteria::SORT_DESC);
            $criteria->limit(1);
            $row = LANewRoleModel::model()->findAll($criteria);
            $data["_id"] = !empty($row[0]->_id) ? $row[0]->_id + 1 : 1;
        }
        $data['creatorId'] = LAManagerService::getUserId();

        $roleRecord->setAttributes($data, false);

        try{
            $roleRecord->save();
            Yii::log(sprintf("success to add  newRole record,roleName[%s]", $data['roleName']),
                CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
        }
        catch(EMongoException $e)
        {
            Yii::log(sprintf("add newRole record err,roleName[%s]" , $data['roleName'], $e->getMessage()), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            //return false;
        }
        return true;
    }



    public static function changeState($ids)
    {
        $criteria = new CDbCriteria();
        $criteria->compare("id", $ids);
        $updateOption = array(
            "state" => LANewRoleModel::STATE_DEL
        );

        try
        {
            LANewRoleModel::model()->updateAll($updateOption, $criteria);
            Yii::log(sprintf("success to del news, id[%s]", implode(',', $ids)),
                CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
        }
        catch (Exception $e)
        {

            Yii::log(sprintf("fail to del news, id[%s]", implode(',', $ids)),
                CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }


        return true;
    }





    protected function log($tpl, $params, $category, $level = CLogger::LEVEL_TRACE)
    {
        Yii::log(call_user_func_array("sprintf", array_merge(array($tpl), $params)), $level, self::LOG_PREFIX . $category);
    }

    /**
     * @param $id
     * @return CActiveRecord
     */
    public static function detail($id)
    {
        $newsRes = LANewRoleModel::model()->findByPk(intval($id));

        if (empty($newsRes))
        {
            return false;
        }

        return $newsRes;
    }

    /**
     * @param $id
     * @return CActiveRecord
     */
    public static function getmenu($nowRight = array())
    {

        $menuObj = new LAMenuService();
        $menuArr = $menuObj->getMenuList();
        foreach($menuArr as $menu)
        {
            //$route = $menu['route'] == '#' ?  $menu['_id'] : $menu['route'];
            $result[] = array(
                'id'  => $menu['_id'],
                'pId' => $menu['parentId'],
                'name'  => $menu['name'],
                'permissionId'  =>  $menu['_id'],
                'checked' => in_array($menu['_id'], $nowRight) ? true: '',
            );

        }

        $menuPermissionArr = LAMenuService::getMenuPermissionAll();
        foreach ($menuPermissionArr as $value)
        {
            if($value['perId'] == 9999)
            {
                continue;
            }
            else
            {
                $result[] = array(

                    'id'  => $value['_id'],
                    'pId' => $value['menuId'],
                    'name'  => $value['name'],
                    'permissionId'  => $value['perId'],
                    'checked' => in_array($value['perId'], $nowRight) ? true: '',
                );
            }
        }
        return $result;
    }

    /**
     * @param $id
     * @return CActiveRecord
     */
    public static function getRoleAuthority($id)
    {

        $roleInfo = self::detail($id);

        $menu = self::getmenu($roleInfo->authority);

        return array_filter(array_map(function($value){

            if(!empty($value['checked']))
            {

                $value['open'] = true;

                return $value;
            }
        }, $menu));
    }



    public static function getAllRole()
    {

        $criteria = new EMongoCriteria();

        $criteria->addCond('state', '==', LANewRoleModel::STATE_OPEN);

        $res = LANewRoleModel::model()->findAll($criteria);

        if(empty($res))
        {
            return array();
        }

        foreach ($res as $value)
        {
            $roleInfo[$value->_id] = $value->roleName;
        }

        return $roleInfo;
    }

    /**
     * 获取角色权限
     * @return array|mixed
     */
    public static function getAuthorityByIds()
    {
//        $key = 'roleAuthority' . LAManagerService::getUserId();
//        //$roleAuthority = Yii::app()->cache->get($key);
//        if (!empty($roleAuthority))
//        {
//            return $roleAuthority;
//        }
        //获取用户的角色
        $ids = LAManagerService::getRole();
        if(empty($ids))
        {
            return array();
        }
        $criteria = new EMongoCriteria();
        foreach ($ids as $id)
        {
            $idsConditions[] = intval($id);
        }
        $criteria->addCond("_id", "in", $idsConditions);

        $res = LANewRoleModel::model()->findAll($criteria);
        if(empty($res))
        {
            return array();
        }

        $roleInfo = array();
        foreach ($res as $value)
        {
            $roleInfo = array_merge($roleInfo, $value->authority);
        }

        $perRow = LAPermissionService::getPersonPermissionAll(array("uid" => intval(LAManagerService::getUserId())));
        if(!empty($perRow))
        {
            foreach($perRow as $val)
            {
                $roleInfo[] = $val["menuId"];
                $roleInfo[] = $val["permission"];
            }
        }
        $roleInfo = array_unique($roleInfo);
        //yii::app()->cache->set($key, $roleInfo);
        return $roleInfo;
    }

    /**
     * 通过角色ID获取权限
     * @param $roleIds
     * @return array
     */
    public static function getRoleIdByAuthority($roleIds)
    {
        $criteria = new EMongoCriteria();
        foreach ($roleIds as $id)
        {
            $idsConditions[] = intval($id);
        }
        $criteria->addCond("_id", "in", $idsConditions);

        $res = LANewRoleModel::model()->findAll($criteria);
        if(empty($res))
        {
            return array();
        }

        $roleInfo = array();
        foreach ($res as $value)
        {
            $roleInfo = array_merge($roleInfo, $value->authority);
        }
        return $roleInfo;
    }



    public static function getUserRole($userRole)
    {
        if (empty($userRole))
        {
            return false;
        }
        $criteria = new EMongoCriteria();

        foreach ($userRole as $id)
        {
            $idsConditions[] = intval($id);
        }

        $criteria->addCond("_id", "in", $idsConditions);

        $criteria->addCond("state", "==", LANewRoleModel::STATE_OPEN);

        $res = LANewRoleModel::model()->findAll($criteria);

        $roleInfo = array();
        foreach ($res as $value)
        {
            $roleInfo[] = $value->_id;
        }

        return $roleInfo;
    }

    /**
     * 删除不存在用户的角色 其中一个角色存在用户都不可删除
     *
     * @param array|string $ids
     * @return bool
     */
    public static function deleteNoUsers(array $ids)
    {
        if(!$ids)
        {
            return false;
        }

        $managerCriteria = new EMongoCriteria;
        $managerCriteria->addCond('role', 'in', $ids);
        $count = LAManagerModel::model()->count($managerCriteria);
        if($count > 0)
        {
            return false;
        }

        $mongoIds = array();
        foreach($ids as $id)
        {
            $mongoIds[] = new MongoId($id);
        }

        $roleCtriteria = new EMongoCriteria();
        $roleCtriteria->_id('in', $mongoIds);
        if(LANewRoleModel::model()->deleteAll($roleCtriteria))
        {
            Yii::log(sprintf("succeed in delete roles ids[%s] in LANewRoleModel ", json_encode($ids)),
                CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return true;
        }
        else
        {
            Yii::log(sprintf("failed to delete roles ids[%s] in LANewRoleModel ", json_encode($ids)),
                CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
    }

}