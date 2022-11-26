<?php

	/*
	*	Post meta fields setup
	*/

	include 'functions/post_data.php';

	/*
	*	Some usefull functions
	*/

	include 'functions/actions/adoptOrRemovePolicy.php';
	include 'functions/actions/adoptOrRemoveTax.php';
	include 'functions/actions/adjustGovermentPolling.php';

	function formatUSD($i) {
		return number_format($i, 2, ",", " ");
	}


	function count_decimals($x){
	   return  strlen(substr(strrchr($x."", "."), 1));
	}

	function random($min, $max){
	   $decimals = max(count_decimals($min), count_decimals($max));
	   $factor = pow(10, $decimals);
	   return rand($min*$factor, $max*$factor) / $factor;
	}

    function randomizeData($thenumber) {
    	$thenumber = doubleval($thenumber);
		$therand = random(-0.0201, 0.0201);
		return $thenumber + ($thenumber * $therand);
    }

	/*
	*	Game creation process
	*/

	include 'functions/create_game.php';


	/*
	*	Next turn
	*/

	include 'functions/next_turn.php';

	/*
	*	Vote processing
	*/

	include 'functions/vote/taxes.php';
	include 'functions/vote/policy.php';


	/*
	*	Election time
	*/

	include 'functions/election.php';


	/*
	*	Party actions
	*/
	include 'functions/partyActions.php';


	/*
	*	Other
	*/

	add_theme_support( 'post-thumbnails' ); 

	add_theme_support( 'menus' );
	if ( function_exists( 'register_nav_menus' ) ) {
	    register_nav_menus(
	        array(
	          'header-menu' => 'Header Menu'
	        )
	    );
	}

	show_admin_bar(false);
?>