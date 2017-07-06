<?php

class UserController extends AdminBaseController
{
    public $layout = false;

    public $freeLogin = true;
    public function actionSignin()
    {
        $err = '';
        $referrer = $this->getReferrer();
        if (Yii::app()->request->isPostRequest)
        {
            $userName = Yii::app()->request->getPost('user');
            $password = Yii::app()->request->getPost('password');
            $error_count = Yii::app()->cache->get("password_error_count_" . $userName);

            if($error_count >= 5)
            {
                $err = "错误次数过多,请稍后登陆";
                $userCookie = Yii::app()->request->cookies['user'];
                $this->render('signin', array(
                    'err' => $err,
                    'user' => $userCookie ? $userCookie->value : "",
                    'referrer' => $referrer
                ));
                return;
            }
            $ret = LAManagerService::login($userName, $password, $this->freeLogin);
            if ($ret)
            {
                $expire = time() + 86400 * 365;
                Yii::app()->request->cookies['user'] = new CHttpCookie('user', $userName, array('expire' => $expire, 'httpOnly' => true, 'domain' => BASE_DOMAIN));
                Yii::app()->request->cookies['USERID'] = new CHttpCookie('USERID', $ret["_id"], array('expire' => $expire, 'httpOnly' => true, 'domain' => BASE_DOMAIN));
                Yii::app()->request->cookies['TOKEN'] = new CHttpCookie('TOKEN', $ret["token"], array('expire' => $expire, 'httpOnly' => true, 'domain' => BASE_DOMAIN));
                Yii::app()->request->cookies['LASTLOGIN'] = new CHttpCookie('LASTLOGIN', $ret["lastLogin"], array('expire' => $expire, 'httpOnly' => true, 'domain' => BASE_DOMAIN));
                Yii::app()->request->cookies['randomKey'] = new CHttpCookie('randomKey', $ret['randomKey'], array('expire' => $expire, 'httpOnly' => true, 'domain' => BASE_DOMAIN));
                if (!empty($ret['role']))
                {
                    Yii::app()->request->cookies['role'] = new CHttpCookie('role', $ret['role'], array('expire' => $expire, 'httpOnly' => true, 'domain' => BASE_DOMAIN));
                }
                Yii::app()->cache->set("token_" . $ret["_id"] . "_" . $ret["randomKey"], $ret["token"], 86400 * 365);

                Yii::app()->request->redirect($referrer);
                return;
            }
            else
            {
                $err = "用户名或密码不正确";

                Yii::app()->cache->set("password_error_count_" . $userName , $error_count + 1, 600);
            }
        }


        $userCookie = Yii::app()->request->cookies['user'];
        $this->render('signin', array(
            'err' => $err,
            'user' => $userCookie ? $userCookie->value : "",
            'referrer' => $referrer
        ));
    }

    public function actionSignout()
    {
        Yii::app()->cache->delete('token_'.Yii::app()->request->cookies['USERID']."_".Yii::app()->request->cookies['randomKey']);
        Yii::app()->request->cookies->remove('TOKEN',array('httpOnly'=>true,'domain' => BASE_DOMAIN));
        Yii::app()->request->cookies->remove('user',array('httpOnly'=>true,'domain' => BASE_DOMAIN));
        Yii::app()->request->cookies->remove('USERID',array('httpOnly'=>true,'domain' => BASE_DOMAIN));
        Yii::app()->request->cookies->remove('randomKey',array('httpOnly'=>true,'domain' => BASE_DOMAIN));
        Yii::app()->request->cookies->remove('isAdmin',array('httpOnly'=>true,'domain' => BASE_DOMAIN));
        Yii::app()->request->redirect(Yii::app()->createUrl('user/signin', array("referrer" => $this->getReferrer())));
    }

    public function getReferrer()
    {
        $referrer = Yii::app()->request->getParam("referrer", "");
        $referrer = !empty($referrer) ? $referrer : Yii::app()->request->getUrlReferrer();

        return strstr(strtolower($referrer), "user/") ? "/" : $referrer;
    }


    /**
     * 错误页面
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
            {
                echo $error['message'];
            }
            else
            {
                $this->render('error', $error);
            }

            Yii::app()->end();
        }
    }




} 