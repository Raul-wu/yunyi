<?php

class AdminBaseController extends LController
{
	public $layout = 'common.web.layouts.default';

    public $topMenu = array();
    public $sonMenu = array();
    public $parentMenuId = 0;
    public $menuId = 0;
    public $logManager = "";

	protected $_jsMain = "main/default";

	public $bodyClass = '';
	public $headerTab = null;
	public $hasHeaderTab = true;

	const HEADER_TAB_USER = 1;
	const HEADER_TAB_PRODUCT = 2;
	const HEADER_TAB_ORDER = 3;
	const HEADER_TAB_TA = 4;
	const HEADER_TAB_COMMON = 5;
    const HEADER_TAB_AD = 6;
	const HEADER_TAB_COMMENT = 7;
    const HEADER_TBG_PERMISSION = 8;

	public function init()
	{
		parent::init();
        $this->pageTitle = Yii::app()->name;

        //判断是否登录
        if (!LAManagerService::isLogin() && !in_array($this->route, Yii::app()->params['noCheck']))
        {
            if (!in_array($_SERVER["REQUEST_URI"], Yii::app()->params["no_check"]))
            {
                Yii::app()->request->redirect("/user/signin");
            }
        }
        else
        {
           LAPermissionService::checkMenuPermission();
            //获取菜单数据
            $menuRow = LAMenuService::getParent($this->menuId);
            $this->parentMenuId = !empty($menuRow["topId"]) ? $menuRow["topId"] : $menuRow["_id"];
            $managerId = LAManagerService::getUserId();
            $topCacheKey = "top_menu_" . $managerId . "_" . Yii::app()->request->cookies['LASTLOGIN'];
            $sonCacheKey = "son_menu_" . $managerId . "_" . $this->parentMenuId . "_" . Yii::app()->request->cookies['LASTLOGIN'];
            //$this->topMenu = Yii::app()->cache->get($topCacheKey);
            //$this->sonMenu = Yii::app()->cache->get($sonCacheKey);
            if (empty($this->topMenu) || empty($this->sonMenu))
            {
                $this->topMenu = LAMenuService::getParentList();
                $this->sonMenu = !empty($this->parentMenuId) ? LAMenuService::getChildrenMenu($this->parentMenuId) : $this->topMenu;

                Yii::app()->cache->set($topCacheKey, $this->topMenu, 86400 * 30);
                Yii::app()->cache->set($sonCacheKey, $this->sonMenu, 86400 * 30);
            }
    	}

        /**
         * 记日志
         */
        $msg = sprintf("url[%s] post[%s] get[%s]",
            $_SERVER["REQUEST_URI"],
            !empty($_POST) ? json_encode($_POST) : "",
            !empty($_GET) ? json_encode($_GET) : ""
        );
        Yii::log($msg, CLogger::LEVEL_TRACE, "admin");
	}

	public function getAssetsDir()
	{
		return 'src';
	}

	public function getJsMain()
	{
		return $this->_jsMain;
	}

	public function getStyleDir()
	{
		return "/assets/src/";
	}

	public function setJsMain($name)
	{
		if ($name)
		{
			$this->_jsMain = "main/$name";
		}
	}

	public function setBodyClass($class)
	{
		$this->bodyClass .= " $class";
	}

    /**
     * 赋值日志
     * @param $log
     */
    public function log($log)
    {
        $msg = sprintf("url[%s] post[%s] get[%s] log[%s]",
            $_SERVER["REQUEST_URI"],
            !empty($_POST) ? json_encode($_POST) : "",
            !empty($_GET) ? json_encode($_GET) : "",
            !empty($log) ? $log : ""
        );
        Yii::log($msg, CLogger::LEVEL_TRACE, "admin");
    }

}