<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-18
 * Time: 下午2:28
 */
class LAMenuService
{
    const LOG_PREFIX = 'admin.services.LAMenuService.';
    public $menuArray = array();
    //菜单ID常量命名规则=M_controller名称_Action名称
    const M_TOP = 0;  //顶级菜单ID

    //菜单相关权限常量命名规则
    const P_ALL_VIEW = 9999; //查看权限


    public static function insert($params)
    {
        if (empty($params["_id"]))
        {
            return false;
        }

        $params["_id"] = intval($params["_id"]);
        $params["parentId"] = intval($params["parentId"]);
        $params["sort"] = intval($params["sort"]);

        $params["topId"] = 0;
        if (!empty($params["parentId"]))
        {
            $menu = self::getMenuRow($params["parentId"]);
            $params["topId"] = !empty($menu) && !empty($menu["topId"]) ? intval($menu["topId"]) : intval($menu["_id"]);
        }

        $menuModel = new LAMenuModel();
        $menuModel->setAttributes($params,false);
        if ($menuModel->save())
        {
            Yii::log(sprintf("succeeded to insert _id[%s] into LAMenuModel", $menuModel->_id),
                CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return $menuModel->_id;
        }
        else
        {
            Yii::log(sprintf("failed to insert _id[%s] into LAMenuModel", $menuModel->_id),
                CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
    }

    public static function insertMenuPermission($params)
    {
        if (empty($params["perId"]) || empty($params["menuId"]) || empty($params["route"]))
        {
            return false;
        }

        $params["perId"] = $params["perId"];
        $params["menuId"] = intval($params["menuId"]);
        $params["route"] = strtolower($params["route"]);

        $menuModel = new LAMenuPermissionModel();
        $menuModel->setAttributes($params,false);
        if ($menuModel->save())
        {
            Yii::log(sprintf("succeeded to insert _id[%s] into LAMenuModel", $menuModel->_id),
                CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return $menuModel->_id;
        }
        else
        {
            Yii::log(sprintf("failed to insert _id[%s] into LAMenuModel", $menuModel->_id),
                CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
    }

    /**
     * 更新数据
     * @param array $params
     * @return bool
     */
    public static function update($params = array())
    {
        if (empty($params["_id"]))
        {
            return false;
        }

        if ($row = self::getMenuRow($params["_id"]))
        {
            if (isset($params["name"]))
            {
                $row->name = !empty($params["name"]) ? $params["name"] : "";
            }

            if (isset($params["route"]))
            {
                $row->route = !empty($params["route"]) ? $params["route"] : "#";
            }

            if (isset($params["parentId"]))
            {
                $row->parentId = !empty($params["parentId"]) ? intval($params["parentId"]) : 0;
                $row->topId = 0;
                if (!empty($params["parentId"]))
                {
                    $menu = self::getMenuRow($params["parentId"]);
                    $row->topId = !empty($menu) && !empty($menu["topId"]) ? intval($menu["topId"]) : intval($menu["_id"]);
                }
            }

            if (isset($params["className"]))
            {
                $row->className = !empty($params["className"]) ? $params["className"] : "";
            }

            if (isset($params["sort"]))
            {
                $row->sort = !empty($params["sort"]) ? intval($params["sort"]) : 0;
            }

            if ($row->save())
            {
                Yii::log(sprintf("succeeded to update _id[%s] in LAMenuModel ", $row->_id),
                    CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
                return $row->_id;
            }
            else
            {
                Yii::log(sprintf("failed to update _id[%s] in LAMenuModel ", $row->_id),
                    CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                return false;
            }
        }

    }

    public static function delete($menuId)
    {
        return LAMenuModel::model()->deleteByPk(intval($menuId));
    }

    public static function deleteMenuPermission($_id)
    {
        return LAMenuPermissionModel::model()->deleteByPk(new MongoId($_id));
    }

    public static function deleteMenuIdPermission($menuId)
    {
        $criteria = new EMongoCriteria();
        $criteria->addCond('menuId', '==', intval($menuId));
        return LAMenuPermissionModel::model()->deleteAll($criteria);
    }

    /**
     * 获取菜单
     * @param $menuId
     * @return bool
     */
    public static function getRow($menuId)
    {
        return self::getMenuRow($menuId);
    }

    public static function getMenuPermissionAll($params = array(), $page=1, $num=3000, $order = array("perId"=>EMongoCriteria::SORT_ASC))
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

        //数据
        $arr = LAMenuPermissionModel::model()->findAll($criteria);
        return array_map(function($list){
            return $list->attributes;
        }, $arr);
    }

    public static function getPerId($params = array(), $page=1, $num=3000, $order = array("perId"=>EMongoCriteria::SORT_DESC))
    {
        $criteriaArr = array(
            "limit" => $num,
            "offset" => ($page - 1) * $num,
            "sort" => $order
        );
        $criteria = new EMongoCriteria($criteriaArr);
        $criteria->addCond('perId', '!=', 9999);
        foreach ($params as $field => $param)
        {
            $criteria->addCond($field, '==', $param);
        }

        //数据
        $arr = LAMenuPermissionModel::model()->findAll($criteria);
        $menuPerArr = array_map(function($list){
            return $list->attributes;
        }, $arr);
        return !empty($menuPerArr) ? $menuPerArr[0] : "";
    }

    public static function getAll($params = array(), $page=1, $num=3000, $order = array("sort"=>EMongoCriteria::SORT_ASC))
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

        //数据
        return LAMenuModel::model()->findAll($criteria);

    }

    public function getMenuList( $parentId = 0 )
    {
        $this->getTree($parentId);
        $menuArr = $this->menuArray;
        foreach ($menuArr as $key => $val)
        {
            $fixStr = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $val["leftFix"]);
            $fixStr .= !empty($val["leftFix"]) ? "┠&nbsp;" : "";
            $menuArr[$key]["fixStr"] = $fixStr;
        }
        return $menuArr;
    }

    public static function getMenuRow($menuId)
    {
        return LAMenuModel::model()->findByAttributes(array("_id"=>intval($menuId)));
    }

    public function getTree($parentId = 0, $left = 0)
    {
        ++$left;
        $criteriaArr = array(
            "sort" => array("sort"=>EMongoCriteria::SORT_ASC)
        );
        $criteria = new EMongoCriteria($criteriaArr);
        $criteria->addCond('parentId', '==', intval($parentId));
        //数据
        $menuArr = LAMenuModel::model()->findAll($criteria);
        $menuAll = array_map(function($list){
            return $list->attributes;
        }, $menuArr);

        foreach ($menuAll as $val )
        {
            $val["leftFix"] = $left;
            $this->menuArray[$val["_id"]] = $val;
            $this->getTree($val["_id"], $left);
        }
    }

    /**
     * 获取父菜单
     * @param $menuId
     * @return bool
     */
    public static function getParent($menuId)
    {
        $mArr = self::getRow($menuId);
        if ( $mArr["parentId"] != 0 )
        {
            return self::getRow($mArr["parentId"]);
        }

        return $mArr;
    }

    /**
     * 获取顶级菜单列表
     * @return array
     */
    public static function getParentList()
    {
        $parentList = array();
        $menuObj = new LAMenuService();
        $menuArr = $menuObj->getMenuList();
        //var_dump($menuArr);die;
        foreach ($menuArr as $key=>$val)
        {
            //过滤菜单菜单
            if (!LAPermissionService::checkRoleAuthority($val["_id"]))
            {
                continue;
            }
            //顶级菜单
            if ($val["parentId"] == 0)
            {
                $parentList[$val["_id"]] = $val;
                $menuSon = self::getChildrenMenu($val["_id"]);
               //var_dump($menuSon);die;
                if (!empty($menuSon))
                {
                    $sonRoute = 0;
                    foreach ($menuSon as $valSon)
                    {
                        if (!empty($valSon["route"]) && $valSon["route"] != "#" && $sonRoute == 0)
                        {
                            $parentList[$val["_id"]]["route"] = $valSon["route"];
                            $sonRoute = 1;
                            continue;
                        }
                    }
                }
            }
        }
        return $parentList;
    }

    public static function getChildrenMenu($parentMenuId)
    {
        $sonMenu = array();
        $menuObj = new LAMenuService();
        $menuArr = $menuObj->getMenuList($parentMenuId);
        foreach ($menuArr as $key => $val)
        {
            //过滤菜单菜单
            if (!LAPermissionService::checkRoleAuthority($val["_id"]))
            {
                continue;
            }
            //子菜单
            if ($val["topId"] == $parentMenuId)
            {
                $sonMenu[$key] = $val;
                $sonMenu[$key]["isChildren"] = 0;
                if (!empty($sonMenu[$val["parentId"]]))
                {
                    $sonMenu[$val["parentId"]]["isChildren"] = 1;
                    if ($sonMenu[$val["parentId"]]["route"] == "#")
                    {
                        $sonMenu[$val["parentId"]]["route"] = $val["route"];
                    }
                }
            }
        }
        //\\var_dump('<pre>', $sonMenu);die;
        return $sonMenu;
    }

}