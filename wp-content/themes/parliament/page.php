<?php
get_header();
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
?>

<div class="other_page_hero" style="background: url('<?= wp_get_attachment_image_src(get_post_thumbnail_id(), 'large')[0]; ?>');">
	<br>
</div>

<div class="container" style="margin-bottom: 50px;">
	<div class="row">
		<div class="col-12">
			<?php the_content(); ?>
		</div>
	</div>
</div>

<?php 
	if(get_post_meta( get_the_ID(), 'blue_footer_block_title', true )):
?>
<div class="lblue-bg" style="margin-top: 50px;">
	<div class="container text-white" style="padding: 50px 10vw;">
		<h5 style="color: white;"><?= get_post_meta( get_the_ID(), 'blue_footer_block_title', true ); ?></h5>
		<p><?= get_post_meta( get_the_ID(), 'blue_footer_block_content', true ); ?></p>
	</div>
</div>
<?php
	endif;
?>


<?php
	} // end while
} // end if

//get_sidebar();
get_footer();
