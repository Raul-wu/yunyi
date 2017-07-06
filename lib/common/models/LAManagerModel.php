<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-18
 * Time: 上午11:19
 *
 */
class LAManagerModel extends LMongoDocument
{
    public $_id;
    public $email;
    public $name;
    public $phone;
    public $password;
    public $roleId;
    public $corpId;
    public $whiteIp;
    public $isAdmin;
    public $lastLogin;
    public $lastActive;
    public $lastIp;
    public $state;
    public $role;

    public static $csRoleId = '55f7d2d77f8b9ad7178b4569';   // 客服组角色ID

    /**
     * This method must return collection name for use with this model
     * this must be implemented in child classes
     *
     *
     * this is read-only defined only at class define
     * if you whant to set different colection during run-time
     * use {@see setCollection()}
     *
     * @return string collection name
     * @since v1.0
     */

    public function getCollectionName()
    {
        return 'manager';
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}