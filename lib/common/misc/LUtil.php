<?php

/**
 * Created by PhpStorm.
 */
class LUtil
{
    const LOG_PREFIX = 'common.misc.LUtil.';
    const PERPAGE = 15;
    const CLIENTID_WEB = 0;
    const CLIENTID_IOS = 1;
    const CLIENTID_ANDROID = 2;
    const CLIENTID_H5 = 3;
    //======================begin 定投扣款类型==========================
    const HS_AIP_WITHHOLD_MONTH = 0;//定投-月扣
    const HS_AIP_WITHHOLD_WEEK = 1;//定投-周扣
    const HS_AIP_WITHHOLD_DAY = 2;//定投-日扣

    //======================end 定投扣款类型======================================

    public static function formatMoney($number, $beforeMoney = "", $afterMoney = "", $unitWrapper = "")
    {
        $unit = "万元";
        if (abs($number) < 1000000)
        {
            $money = $number / 100;
            $unit = "元";
        }
        else
        {
            $money = $number / 1000000;
        }

        if ($unitWrapper)
        {
            $unit = "<{$unitWrapper}>{$unit}</{$unitWrapper}>";
        }

        return "{$beforeMoney}{$money}{$afterMoney}{$unit}";
    }

    public static function formatAmt($number, $beforeMoney = "", $afterMoney = "", $unitWrapper = "")
    {

            $money = $number / 100;



        if ($unitWrapper)
        {
            $unit = "<{$unitWrapper}>{$unit}</{$unitWrapper}>";
        }

        return "{$beforeMoney}{$money}{$afterMoney}";
    }

