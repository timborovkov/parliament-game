<div class="container game_page basic_info">
    <div class="row">
        <div class="col-md-2 text-center">
            <img src="<?php echo wp_get_attachment_image_src($flag, 'full')[0]; ?>" style="width: 100%;">
        </div>
        <div class="col-md-10">
            <h2><?php echo $country_name; ?></h2>
        </div>
        <div class="col-md-6">
            <figure class="highcharts-figure">
                <div id="parliament_chart"></div>
            </figure>
        </div>
        <div class="col-md-6">
            <?php echo $country_content; ?>
        </div>
    </div>
</div>