<?php

/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 */
class RoleController extends AdminBaseController
{
    public $menuId = 1003;

    const LIST_NUM = 30;

    const LIST_ORDER = 'roleSort'; //默认排序

    const LIST_ASC = 'desc';

    //列表页表头的绑定关系与样式设置
    private $headerCol = array(

        array(
            'title' => '', //表头要显示的内容
            'mapping' => array('_id'), //绑定的数据的键值
            'type' => 'checkbox', //数据的类型包括text，decimal，rate，integer，input，checkbox目前只影响对齐方式
            'width' => '5%' //宽度
        ),
        array(
            'title' => '角色ID',
            'mapping' => array('_id'), //此两个必须填，别的都可以忽略
            'width' => '15%'
        ),
        array(
            'title' => '角色名称',
            'mapping' => array('roleName'), //此两个必须填，别的都可以忽略
            'width' => '15%'
        ),
        array(
            'title' => '角色说明',
            'mapping' => array('roleContent'),

        ),
        array(
            'title' => '排序',
            'mapping' => array('roleSort'),
            'sortable' => true,
            'width' => '15%'
        ),
        array(
            'title' => '创建时间',
            'mapping' => array('createTime'),
            'sortable' => true,
            'width' => '15%'),
        array(
            'title' => '状态',
            'mapping' => array('stateName'),
            'sortable' => true,
            'sortField' => array('state'),
            'width' => '15%'),
        array(
            'title' => '查看',
            'mapping' => array('_id'),
            'width' => '10%'
        ),
    );

    /**
     * 角色列表
     */
    public function actionIndex()
    {
        $this->setJsMain("NewRoleList");
        $this->render('index', array(
            'header' => json_encode($this->headerCol), //定义表头
            //registerScript 是需要传给window的变量集合，供JS调用
            'registerScript' => array(
                'listUrl' => Yii::app()->createUrl("/role/list"),
                'editUrl' => Yii::app()->createUrl("/role/newRoleEdit"),
                'addUrl' => Yii::app()->createUrl("/role/newRoleAdd"),
                'menuUrl' => Yii::app()->createUrl("/role/roleAuthority"),
                'roleDownLoanUrl' => Yii::app()->createUrl("/role/Download"),
                //'delUrl'  => Yii::app()->createUrl("/role/newRoledel"),
            )
        ));
    }

    /**
     * 新增角色入库
     */
    public function actionInsert()
    {
        $params = $_POST;
        if (empty($params["name"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入角色名！");
        }

        if (!isset($params["sort"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入排序号！");
        }

        $ret = LARoleService::insert(array(
            "name" => $params['name'],
            "sort" => $params['sort']
        ));
        if ($ret) {
            //日志记录
            $this->log("新增角色数据");

            $this->ajaxReturn(LError::SUCCESS, "保存成功");
        }
        $this->ajaxReturn(LError::INTERNAL_ERROR, "保存失败！");

    }

    /**
     * 编辑角色
     */
    public function actionEdit()
    {
        //日志记录
        $this->log("编辑角色页面");
        $this->setJsMain('permission');
        $_id = Yii::app()->request->getQuery("_id");
        if (empty($_id)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("role/index"));
        }
        $roleRow = LARoleService::getRow(array("_id" => intval($_id)));
        if (empty($roleRow)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("role/index"));
        }
        $this->render("edit", array(
            "roleRow" => $roleRow
        ));
    }

    /**
     * 角色更新
     */
    public function actionUpdate()
    {
        $params = $_POST;
        if (empty($params["_id"])) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "数据不合法！");
        }
        if (empty($params["name"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入角色名！");
        }
        if (!isset($params["sort"])) {
            $this->ajaxReturn(LError::PARAM_ERROR, "请输入排序号！");
        }

        $roleRow = LARoleService::getRow(array("_id" => intval($params["_id"])));
        if (empty($roleRow)) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "数据不合法！");
        }

        $ret = LARoleService::update(array(
            "_id" => intval($params["_id"]),
            "name" => $params["name"],
            "sort" => $params["sort"]
        ));
        if ($ret) {
            //日志记录
            $this->log("更新角色信息");

            $this->ajaxReturn(LError::SUCCESS, "保存成功");
        }
        $this->ajaxReturn(LError::INTERNAL_ERROR, "保存失败！");
    }

