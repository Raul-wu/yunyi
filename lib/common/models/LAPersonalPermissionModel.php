<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-18
 * Time: 下午2:25
 */
class LAPersonalPermissionModel extends LMongoDocument
{
    public $uid;
    public $menuId;
    public $permission;

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
        return 'personalPermission';
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}