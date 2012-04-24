<html>
<head>
<meta charset="utf-8">
<title>Upload Form</title>
</head>
<body>

<?php echo $error;?>

<?php echo form_open_multipart('upload/do_upload');?>
上传一个图片试试吧：
<input type="file" name="userfile" size="20" />

<br /><br />

<input type="submit" value="upload" />

</form>
<?php echo anchor(base_url(), '返回首页');?>

</body>
</html>