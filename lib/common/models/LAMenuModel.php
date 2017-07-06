<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-18
 * Time: 上午11:19
 */
class LAMenuModel extends LMongoDocument
{
    public $_id;
    public $name;
    public $route;
    public $parentId;
    public $topId;
    public $className;
    public $sort;

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
        return 'menu';
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}