    public static function getShortLink(&$tinyLink, $url, $serverUrl, $proxy = "")
    {
        $ch = curl_init();
        if (!empty($proxy))
        {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($ch, CURLOPT_URL, $serverUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = array('url' => $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $strRes = curl_exec($ch);
        curl_close($ch);
        $arrResponse = json_decode($strRes, true);
        if ($arrResponse['status'] == 0)
        {
            $tinyLink = $arrResponse["tinyurl"];
            return true;
        }
        else
        {
            return false;
        }
    }

    /**计算非周末的交易
     * @param $paramDay
     */
    private static function calcMarketDay($paramDay)
    {
        $calcDay = $paramDay;
        $code = (int)date('w', $calcDay);
        if ($code === 0 || $code === 6)
        {//周末
            if ($code === 6)
            {
                $format = '+2 days';
            }
            else
            {
                $format = '+1 days';
            }
            $calcDay = strtotime($format, $calcDay);
        }
        return $calcDay;
    }

    /**
     * 根据参数指定日期获取一个交易日，当该日期为交易日时返回交易日为参数日期，否则返回该日期之后最近一个交易日
     * @param int $paramDay 指定日期
     * @return int 交易日
     */
    private static function getMarketDay($paramDay)
    {
        //生成假日数据池
        $ret = LHolidayService::getHolidayByDate($paramDay);
        $calcDay = self::calcMarketDay($paramDay);
        while (isset($ret[(string)date('Ymd', $calcDay)]))
        {//在节假日数据池中找到相应日期
            //提取下一个工作日期
            $calcDay = self::calcMarketDay(strtotime('+1 days', $calcDay));
        }
        return $calcDay;
    }


    /**计算按月扣款的T+1日
     * @param $paramT1
     */
    private static function calcT1ByMonth($paramLimit, $paramDate)
    {
        $t1Time = strtotime(sprintf('+%d days', $paramLimit), $paramDate);
        $year = (int)date('Y', $t1Time);
        $month = (int)date('m', $t1Time);
        $day = (int)date('d', $t1Time);
        if ($day > 28)
        {
            ++$month;
            if ($month > 12)
            {
                $month = 1;
                ++$year;
            }
        }
        return strtotime(sprintf('%d-%d-%d', $year, $month, $day));

    }

    /**根据给定的一组扣款日计算定投下次一组扣款日
     * @param array $paramPayDay 扣款日/星期数($paramRule=HS_AIP_WITHHOLD_WEEK，星期数)
     * @param int $paramRule 扣款规则(月(default)=HS_AIP_WITHHOLD_MONTH、周=HS_AIP_WITHHOLD_WEEK、日=HS_AIP_WITHHOLD_DAY)
     * @param int $paramLimit 扣款频率,default=1
     * @return array 返回下一组扣款日期
     * @throws Exception
     */
    public static function getAIPWithholdByArray($paramPayDay, $paramRule = LUtil::HS_AIP_WITHHOLD_MONTH, $paramLimit = 1)
    {
        $dayArr = array();
        foreach ($paramPayDay as $day)
        {
            $dayArr[$day] = self::getAIPWithhold($day, $paramRule, $paramLimit);
        }
        return $dayArr;
    }

    /**根据给定扣款日计算定投下次扣款日
     * @param int $paramPayDay 扣款日/星期数($paramRule=HS_AIP_WITHHOLD_WEEK，星期数)
     * @param int $paramRule 扣款规则(月(default)=HS_AIP_WITHHOLD_MONTH、周=HS_AIP_WITHHOLD_WEEK、日=HS_AIP_WITHHOLD_DAY)
     * @param int $paramLimit 扣款频率,default=1
     * @param bool $paramIsFirst 是否是首次扣款
     * @return int 返回下一个扣款日期
     * @throws Exception
     */
    public static function getAIPWithhold($paramPayDay, $paramRule = LUtil::HS_AIP_WITHHOLD_MONTH, $paramLimit = 1, $paramIsFirst = true)
    {
        $curDate = time();

        $year = (int)date('Y', $curDate);
        $month = (int)date('m', $curDate);
        $hour = (int)date('H', $curDate);
        $limit = $hour >= 15 ? 2 : 1;
        $payDate = $curDate;
        if ($paramRule == LUtil::HS_AIP_WITHHOLD_MONTH)
        {
            //获取t+1日
            $payDate = self::calcT1ByMonth($limit, $payDate);
            //获取交易日
            $payDate = LUtil::getMarketDay($payDate);
        }
        else
        {
            $payDate = strtotime(sprintf('+%d days', $limit), $payDate);
            //获取交易日
            $payDate = LUtil::getMarketDay($payDate);
        }


        $resLimit = $paramIsFirst ? ($paramLimit == 1 ? 1 : $paramLimit-1) : $paramLimit;
        switch ($paramRule)
        {
            case self::HS_AIP_WITHHOLD_MONTH://月扣款
            {
                //生成本月扣款日
                $curPayDate = strtotime(sprintf('%d-%d-%d', $year, $month, $paramPayDay));
                if ($payDate > $curPayDate)
                {
                    //取下个月
                    $month += $resLimit;
                    if ($month > 12)
                    {
                        ++$year;
                        $month -= 12;
                    }
                    $payDate = strtotime(sprintf('%d-%d-%d', $year, $month, $paramPayDay));;
                }
                else
                {
                    $payDate = $curPayDate;
                }
                break;
            }
            case self::HS_AIP_WITHHOLD_WEEK://周扣款
            {
                //获取t+1的周数
                $week = date('w', $payDate);
                $limit = 0;
                if ($week > $paramPayDay)
                {//取下周
                    $payDate = strtotime(sprintf('-%d days', $week - $paramPayDay), $payDate);
                    $limit = 7 * $resLimit;
                }
                else
                {
                    $limit = $paramPayDay - $week;
                }
                $payDate = strtotime(sprintf('+%d days', $limit), $payDate);
                break;
            }
            case self::HS_AIP_WITHHOLD_DAY://日扣款
            {
//                $payDate = strtotime('+1 days', $paramPayDay);;
                break;
            }
        }
        return $payDate;
    }

    public static function formatPrice($numberE6, $unit = 1)
    {
        $price = "";
        switch ($unit)
        {
            case 1 :
                $price = number_format($numberE6 / 1000000) . '元';
                break;
            case 2 :
                $price = number_format($numberE6 / 10000000000) . '万元';
                break;
        }
        return $price;
    }

    /**
     * 格式化数值，可以指定小数位数，小数部分可以指定wrapper，可以强制带正号
     * @param float $value 实际数值
     * @param bool $forcePrefix 是否强制前缀，默认false
     * @param int $decimal 小数位数，默认2位
     * @param string $decimalWrapper 小数部分修饰标签，不使用标签传false，默认em
     * @return string
     */
    public static function formatAssetValue($value, $forcePrefix = false, $decimal = 2, $decimalWrapper = 'em')
    {
        $ret = '';

        if ($forcePrefix && $value > 0)
        {
            $ret .= '+';
        }

        $number = number_format($value, $decimal);

        if ($decimalWrapper)
        {
            list($intPart, $decimalPart) = explode('.', $number);
            $ret .= "{$intPart}.<{$decimalWrapper}>{$decimalPart}</{$decimalWrapper}>";
        }
        else
        {
            $ret .= $number;
        }

        return $ret;
    }

    public static function formatExpire($time)
    {
        $time = intval($time);
        if (!$time || $time < 0)
        {
            $time = 0;
        }
        $sec = $time % 60;
        $min = intval(floor($time / 60)) % 60;
        $hour = intval(floor($time / 3600)) % 24;
        $day = intval(floor($time / 86400));

        return ($day ? "{$day}天" : '') . ($hour ? "{$hour}小时" : '') . ($min ? "{$min}分钟" : '') . ($sec ? "{$sec}秒" : '');
    }

    public static function formatExpireLite($time)
    {
        $time = intval($time);
        if (!$time || $time < 0)
        {
            $time = 0;
        }
        $sec = $time % 60;
        $min = intval(floor($time / 60)) % 60;
        $hour = intval(floor($time / 3600)) % 24;
        $day = intval(floor($time / 86400));

        return ($day ? "{$day}天" : ($hour ? "{$hour}小时" : ($min ? "{$min}分钟" : ($sec ? "{$sec}秒" : ''))));
    }

    public static function cn_truncate($string, $strlen = 20, $etc = '...', $keep_first_style = false, $charset = 'utf-8')
    {
        $slen = mb_strlen($string, $charset);
        if ($slen > $strlen + 2)
        {
            $tstr = mb_substr($string, 0, $strlen, $charset);
            $matches = array();
            $mcount = preg_match_all("/[\x{4e00}-\x{9fa5}]/u", $tstr, $matches);
            unset($matches);
            $offset = ($strlen - $mcount) * 0.35;//0;//intval((3*mb_strlen($tstr,$charset)-strlen($tstr))*0.35);
            return preg_replace('/\&\w*$/', '', mb_substr($string, 0, $strlen + $offset, $charset)) . $etc;
        }
        else
        {
            return $string;
        }
    }

    public static function isLAN($ip)
    {
        $ip = ip2long($ip);
        $net_a = ip2long('10.0.0.0') >> 24; //A类网预留ip的网络地址 10.0.0.0 ～ 10.255.255.255
        $net_b = ip2long('172.16.0.0') >> 20; //B类网预留ip的网络地址 172.16.0.0 ～ 172.31.255.255
        $net_c = ip2long('192.168.0.0') >> 16; //C类网预留ip的网络地址 192.168.0.0 ～ 192.168.255.255

        return $ip >> 24 === $net_a || $ip >> 20 === $net_b || $ip >> 16 === $net_c;
    }

    public static function setSession($sessionID)
    {
        if ($sessionID && is_string($sessionID))
        {
            /** @var $session CCacheHttpSession */
            $session = Yii::app()->session;

            if ($session->isStarted)
            {
                //Yii::log("Close current session", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
                $session->close();
            }
            //Yii::log("Reopen session by specified sessionId", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            $session->sessionID = $sessionID;
            $session->open();
        }
    }

    public static function redirectIndex()
    {
        Yii::app()->request->redirect('/product/index');
    }

    public static function redirectService()
    {
        Yii::app()->request->redirect('/service/index');
    }

    public static function goAuth()
    {
        $user = LUserService::getLoginUser();
        if (LUserService::isCorpRequiredVerify($user))
        {
            Yii::app()->request->redirect(Yii::app()->createUrl('profile/bindEmail'));
        }
        else
        {
            Yii::app()->request->redirect(Yii::app()->createUrl('/product/index'));
        }
    }

    public static function checkAllowRedirect($url)
    {
        if ($url && is_string($url))
        {
            if ($host = parse_url($url, PHP_URL_HOST))
            {
                $allowDomains = Yii::app()->params['allowDomains'];
                if (!$allowDomains)
                {
                    return true;
                }
                else
                {
                    foreach ($allowDomains as $domain)
                    {
                        if (strcasecmp(substr($host, strripos($host, $domain)), $domain) == 0)
                        {
                            return true;
                        }
                    }
                    Yii::log("Redirect denied domain not allowed: url[$url]", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
                    return false;
                }
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * 计算日期差
     * @param $day1
     * @param $day2
     * @return int
     */
    public static function dayDiff($day1, $day2)
    {
        return intval((strtotime(date('Y-m-d', $day2)) - strtotime(date('Y-m-d', $day1))) / 86400);
    }

    public static function isMobile($mobile)
    {
        return $mobile && preg_match('/^1\d{10}$/', $mobile) === 1;
    }

    public static function isQQUin($qqUin)
    {
        return $qqUin && preg_match('/^[1-9]\d{4,12}$/', $qqUin) === 1;
    }

    public static function isBankMobile($mobile)
    {
        return $mobile && preg_match('/^1[34578]\d{9}$/', $mobile) === 1;
    }

    public static function isEmail($email)
    {
        return $email && preg_match("/^[a-zA-Z0-9.!#$%&'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/", $email) === 1;
    }

    public static function checkIdcardNo($idcardNo)
    {
        $city = array(
            11 => "北京",
            12 => "天津",
            13 => "河北",
            14 => "山西",
            15 => "内蒙古",
            21 => "辽宁",
            22 => "吉林",
            23 => "黑龙江",
            31 => "上海",
            32 => "江苏",
            33 => "浙江",
            34 => "安徽",
            35 => "福建",
            36 => "江西",
            37 => "山东",
            41 => "河南",
            42 => "湖北",
            43 => "湖南",
            44 => "广东",
            45 => "广西",
            46 => "海南",
            50 => "重庆",
            51 => "四川",
            52 => "贵州",
            53 => "云南",
            54 => "西藏",
            61 => "陕西",
            62 => "甘肃",
            63 => "青海",
            64 => "宁夏",
            65 => "新疆",
            71 => "台湾",
            81 => "香港",
            82 => "澳门",
            91 => "国外",
        );

        $pass = true;

        if (!$idcardNo || !preg_match('/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i', $idcardNo))
        {
            // 身份证号格式错误
            $pass = false;
        }
        else if (!isset($city[substr($idcardNo, 0, 2)]))
        {
            // 地址编码错误
            $pass = false;
        }
        else
        {
            // 18位身份证需要验证最后一位校验位
            if (strlen($idcardNo) == 18)
            {
                return true;
                // ∑(ai×Wi)(mod 11)
                // 加权因子
                $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                // 校验位
                $parity = array(1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2);
                $sum = 0;
                for ($i = 0; $i < 17; $i++)
                {
                    $ai = $idcardNo[$i];
                    $wi = $factor[$i];
                    $sum += $ai * $wi;
                }
                $last = $parity[$sum % 11];
                if ($last != $idcardNo[17])
                {
                    // 校验位错误
                    $pass = false;
                }
            }
        }
        return $pass;

    }

    /**
     * 获取性别
     * @param $idCardNo
     * @param int $return
     * @return int|string
     */
    public static function getGender($idCardNo, $return = 1)
    {
        $gender = 1;
        if (!empty($idCardNo) && strlen($idCardNo) == 18)
        {
            $genderValue = substr($idCardNo, -2, 1);
            if ($genderValue % 2 == 0)
            {
                $gender = 0;
            }
        }
        else if (!empty($idCardNo) && strlen($idCardNo) == 15)
        {
            $genderValue = substr($idCardNo, -1, 1);
            if ($genderValue % 2 == 0)
            {
                $gender = 0;
            }
        }
        else
        {
            return $return ? '' : 1;
        }

        if ($return == 1)
        {
            return $gender == 1 ? '男' : '女';
        }
        else
        {
            return $gender;
        }

    }

    public static function getUrlHost($url)
    {
        $host = @parse_url(trim(strtolower($url)), PHP_URL_HOST);
        if ($host)
        {
            return $host;
        }

        return null;
    }

    public static function pwdStrength($str)
    {
        if (!preg_match('/^\S{6,20}$/', $str))
        {
            return 0;
        }

        $length = strlen($str);
        if ($length < 6)
        {
            return 0;
        }
        $m = 0;
        for ($i = 0; $i < $length; $i++)
        {
            $ascii = ord($str[$i]);
            if ($ascii >= 48 && $ascii <= 57)
            {
                $m |= 1;
            }
            elseif ($ascii >= 65 && $ascii <= 90)
            {
                $m |= 2;
            }
            elseif ($ascii >= 97 && $ascii <= 122)
            {
                $m |= 4;
            }
            else
            {
                $m |= 8;
            }
        }

        $strong = 0;
        for ($i = 0; $i < 4; $i++)
        {
            if ($m & 1)
            {
                $strong++;
            }
            $m >>= 1;
        }

        return $strong;
    }

    /**
     * 获取服务器ip
     *
     * @return string
     */
    public static function svrIp()
    {
        //如果服务器访问自身，SERVER_ADDR是127.0.0.1
        if (!empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== "127.0.0.1")
        {
            return $_SERVER['SERVER_ADDR'];
        }
        return exec("hostname -I | awk '{print \$1}'");
        //机房中的机器不可能没有eth1或eth0吧，一般情况下eth0是内网ip
//		$ifconfig = shell_exec('/sbin/ifconfig');
//		preg_match('/addr:([\d\.]+)/', $ifconfig, $match);
//		return $match[1];
    }

    public static function statsLog($msg, $level, $category)
    {
        if (php_sapi_name() != 'cli' && isset($_SERVER['HTTP_USER_AGENT']) && ($_SERVER['HTTP_USER_AGENT'] == 'Yijie_Automation'))
        {
            return;
        }

        Yii::log($msg, $level, $category);
    }

    public static function maskMobile($mobile)
    {
        return empty($mobile) ? '' : substr($mobile, 0, 3) . ' **** ' . substr($mobile, 7, 4);
    }

    public static function maskIdCardNo($idCardNo)
    {
        return empty($idCardNo) ? '' : substr($idCardNo, 0, 3) . ' ********** ' . substr($idCardNo, -4);
    }

    public static function maskBankcardNo($cardNo, $prefix = '尾号 ')
    {
        $str = substr($cardNo, -4);
        if (empty($str)) {
            return '--';
        }
        return $prefix . substr($cardNo, -4);
    }

    public static function maskNameAnonymous()
    {
        return '匿名';
    }

    public static function maskName($name)
    {
        $len = mb_strlen($name, 'utf-8');
        return ($len <= 1) ? '**' : mb_substr($name, 0, 1, 'utf-8') . '**';
    }

    public static function maskNameNew($name)
    {
        $len = mb_strlen($name, 'utf-8');
        return ($len <= 1) ? '**' : mb_substr($name, 0, 1, 'utf-8') . '*';
    }

    public static function maskNameGender($name, $idCardNo)
    {
        return (self::getGender($idCardNo, 0)) == 1 ? mb_substr($name, 0, 1, 'utf-8') . '先生' : mb_substr($name, 0, 1, 'utf-8') . '女士';
    }

    public static function maskEmail($email)
    {
        return preg_replace('/(?<=\w)[^@]+(?=\w@)/', '****', $email);
    }

    public static function formatShare($shareE6, $retZero = false)
    {
        if ($shareE6 == 0 && !$retZero)
        {
            return '待披露';
        }
        return number_format(floor($shareE6 / 10000) / 100, 2);
    }

    /**
     * 获取指定时间戳是星期几(如果不指定时间，则获取当天)
     * @param int $time
     * @return string
     */
    public static function getWeekDay($time = null)
    {
        $time = $time ? $time : time();
        return '星期' . mb_substr("日一二三四五六", date("w", $time), 1, "utf-8");
    }


    /**
     * 判断两个时间是否跨年
     * @param $firstTime
     * @param null | $secondTime 如果为空则默认为当前时间，即与当前时间比较是否跨年
     * @return bool
     */
    public static function isCrossYear($firstTime, $secondTime = null)
    {
        $secondTime = $secondTime ? $secondTime : time();
        return date('Y', $firstTime) != date('Y', $secondTime);
    }

    /**
     * @param int $profitAndLoss
     * @return string
     */
    public static function formatProfitAndLoss($profitAndLoss)
    {
        return ($profitAndLoss > 0 ? '+' : '') . number_format($profitAndLoss, 2);
    }

    public static function getProfitAndLossClass($formatNum)
    {
        return $formatNum == 0 ? '' : (strpos($formatNum, '-') === 0 ? 'green' : 'red');
    }

    public static function formatSharePrice($sharePriceE6)
    {
        return number_format(floor($sharePriceE6 / 100) / 10000, 4);
    }

    public static function formatRate($rateE6, $before = '', $after = '%', $middle = '')
    {
        return $before . sprintf('%.1f', floor($rateE6 / 1000) / 10) . $middle . $after;
    }

    /**
     * @param $incomeRateE6
     * @param $minIncomeRateE6
     * @param string $beforeMoney
     * @param string $afterMoney
     * @param string $unitWrapper
     * @return string
     */
    public static function formatIncomeRate($incomeRateE6, $minIncomeRateE6 = 0, $beforeMoney = "", $afterMoney = "", $unitWrapper = "")
    {
        /** @var LProductCoreModel $product */
        if (($incomeRateE6 == 0 || $minIncomeRateE6 < -100))
        {
            $str = "浮动";
            if ($unitWrapper)
            {
                $str = $str . "<{$unitWrapper}>&nbsp;</{$unitWrapper}>";
            }
            return $str;
        }

        $unit = "%";
        if ($unitWrapper)
        {
            $unit = "<{$unitWrapper}>{$unit}</{$unitWrapper}>";
        }

        if (($incomeRateE6 != $minIncomeRateE6))
        {
            $min = self::formatRate($minIncomeRateE6, $beforeMoney, $afterMoney);
            $max = self::formatRate($incomeRateE6, $beforeMoney, $afterMoney) . $unit;
            $ret = $min . '~' . $max;
        }
        else
        {
            $ret = self::formatRate($incomeRateE6, $beforeMoney, $afterMoney) . $unit;
        }

        return $ret;
    }


    const ENUM_DATEDIFF_DAY = 0;
    const ENUM_DATEDIFF_MONTH = 1;
    const ENUM_DATEDIFF_ONLY_MONTH = 2;
    const ENUM_DATEDIFF_MAXDAY = 100;

    /**
     * 获取产品期限
     * @param int $endTime 收益结束时间
     * @param int $beginTime 收益开始时间
     * @param int $type 展示类型（天数，月数）
     * @param string $numWrapper
     * @param string $unitWrapper
     * @param bool $returnString
     * @return array|string
     */
    public static function getPeriod($endTime, $beginTime, $type = self::ENUM_DATEDIFF_DAY, $numWrapper = '', $unitWrapper = '', $returnString = true)
    {
        if ($endTime == 0)
        {
            $str = "非固定期限";
            if ($unitWrapper)
            {
                $str = $str . "<{$unitWrapper}>&nbsp;</{$unitWrapper}>";
            }

            if ($returnString)
            {
                return $str;
            }
            else
            {
                return array('days' => '', 'unit' => $str);
            }
        }

        $days = self::dayDiff($beginTime, $endTime);

        $month = false;
        switch ($type)
        {
            case self::ENUM_DATEDIFF_MONTH:
                if ($days > self::ENUM_DATEDIFF_MAXDAY)
                {
                    $month = true;
                    $days = floor($days / 365) * 12 + round($days % 365 / 30);
                }
                break;
            case self::ENUM_DATEDIFF_ONLY_MONTH:
                $month = true;
                $days = floor($days / 365) * 12 + round($days % 365 / 30);
                break;
        }

        if ($numWrapper)
        {
            $days = "<{$numWrapper}>$days</{$numWrapper}>";
        }

        $unit = "个月";
        if (!$month)
        {
            $unit = "天";
        }

        if ($unitWrapper)
        {
            $unit = "<{$unitWrapper}>$unit</{$unitWrapper}>";
        }

        if ($returnString)
        {
            return "{$days}{$unit}";
        }
        else
        {
            return array('days' => $days, 'unit' => $unit);
        }
    }

    public static function getDateByPeriod($timestamp, $period)
    {
        return strtotime(" -" . $period . " days", $timestamp);
    }

    public static function getTimeChineseMeridian($timestamp)
    {
        return (date('A', $timestamp) === 'AM' ? '上午' : '下午');
    }

    public static function arraySort($arr, $key, $type = 'ASC')
    {
        $array = $newArray = array();
        foreach ($arr as $k => $v)
        {
            // 把二维数组的 index 作为key值 ,从0开始
            $array[$k] = $v[$key];
        }
        if ($type == 'ASC')
        {
            asort($array);
        }
        else
        {
            arsort($array);
        }
        reset($array);
        $tmp = 0;
        foreach ($array as $k => $v)
        {
            $tmp++;
            $newArray[$tmp] = $arr[$k];
        }

        return $newArray;
    }


    private function customSort($a, $b)
    {
        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }

    /**
     * @return array
     */
    public static function getQualifiedInvestorPrecondition()
    {
        return array(
            '1. 金融资产不低于300万元',
            '2. 最近三年个人年均收入不低于50万元'
        );
    }

    /**
     * 获取剪裁后的七牛图片
     * @param $url
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function getImageBySize($url, $width = 120, $height = 120)
    {
        if (empty($url) || (empty($width) && empty($height)))
        {
            return $url;
        }

        //https://dn-lcs-static.qbox.me/uicon/o_19ibcdkqrflhlh319i8108fkv9.jpg?imageMogr2/auto-orient/strip/thumbnail/!1088x1630/crop/!500x500a370a55
        $pattern = "/thumbnail\/!(\d*?)x(\d*?)\/crop\/!(\d*?)x(\d*?)a(\d*?)a(\d*)/i";
        preg_match($pattern, $url, $result);

        if (count($result) == 7 && !empty($result[1]) && !empty($result[2]) && !empty($result[3]) && !empty($result[4]))
        {
            $scole = $width / $result[3];
            $nurl = explode('thumbnail', $url);

            $url = $nurl[0] . 'thumbnail/!' . round($scole * $result[1]) . 'x' . round($scole * $result[2]) . '/crop/!' . round($scole * $result[3]) . 'x' . round($scole * $result[4]) . 'a' . round($scole * $result[5]) . 'a' . round($scole * $result[6]);
        }

        return $url;
    }


    /**
     * 将18位身份证号转为15位；其他的则直接返回
     *
     * @param $idCard
     * @return string
     */
    public static function convertIdCardNo($idCard)
    {
        if (18 == strlen($idCard))
        {
            $idCard = substr_replace($idCard, '', 6, 2);
            $idCard = substr_replace($idCard, '', -1, 1);
        }

        return $idCard;
    }

    /**
     * 获取唯一身份标识，用于标识唯一访客
     * @return string
     */
    public static function getUniqueIdentifier()
    {
        $userIP = Yii::app()->request->userHostAddress;
        return uniqid(md5(rand(0, 10000) . $userIP), true);
    }


    /**
     * 数字转大写（仅支持100以下）
     * @param $num
     * @return string
     */
    public static function intToCn($num)
    {
        if (!is_numeric($num) || $num >= 100)
        {
            return $num;
        }

        $str = '';
        $ints = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
        if (isset($ints[$num]))
        {
            return $ints[$num];
        }
        else
        {
            $floor = floor($num / 10);
            if ($floor > 1)
            {
                $str .= $ints[$floor];
            }

            if ($floor != 0)
            {
                $str .= '十';
            }

            $remainder = $num % 10;
            if ($remainder > 0)
            {
                $str .= $ints[$remainder];
            }
        }
        return $str;
    }

    /**
     * 获取字符长度
     * @param $str
     * @return float
     */
    public static function length($str)
    {
        return (strlen($str) + mb_strlen($str, 'UTF8')) / 2;
    }


    //获取随机字符串
    public static function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++)
        {
            $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }

    //根据身份证获取年龄
    public static function getAgeByIdCard($idCard)
    {
        if (!$idCard || !self::checkIdcardNo($idCard)) return false;

        $year = intval(substr($idCard, 6, 4));
        $month = intval(substr($idCard, 10, 2));
        $day = intval(substr($idCard, 12, 2));
        list($nowYear, $nowMonth, $nowDay) = explode("-", date("Y-m-d", time()));
        $age = intval((int)$nowYear - (int)$year);
        if ($age < 0) return false;
        $diffMonth = intval((int)$nowMonth - (int)$month);
        $diffDay = intval((int)$nowDay - (int)$day);

        if ($diffMonth < 0 || ($diffMonth == 0 && $diffDay < 0))
        {
            $age--;
        }
        if ($age < 0) return false;
        return $age;
    }

    /**
     * 格式化收益图表的Y轴
     * @param $maxRate , 最大收益率
     * @param $dotCount , Y轴点数量,不包括原点
     * @param $decimal ,  小数点位数
     * @return array
     */
    public static function formatRateChartY($maxRate, $dotCount, $decimal = 2)
    {
        $ret = array();
        if ($dotCount <= 1)
        {
            $ret[] = $maxRate;
        }
        else
        {
            for ($i = 0; $i < $dotCount; $i++)
            {
                $ret[] = sprintf("%.{$decimal}f", ($dotCount - $i) * $maxRate / ($dotCount - 1));
            }
        }
        $ret[] = 0; //起点
        return $ret;
    }

    public static function getBirthdayByIdCard($idCard)
    {
        if (!$idCard) return false;

        return substr($idCard, 6, 8);
    }

    //0:女 1：男 注意
    public function getSexByIdCard($idCard)
    {
        if (!$idCard) return false;

        return substr($idCard, -2, 1) % 2;
    }

    //根据证件类型，证件号来返回恒生国籍id
    public static function getNationIdByCard($identityNo, $identityType)
    {
        if (!$identityNo || !$identityType) return false;

        if ($identityType == LUserModel::IDCARD_TYPE_IDENTIFICATION)
        {
            return 156;//中国
        }
        else if ($identityType == LUserModel::IDCARD_TYPE_HK_PASSPORT) //港台
        {
            if (strpos(strtolower($identityNo), 'h') !== false) //香港
            {
                return 344;
            }
            else if (strpos(strtolower($identityNo), 'm') !== false)//澳门
            {
                return 446;
            }
            else //台湾
            {
                return 158;
            }

        }
        else //没有默认是中国
        {
            return 156;
        }
    }


    /**
     * 获取数组中的某个值
     *
     * @param $arr
     * @param $key
     * @param string $defaultValue
     * @return string
     */
    public static function getArrValue($arr, $key, $defaultValue = '')
    {
        if (isset($arr[$key]))
        {
            return $arr[$key];
        }
        else
        {
            return $defaultValue;
        }
    }

    /**
     * 调整浮点数的精度
     *
     * @param double $number 原始数据
     * @param int $precision 精度整数
     * @param int $type 0 四舍五入 -1 舍尾 1进一
     * @return float
     * @throws LException
     */
    public static function adjustPrecision($number, $precision, $type = 0)
    {
        $number = floatval($number);
        $precision = intval($precision);
        if ($type == 0)
        {
            return round($number, $precision);
        }

        $pointPart = explode(".", strval($number));
        if (!isset($pointPart[1]) || strlen($pointPart[1]) <= $precision)
        {
            return $number;
        }

        $decimalDigit = pow(10, $precision);

        if ($type == -1)
        {
            return floor($number * $decimalDigit) / $decimalDigit;
        }
        else if ($type == 1)
        {
            return ceil($number * $decimalDigit) / $decimalDigit;
        }
        else
        {
            throw new LException(LError::PARAM_ERROR, " type 类型不合法 ");
        }
    }

    public static function ping($host, $port, $timeout)
    {
        $responding = 1;
        $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!is_resource($fp))
        {
            $responding = 0;
        }
        else
        {
            fclose($fp);
        }

        if ($responding)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

	public static function encrypt($plainText, $spec = 'pJ1bUk~dKJvKPv9BzbZ7x4qlKo3g6iz~')
	{
		if ($spec && $plainText)
		{
			if (function_exists('yj_encrypt'))
			{
				return yj_encrypt($spec, $plainText);
			}
			else
			{
				return LAESHelper::getInstance()->initKey($spec)->encrypt($plainText);
			}
		}
		else
		{
			return $plainText;
		}
	}

	public static function decrypt($cipherText, $spec = 'pJ1bUk~dKJvKPv9BzbZ7x4qlKo3g6iz~')
	{
		if ($spec && $cipherText)
		{
			if (function_exists('yj_decrypt'))
			{
				return yj_decrypt($spec, $cipherText);
			}
			else
			{
				return LAESHelper::getInstance()->initKey($spec)->decrypt($cipherText);
			}
		}
		else
		{
			return $cipherText;
		}
	}

    public static function date($date, $period, $period_type = 1) {
        $date = date('Y-m-d', $date);
        if ($period_type == 1) {
            $original_day = date('d', strtotime($date));
            $result_date = strtotime($date . ' +' . $period . 'months');
            $year = date('Y', $result_date);
            $month = date('m', $result_date);
            $day = date('d', $result_date);
            if ($original_day != $day) {
                $month = $month - 1;
                $month = strlen($month) == 1 ? '0' . $month : $month;
                $day = date('t', strtotime($year . '-' . $month));
            }
            return $year . '-' . $month . '-' . $day;
        } else {
            return date('Y-m-d', strtotime($date . ' +' . $period . ' days'));
        }
    }

    public static function isMobileDevice() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }
}