<?php

/**
 * 金融贷款平台登陆service
 * Created by PhpStorm.
 * User: mmao
 * Date: 2017/6/17
 * Time: 14:54
 */
class LALoginService
{
    const LOG_PREFIX = 'admin.services.LALoginService.';
    public static function login($mobile, $verifyCode, $freeLogin = false)
    {
        //验证手机号码和验证码非空
        if (empty($mobile) || empty($verifyCode)) {
            return false;
        }
//        //从redis中获取验证码
//        $verify = Yii::app()->cache->getRedis()->get('loan_verify' . $mobile);
//        //验证验证码是否正确
//        if($verifyCode != $verify){
//            return false;
//        }

        if (empty($freeLogin)) {
            // 更新用户数据
            $time = time();
            //生成6位随机数
            $randomKey = LALoginService::randomKey(6);
            // Cookies数据更新
            $token = md5($mobile .$verifyCode . $randomKey);
            return array(
                "_id" => $mobile,
                "token" => $token,
                'randomKey' => $randomKey,
                'lastLogin' => $time,
            );
        }
        return false;
    }

    /**
     * 生成以字母和数字组成的随机数
     * @param int $num
     * @return string
     */
    public static function randomKey($num = 4)
    {
        $re = '';
        $s = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        while (strlen($re) < $num) {
            $re .= $s[rand(0, strlen($s) - 1)]; //从$s中随机产生一个字符
        }
        return $re;
    }

    /**
     * 获取验证码
     * @throws LException
     */
    public function actionGetVerify()
    {
        //手机号码
        $mobile = Yii::app()->request->getPost('mobile');
        if (empty($mobile) || !preg_match('/^1\d{10}$/', $mobile)) {
            $this->ajaxReturn(LError::PARAM_ERROR, '非法操作');
        }
//        $exist = LSUserService::isMobileExist('mobile', LUtil::encrypt($mobile));
//        if ($exist) {
//            $this->ajaxReturn(LError::PARAM_ERROR, '手机号已经注册');
//        }

        /** @var LRedisCache $cache */
        $cache = Yii::app()->cache;
        /** @var Redis $redis */
        $redis = $cache->getRedis();
        $key = 'mobile' . date('YmdH') . $mobile;
        if ($redis->get($key) > 2) {
            $this->ajaxReturn(LError::INTERNAL_ERROR, '发送太频繁');
        }
        $verify = rand(100000, 999999);
        $redis->lpush('verify', json_encode(array(
            'phone' => $mobile,
            'content' => base64_encode('您的验证码：' . $verify . '，请在5分钟内完成验证。如非本人操作，请忽略本短信。'),
            'sendTimes' => 1)));
        $redis->incr($key);
        $redis->set('loan_verify' . $mobile, $verify, 900);
        $this->ajaxReturn(LError::SUCCESS);
    }

    /**
     * 判断是否登录
     * @return bool
     */
    public static function isLogin()
    {
        $_id = !empty(Yii::app()->request->cookies['USERID']) ? Yii::app()->request->cookies['USERID']->value : null;
        $token = !empty(Yii::app()->request->cookies['TOKEN']) ? Yii::app()->request->cookies['TOKEN']->value : null;
        $randomKey = !empty(Yii::app()->request->cookies['randomKey']) ? Yii::app()->request->cookies['randomKey']->value : null;
        if (empty($_id) || empty($token) || empty($randomKey)) {
            return false;
        }
        return true;
    }
}