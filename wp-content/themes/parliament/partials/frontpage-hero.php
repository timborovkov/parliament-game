<?php
	$section = get_query_var('section');
	$slides = $section['crb_slides'];
?>

<div class="hero">
	<!-- Slider main container -->
	<div class="swiper-container" style="width: 100vw;height: 37vw;">
	    <!-- Additional required wrapper -->
	    <div class="swiper-wrapper">
		    <!-- Slides -->
		    <?php
		    foreach ($slides as $key => $slide) {
		    ?>
		    <div class="swiper-slide" style="background: url(<?php echo wp_get_attachment_image_url($slide['image'], 'full');?>);background-position:center center;background-size:cover;width: 100%;height: 37vw;">
			</div>
		    <?php
		    }
		    ?>
	    </div>
	    <!-- If we need navigation buttons -->
	    <div class="swiper-button-prev"></div>
	    <div class="swiper-button-next"></div>
	</div>
</div>