    public function actionDel()
    {
        //日志记录
        $this->log("删除角色");
        $_id = Yii::app()->request->getQuery("_id");
        if (empty($_id)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("role/index"));
        }
        $roleRow = LARoleService::getRow(array("_id" => intval($_id)));
        if (empty($roleRow)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("role/index"));
        }
        LARoleService::delete($_id);
        Yii::app()->request->redirect(Yii::app()->createUrl("role/index"));
    }

    /**
     * 角色权限分配
     */
    public function actionPermission()
    {
        //日志记录
        $this->log("角色权限分配页面");
        $this->setJsMain('permission');
        $roleId = Yii::app()->request->getQuery("roleId");
        if (empty($roleId)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("role/index"));
        }
        $roleRow = LARoleService::getRow(array("_id" => intval($roleId)));
        if (empty($roleRow)) {
            Yii::app()->request->redirect(Yii::app()->createUrl("role/index"));
        }
        $permissionArr = array();
        $menuObj = new LAMenuService();
        $menuArr = $menuObj->getMenuList();
        foreach ($menuArr as $key => $val) {
            $menuPermissionArr = LAMenuService::getMenuPermissionAll(array("menuId" => intval($val["_id"])));
            $menuArr[$key]["permission"] = $menuPermissionArr;
            foreach ($menuPermissionArr as $valPer) {
                $perRow = LAPermissionService::getRow(array(
                    "roleId" => intval($roleId),
                    "menuId" => intval($val["_id"]),
                    "permission" => intval($valPer["perId"]),
                ));
                $permissionArr[$roleId][$val["_id"]][$valPer["perId"]] = empty($perRow) ? 0 : 1;
            }
        }
        $this->render("permission", array(
            "roleRow" => $roleRow,
            "menuArr" => $menuArr,
            "permissionArr" => $permissionArr
        ));
    }

    /**
     * 角色权限更新
     */
    public function actionSavePermission()
    {
        $roleId = Yii::app()->request->getParam("roleId");
        if (empty($roleId)) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "权限更新失败！");
        }
        $roleRow = LARoleService::getRow(array("_id" => intval($roleId)));
        if (empty($roleRow)) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "权限更新失败！");
        }
        $perArr = !empty($_POST["listid"]) ? $_POST["listid"] : array();
        $ret = LAPermissionService::SavePermission($roleId, $perArr);
        if ($ret) {
            //日志记录
            $this->log("更新角色权限分配页面");

            $this->ajaxReturn(LError::SUCCESS, "权限更新成功");
        }
        $this->ajaxReturn(LError::INTERNAL_ERROR, "权限更新失败！");
    }

    /**
     * 列表页ajax获取列表数据
     */
    public function actionList()
    {
        //初始化默认的变量
        $page = Yii::app()->request->getParam('page', 1);
        $pageSize = Yii::app()->request->getParam('page_size', self::LIST_NUM);
        $orderBy = Yii::app()->request->getParam('orderBy', self::LIST_ORDER);
        $asc = Yii::app()->request->getParam('asc', self::LIST_ASC);
        /* 验证查询条件的参数，如果没有别的查询条件，可以不用*/
        $form = new newRoleFormModel();
        $scenario = newRoleFormModel::LIST_FIND;
        $form->setScenario($scenario);
        $form->setAttributes($_POST);
        $form->validate();
        if ($errs = $form->getErrors()) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "", $errs);
        }
        $data = $form->getAttributes();

        /* 验证查询条件的参数 end    */
        $infoRes = LANewRoleService::getLists($page, $pageSize, $orderBy, $asc, $data); //查询数据，然后返回

        $this->ajaxReturn(LError::SUCCESS, '获取列表成功', $infoRes);

    }

    /**
     * 新增页面，加载页面上需要的一些变量
     *
     */
    public function actionNewRoleAdd()
    {
        header("Content-type: text/html; charset=utf-8");
        //加载js与页面
        $this->setJsMain("NewRoleAdd");
        $allAuthority = LANewRoleService::getMenu();
        $this->render('newRoleAdd', array(
            'allAuthority' => json_encode($allAuthority),
            'registerScript' => array(
                'listUrl' => Yii::app()->createUrl("/role/newIndex"),
                'saveUrl' => Yii::app()->createUrl("/role/addSave"),
            ),
            'title' => '创建角色'
        ));
    }

    /**
     *  新增保存
     */

    public function actionAddSave()
    {
        /* 验证参数，统一使用formModel，禁止使用if else*/
        $form = new newRoleFormModel();
        $scenario = newRoleFormModel::ADD;
        $form->setScenario($scenario);
        $form->setAttributes($_POST);
        $form->validate();
        if ($errs = $form->getErrors()) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "", $errs);
        }
        $data = $form->getAttributes();
        /* 验证参数end    */
        if (!LANewRoleService::addNewRecord($data)) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, '保存失败');
        }
        $key = 'roleAuthority' . LAManagerService::getUserId();
        Yii::app()->cache->set($key, '');
        $this->ajaxReturn(LError::SUCCESS, '保存成功');
    }

    /**
     * 编辑页面，加载页面上需要的一些变量
     *
     */
    public function actionNewRoleEdit()
    {
        $id = Yii::app()->request->getParam('id');
        if (empty($id)) {
            throw new LException(LError::PARAM_ERROR, "ID不正确");
            //返回有问题
        }
        $roleInfo = LANewRoleService::detail(intval($id));
        $allAuthority = LANewRoleService::getMenu($roleInfo->authority);
       //var_dump('<pre>', $allAuthority);die;
        $this->setJsMain("NewRoleAdd");
        $this->render('newRoleAdd', array(
            'allAuthority' => json_encode($allAuthority),
            'roleInfo' => $roleInfo,
            'title' => '编辑角色',
            'registerScript' => array(
                'listUrl' => Yii::app()->createUrl("/role/newIndex"),
                'saveUrl' => Yii::app()->createUrl("/role/addSave"),
            )));
    }

    public function actionNewRoleDel()
    {
        if (!LANewRoleService::changeState($_POST['lcsIds'])) {
            $this->ajaxReturn(LError::PARAM_ERROR, '移除失败');
        }
        $this->ajaxReturn(LError::SUCCESS, '移除成功');
    }

    public function actionRoleAuthority()
    {
        $id = Yii::app()->request->getPost('id');
        $authority = LANewRoleService::getRoleAuthority(intval($id));
        if (!$authority) {
            $this->ajaxReturn(LError::PARAM_ERROR, '获取失败');
        }
        $this->ajaxReturn(LError::SUCCESS, '', $authority);
    }

    /**
     * 下载权限列表
     */
    public function actionDownload()
    {
        /* 验证查询条件的参数 end    */
        $infoRes = LANewRoleService::getLists(0, 0, "roleSort", "asc", array()); //查询数据，然后返回
        //檢索數據
        $roleArr = array();
        foreach ($infoRes["checkInfo"] as $key => $val) {
            $roleArr[$key] = (array)$val;
            $authority = LANewRoleService::getRoleAuthority(intval($val["_id"]));
            $roleArr[$key]["authority"] = !empty($authority) ? $authority : array();
        }
        Yii::$enableIncludePath = false;
        Yii::import('extensions.PHPExcelSuite.*', true);
        $objPhpExcel = new PHPExcel();
        //设值
        $objPhpExcel->getProperties()->setCreator("TA")
            ->setLastModifiedBy("role")
            ->setTitle('Noahwm')
            ->setSubject("")
            ->setDescription("")
            ->setKeywords("")
            ->setCategory("");
        $objActSheet = $objPhpExcel->getActiveSheet();

        $objStyleA1 = $objActSheet->getStyle('A1');
        $objAlignA1 = $objStyleA1->getAlignment();
        $objAlignA1->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    //左右居中
        $objAlignA1->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);  //上下居中

        $objPhpExcel->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('bold' => true, 'size' => 12)));
        $objActSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPhpExcel->getActiveSheet()->getStyle('B1')->applyFromArray(array('font' => array('bold' => true, 'size' => 12)));
        $objActSheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPhpExcel->getActiveSheet()->getStyle('C1')->applyFromArray(array('font' => array('bold' => true, 'size' => 12)));
        $objActSheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置宽度
        $objActSheet->getColumnDimension('A')->setWidth(5);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(15);

        $objPhpExcel->getActiveSheet()->setCellValue('A1', '序号');
        $objPhpExcel->getActiveSheet()->setCellValue('B1', '角色名称');
        $objPhpExcel->getActiveSheet()->setCellValue('C1', '权限');

        $i = 1;
        foreach ($roleArr as $val) {
            $i++;
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $i, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $val["roleName"], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPhpExcel->getActiveSheet()->setCellValueExplicit('C' . $i, "", PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel5');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="角色列表.xls"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }


}//end class


class newRoleFormModel extends AdminBaseFormModel
{

    const  LIST_FIND = 'list';
    const  ADD = 'newRecord';
    const  CANCEL_REFUSE = 'cancelRefuse';

    public $id;
    public $roleName;
    public $roleContent;
    public $roleSort;
    public $state;
    public $authority;


    public function rules()
    {
        return array(
            //一般的参数验证
            array('state,id,roleName', 'safe', 'on' => array(self::ADD, self::LIST_FIND)),

            array("roleName,roleContent,roleSort", 'required', 'message' => '{attribute}不能为空', 'on' => self::ADD),
            array('authority', 'authorityRole', 'on' => array(self::ADD)),


        );
    }

    public function attributeLabels()
    {
        return array(
            'roleName' => '角色名称',
            'roleContent' => '角色说明',
            'roleSort' => '排序',
        );
    }

    public function authorityRole()
    {
        $data = $this->getAttributes();

        if (empty($data['authority'])) {
            $this->addErrors(array('请选择权限'));
            return false;
        }
        return true;
    }


}
