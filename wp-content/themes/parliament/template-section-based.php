<?php
/*
Template Name: Section-based
*/
?>
<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
    <?php
    if (get_post_thumbnail_id() != null && get_post_thumbnail_id() != "") {
        ?>
    <div class="other_page_hero" style="background: url('<?= wp_get_attachment_image_src(get_post_thumbnail_id(), 'full')[0]; ?>');">
        <br>
    </div>
        <?php
    }
    ?>
    <div class="container">
        <?php echo get_the_content(); ?>
    </div>
    <?php
    $sections = carbon_get_the_post_meta( 'crb_sections' );
    foreach ( $sections as $section ) {
        set_query_var('section', $section);
        switch ( $section['_type'] ) {
            case 'text':
                get_template_part( 'partials/block', 'text' );
            break;
            case 'text_left_img_2_links': 
                get_template_part( 'partials/block', 'text_left_img_2_links' );
            break;
            case 'frontpage_hero':
                get_template_part( 'partials/home/frontpage', 'hero' );
            break;
            case 'link_to_game':
                get_template_part( 'partials/block', 'link_to_game' );
            break;
        }
    }
    ?>
<?php endwhile; ?>

<?php get_footer(); ?>