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
class LFileCache extends CFileCache
{
	const LOG_PREFIX = 'common.components.LFileCache.';
}