<?php

/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14-4-18
 * Time: AM11:25
 */
class LWebUser extends CWebUser
{
    public $allowAutoLogin = true;

    public function init()
    {
        parent::init();
        $this->loginRequiredAjaxResponse = json_encode(array(
            'retCode' => LError::NO_LOGIN,
            'retMsg' => LError::getErrMsgByCode(LError::NO_LOGIN),
            'retData' => array(
                'loginUrl' => Yii::app()->createUrl('user/login'),
            ),
            'retHtml' => '',
        ));
    }

//    public function login($user, $duration = 0)
//    {
////        $id = $user->getId();
////        $states = array(
////            'status' => $user->getState('status'),
////            'mobile' => $user->getState('mobile'),
////            'appId' => $user->getState('appId'),
////            'isSuperAdmin' => $user->getState('isSuperAdmin'),
////            'corpName' => $user->getState('corpName'),
////'uid' => $user->getState('uid'),
////        );
////        $this->changeIdentity($id,$user->getName(),$states);
////        return !$this->getIsGuest();
//    }

    public function loginAddLoginKey($user, $loginKey = '')
    {
        $id = $user->uid;
        $states = array(
            '__corpId' => $user->corpId,
            '__needResetPwd' => !$user->isResetPwd,
            '__state' => $user->state,
        );
        if ($loginKey) {
            $states['__loginKey'] = $loginKey;
        }
        $name = $user->name ? $user->name : ($user->email && $user->emailVerified == LUserModel::ACTIVED ? substr($user->email, 0, strpos($user->email, '@')) : LUtil::maskMobile($user->mobile));
        $this->changeIdentity($id, $name, $states);

        return !$this->getIsGuest();
    }

    public function loginRequired()
    {
        $app = Yii::app();
        $request = $app->getRequest();

        if (!$request->getIsAjaxRequest()) {
            $this->setReturnUrl($request->getHostInfo() . $request->getUrl());
            if (($url = $this->loginUrl) !== null) {
                if (is_array($url)) {
                    $route = isset($url[0]) ? $url[0] : $app->defaultController;
                    $url = $app->createUrl($route, array_splice($url, 1));
                }
                $request->redirect($url);
            }
        } elseif (isset($this->loginRequiredAjaxResponse)) {
            echo $this->loginRequiredAjaxResponse;
            Yii::app()->end();
        }

        throw new CHttpException(403, Yii::t('yii', 'Login Required'));
    }

    public function setFlash($key, $value, $defaultValue = null, $flashCount = 1)
    {
        $this->setState(self::FLASH_KEY_PREFIX . $key, $value, $defaultValue);
        $counters = $this->getState(self::FLASH_COUNTERS, array());
        if ($value === $defaultValue) {
            unset($counters[$key]);
        } else {
            $counters[$key] = array(0, $flashCount);
        }
        $this->setState(self::FLASH_COUNTERS, $counters, array());
    }

    protected function updateFlash()
    {
        $counters = $this->getState(self::FLASH_COUNTERS);
        if (!is_array($counters)) {
            return;
        }
        foreach ($counters as $key => $count) {
            if (is_array($count)) {
                if ($count[1] && $count[0] >= $count[1]) {
                    unset($counters[$key]);
                    $this->setState(self::FLASH_KEY_PREFIX . $key, null);
                } else {
                    $count[0]++;
                    $counters[$key] = $count;
                }
            } else {
                unset($counters[$key]);
            }
        }
        $this->setState(self::FLASH_COUNTERS, $counters, array());
    }

    public function checkAccess($operation, $params = array(), $allowCaching = true)
    {
        if (!in_array($operation, array('noauth'))) {
            return false;
        }

        return !(($user = LUserService::getLoginUser()) && $user->state);
    }

    public function setCorpId($value)
    {
        $this->setState('__corpId', $value);
    }

    public function getCorpId()
    {
        return $this->getState('__corpId');
    }

    public function flagResetPwd()
    {
        $this->setState('__needResetPwd', false);
    }

    public function needResetPwd()
    {
        return $this->getState('__needResetPwd') === true;
    }

    public function setIsH5()
    {
        $this->setState('__isH5', true);
    }

    public function isH5()
    {
        return $this->getState('__isH5') === true;
    }

    public function setIsWeb()
    {
        $this->setState('__isWeb', true);
    }

    public function isWeb()
    {
        return $this->getState('__isWeb') === true;
    }

    public function setLoginKey($loginKey)
    {
        $this->setState('__loginKey', $loginKey);
    }

    public function getLoginKey()
    {
        return $this->getState('__loginKey');
    }

    public function getIsGuest()
    {
        $uid = $this->getState('uid');
        $TOKEN = $this->getState('TOKEN');
        $redis_token = Yii::app()->cache->get("token_" . $uid);
        //var_dump($uid, $TOKEN, $redis_token);die;
        return $TOKEN != $redis_token || empty($TOKEN) || empty($redis_token);
    }
}