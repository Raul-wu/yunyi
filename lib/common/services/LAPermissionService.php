<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-19
 * Time: 下午5:44
 */
class LAPermissionService
{
    const LOG_PREFIX = 'admin.services.LAPermissionService.';

    /**
     * 查询数据
     * @param $params
     * @param int $page
     * @param int $num
     * @param array $order
     * @return array
     */
    public static function getAll($params, $page=1, $num=30, $order = array())
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
        $count = LAPermissionModel::model()->count($criteria);

        //数据
        $roleArr = LAPermissionModel::model()->findAll($criteria);

        return array(
            "managerArr" => $roleArr,
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
        $perModel = new LAPermissionModel();
        $perModel->setAttributes($params,false);
        if ($perModel->save())
        {
            Yii::log(sprintf("succeeded to insert _id[%s] into LARoleModel", $perModel->_id),
                CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return $perModel->_id;
        }
        else
        {
            Yii::log(sprintf("failed to insert _id[%s] into LARoleModel", $perModel->_id),
                CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
    }

    /**
     * 批量更新權限
     * @param $roleId
     * @param $params
     * @return bool
     */
    public static function SavePermission($roleId, $params)
    {
        if (empty($roleId))
        {
            return false;
        }

        if (!empty($params))
        {
            $menuArr = LAMenuService::getAll();
            foreach ($menuArr as $val)
            {
                $permissionArr = LAMenuService::getMenuPermissionAll(array("menuId" => intval($val["_id"])));
                foreach ($permissionArr as $valPer)
                {
                    //删除对应角色权限
                    $criteria = new EMongoCriteria();
                    $criteria->addCond("roleId", '==', intval($roleId));
                    $criteria->addCond("menuId", '==', intval($val["_id"]));
                    $criteria->addCond("permission", '==', intval($valPer["perId"]));
                    LAPermissionModel::model()->deleteAll($criteria);

                    //判断当前角色此菜单的权限是否选中
                    if (!empty($params[$val["_id"]][$valPer["perId"]]))
                    {
                        //新增当前角色此菜单当前选中的权限
                        self::insert(array(
                            "roleId" => intval($roleId),
                            "menuId" => intval($val["_id"]),
                            "permission" => intval($valPer["perId"])
                        ));
                    }
                }
            }
        }
        return true;
    }

    /**
     * 獲取單條數據
     * @param $params
     * @return EMongoDocument
     */
    public static function getRow($params)
    {
        //获取数据
        return LAPermissionModel::model()->findByAttributes($params);
    }
//    /**
//     * 验证角色权限
//     * @param $menuId
//     * @param $permission
//     * @return bool|mixed
//     */
//
//    public static function checkRoleAuthority($menuId, $permission)
//    {
//        //获取用户的角色
//        $managerRole = LAManagerService::getRole();
//
//        if($permission == LAMenuService::P_ALL_VIEW)
//        {
//            $permission = $menuId;
//        }
//
//        if(empty($managerRole))
//        {
//            return false;
//        }
//
//        $roleAuthority = LANewRoleService::geteAuthorityByIds($managerRole);
//
//        if(in_array($permission, $roleAuthority))
//        {
//            return true;
//        }
//        return false;
//    }
    /**
     * 檢查權限
     * @param $menuId
     * @param $permission
     * @throws CException
     * @return bool
     */
    //public static function checkMenuPermission($menuId, $permission)
//    public static function checkMenuPermission()
//    {
//
//       /* if (self::selectMenuPermission($menuId, $permission))
//        {
//            return true;
//        }*/
//
//        //更换权限验证，从个人权限验证，更换为角色权限验证
//        if (self::checkRoleAuthority($menuId, $permission))
//        {
//            return true;
//        }
//        else
//        {
//            $logText = sprintf("url[%s] menuId[%s] permission[%s] no permission 403",
//                $_SERVER["REQUEST_URI"],
//                $menuId,
//                $permission
//            );
//            Yii::log($logText, CLogger::LEVEL_INFO, "admin");
//			throw new LException(LError::NO_PERMISSION);
//        }
//    }

    /**
     * 檢查權限
     * @param $menuId
     * @param $permission
     * @throws CException
     * @return bool
     */
    //public static function checkMenuPermission($menuId, $permission)
    public static function checkMenuPermission()
    {
        //更换权限验证，从个人权限验证，更换为角色权限验证
        if (self::checkRoleAuthority())
        {
            return true;
        }
        else
        {
            $logText = sprintf("url[%s] no permission 403",
                $_SERVER["REQUEST_URI"]
            );
            Yii::log($logText, CLogger::LEVEL_INFO, "admin");
			throw new LException(LError::NO_PERMISSION);
        }
    }

    /**
     * 查詢權限
     * @param $menuId
     * @param $permission
     * @return bool
     */
    public static function selectMenuPermission($menuId)
    {
       /* $row = self::getRow(array(
            "roleId" => intval(LAManagerService::getRoleId()),
            "menuId" => intval($menuId),
            "permission" => intval($permission)
        ));*/
        if (self::checkRoleAuthority($menuId))
        {
            return true;
        }
        else
        {
            return false;
        }

      /*   $row = self::getPersonPermission(array(
            "uid" => intval(LAManagerService::getUserId()),
            "menuId" => intval($menuId),
            "permission" => intval($permission)
        ));
        return !empty($row);*/
    }


    /**
     * 獲取个人权限
     * @param $params
     * @return EMongoDocument
     */
    public static function getPersonPermission($params)
    {
        //获取数据
        return LAPersonalPermissionModel::model()->findByAttributes($params);
    }
    public static function getPersonPermissionAll($params)
    {
        $criteria = new EMongoCriteria();
        foreach ($params as $field => $param)
        {
            $criteria->addCond($field, '==', $param);
        }
        return LAPersonalPermissionModel::model()->findAll($criteria);
    }


    /**
     * 批量更新權限
     * @param $roleId
     * @param $params
     * @return bool
     */
    public static function SavePersonalPermission($uid, $params)
    {
        if (empty($uid))
        {
            return false;
        }

        if (!empty($params))
        {
            $menuArr = LAMenuService::getAll();
            foreach ($menuArr as $val)
            {
                $permissionArr = LAMenuService::getMenuPermissionAll(array("menuId" => intval($val["_id"])));
                foreach ($permissionArr as $valPer)
                {
                    //删除对应角色权限
                    $criteria = new EMongoCriteria();
                    $criteria->addCond("uid", '==', intval($uid));
                    $criteria->addCond("menuId", '==', intval($val["_id"]));
                    $criteria->addCond("permission", '==', intval($valPer["perId"]));
                    LAPersonalPermissionModel::model()->deleteAll($criteria);

                    //判断当前角色此菜单的权限是否选中
                    if (!empty($params[$val["_id"]][$valPer["perId"]]))
                    {
                        //新增当前角色此菜单当前选中的权限
                        self::insertPersonalPermission(array(
                            "uid" => intval($uid),
                            "menuId" => intval($val["_id"]),
                            "permission" => intval($valPer["perId"])
                        ));
                    }
                }
            }
        }
        return true;
    }

    /**
     * 插入数据
     * @param $params
     * @return bool
     */
    public static function insertPersonalPermission($params)
    {
        $perModel = new LAPersonalPermissionModel();
        $perModel->setAttributes($params,false);
        if ($perModel->save())
        {
            Yii::log(sprintf("succeeded to insert _id[%s] into LAPersonalPermissionModel", $perModel->_id),
                CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return $perModel->_id;
        }
        else
        {
            Yii::log(sprintf("failed to insert _id[%s] into LAPersonalPermissionModel", $perModel->_id),
                CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
    }

    /**
     * 验证角色权限
     * @param $menuId
     * @param $permission
     * @return bool|mixed
     */

    public static function checkRoleAuthority($id = 0)
    {
        if (empty($id))
        {
            //用户登录和登录后的个人相关操作不需要权限，在lib/admin/config下配置
            $uri = strtolower('/' . Yii::app()->request->pathinfo);
            if (in_array($uri, Yii::app()->params['no_check']) || ((in_array($uri, Yii::app()->params['user_operation']) || $uri == '/') && LAManagerService::isLogin() ))
            {
                return true;
            }
            $menuPermission = LAMenuPermissionModel::model()->findByAttributes(array('route' => $uri));
            if (!empty($menuPermission))
            {
                $id = $menuPermission['perId'];
            }
            else
            { //权限未入库,默认有权限
                return true;
            }
        }
        $roleAuthority = LANewRoleService::getAuthorityByIds();
        if (empty($roleAuthority))
        {
            return false;
        }
        if(in_array($id, $roleAuthority))
        {
            return true;
        }
        return false;
    }



}