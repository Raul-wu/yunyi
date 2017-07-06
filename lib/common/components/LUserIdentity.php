<?php

/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14-4-18
 * Time: AM11:03
 */
class LUserIdentity extends CUserIdentity
{
    private $_id;
    private $_name;

    public function authenticate()
    {
        /** @var LSUserModel $user */
        $user = LSUserModel::model()->findByAttributes(array('mobile' => LSUserModel::encryptAttribute('mobile', $this->username)));
        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif (!$user->validatePassword($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            if ($user->status == LSUserModel::STATUS_OFF) {
                $this->errorCode = self::ERROR_USERNAME_INVALID;
            } else {
                $this->_id = $user->uid;
                $this->_name = empty($user->realName) ? $user->mobile : $user->realName;
                $this->setState('uid', $user->uid);
                $this->setState('realName', $user->realName);
                $this->setState('mobile', $user->mobile);
                $this->setState('appId', $user->appId);
                $this->setState('isSuperAdmin', $user->isSuperAdmin == 1 ? true : false);
                $this->setState('status', $user->status);
                $this->setState('lastLogin', $user->lastLogin);
                $this->setState('appId', $user->appId);
                $this->setState('ip', $user->ip);
                Yii::app()->cache->set('auth' . $user->uid, json_decode(base64_decode($user->auth), true));
                if (!empty($user->appId)) {
                    $corp = LACustomerService::getCorpInfo($user->appId);
                    empty($corp) ? $this->setState('corpName', '') : $this->setState('corpName', $corp->name);
                    empty($corp) ? $this->setState('use_status', '') : $this->setState('use_status', $corp->use_status);
                    empty($corp) ? $this->setState('maturity_ay', '') : $this->setState('maturity_ay', LUtil::dayDiff(time(), $corp->maturity_date));
                } else {
                    $this->setState('corpName', '');
                }
                $token = md5(LSUserService::$mac . $user["uid"] . $user["password"] . $user["status"] . LSUserService::randomKey());
                $this->setState('TOKEN', $token);
                Yii::app()->cache->set("token_" . $user["uid"], $token, 86400*90);

                $user->lastLogin = time();
                $user->ip = Yii::app()->request->getUserHostAddress();
                $user->updateTime = time();
                $user->save();
                $this->errorCode = self::ERROR_NONE;
            }
        }
        return $this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getName()
    {
        return $this->_name;
    }
}