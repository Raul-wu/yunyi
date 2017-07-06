<?php
/**
 * Created by PhpStorm.
 * User: dtl8862
 * Date: 2016/4/13
 * Time: 19:20
 */
class LSUser 
{
    private $loginKey;
    
    private $uid;
    private $name;
    private $corpId;
    private $state;
    private $needResetPwd;
    private $isWeb;
    
    public function __construct()
    {
        $this->loginKey = null;
        
        $this->uid = null;
        $this->name = null;
        $this->corpId = null;
        $this->state = null;
        $this->needResetPwd = null;
        $this->isWeb = null;
    }

    /**
     * @param $user 
     */
    public function setUserInfo($user)
    {
        if (is_object($user))
        {
            $user = get_object_vars($user);
        }

        if (isset($user['loginKey']) && !empty($user['loginKey']))
        {
            $this->loginKey = $user['loginKey'];
        }
       
        if (isset($user['uid']) && $user['uid'] !== null )
        {
            $this->uid = $user['uid'];
        }
        
        if (isset($user['name']) && !empty($user['name']))
        {
            $this->name = $user['name'];
        }

        if (isset($user['corpId']) && $user['corpId'] !== null )
        {
            $this->corpId = $user['corpId'];
        }

        if (isset($user['state']) && $user['state'] !== null )
        {
            $this->state = $user['state'];
        }

        if (isset($user['needResetPwd']) && is_bool($user['needResetPwd']))
        {
            $this->needResetPwd = $user['needResetPwd'];
        }
        
        if (isset($user['isWeb']) && is_bool($user['isWeb']))
        {
            $this->isWeb = $user['isWeb'];
        }
    }
    
    public function set($name, $value)
    {
        if (property_exists($this , $name))
        {
            $this->$name = $value;
        }
    }
    
    public function get($name)
    {
        if (property_exists($this , $name))
        {
            return $this->$name;
        }
        
        return null;
    }
    
}
