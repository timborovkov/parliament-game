<?php
	$section = get_query_var('section');
?>
<div class="section-text">
    <?php echo wpautop( $section['text'] ); ?>
</div>