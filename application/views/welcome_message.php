<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1,h2,h3 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}
	h3{font-size: 16px;border:0;margin:0}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 5px 0 5px 0;
		padding: 3px 10px;
	}

	#body{
		margin: 0 15px 0 15px;
	}
	
	p.footer{
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}
	
	#container{
		margin: 10px;
		border: 1px solid #D0D0D0;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}
	.layer{margin:0 20px}
	</style>
</head>
<body>

<div id="container">
	<h1>Welcome to CodeIgniter <?php echo CI_VERSION?> for Sae!</h1>

	<div id="body">
		<p>这是php框架CodeIgniter的非官方sae版本.</p>

		<p>之前sae的应用库中有个1.x.x版本的，但那位同学也发布了一个<?php echo CI_VERSION?>版本的在google code.但是我个人不喜欢改动框架本身，借着读一读并练习一下CI的机会，就写了一份For Sae版本的。完全以标准的继承扩展形式。</p>
		<p>该版本有以下几个优点：</p>
		<ol>
			<li>完全不修改框架本身。未对system下的文件做任何修改。利于你的升级和扩展。</li>
			<li>自动适应Sae环境和非Sae环境。因此你可以在本地开发时使用你的普通php环境。</li>
			<li>针对非SAE环境增强了CI的文件缓存功能（包括页面缓存），分级目录存放，避免一个目录下文件太多。非页面缓存增加防下载防执行并验证有效性功能。</li>
			<li>如果是Sae环境，则使用kvdb存储缓存，如果你本地开发使用了file，到Sae环境会自动使用kvdb，无须任何修改。</li>
			<li>文件上传则使用Storage，与本地的区别仅仅是目录名配置的区别。</li>
			<li>log使用sae的sae_debug。记录的log可在应用管理的日志中查看debug类型的日志。</li>
			<li>支持图片处理，但sae只能使用GD2。</li>
		</ol>
		<h2>使用说明</h2>
		<h3>日志记录：</h3>
		<div class="layer">
		如果想在sae的日志管理中能查看到你记录的日志，需要在入口文件index.php的
		<code>require_once BASEPATH.'core/CodeIgniter.php';</code>
		之前，加入一行代码：
		<code>if (class_exists('SaeKV') ) include_once APPPATH.'core/Sae'.EXT;</code>
		然后在sae应用的日志管理中，查看debug类型的日志。在记录日志时，使用
		<code>log_message('error','your messages');</code>
		来记录日志。同时，记得在$config.php中将$config['log_threshold'] 设置为0，避免不必要的记录;而且，只能记录error级别的日志。
		</div>
		
		<h3>缓存使用：</h3>
		<div class="layer">
		答案很简单，手册上怎么用，你就怎么用就好了。<br />
		支持Sae的Memcache缓存（需自己在应用中开启）。如果你使用文件缓存或apc，到sae上会自动使用kvdb，注：sae不支持apc缓存。<br />
		如果你在非SAE使用文件缓存，并且使用清空缓存的方法，那么在SAE上会自动清空kvdb，当然页面缓存也在这儿。这在非sae环境也一样。
		</div>
		
		<h3>文件上传：</h3>
		<div class="layer">
		与非sae环境不同的地方是，非sae环境需要给出绝对或相对地址，而sae环境，则只需要在$config中设定
		<code>$config['upload_path'] = 'domain_name/directory/'; // domain_name 是Storage的domain名，directory是你想要的目录名</code>
		你需要改变的仅仅是这个配置，支持多级目录。差点忘了说，也支持没有目录：）。
		</div>
		
		<h3>图像处理：</h3>
		<div class="layer">
		与文件上传一样，涉及到目录和文件地址的，只需要写Storeage的domain和目录及文件名就好了（代码中有示例），例如：
		<code>$config['source_image'] = 'domain_name/uploads/xyz.jpg'</code>
		<p>文件上传和图像处理，如果你安装了应用，可以在<?php echo anchor('upload', '这里'); ?>上传一个图片查看示例。</p>
		</div>
		
		<h3>Mysql数据库：</h3>
		<div class="layer">
		直接使用SAE定义的常量，使用mysql连接方式即可。主从？自己在配置文件中多写份儿配置就好了。如果你都没有使用过主从，那似乎更不必关心了。
		</div>
		
		<h3>CAPTCHA：</h3>
		<div class="layer">
		关于验证码，我想说的是，没见过这么2的验证码，还要写文件？还要写数据库？所以我放弃移植。<br />
		传统且有效的做法，要么写cookie，要么写session，直接浏览器输出图象就好了。自己写一个或找个开源的类库顶上去吧，如果你用的话。
		</div>
		
		<h3>Source code：</h3>
		<div class="layer">
		扩展的类所在的文件以 MY_ 为前缀，类名也是，如果你在配置文件中改变了subclass_prefix，那么也要对应的改文件名和类名。
		</div>
		
		<h2>后记</h2>
		<div class="layer">
			<ul>
				<li>我已将纯净的不包含框架的文件放在github上。地址：<a href="https://github.com/wkii/CodeIgniter-for-SAE">https://github.com/wkii/CodeIgniter-for-SAE</a></li>
				<li>从github上clone的代码，合并到你的项目目录即可。请注意默认的模板welcome_message.php如果你在用的话，不要覆盖。</li>
				<li>在移植过程中，发现ci的一些不足，包括类库加载等方式都是我不满意的，但这个项目不想半途而废，现在终于移植完了，但可能不会及时维护。</li>
				<li>有想接手的，直接从github拿源码就好了，代码中请保留作者信息。</li>
				<li>关于php的框架，国内的不做评论，超轻量级的自己团队写一个就好了，国外的推荐Yii框架（慎用ORM的AR），至于Symfony这种大型的，就不推荐了。php本来就不适合干那么复杂的事儿。</li>
			</ul>
			By: <a href="http://weibo.com/terryak" target="_blank">纳兰佛德</a> 2012.04.23凌晨，祝你幸福
		</div>
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>