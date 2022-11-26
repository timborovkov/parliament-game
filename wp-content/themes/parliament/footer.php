	</div><!-- #content -->

	<div class="footer" style="padding-top: 100px;">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12">
					<?php echo carbon_get_theme_option( 'crb_copyright' ); ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Slider -->
	<script src="https://unpkg.com/swiper/js/swiper.min.js"></script>
 
	<!-- JQuery -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.js"></script>

    <!-- Parliament charts -->
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/item-series.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script>

	<!-- Chart JS -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

	<!-- Main JS -->
	<script type="text/javascript" src="<?= get_template_directory_uri();?>/js/main.js?v=<?= rand() ?>"></script>

	<?php wp_footer(); ?>

</body>
</html> 