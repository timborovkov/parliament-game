<?php
get_header();
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
?>


<div class="other_page_hero" style="background: url('<?= wp_get_attachment_image_src(get_post_thumbnail_id(), 'large')[0]; ?>');">
	<div class="darkenBg">
		<div class="container">
			<div class="pagenameLabel">
				<h1>Ajankohtaista</h1>
			</div>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-12">
			<div class="blog_content">
				<h2><?= get_the_title(); ?></h2>
				<p><b class="green_text"><?= get_the_date(); ?></b></p>
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</div>

<?php
	} // end while
} // end if

//get_sidebar();
get_footer();
