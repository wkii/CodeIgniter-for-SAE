#Welcome to CodeIgniter 2.1.0 for Sae!
--------------------------------------

这是php框架CodeIgniter的非官方sae版本.

**请注意：github上发布的是仅仅不包含框架和应用的纯净版本。你可以从官方下载ci，然后合并到ci中。**

（为什么这么做：让你知道那些文件是被修改过的。可以和框架中的原文件对比。同时你容易检查我有没有放恶意代码。）

之前sae的应用库中有个1.x.x版本的，但那位同学也发布了一个2.1.0版本的在google
code.但是我个人不喜欢改动框架本身，借着读一读并练习一下CI的机会，就写了一份For Sae版本的。完全以标准的继承扩展形式。

该版本有以下几个优点：

  1. 完全不修改框架本身。未对system下的文件做任何修改。利于你的升级和扩展。
  2. 自动适应Sae环境和非Sae环境。因此你可以在本地开发时使用你的普通php环境。
  3. 针对非SAE环境增强了CI的文件缓存功能（包括页面缓存），分级目录存放，避免一个目录下文件太多。非页面缓存增加防下载防执行并验证有效性功能。
  4. 如果是Sae环境，则使用kvdb存储缓存，如果你本地开发使用了file，到Sae环境会自动使用kvdb，无须任何修改。
  5. 文件上传则使用Storage，与本地的区别仅仅是目录名配置的区别。
  6. log使用sae的sae_debug。记录的log可在应用管理的日志中查看debug类型的日志。
  7. 支持图片处理，但sae只能使用GD2。
  8. 如果你是直接从应用商店中安装，会自动建议一个名为"uploads"的Storage domain.以前激活kvdb.

## 使用说明
-----------

### 日志记录：

如果想在sae的日志管理中能查看到你记录的日志，需要在入口文件index.php的

	require_once BASEPATH.'core/CodeIgniter.php';

之前，加入一行代码： 

	if (class_exists('SaeKV') ) include_once APPPATH.'core/Sae'.EXT;

然后在sae应用的日志管理中，查看debug类型的日志。在记录日志时，使用

	log_message('error','your messages');

来记录日志。同时，记得在$config.php中将$config['log_threshold']
设置为0，避免不必要的记录;而且，只能记录error级别的日志。

### 缓存使用：

答案很简单，手册上怎么用，你就怎么用就好了。

支持Sae的Memcache缓存（需自己在应用中开启）。如果你使用文件缓存或apc，到sae上会自动使用kvdb，注：sae不支持apc缓存。

如果你在非SAE使用文件缓存，并且使用清空缓存的方法，那么在SAE上会自动清空kvdb，当然页面缓存也在这儿。这在非sae环境也一样。

### 文件上传：

与非sae环境不同的地方是，非sae环境需要给出绝对或相对地址，而sae环境，则只需要在$config中设定 

	$config['upload_path'] = 'domain_name/directory/'; // domain_name 是Storage的domain名，directory是你想要的目录名
	
你需要改变的仅仅是这个配置，支持多级目录。差点忘了说，也支持没有目录：）。

### 图像处理：

与文件上传一样，涉及到目录和文件地址的，只需要写Storeage的domain和目录及文件名就好了（代码中有示例），例如：
	
	$config['source_image'] = 'domain_name/uploads/xyz.jpg'

文件上传和图像处理，如果你安装了应用，可以在[这里](http://saecodeigniter.sinaapp.com/index.php/upload)
上传一个图片查看示例。

### Mysql数据库：

直接使用SAE定义的常量，使用mysql连接方式即可。主从？自己在配置文件中多写份儿配置就好了。如果你都没有使用过主从，那似乎更不必关心了。

### CAPTCHA：

关于验证码，我想说的是，没见过这么2的验证码，还要写文件？还要写数据库？所以我放弃移植。

传统且有效的做法，要么写cookie，要么写session，直接浏览器输出图象就好了。自己写一个或找个开源的类库顶上去吧，如果你用的话。

### Source code：

扩展的类所在的文件以 MY_ 为前缀，类名也是，如果你在配置文件中改变了subclass_prefix，那么也要对应的改文件名和类名。

## 后记
-------

  * 我已将纯净的不包含框架的文件放在github上。地址：[https://github.com/wkii/CodeIgniter-for-SAE](https://github.com/wkii/CodeIgniter-for-SAE)
  * 从github上clone的代码，合并到你的项目目录即可。请注意默认的模板welcome_message.php如果你在用的话，不要覆盖。
  * 在移植过程中，发现ci的一些不足，包括类库加载等方式都是我不满意的，但这个项目不想半途而废，现在终于移植完了。
  * 有想接手的，直接从github拿源码就好了，代码中请保留作者信息。
  * 关于php的框架，国内的不做评论，超轻量级的自己团队写一个就好了，国外的推荐Yii框架（慎用ORM的AR），至于Symfony这种大型的，就不推荐了。php本来就不适合干那么复杂的事儿。
  
By: [纳兰佛德](http://weibo.com/terryak) 2012.04.23凌晨，祝你幸福

