<?php

class IndexController extends AdminBaseController
{
    public function actionIndex()
    {
        $this->render("index", array());
    }
}