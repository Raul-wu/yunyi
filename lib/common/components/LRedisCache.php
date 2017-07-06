<?php

/**
 * To use LRedisCache as the cache application component, configure the application as follows,
 * <pre>
 * array(
 *     'components'=>array(
 *         'cache'=>array(
 *             'class'=>'LRedisCache',
 *             'servers'=>array(
 *                 array(
 *                     'host'=>'server1',
 *                     'port'=>11211,
 *                     'weight'=>60,
 *                 ),
 *                 array(
 *                     'host'=>'server2',
 *                     'port'=>11211,
 *                     'weight'=>40,
 *                 ),
 *             ),
 *         ),
 *     ),
 * )
 * </pre>
 *
 * @property RedisArray $redis The RedisArray instance used by this component.
 * @property array $servers List of redis server configurations.
 *
 * @package common.caching
 */
class LRedisCache extends CRedisCache
{
    const LOG_PREFIX = 'common.components.LRedisCache.';

    /**
     * @var RedisArray the redis array instance
     */
    private $_cache = null;
    /**
     * @var array list of redis server configurations
     */
    protected $_servers = array();

    protected $_options = array();

    protected $_ping = false;

    /**
     * Initializes this application component.
     * This method is required by the {@link IApplicationComponent} interface.
     * It creates the redis array instance.
     * @throws CException if extension isn't loaded
     */
    public function init()
    {
        parent::init();
        $this->getRedis();
    }

    /**
     * @return RedisArray this redis array instance used by this component.
     * @throws CException if extension isn't loaded
     */
    public function getRedis()
    {
        if ($this->_cache === null) {
            if (!extension_loaded('redis')) {
                Yii::log("LRedisCache requires PHP redis extension to be loaded", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
                throw new LException(LError::INTERNAL_ERROR);
            }
            $servers = $this->getServers();
            if (count($servers)) {
                $this->_cache = new RedisArray($servers, $this->getOptions());

            } else {
                $this->_cache = new RedisArray(array('127.0.0.1:6379'), $this->getOptions());
            }
        }

        return $this->_cache;
    }

    /**
     * @return array list of redis server configurations. Each element is a host:port pair string.
     */
    public function getServers()
    {
        return $this->_servers;
    }

    /**
     * @param array $config list of redis server configurations. Each element must be an array
     * with the following keys: host, port
     */
    public function setServers(array $config)
    {
        foreach ($config as $c) {
            $this->_servers[] = "{$c['host']}:{$c['port']}";
        }
    }

    /**
     * @return array options for connecting to redis server
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @param array $options options for connecting to redis server.
     * @see https://github.com/nicolasff/phpredis/blob/master/arrays.markdown
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    public function getPing()
    {
        return $this->_ping;
    }

    public function setPing($ping)
    {
        $this->_ping = $ping;
    }

    /**
     * Retrieves a value from cache with a specified key.
     * This is the implementation of the method declared in the parent class.
     * @param string $key a unique key identifying the cached value
     * @return string|boolean the value stored in cache, false if the value is not in the cache or expired.
     */
    protected function getValue($key)
    {
        if ($this->_ping) {
            $this->checkConnection();
        }
        return $this->_cache->get($key);
    }

    protected function checkConnection()
    {
        $this->_cache->ping();
        Yii::log("redis ping", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
    }

    /**
     * Retrieves multiple values from cache with the specified keys.
     * @param array $keys a list of keys identifying the cached values
     * @return array a list of cached values indexed by the keys
     */
    protected function getValues($keys)
    {
        if ($this->_ping) {
            $this->checkConnection();
        }
        $response = $this->_cache->mGet($keys);
        $result = array();
        $i = 0;
        foreach ($keys as $key)
            $result[$key] = $response[$i++];
        return $result;
    }

    public function lpush($key,$value){
        if(!is_string($value)){
            throw new Exception('The value type is not a string.If u want to push an array data.You can use JSON.');
        }
        $num = $this->executeCommand('LPUSH',array($key,$value));
        return $num;
    }

    public function rpop($key){
        return $this->executeCommand('LPOP',array($key));
    }

    /**
     * Stores a value identified by a key in cache.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string $key the key identifying the value to be cached
     * @param string $value the value to be cached
     * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    protected function setValue($key, $value, $expire)
    {
        if ($this->_ping) {
            $this->checkConnection();
        }

        //兼容PHP7
        if ($expire <= 0) {
            $result = $this->_cache->set($key, $value);
        } else {
            $result = $this->_cache->set($key, $value, $expire);
        }

        if ($result !== true) {
            Yii::log("save key[" . $key . "] failed", CLogger::LEVEL_ERROR, "common.components.LRedisCache.setValue");

            return false;
        }

        return true;

    }

    /**
     * Stores a value identified by a key into cache if the cache does not contain this key.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string $key the key identifying the value to be cached
     * @param string $value the value to be cached
     * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    protected function addValue($key, $value, $expire)
    {
        if ($expire <= 0) {
            $expire = 0;
        }

        if ($this->_ping) {
            $this->checkConnection();
        }
        return $this->_cache->set($key, $value, array('nx', 'ex' => $expire));
    }

    /**
     * Deletes a value with the specified key from cache
     * This is the implementation of the method declared in the parent class.
     * @param string $key the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    protected function deleteValue($key)
    {
        if ($this->_ping) {
            $this->checkConnection();
        }
        return $this->_cache->delete($key) == 1;
    }

    /**
     * Deletes all values from cache.
     * This is the implementation of the method declared in the parent class.
     * @return boolean whether the flush operation was successful.
     * @since 1.1.5
     */
    protected function flushValues()
    {
        if ($this->_ping) {
            $this->checkConnection();
        }
        return $this->_cache->flushdb();
    }
}