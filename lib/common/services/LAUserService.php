<?php
class LAUserService
{
    const LOG_PREFIX = 'admin.services.LAUserService.';

    public static function login($username, $password)
    {
        $identity = new LUserIdentity($username, $password);
        $identity->authenticate();
        if ($identity->errorCode === LUserIdentity::ERROR_NONE)       {//如果没有错误，则进行登陆
            Yii::app()->user->login($identity, 0);
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function addUser($userData = array())
    {
        $trans = Yii::app()->financeLeaseDB->beginTransaction();
        try {
            $clientModel = new LAClientEnterpriseModel();
            $userData['appId'] = $clientModel->primaryKey;
            $userData['isSuperAdmin'] = 1;

            $userModel = new LSUserModel();
            $userData['salt'] = $userModel->setSalt();
//            $userData['password'] = $userModel->hashPassword($userData['password']);
            $userModel->setAttributes($userData, false);

            if (!$userModel->save()) {
                Yii::log(sprintf("Create LSUserModel userData[%s] failed.", json_encode($userData)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                $trans->rollback();
                return false;
            }

            $trans->commit();
            return true;
        } catch (Exception $e) {
            $trans->rollback();
            Yii::log("failed to addUser msg[{$e->getMessage()}]",
                CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
    }

}