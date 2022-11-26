<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<!-- Required meta tags -->
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="profile" href="https://gmpg.org/xfn/11" /> 
    <meta name="author" content="Tim Borovkov" charset="utf-8">
    <link rel="icon" href="<?= get_template_directory_uri();?>/favicon.ico">

	<!-- Fonts -->
    <link rel="stylesheet" href="https://use.typekit.net/cwk8zir.css">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Normalize -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <!-- Slider -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/css/swiper.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" type="text/css" href="<?= get_template_directory_uri();?>/css/main.css?v=<?= rand() ?>">

	<!-- JQuery -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

    <?php wp_head(); ?>
</head>
 
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
 
    	<div class="main_navbar">
    		<div class="row">
    			<div class="col-12 text-center">
    				<a href="/"><h1>Parliament Game</h1></a>
    			</div>
    		</div>
    	</div>

        <!-- Start of the content -->
        <div id="content" class="site-content">






