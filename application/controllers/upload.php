<?php
class upload extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}

	function index()
	{
		$this->load->helper('url');
		$this->load->view('upload_form', array('error' => ' ' ));
	}

	function do_upload()
	{
		$path_of_sae = 'uploads/uploads/'; // 第一个uploads是sae storage名字，第二个uploads是目录
		$path_no_sae = FCPATH.'uploads/'; // 站点根目录/uploads/
		
		/**
		 * 如果是sae环境，可以返回完整的路径，包括sae的sotrage访问地址，带http://
		 * 如果是非sae环境，只能根据目录拿到相对当前url的地址
		 * 
		 */
		
		if (class_exists('SaeKV'))
		{
			$config['upload_path'] = $path_of_sae;
		}
		else
		{
			$config['upload_path'] = $path_no_sae;
		}
		
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size'] = '1000';
		$config['max_width']  = '2048';
		$config['max_height']  = '1680';
		$config['overwrite'] = true;

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			 
			$this->load->view('upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			// 上传成功后生成一个缩略图
			$config = array();
			$config['image_library'] = 'gd2';
			// 如果是sae，返回的file_path将是包含了sae storage domain的路径，例如:  domain_name/uploads_dir
			$config['source_image'] = $data['upload_data']['file_path'].$data['upload_data']['file_name'];
			$config['create_thumb'] = TRUE;
			$config['maintain_ratio'] = TRUE;
			$config['width'] = 75;
			$config['height'] = 50;
			
			$this->load->library('image_lib', $config);
			
			// 成功显示原图和缩略图
			if ($this->image_lib->resize())
			{
				// 非sae环境
				if (!class_exists('SaeKV'))
				{
					$data['image']['source_image'] = base_url().'uploads/'.$data['upload_data']['file_name'];
					$data['image']['thumb'] = base_url().'uploads/'.$data['upload_data']['raw_name'].'_thumb'.$data['upload_data']['file_ext'];
				}
				// sae环境
				else
				{
					$data['image']['source_image'] = $data['upload_data']['full_path'];
					$data['image']['thumb'] = dirname($data['upload_data']['full_path']).'/'.$data['upload_data']['raw_name'].'_thumb'.$data['upload_data']['file_ext'];
				}
			}
			
			$this->load->view('upload_success', $data);
		}
		
		
	}
}