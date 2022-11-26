<?php


	// Create game
	function create_game_now_function() {
        $gameCode = rand(100000,999999);  

        $my_game = array(
          'post_title'    => $gameCode,
          'post_status'   => 'publish',
          'post_type' => 'the_game'
        );
        $post_id = wp_insert_post( $my_game );
        $gameID = $post_id;
        $countryID = $_POST['creategame_country'];

        // Basic data
        update_post_meta($post_id, '_player_email', $_POST['creategame_email']);
        update_post_meta($post_id, '_player_name', $_POST['creategame_player_name']);
        update_post_meta($post_id, '_player_party_affiliation', $_POST['creategame_party']);
        update_post_meta($post_id, '_country', $countryID);
        update_post_meta($post_id, '_turn', 1);
        update_post_meta($post_id, '_actions_left_this_turn', 3);

        update_post_meta($post_id, '_gdp_per_turn_effects', 0.002);
        update_post_meta($post_id, '_birth_rate_per_turn_effects', 0.007);
        update_post_meta($post_id, '_death_rate_per_turn_effects', 0.003);
        update_post_meta($post_id, '_immigration_rate_per_turn_effects', 0.001);
        update_post_meta($post_id, '_emigration_rate_per_turn_effects', 0.001);
        update_post_meta($post_id, '_goverment_credit_rating', "AA");
        update_post_meta($post_id, '_goverment_debt', (int)get_post_meta($countryID, '_debt', true));
        update_post_meta($post_id, '_happiness_score', 20);
        update_post_meta($post_id, '_turns_until_election', 20);
        update_post_meta($post_id, '_taxes_of_gdp', 0);
        update_post_meta($post_id, '_inflation_add_per_turn', 0.002); // 1% per year
        update_post_meta($post_id, '_inflation_multiplier_total', 0);
        update_post_meta($post_id, '_policy_budgetary_cost_per_capita', 10);
        update_post_meta($post_id, '_policies_instance', json_encode(Array()));
        update_post_meta($post_id, '_taxes_instance', json_encode(Array()));

        // Get the country
        $country_name = get_the_title($countryID);
        $parties = Array();
        $default_policies = Array();
       	$default_taxes = Array();
        $loop = new WP_Query( 
            array( 
                    'post_type' => 'country',
                    'posts_per_page' => -1,
                )
        ); 
        while ( $loop->have_posts() ) : $loop->the_post(); 
            if ($country_name == get_the_title()) {
                $parties = carbon_get_the_post_meta('parties');
                $default_policies = carbon_get_the_post_meta('adopted_policies');
                $default_taxes = carbon_get_the_post_meta('adopted_taxes');
                break;
            }
        endwhile;

        // Define the variables
        $policies_instance = Array();
        $taxes_instance = Array();

	    $employment_rate = (double)get_post_meta( $countryID, '_employment_rate', true );
        $crime_level = (double)get_post_meta( $countryID, '_crime_level', true );
        $freedom_level = (double)get_post_meta( $countryID, '_crime_level', true );
        $civil_rights_level = (double)get_post_meta( $countryID, '_civil_rights_level', true );
        $health_level = (double)get_post_meta( $countryID, '_health_level', true );
        $tourist_attractiveness_level = (double)get_post_meta( $countryID, '_tourist_attractiveness_level', true );
        $education_level = (double)get_post_meta( $countryID, '_education_level', true );
        $culture_level = (double)get_post_meta( $countryID, '_culture_level', true );
        $average_income = (double)get_post_meta( $countryID, '_average_income', true );
        $average_income_high = (double)get_post_meta( $countryID, '_average_income_high', true );
        $average_income_low = (double)get_post_meta( $countryID, '_average_income_low', true );

        // Other ...

        // Check if player's party is the PM
        $players_party_leader = "";
        foreach ($parties as $key => $party) {
        	if ($party['name'] == $_POST['creategame_party']) {
        		$players_party_leader = $party['leader'];
           	}
        }

        $goverment = Array();
        foreach ($parties as $key => $party) {
        	// Goverment
        	if ($party['goverment'] == "true") {
        		array_push($goverment, Array(
        			"name" => $party['name'],
        			"leader" => $party['leader'],
        			"number_of_seats" => $party['number_of_seats'],
        		));
        	}			
        	if ($party['name'] == $_POST['creategame_party']) {
        		$parties[$key]['leader'] = $_POST['creategame_player_name'];
        	}
        	// Party finance etc.
        	$parties[$key]['party_instance'] = Array(
        		'balance' => 100000,
        		'fixed_expenditure_per_mp_per_turn' => 200
        	);
        }

        $country_instance = Array(
        	"population" => get_post_meta( $countryID, '_population', true),
			"gdp" => get_post_meta( $countryID, '_gdp', true),
			"number_of_seats_in_parliament" => get_post_meta( $countryID, '_number_of_seats_in_parliament', true),
			"currency" => get_post_meta( $countryID, '_currency', true),
			"parties" => $parties,
			"party_funding_per_mp_per_turn" => 500,
			"employment_rate" => $employment_rate,
			"crime_level" => $crime_level,
			"freedom_level" => $freedom_level,
			"civil_rights_level" => $civil_rights_level,
			"health_level" => $health_level,
			"tourist_attractiveness_level" => $tourist_attractiveness_level,
			"education_level" => $education_level,
			"culture_level" => $culture_level,
			"average_income" => $average_income,
			"average_income_high" => $average_income_high,
			"average_income_low" => $average_income_low,
        );
        update_post_meta($post_id, '_country_instance', json_encode($country_instance));
        update_post_meta($post_id, '_goverment', json_encode($goverment));
        update_post_meta($post_id, '_history_instance', json_encode(Array()));
        update_post_meta($post_id, '_turn_results', json_encode(Array()));

        if (get_post_meta( $_POST['creategame_country'], '_prime_minister', true) == $players_party_leader) {
        	// Player is the Prime Minister
        	update_post_meta($post_id, '_prime_minister', $_POST['creategame_player_name']);
        } else {
        	// Player is not the Prime Minister
        	update_post_meta($post_id, '_prime_minister', get_post_meta( $_POST['creategame_country'], '_prime_minister', true));
        }

        session_start();
        $_SESSION['gameCode'] = $gameCode;

        // Set the default policies
        foreach ($default_policies as $key => $policy) {
        	$legislationID = $policy['the_policy'][0]['id'];
        	$level = (int)$policy['level'];
        	$adoptPolicyResults = adoptPolicy($gameID, $legislationID, $level, "Initial policy", $policies_instance, $country_instance);
        	$policies_instance = $adoptPolicyResults['policies_instance'];
        	$country_instance = $adoptPolicyResults['country_instance'];
        }

        // Set the default taxes
        foreach ($default_taxes as $key => $tax) {
        	$taxID = $tax['the_tax'][0]['id'];
        	$level = (int)$tax['level'];
        	$adoptTaxResults = adoptTax($gameID, $taxID, $level, "Initial tax", $taxes_instance, $country_instance);
        	$taxes_instance = $adoptTaxResults['taxes_instance'];
        	$country_instance = $adoptTaxResults['country_instance'];
        }

        // Update stats at election
        update_stats_at_previous_election($gameID);
         // Update stats at turn
		update_stats_at_previous_turn($gameID);

        // Redirect to login
        header("Location: ".get_site_url()."/game");
	}
	add_action( 'admin_post_nopriv_create_game', 'create_game_now_function' );
	add_action( 'admin_post_create_game', 'create_game_now_function' );
