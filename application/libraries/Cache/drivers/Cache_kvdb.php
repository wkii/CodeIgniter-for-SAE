<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter KVDB Caching Class 
 * 等效于Cache_file
 * 可以使用$this->cache->file来调用，也可以使用$this->cache->kvdb来调用，
 * 使用方法同ci默认的file cache
 *
 * @package		CodeIgniter
 * @author		Terry <digihero@gmail.com>
 * @since		Version 2.1.0
 * @link		
 */

class Cache_kvdb extends CI_Driver {

	protected $active; // 是否激活
	protected $kvdb; // kvdb
	protected $prefix; //key前缀

	/**
	 * 使用sae的kvdb来存储cache，sotre不适合存储cache，只适合存储用户上传的图片等资源
	 */
	public function __construct()
	{
		$CI =& get_instance();
		$this->kvdb = new SaeKV();
		$this->active = $this->kvdb->init();
		if (!$this->active) show_error("SAE application KVDB is disable, please open KVDB.");
		$path = $CI->config->item('cache_path');
		$this->_prefix = $path == '' ? 'ci_cache_' : $path;
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch from cache
	 *
	 * @param 	mixed		unique key id
	 * @return 	mixed		data on success/false on failure
	 */
	public function get($id)
	{
		$data = $this->kvdb->get($this->prefix.$id);
		if ($data == false) return false;
		$data = unserialize($data);
		if ($data['ttl'] > 0 && time() > ($data['time'] + $data['ttl']) )
		{
			$this->delete($id);
			return FALSE;
		}
		return $data['data'];
	}

	// ------------------------------------------------------------------------

	/**
	 * Save into cache
	 *
	 * @param 	string		unique key
	 * @param 	mixed		data to store
	 * @param 	int			length of time (in seconds) the cache is valid 
	 *						- Default is 60 seconds
	 *						- $ttl 为0时，长期有效
	 * @return 	boolean		true on success/false on failure
	 */
	public function save($id, $data, $ttl = 60)
	{	
		$ttl = intval($ttl);
		if ($ttl < 0) $ttl = 0;
		$contents = array(
				'time'		=> time(),
				'ttl'		=> $ttl,			
				'data'		=> $data
			);
		
		return $this->kvdb->set($this->prefix.$id,serialize($contents));
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param 	mixed		unique identifier of item in cache
	 * @return 	boolean		true on success/false on failure
	 */
	public function delete($id)
	{
		return $this->kvdb->delete($this->prefix.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the Cache
	 * 可以通过关闭再打开kvdb来清除所有数据，但sae目前不支持通过程序打开关闭，只能循环读取删除
	 *
	 * @return 	boolean		false on failure/true on success
	 */	
	public function clean()
	{
		while (true)
		{
			$keys = $this->kvdb->pkrget($this->prefix, 100);
			if ($keys == false)
			foreach ($keys as $k => $v)
				$this->delete($k); 
			if (count($keys) < 100) break;
		}
		return true;
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * Not supported by file-based caching
	 *
	 * @param 	string	user/filehits
	 * @return 	mixed 	FALSE
	 */
	public function cache_info($type = NULL)
	{
		return $this->kvdb->get_info();
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		FALSE on failure, array on success.
	 */
	public function get_metadata($id)
	{
		$data = $this->get($id);
		if ($data == false) return false;
		
		$data = unserialize($data);
		
		if (is_array($data))
		{
			$data = $data['data'];
			$mtime = $data['time'];

			if ( ! isset($data['ttl']))
			{
				return FALSE;
			}

			return array(
				'expire' 	=> $mtime + $data['ttl'],
				'mtime'		=> $mtime
			);
		}
		
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Is supported
	 *
	 * In the file driver, check to see that the cache directory is indeed writable
	 * 
	 * @return boolean
	 */
	public function is_supported()
	{
		return $this->active ? true : false;
	}

	// ------------------------------------------------------------------------
}
// End Class

/* End of file Cache_file.php */
/* Location: ./system/libraries/Cache/drivers/Cache_file.php */