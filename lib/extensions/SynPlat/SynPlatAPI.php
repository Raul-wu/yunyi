<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 19:20
 */
include "../../lib/extensions/SynPlat/DES.php";

class SynPlatAPI {
    /**
     * 取得数据
     * @param string $type  查询类型
     * @param string $param 查询参数
     * @return string
     */

    function getData($type, $param) {
        $wsdlURL = "http://gboss.id5.cn/services/QueryValidatorServices?wsdl";
        $partner = "lltzservice";
        $partnerPW = "lltzservice_S03r~6v!";
        $Key = "12345678";
        $iv = "12345678";
        $supportClass = array ("1A020201" => "Name,CardNum" );
        $batchPad = "";

        $DES = new DES ( $Key, $iv );
        try {
            $soap = new SoapClient ( $wsdlURL );
        } catch ( Exception $e ) {
            return "Linkerror";
        }
//var_dump ( $soap->__getTypes () );
//@todo 加密数据
        $partner = $DES->encrypt ( $partner );
        $partnerPW = $DES->encrypt ( $partnerPW );
        $type = $DES->encrypt ( $type );
//先将中文转码
        $param = mb_convert_encoding ( $param, "GBK", "UTF-8" );
        $param = $DES->encrypt ( $param );
        $params = array ("userName_" => $partner, "password_" => $partnerPW,
            "type_" => $type, "param_" => $param );
        ini_set('default_socket_timeout', 500);
//请求查询
        $data = $soap->querySingle( $params );
//@todo 解密数据
        $resultXML = $DES->decrypt ( $data->querySingleReturn );
        $resultXML = mb_convert_encoding ( $resultXML, "UTF-8", "GBK" );
        return $resultXML;
    }
    /**
     * 格式化参数
     * @param array $params 参数数组
     * @return string
     */
    function formatParam($queryType, $params) {
//        include './SynPlat/config.php';
        if (empty ( $supportClass [$queryType] )) {
            return - 1;
        }
        $keys = array ();
        $values = array ();
        foreach ( $params as $key => $value ) {
            $keys [] = $key;
            $values [] = strtoupper ( $value );
        }
        $param = str_replace ( $keys, $values, $supportClass [$queryType] );
        return $param;
    }
    /**
     * 取得生日（由身份证号）
     * @param int $id 身份证号
     * @return string
     */
    function getBirthDay($id) {
        switch (strlen ( $id )) {
            case 15 :
                $year = "19" . substr ( $id, 6, 2 );
                $month = substr ( $id, 8, 2 );
                $day = substr ( $id, 10, 2 );
                break;
            case 18 :
                $year = substr ( $id, 6, 4 );
                $month = substr ( $id, 10, 2 );
                $day = substr ( $id, 12, 2 );
                break;
        }
        $birthday = array ('year' => $year, 'month' => $month, 'day' => $day );
        return $birthday;
    }
/**
 * 取得性别（由身份证号）--可能不准
 * * @param int $id 身份证号
 * @return string
 */
    function getSex($id) {
        switch (strlen ( $id )) {
            case 15 :
                $sexCode = substr ( $id, 14, 1 );
                break;
            case 18 :
                $sexCode = substr ( $id, 16, 1 );
                break;
        }
        if ($sexCode % 2) {
            return "男";
        } else {
            return "女";
        }
    }
    /**
     * 格式化数据
     * @param string $type
     * @param srring $data
     * @return array
     */
    function formatData($type, $data) {
        switch ($type) {
            case "1A020201" :
                $detailInfo = $data ['policeCheckInfos'] ['policeCheckInfo'];
                $info = array (
                    'name' => $detailInfo ['name'],
                    'identitycard' => $detailInfo ['identitycard'],
                    'compStatus' => $detailInfo ['compStatus'],
                    'compResult' => $detailInfo ['compResult'],
                    'policeadd' => $detailInfo ['policeadd'],
//'checkPhoto' => $detailInfo ['checkPhoto'],
                    'idcOriCt2' => $detailInfo ['idcOriCt2'],
                    'resultStatus' => $detailInfo ['compStatus'] );
                break;
            default :
                $info = array (false );
                break;
        }
        return $info;
    }

}
