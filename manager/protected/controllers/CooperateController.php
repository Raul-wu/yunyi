<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 10/12/17
 * Time: 22:20
 */
class CooperateController extends AdminBaseController
{
    public $menuId = 2005;

    public function actionList()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $this->setJsMain('cooperateList');

        $conditions['name'] = trim(Yii::app()->request->getParam('name', ''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));
        $arrCooperate = LACooperateService::getAll($conditions, $conditions['page']);

        $this->render('list', array(
            'cooperateAll' => $arrCooperate['cooperateAll'],
            'pageBar' => $arrCooperate['pageBar'],
            'count' => $arrCooperate['count'],
            'name' => $conditions['name'],
        ));
    }

    public function actionAdd()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2005102);

        $this->setJsMain('cooperateEdit');

        $this->render('edit',array(
        ));
    }

    public function actionEdit()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2005103);

        $this->setJsMain('cooperateEdit');

        $cid = trim(Yii::app()->request->getParam('cid',''));
        $cooperate = LACooperateService::getByID($cid);

        $this->render('edit',array(
            'cid'     => $cid,
            'cooperate'  => $cooperate,
        ));
    }

    public function actionSave()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        if (!$cid = Yii::app()->request->getParam('cid'))
        {
            $cooperate = new CooperateFormModel();
            $cooperate->setAttributes($_POST);
            $cooperate->setScenario(CooperateFormModel::COOPERATE_NEW);
            $cooperate->validate();
            if ($errors = $cooperate->getErrors())
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, '数据不能为空');
            }

            $cooperateData = $cooperate->getData();
            if(LACooperateService::create($cooperateData))
            {
                $this->ajaxReturn(LError::SUCCESS, "创建成功！", array("url" => Yii::app()->createUrl("cooperate/list?cid=". $cid)));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "创建失败！");
            }
        }
        else
        {
            $cooperate = new CooperateFormModel();
            $cooperate->setAttributes($_POST);
            $cooperate->setScenario(CooperateFormModel::COOPERATE_EDIT);
            $cooperate->validate();
            if ($errors = $cooperate->getErrors())
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, '数据不能为空');
            }

            $cooperateData = $cooperate->getData();
            if(LACooperateService::update($cid, $cooperateData))
            {
                $this->ajaxReturn(LError::SUCCESS, "更新成功！", array("url" => Yii::app()->createUrl("cooperate/list?pid=". $cid)));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "更新失败！");
            }
        }
    }
}

class CooperateFormModel extends AdminBaseFormModel
{
    const COOPERATE_NEW          = 'cooperate_new';
    const COOPERATE_EDIT         = 'cooperate_edit';

    public $cid;
    public $name;
    public $nature;
    public $location;
    public $cooperater;
    public $limitation_cooperater;
    public $delegate;
    public $project_manager;
    public $department;
    public $team_leader;
    public $tax;
    public $agent;
    public $account_type;
    public $id_img;
    public $status;

    public function rules()
    {
        return array(
            array('cid, name, nature, location, cooperater, limitation_cooperater, delegate, project_manager, department, team_leader, tax, agent, account_type, id_img, status, create_time, update_time', 'safe'),

            array('name', 'required', 'on' => array(self::COOPERATE_NEW, self::COOPERATE_EDIT)),
        );
    }

    public function getData()
    {
        $data = $this->attributes;

        return $this->trimData($data);
    }

    public function trimData($data)
    {
        foreach ($data as $key =>  $val)
        {
            if (!is_array($val))
            {
                $data[$key] = trim($val);
            }
        }

        return $data;
    }
}