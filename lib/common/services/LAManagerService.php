<?php

/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-19
 * Time: 下午4:14
 */
class LAManagerService
{
    const LOG_PREFIX = 'admin.services.LAManagerService.';
    public static $mac = "!@#wsefSAD";

    /**
     * 根据keyword获取管理查询条件
     * 判断输入的是ID、用户名、邮箱地址
     *
     * @param string $keyword
     * @return array
     */
    public static function getParamsByKeyword($keyword)
    {
        if (!$keyword) {
            return array();
        } elseif (preg_match('/^\d+$/', $keyword)) {
            return array('_id' => intval($keyword));
        } elseif (preg_match('/^[\w@.]+$/', $keyword)) {
            return array('email' => $keyword);
        } else {
            return array('name' => $keyword);
        }
    }

    /**
     * 查询数据
     * @param $params
     * @param int $page
     * @param int $num
     * @param array $order
     * @return array
     */
    public static function getAll($params = array(), $page = 1, $num = 30, $order = array("_id" => EMongoCriteria::SORT_DESC))
    {
        $criteriaArr["sort"] = $order;
        if ($page > 0) {
            $criteriaArr["limit"] = $num;
            $criteriaArr["offset"] = ($page - 1) * $num;
        }
        $criteria = new EMongoCriteria($criteriaArr);

        /**
         * name 采用 *name* 模糊查询
         * email 采用 email* 模糊查询
         * 其他字段采用绝对匹配
         */
        foreach ($params as $key => $value) {
            if ($key == 'name') {
                $criteria->addCond('name', '==', new MongoRegex('/.*' . $value . '.*/'));
            } elseif ($key == 'email') {
                $criteria->addCond('email', '==', new MongoRegex('/^' . $value . '.*/'));
            } else if($key == 'state'){
                $criteria->addCond($key, '==', $value);
            }else {
                $criteria->addCond($key, '==', $value);
            }
        }

        //总条数
        $count = LAManagerModel::model()->count($criteria);

        //数据
        $managerArr = array();
        if ($count > 0) {
            $managerArr = LAManagerModel::model()->findAll($criteria);
        }
        return array(
            "managerArr" => $managerArr,
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


        $row = LAManagerModel::model()->findAll($criteria);
        $params["_id"] = !empty($row[0]->_id) ? $row[0]->_id + 1 : 1;

        $managerModel = new LAManagerModel();
        $managerModel->setAttributes($params, false);

        if ($managerModel->save()) {
            Yii::log(sprintf("succeeded to insert _id[%s] into LAManagerModel", $managerModel->_id),
                CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return $managerModel->_id;
        } else {
            Yii::log(sprintf("failed to insert _id[%s] into LAManagerModel", $managerModel->_id),
                CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
    }

    public static function getRow($params)
    {
        //获取数据
        return LAManagerModel::model()->findByAttributes($params);
    }
    public static function getAllManager()
    {
        return LAManagerModel::model()->findAllByAttributes(["state" => 1]);
    }
    public static function update($params)
    {
        if (!isset($params["_id"]) || !$params["_id"]) {
            return false;
        }

        if ($managerRow = self::getRow(array("_id" => intval($params["_id"])))) {

            $managerRow->setAttributes($params, false);

            if ($managerRow->save() !== false) {

                Yii::log(sprintf("succeeded to update _id[%s] in LAManagerModel ", $managerRow->_id),
                    CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
                return true;
            } else {
                Yii::log(sprintf("failed to update _id[%s] in LAManagerModel ", $managerRow->_id),
                    CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                return false;
            }
        }
    }

    public static function delete($_id)
    {
        return LAManagerModel::model()->deleteByPk(intval($_id));
    }

    /**
     * 随机数
     * @param $length
     * @param bool $numeric
     * @return string
     */
    public static function random($length, $numeric = false)
    {
        if ($numeric) {
            return sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
        } else {
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $max = 61;

            /** 生成以字母开头的随机字符串 **/
            $str = $chars[mt_rand(9, $max)];
            for ($i = 1; $i < $length; $i++) {
                $str .= $chars[mt_rand(0, $max)];
            }
            return $str;
        }
    }

    /**
     * 获取客户端 IP
     * @return bool
     */
    public static function getIp()
    {
        $ip = false;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', str_replace(' ', '', $_SERVER['HTTP_X_FORWARDED_FOR']));
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }

            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi('^(10|172\.16|192\.168)\.', $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    public static function login($email, $password, $freeLogin = false)
    {

        if (empty($email) || empty($password)) {
            return false;
        }
        $managerRow = self::getRow(array(
            "email" => strtolower($email)
        ));
        if (empty($managerRow)) {
            return false;
        }

        if ($managerRow["state"] == 0) {
            return false;
        }


        if ((md5($password) == $managerRow["password"]) && !empty($freeLogin)) {
            // 更新用户数据
            $time = time();
            $managerRow->lastLogin = $time;
            $managerRow->lastActive = $time;
            $managerRow->lastIp = self::getIp();
            $managerRow->save();

            //生成6位随机数
            $randomKey = self::randomKey(6);

            // Cookies数据更新
            $token = md5(self::$mac . $managerRow["_id"] . $managerRow["password"] . $randomKey . json_encode($managerRow["role"]));
            return array(
                "_id" => $managerRow["_id"],
                "token" => $token,
                'randomKey' => $randomKey,
                'role' => LANewRoleService::getUserRole($managerRow["role"]),
                'lastLogin' => $time,
            );
        }
        return false;
    }

    /**
     * 判断是否登录
     * @return bool
     */
    public static function isLogin()
    {
//        return true;//TODO 特殊帐号登录

        $_id = self::getUserId();
        $token = self::getToken();
        $randomKey = self::getRandomKey();
        if (empty($_id) || empty($token) || empty($randomKey)) {
            return false;
        }

        $managerRow = self::getRow(array(
            "_id" => intval($_id)
        ));

        if (empty($managerRow)) {
            return false;
        }

        if ($managerRow["state"] == 0) {
            return false;
        }

        $mac = md5(self::$mac . $managerRow["_id"] . $managerRow["password"] . $randomKey . json_encode($managerRow["role"]));
        $tokenRedis = Yii::app()->cache->get("token_" . $managerRow["_id"] . "_" . $randomKey);
        if (empty($tokenRedis) || $token != $tokenRedis) {
            return false;
        }
        if ($mac == $token) {
            // 更新用户数据
            $time = time();
            $managerRow->lastActive = $time;
            $managerRow->save();
            return true;
        }
        return false;
    }

    /**
     * 获取用户ID
     * @return null|string
     */
    public static function getUserId()
    {
        return !empty(Yii::app()->request->cookies['USERID']) ? Yii::app()->request->cookies['USERID']->value : null;
    }

    /**
     * 获取登录帐号
     * @return null|string
     */
    public static function getUser()
    {
        $_id = !empty(Yii::app()->request->cookies['USERID']) ? Yii::app()->request->cookies['USERID']->value : null;
        if (empty($_id)) {
            return null;
        }

        $managerRow = LAManagerService::getRow(array(
            "_id" => intval($_id)
        ));
        return !empty($managerRow) ? $managerRow["email"] : null;
    }

    /**
     * 获取token
     * @return null|string
     */
    public static function getToken()
    {
        return !empty(Yii::app()->request->cookies['TOKEN']) ? Yii::app()->request->cookies['TOKEN']->value : null;
    }

    /**
     * 获取角色
     * @return bool|null
     */
    public static function getRoleId()
    {
        $_id = !empty(Yii::app()->request->cookies['USERID']) ? Yii::app()->request->cookies['USERID']->value : null;
        if (empty($_id)) {
            return null;
        }

        $managerRow = LAManagerService::getRow(array(
            "_id" => intval($_id)
        ));
        return !empty($managerRow["role"]) ? $managerRow["role"][0] : null;
    }


    /**
     * 获取该登陆账号下面所有的部门id集合
     * @return bool|null
     */
    public static function getRoleIds()
    {
        $_id = !empty(Yii::app()->request->cookies['USERID']) ? Yii::app()->request->cookies['USERID']->value : null;
        if (empty($_id)) {
            return null;
        }

        $managerRow = LAManagerService::getRow(array(
            "_id" => intval($_id)
        ));
        if(!empty($managerRow["role"])){
            $ids = array();
            foreach ($managerRow["role"] as $key =>$val){
                $ids[$val] = $val;
            }
            return $ids;
        }else{
            return null;
        }
    }

    /**
     * 获取角色
     * @return bool|null
     */
    public static function getRoleName()
    {
        $_id = !empty(Yii::app()->request->cookies['USERID']) ? Yii::app()->request->cookies['USERID']->value : null;
        if (empty($_id)) {
            return null;
        }

        $managerRow = LAManagerService::getRow(array(
            "_id" => intval($_id)
        ));
        if (!empty($managerRow["roleId"])) {
            $roleRow = LARoleService::getRow(array(
                "_id" => intval($managerRow["roleId"])
            ));
            return !empty($roleRow["name"]) ? $roleRow["name"] : null;
        }
        return null;
    }

    /**
     * 获取姓名
     * @return bool|null
     */
    public static function getName()
    {
        $_id = !empty(Yii::app()->request->cookies['USERID']) ? Yii::app()->request->cookies['USERID']->value : null;
        if (empty($_id)) {
            return null;
        }

        $managerRow = LAManagerService::getRow(array(
            "_id" => intval($_id)
        ));

        return !empty($managerRow["name"]) ? $managerRow["name"] : null;
    }

    /**
     * 获取姓名
     * @return bool|null
     */
    public static function getNameById($id)
    {

        if (empty($id)) {
            return null;
        }

        $managerRow = LAManagerService::getRow(array(
            "_id" => intval($id)
        ));

        return !empty($managerRow["name"]) ? $managerRow["name"] : null;
    }

    /**
     * 获取姓名
     * @return bool|null
     */
    public static function getNameByEmail($email)
    {
        if (empty($email)) {
            return null;
        }

        $managerRow = LAManagerService::getRow(array(
            "email" => $email
        ));

        return !empty($managerRow["name"]) ? $managerRow["name"] : null;
    }


    /**
     * 获取邮箱
     * @return bool|null
     */
    public static function getEmailById($id)
    {

        if (empty($id)) {
            return null;
        }

        $managerRow = LAManagerService::getRow(array(
            "_id" => intval($id)
        ));

        return !empty($managerRow) ? $managerRow["email"] : null;
    }

    /**
     * 生成以字母和数字组成的随机数
     * @param int $num
     * @return string
     */
    public static function randomKey($num = 4)
    {
        $re = '';
        $s = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        while (strlen($re) < $num) {
            $re .= $s[rand(0, strlen($s) - 1)]; //从$s中随机产生一个字符
        }
        return $re;
    }

    /**
     * 获取cookie里面的randomKey
     * @return null|string
     */
    public static function getRandomKey()
    {
        return !empty(Yii::app()->request->cookies['randomKey']) ? Yii::app()->request->cookies['randomKey']->value : null;
    }


    /**
     * 域登录，如果成功需要转化成为后台用户
     * @param $userId
     * @param $password
     */
    public static function loginByAd($userId, $password)
    {
        if (empty($userId) || empty($password)) {
            return false;
        }
        Yii::import("extensions.adLDAP.src.adLDAP");
        try {
            $options = array(
                "account_suffix" => "@noahwm",
                "base_dn" => "DC=noahwm,DC=com,DC=local",
                "domain_controllers" => array("e-neway.com.local"),
                "admin_username" => $userId,
                "admin_password" => $password,
            );
            $adldap = new adLDAP($options);
            $result = $adldap->user()->info($userId);

        } catch (adLDAPException $e) {
            Yii::log(sprintf("yu lian jie shibai[%s]", $e->getMessage(), $e->getTrace(), $e->getCode()), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }


        if (!empty($result[0]['mail'][0])) {
            return $result[0]['mail'][0];
        }

        return false;

    }

    /**
     * 获取token
     * @return null|string
     */
    public static function getRole()
    {
        return !empty(Yii::app()->request->cookies['role']) ? Yii::app()->request->cookies['role']->value : null;
    }

    /**
     * 获取指定角色组下的用户名称列表
     * @deprecated 该方法需要废弃，请使用getManagerByRoleId
     * @param array $roleId
     * @return array
     */
    public static function getManagerNameByRoleId($roleId = array())
    {
        if (empty($roleId)) {
            return array();
        }

        !is_array($roleId) && $roleId = array($roleId);

        $list = array();
        $criteral = new EMongoCriteria();
        $criteral->addCond('role', 'in', $roleId);
        $mangerList = LAManagerModel::model()->findAll($criteral);
        if (!empty($mangerList)) {
            foreach ($mangerList as $manager) {
                $list[$manager->_id] = $manager->name;
            }
        }
        return $list;
    }

    /**
     * 获取指定角色组下的用户列表
     * @param array $roleId
     * @return array
     */
    public static function getManagerByRoleId($roleId = array())
    {
        if (empty($roleId)) {
            return array();
        }

        !is_array($roleId) && $roleId = array($roleId);

        $list = array();
        $criteral = new EMongoCriteria();
        $criteral->addCond('role', 'in', $roleId);
        $mangerList = LAManagerModel::model()->findAll($criteral);
        if (!empty($mangerList)) {
            foreach ($mangerList as $manager) {
                $list[$manager->_id] = $manager;
            }
        }
        return $list;
    }

    /**
     * 根据id批量获取用户信息
     * @param array $idArray
     * @return array
     * @author wbz7519
     */
    public static function getManagerListByIds($idArray = array())
    {
        if (empty($idArray)) {
            return array();
        }

        $list = array();
        $criteria = new EMongoCriteria();
        $criteria->addCond('_id', 'in', array_values($idArray));
        if ($managerList = LAManagerModel::model()->findAll($criteria)) {
            foreach ($managerList as $manager) {
                $list[$manager->_id] = $manager;
            }
        }
        return $list;
    }

    /**
     * 根据id批量获取用户信息
     * @param array $idArray
     * @return array
     * @author wbz7519
     */
    public static function getManagerListByName($name)
    {
        if (empty($name)) {
            return array();
        }

        $list = array();
        $criteria = new EMongoCriteria();
        $criteria->addCond('name', '==', new MongoRegex('/^' . $name . '.*/'));
        $criteria->addCond('state', '==', intval(1));
        if ($managerList = LAManagerModel::model()->findAll($criteria)) {
            foreach ($managerList as $manager) {
                $list[$manager->_id] = $manager;
            }
        }
        return $list;
    }

    /**
     * 通过名称获取email
     * @param $name
     * @return array
     */
    public static function getManagerEmailByName($name)
    {
        if (empty($name)) {
            return array();
        }
        $criteria = new EMongoCriteria();
        $criteria->addCond('name', '==', new MongoRegex('/^' . $name . '.*/'));
        $criteria->addCond('state', '==', intval(1));
        $managerList = LAManagerModel::model()->findAll($criteria);

        return $managerList;
    }
    /**
     * 根据用户名获取uid
     * @param $name
     * @return null
     * @author way0z42
     */
    public static function getManagerIdByName($name)
    {
        $criteria = new EMongoCriteria();
        $criteria->addCond('name', '==', $name);
        $list = array();
        if ($managerList = LAManagerModel::model()->findAll($criteria)) {
            foreach ($managerList as $manager) {
                $list[] = $manager->_id;
            }
        }
        return $list;
    }

    /**
     * 判断用户是否是超级管理员
     * @return null|string
     */
    public static function isAdmin()
    {
        if (empty(self::getUser())) {
            return false;
        }
        $manager = self::getRow(array("email" => self::getUser()));

        return !empty($manager) ? $manager['isAdmin'] == 1 : false;
    }

}
