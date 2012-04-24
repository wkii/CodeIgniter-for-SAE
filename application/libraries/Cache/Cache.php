<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1 or newer
 * CI Cache类改造，可自动适应sae。
 * 在sae环境中：apc,file缓存会自动使用kvdb，memcache类也会自动适应，无需改动程序。
 * 如果在非sae环境中，依然按照原有方式执行（除了下面所说的优化点）。
 * 
 * 优化点：
 * 以key的hash字符串并加".php"做为缓存文件名存储，别担心，它是安全的（执行时直接是exit的）。
 * 这样做的目的之一就是防止某些情况缓存文件被直接访问。
 * 文件缓存分目录存储，可防止一个目录下文件太多，并增加读取时对缓存内容的验证
 *
 * @package		CodeIgniter
 * @author		Terry <digihero@gmail.com>
 * @since		Version 2.1.0
 * @filesource	
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Caching Class 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		
 */
class Cache extends CI_Driver_Library {
	
	protected $valid_drivers 	= array(
		'cache_apc', 'cache_file', 'cache_kvdb', 'cache_memcached', 'cache_dummy'
	);

	protected $_cache_path		= NULL;		// Path of cache files (if file-based cache)
	protected $_adapter			= 'dummy';
	protected $_backup_driver;
	
	// ------------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param array
	 */
	public function __construct($config = array())
	{
		if ( ! empty($config))
		{
			// 判断如果是sae环境，则使用kvdb
			if (class_exists('SaeKV'))
			{
				if (isset($config['adapter']) && in_array($config['adapter'], array('apc','file')))
				{
					$config['adapter'] = 'kvdb';
					if (isset($config['backup'])) $config['backup'] = 'dummy';
				}
			}
			$this->_initialize($config);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get 
	 *
	 * Look for a value in the cache.  If it exists, return the data 
	 * if not, return FALSE
	 *
	 * @param 	string	
	 * @return 	mixed		value that is stored/FALSE on failure
	 */
	public function get($id)
	{	
		return $this->{$this->_adapter}->get($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 *
	 * @param 	string		Unique Key
	 * @param 	mixed		Data to store
	 * @param 	int			Length of time (in seconds) to cache the data
	 *
	 * @return 	boolean		true on success/false on failure
	 */
	public function save($id, $data, $ttl = 60)
	{
		return $this->{$this->_adapter}->save($id, $data, $ttl);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param 	mixed		unique identifier of the item in the cache
	 * @return 	boolean		true on success/false on failure
	 */
	public function delete($id)
	{
		return $this->{$this->_adapter}->delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 *
	 * @return 	boolean		false on failure/true on success
	 */
	public function clean()
	{
		return $this->{$this->_adapter}->clean();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @param 	string		user/filehits
	 * @return 	mixed		array on success, false on failure	
	 */
	public function cache_info($type = 'user')
	{
		return $this->{$this->_adapter}->cache_info($type);
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Get Cache Metadata
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		return value from child method
	 */
	public function get_metadata($id)
	{
		return $this->{$this->_adapter}->get_metadata($id);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Initialize
	 *
	 * Initialize class properties based on the configuration array.
	 *
	 * @param	array 	
	 * @return 	void
	 */
	private function _initialize($config)
	{
		$default_config = array(
				'adapter',
				'memcached'
			);
		
		foreach ($default_config as $key)
		{
			if (isset($config[$key]))
			{
				$param = '_'.$key;
				$this->{$param} = $config[$key];
			}
		}

		if (isset($config['backup']))
		{
			if (in_array('cache_'.$config['backup'], $this->valid_drivers))
			{
				$this->_backup_driver = $config['backup'];
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Is the requested driver supported in this environment?
	 *
	 * @param 	string	The driver to test.
	 * @return 	array
	 */
	public function is_supported($driver)
	{
		static $support = array();
		if ( ! isset($support[$driver]))
		{
			$support[$driver] = $this->{$driver}->is_supported();
		}
		return $support[$driver];
	}

	// ------------------------------------------------------------------------

	/**
	 * __get()
	 *
	 * @param 	child
	 * @return 	object
	 */
	public function __get($child)
	{
		// 使用cache_file和cache_kvdb时，统一使用kvdb
		if (class_exists('SaeKV') && ($child == 'file' || $child == 'apc') ) 
		{
			$child = 'kvdb';
		}
		
		$obj = parent::__get($child);

		if ( ! $this->is_supported($child))
		{
			$this->_adapter = $this->_backup_driver;
		}

		return $obj;
	}
	
	// ------------------------------------------------------------------------
}
// End Class

/* End of file Cache.php */
/* Location: ./system/libraries/Cache/Cache.php */