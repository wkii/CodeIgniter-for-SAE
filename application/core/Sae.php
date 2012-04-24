<?php
/**
 * sae需要覆盖的函数
 * 
 * @package		CodeIgniter
 * @author		Terry <digihero@gmail.com>
 * @since		Version 2.1.0
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 针对sae的日志输出，注意，仅仅会记录error级别的日志
 * @param unknown_type $level
 * @param unknown_type $message
 * @param unknown_type $php_error
 */
function log_message($level = 'error', $message, $php_error = FALSE)
{
	static $_log;
	// sae记录日志 然后在sae控制台查看debug模式的日志
	if (class_exists('SaeKV'))
	{
		if ($level == 'error')
		{
			sae_set_display_errors(false); // 关闭信息输出
			sae_debug($message);//记录日志
			sae_set_display_errors(true); // 记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
			return true;
		}
		return false;
	}

	if (config_item('log_threshold') == 0)
	{
		return;
	}

	$_log =& load_class('Log');
	$_log->write_log($level, $message, $php_error);
}
