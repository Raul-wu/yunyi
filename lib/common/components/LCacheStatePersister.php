<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14-4-3
 * Time: PM2:25
 */

class LCacheStatePersister extends CApplicationComponent implements IStatePersister
{
	const CACHE_KEY_PREFIX = 'Common.LCacheStatePersister.';
	const LOG_PREFIX = 'common.components.LCacheStatePersister.';

	/**
	 * @var string the ID of the cache application component that is used to cache the state values.
	 * Defaults to 'cache' which refers to the primary cache application component.
	 * Set this property to false if you want to disable caching state values.
	 */
	public $cacheID = 'cache';

	/**
	 * @var string
	 */
	public $cacheKey = 'state';

	/**
	 * @var CCache local refrence to cache application component that is used.
	 */
	private $_cache;

	/**
	 * Initializes the component.
	 * This method overrides the parent implementation by making sure valid cache application component
	 * is used.
	 */
	public function init()
	{
		parent::init();
		if (!$this->cacheID || ($this->_cache = Yii::app()->getComponent($this->cacheID)) === null)
		{
			Yii::log("Get cache component failed: componentId[{$this->cacheID}]", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
			throw new LException(LError::INTERNAL_ERROR);
		}
	}

	/**
	 * Loads state data from a persistent storage.
	 * @return mixed the state
	 */
	public function load()
	{
		$cacheKey = self::CACHE_KEY_PREFIX . $this->cacheKey;
		if (($value = $this->_cache->get($cacheKey)) !== false)
		{
			return unserialize($value);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Saves state data into a persistent storage.
	 * @param mixed $state the state to be saved
	 */
	public function save($state)
	{
		$cacheKey = self::CACHE_KEY_PREFIX . $this->cacheKey;
		$this->_cache->set($cacheKey, serialize($state));
	}
}