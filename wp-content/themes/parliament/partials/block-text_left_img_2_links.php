<?php
	$section = get_query_var('section');
	$left_image = $section['left_image'];
?>

<div class="container-fluid">
	<div class="row after_hero_block">
		<div class="col-md-4">
			<img src="<?php echo wp_get_attachment_image_url($left_image, 'full');?>">
		</div>
		<div class="col-md-8">
			<div class="about">
				<h1><?php echo $section['title']; ?></h1>
				<p><?php echo $section['text']; ?></p>
			</div>
		</div>
	</div>
</div>
