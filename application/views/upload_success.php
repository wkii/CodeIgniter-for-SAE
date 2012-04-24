<html>
<head>
<title>Upload Form</title>
</head>
<body>

<h3>Your file was successfully uploaded!</h3>

<ul>
<?php foreach ($upload_data as $item => $value):?>
<li><?php echo $item;?>: <?php echo $value;?></li>
<?php endforeach; ?>
</ul>

<?php if (isset($image))
{
	echo '<h3>Your file was successfully resize!</h3>';
	echo '<ul>';
	echo '<li>source_image:'.$image['source_image'].'<br /><img src="'.$image['source_image'].'" /></li>';
	echo '<li>thumb_image:'.$image['thumb'].'<br /><img src="'.$image['thumb'].'" /></li>';
	echo '</ul>';
}
else
{
	echo '<h3 style="color:red">Your file was failure resize!</h3>';
}

?>

<p><?php echo anchor('upload', 'Upload Another File!'); ?></p>



</body>
</html>