<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Memcached Caching Class 
 *
 * @package		CodeIgniter
 * @author		Terry <digihero@gmail.com>
 * @since		Version 2.1.0
 * @link		
 */

class Cache_file extends CI_Driver
{
	// 缓存目录，在设置中要加上最后的斜杠"/"
	protected $cache_path;
	// 文件缓存目录深度
	protected $dir_depth = 2;
	// 是否在读取缓存内容时进行完整性验证
	protected $validate_data = true;
	// 检验缓存内容完整性的方式，支持md5,crc32,strlen
	protected $validate_method = 'strlen'; 
	// 缓存文件头，防止文件被访问
	static protected $cache_header = '<?php die();?>\n';
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$CI =& get_instance();
		$CI->load->helper('file');
		
		$path = $CI->config->item('cache_path');
	
		$this->cache_path = ($path == '') ? APPPATH.'cache/' : $path;
	}

	// ------------------------------------------------------------------------
	
	/**
	 * 生成key的加密串
	 * @param string $key
	 */
	protected function hashkey($key)
	{
		return sha1($key);
	}
	
	/**
	 * 获得数据的检验码
	 *
	 * @param string $value
	 * @param string $type
	 * @return string
	 */
	protected function hash($value, $type){
		switch ($type){
			case 'md5':
				return md5($value);
				break;
			case 'crc32':
				return sprintf('% 32d',crc32($value));
				break;
			case 'strlen':
			default:
				return sprintf('% 32d',strlen($value));
				break;
		}
	}
	
	/**
	 * 获取缓存id对应的目录及文件名
	 * @param string $id
	 */
	protected function get_path($id)
	{
		$key = $this->hashKey($id);
		$path = $this->cache_path;
		$filename = $key.'.php';
	
		if ( $this->dir_depth < 1 ){
			return $path.$filename;
		}
	
		for ($i=0; $i < $this->dir_depth; $i++){
			$path .= substr($key,$i,1).'/';
			if ( !is_dir($path) ){
				mkdir($path,0777);
			}
		}
		return $path.$filename;
	}

	/**
	 * Fetch from cache
	 *
	 * @param 	mixed		unique key id
	 * @return 	mixed		data on success/false on failure
	 */
	public function get($id, $get_metadata = false)
	{
		$file = $this->get_path($id);
		clearstatcache();
		if (!is_file($file) || !file_exists($file)) 
			return FALSE;
		
		// 读取文件头部
		$fp = fopen($file,'rb');
		if (!$fp) return FALSE;
		
		flock($fp,LOCK_SH);
		$len = filesize($file);
		$head_len = strlen(self::$cache_header);
		$len -= ($head_len+14);
		fseek($fp,strlen(self::$cache_header));
		$policy = unpack('Vexpire/vserialize/vvalidate_data',fread($fp,8));
		
		if ($get_metadata) {
			$mtime = filemtime($file);
			return array(
				'expire' => $policy['expire'] + $mtime,
				'mtime' => $mtime,
			);
		}
		if ( $policy['validate_data'] ){
			$policy['validate_method'] = trim(fread($fp,6));
			$policy['crc'] = fread($fp,32); 
			$len -= 32;
		}
		
		$now = time();
		do {
			// 检查是否已经过期
			if ( $policy['expire'] && filemtime($file) <= $now - $policy['expire'] ){
				$data = FALSE;
				break;
			}
			if ( $len > 0 ){
				$data = fread($fp,$len);
			}
			else {
				$data = '';
			}
		}while (false);
		
		flock($fp,LOCK_UN);
		fclose($fp);
		
		//过期就删除缓存文件
		if ( $data === FALSE ){
			$this->delete($id);
			return FALSE;
		}
		
		//校验错误也删除缓存文件
		if ($policy['validate_data'] && $this->hash($data, $policy['validate_method']) != $policy['crc'] )
		{
			$this->delete($id);
			return FALSE;
		}
		
		if ( $policy['serialize'] ){
			$data = @unserialize($data);
		}
		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Save into cache
	 *
	 * @param 	string		unique key
	 * @param 	mixed		data to store
	 * @param 	int			length of time (in seconds) the cache is valid 
	 *						- Default is 60 seconds
	 *						- 0 or null 为长期有效
	 * @return 	boolean		true on success/false on failure
	 */
	public function save($id, $data, $ttl = 60)
	{
		if ( is_string($data) ){
			$is_serialize = 0;
		}
		else {
			$data = serialize($data);
			$is_serialize = 1;
		}
		
		// 构造缓存文件头部
		$head = self::$cache_header;
		$head .= pack('Vvv', $ttl, $is_serialize, $this->validate_data);
		if ( $this->validate_data ){
			$head .= sprintf('% 6s',$this->validate_method);
			$head .= $this->hash($data,$this->validate_method);
		}
		$data = $head.$data;
		$path = $this->get_path($id);
		if (write_file($path,$data))
		{
			@chmod($path,0766);
			return TRUE;
		}
		
		return FALSE;
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
		return unlink($this->get_path($id));
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the Cache
	 *
	 * @return 	boolean		false on failure/true on success
	 */	
	public function clean()
	{
		return delete_files($this->cache_path, true);
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
		return get_dir_file_info($this->cache_path, false);
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
		return $this->get($id, true);
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
		return is_really_writable($this->cache_path);
	}

	// ------------------------------------------------------------------------
}
// End Class

/* End of file Cache_file.php */
/* Location: ./system/libraries/Cache/drivers/Cache_file.php */