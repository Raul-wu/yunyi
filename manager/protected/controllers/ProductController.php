<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/18/17
 * Time: 09:05
 */
class ProductController extends AdminBaseController
{
    public $menuId = 2002;

    public function actionList()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 9999);

        $this->setJsMain('productList');

        $conditions['fund_code'] = trim(Yii::app()->request->getParam('fund_code',''));
        $conditions['page'] = trim(Yii::app()->request->getParam('page', 1));
        $arrProduct = LAProductService::getAll($conditions, $conditions['page']);

        $this->render('list',array(
            'products'  => $arrProduct['productAll'],
            'pageBar'   => $arrProduct['pageBar'],
            'count'     => $arrProduct['count'],
            'fund_code'      => $conditions['fund_code'],
            'url'       => array('editUrl'=>Yii::app()->createUrl("product/edit/")),
        ));
    }

    public function actionEdit()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006103);

        $this->setJsMain('productEdit');

        $pid = trim(Yii::app()->request->getParam('pid',''));
        $ppid = Yii::app()->request->getParam('ppid','');

        $objProduct = LAProductService::getByID($pid);
        $objPProduct = LAPProductService::getById($ppid);
        $objPProductDetail = LAPProductDetailService::getByPPid($ppid);

        $this->render('edit',array(
            'opType'    => 'edit',
            'pid'           => $pid,
            'ppid'          => $ppid,
            'product'     => $objProduct,
            'pproduct'  => $objPProduct,
            'pproduct_detail'   => $objPProductDetail,
        ));
    }

    public function actionAdd()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2006104);

        $this->setJsMain('productEdit');

        $ppid = Yii::app()->request->getParam('ppid','');
        $objPProduct = LAPProductService::getById($ppid);
        $objPProductDetail = LAPProductDetailService::getByPPid($ppid);

        $this->render('edit',array(
            'ppid'            => $ppid,
            'pproduct'  => $objPProduct,
            'pproductDetail'   => $objPProductDetail,
        ));
    }

    public function actionSave()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(404,'非法操作');
            Yii::app()->end();
        }

        $ppid = Yii::app()->request->getParam('ppid');
        if(!$ppid)
        {
            throw new CHttpException(404,'缺少必要参数');
            Yii::app()->end();
        }

        if (!$pid = Yii::app()->request->getParam('pid'))
        {
            $product = new ProductFormModel();
            $product->setAttributes($_POST);
            $product->validate();
            $productData = $product->getData();

            if(LAProductService::create($ppid, $productData))
            {
                $this->ajaxReturn(LError::SUCCESS, "创建子产品成功！", array("url" => Yii::app()->createUrl("product/list" )));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "创建子产品失败！");
            }
        }
        else
        {
            $product = new ProductFormModel();
            $product->setAttributes($_POST);
            $product->validate();
            $productData = $product->getData();

            if(LAProductService::update($pid, $productData))
            {
                $this->ajaxReturn(LError::SUCCESS, "更新子产品成功！", array("url" => Yii::app()->createUrl("product/list" )));
            }
            else
            {
                $this->ajaxReturn(LError::INTERNAL_ERROR, "更新子产品失败！");
            }
        }
    }

    public function actionDelete()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2001104);

        if (!$pids = Yii::app()->request->getParam('pids'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        $arrPids = explode(',', $pids);
        $succID = $failID = '';
        foreach($arrPids as $pid)
        {
            $product = LAProductService::getById($pid);

            if (LAProductService::updatePProductStatus($product, LAProductModel::STATUS_DELETE))
            {
                $succID .= empty($succID) ? $pid : ',' . $pid;
            }
            else
            {
                $failID .= empty($failID) ? $pid : ',' . $pid;
            }
        }

        if(!$failID)
        {
            $this->ajaxReturn(LError::SUCCESS, "子产品ID:{$succID}删除成功");
        }
        else
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "子产品ID:{$succID}删除成功;母产品ID:{$failID}删除失败");
        }
    }

    public function actionVerify()
    {
        LAPermissionService::checkMenuPermission($this->menuId, 2001104);

        if (!$pids = Yii::app()->request->getParam('pids'))
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "缺少必要参数！");
        }

        $arrPids = explode(',', $pids);
        $succID = $failID = '';
        foreach($arrPids as $pid)
        {
            $product = LAProductService::getById($pid);

            if (LAProductService::updatePProductStatus($product, LAProductModel::STATUS_VERIFY))
            {
                $succID .= empty($succID) ? $pid : ',' . $pid;
            }
            else
            {
                $failID .= empty($failID) ? $pid : ',' . $pid;
            }
        }

        if(!$failID)
        {
            $this->ajaxReturn(LError::SUCCESS, "子产品ID:{$succID}审核成功");
        }
        else
        {
            $this->ajaxReturn(LError::INTERNAL_ERROR, "子产品ID:{$succID}删除成功;母产品ID:{$failID}审核失败");
        }
    }
}

class ProductFormModel extends AdminBaseFormModel
{
    const PRODUCT_NEW           = 'product_new';
    const PRODUCT_EDIT          = 'product_edit';

    public $expected_income_rate_E6;
    public $total_count;
    public $actually_total;
    public $per_user_by_limit;
    public $max_buy;
    public $min_buy;
    public $status;

    public function rules()
    {
        return array(
            array('expected_income_rate_E6, total_count, actually_total, per_user_by_limit, max_buy, min_buy, mode, status, create_time, update_time', 'safe'),
        );
    }

    public function getData()
    {
        $data = $this->attributes;
        $data['expected_income_rate_E6'] = $this->expected_income_rate_E6 * LConstService::E4;

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