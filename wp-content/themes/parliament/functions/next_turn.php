<?php
	/*
	*	Next turn processing
	*/

	// Next turn
	function game_next_turn_function() {
        $gameCode = $_POST['gameCode']; 
        $gameID = $_POST['gameID']; 
        $currentTurn = (int)get_post_meta($gameID, '_turn', true);

        $history_instance = json_decode(get_post_meta($gameID, '_history_instance', true), true);
        $country_instance = json_decode(get_post_meta($gameID, '_country_instance', true), true);

        $results = Array(
        	"revolution" => false,
        	"election" => false,
        	"hyperinflation" => false,
        	"default_on_debt" => false,
        	"event" => Array()
        );

        // Check if happiness rate below zero 
            $happiness = get_post_meta($gameID, '_happiness_score', true);
        	// If is trigger a revolution
        	if ($happiness <= 0) {
        		$results['revolution'] = true;
        	}
        // Count population changes
        	$birth_rate = (float)get_post_meta($gameID, '_birth_rate_per_turn_effects', true);
        	$death_rate = (float)get_post_meta($gameID, '_death_rate_per_turn_effects', true);
        	$immigration_rate = (float)get_post_meta($gameID, '_immigration_rate_per_turn_effects', true);
        	$emigration_rate = (float)get_post_meta($gameID, '_emigration_rate_per_turn_effects', true);

        	// Randomize by 10% each way
        	$birth_rate = randomizeData($birth_rate);
        	$death_rate = randomizeData($death_rate);
        	$immigration_rate = randomizeData($immigration_rate);
        	$emigration_rate = randomizeData($emigration_rate);

        	// Get population
        	$population = $country_instance['population'];
        	// Current population = current_population + (current_population * (birth_rate - death_rate)) + (current_population * (immigration_rate - emigration_rate))
        	// Get population influx
        	$population_influx = (($population * ($birth_rate - $death_rate)) + ($population * ($immigration_rate - $emigration_rate)));
        	// Update population
        	$new_population = intval($population + $population_influx);
        	$country_instance['population'] = $new_population;

	    // Count inflation
	       	$inflationToAdd = (double)get_post_meta( $gameID, '_inflation_add_per_turn', true );
	       	$inflationMultiplierTotal = (double)get_post_meta( $gameID, '_inflation_multiplier_total', true ) + $inflationToAdd;
	       	update_post_meta($gameID, '_inflation_multiplier_total', $inflationMultiplierTotal);
			$adjustToInflation = $inflationMultiplierTotal + 1;

		// Count GDP
        	$gdp = $country_instance['gdp'];
        	$gdp_growth_per_turn = (float)get_post_meta($gameID, '_gdp_per_turn_effects', true);
        	// Count inflation in
        	$gdp_growth_per_turn = $gdp_growth_per_turn;
        	if ($inflationToAdd < 0) {
        		// Deflation, consumers don't spend
        		$gdp_growth_per_turn = $gdp_growth_per_turn + ($inflationToAdd * 2);
        	} else if ($inflationToAdd > 0.02) { // Inflation over 10% per year
        		// Hyper inflation
        		$inflationToGdpEffect = $inflationToAdd * 2;
        		$gdp_growth_per_turn = $gdp_growth_per_turn - $inflationToGdpEffect;
        		$results['hyperinflation'] = true;
        	}

        	// Count new gdp
        	$new_gdp = intval($gdp + ($gdp * $gdp_growth_per_turn));
        	$country_instance['gdp'] = $new_gdp;

        // Assign credit rating to the goverment
        	// Get current debt
        	$current_debt = (float)get_post_meta($gameID, '_goverment_debt', true);
        	// Get the percantage of debt to GDP
        	$debt_to_gdp = ($current_debt / ($gdp / 100));
        	// Assign rating, get intrest rate
        	$intrest_rate = 0;
        	$goverment_credit_rating = "";
        	if ($debt_to_gdp < 50 && $debt_to_gdp  > 0) {
        		$intrest_rate = 0.05;
        		$goverment_credit_rating = "AA";
        	} elseif ($debt_to_gdp >= 50 && $debt_to_gdp < 100) {
        		$intrest_rate = 0.15;
        		$goverment_credit_rating = "A+";
        	} elseif ($debt_to_gdp >= 100 && $debt_to_gdp < 150) {
        		$intrest_rate = 0.3;
        		$goverment_credit_rating = "BB+";
        	} elseif ($debt_to_gdp >= 150) {
        		$intrest_rate = 0.5;
        		$goverment_credit_rating = "CCC";
        	} elseif ($debt_to_gdp >= 200) {
        		$intrest_rate = 1;
        		$goverment_credit_rating = "D";
        		$results ['default_on_debt'] = true;
        	} elseif ($debt_to_gdp <= 0) {
        		// Debt is negative
        		$intrest_rate = 0.02;
        		$goverment_credit_rating = "AAA";
        	}
        	// Update credit rating
        	update_post_meta($gameID, '_goverment_credit_rating', $goverment_credit_rating);

		// Calculate cost of debt maintenance
        	$debt_maintenance = $current_debt * $intrest_rate;
        	// Divide debt maintenance by 5 to get  debt maintenance for this turn
        	$debt_maintenance = $debt_maintenance / 5;

		// Count goverment income
        	$taxes_of_gdp = (float)get_post_meta($gameID, '_taxes_of_gdp', true);
        	$taxes = $new_gdp * $taxes_of_gdp;
        	// Divide taxes by 5 to get spending for this turn
        	$taxes = $taxes / 5;
        	// Randomize by 10% each way
        	$taxes = randomizeData($taxes);

		// Count goverment spending
        	$policy_cost_per_capita = (float)get_post_meta($gameID, '_policy_budgetary_cost_per_capita', true); 
        	$spending = $policy_cost_per_capita * $new_population * $adjustToInflation;
        	// Add demt maintenance
        	$spending = $spending + $debt_maintenance;
        	// Divide spending by 5 to get spending for this turn
        	$spending = $spending / 5;

        // Count goverment budget surplus
        	$surplus = $taxes - $spending;

        // Subtract surplus from debt (can be negative)
        	$current_debt = intval($current_debt - $surplus);
        	update_post_meta($gameID, '_goverment_debt', $current_debt);

        // Reset actions left this turn
        	update_post_meta($gameID, '_actions_left_this_turn', 3);

        // Change turn
        	$nextTurnNumber = $currentTurn + 1;
        	update_post_meta($gameID, '_turn', $nextTurnNumber);

        // Calculate days till election
        	$turns_until_election = (int)get_post_meta($gameID, '_turns_until_election', true) - 1;

        // Check if this turn is election
        	if ($turns_until_election == 0) {
        		update_post_meta($gameID, '_turns_until_election', 20);
        		$results['election'] = true;
        	} else {
        		update_post_meta($gameID, '_turns_until_election', $turns_until_election);
        	}

        // Get other stats
        	$employment_rate = $country_instance['employment_rate'];
			$crime_level = $country_instance['crime_level'];
			$freedom_level = $country_instance['freedom_level'];
			$civil_rights_level = $country_instance['civil_rights_level'];
			$health_level = $country_instance['health_level'];
			$tourist_attractiveness_level = $country_instance['tourist_attractiveness_level'];
			$education_level = $country_instance['education_level'];
			$culture_level = $country_instance['culture_level'];
			$average_income = $country_instance['average_income'];
			$average_income_high = $country_instance['average_income_high'];
			$average_income_low = $country_instance['average_income_low'];

        // Change other stats 
        	// Change incomes
        	$gdp_multiplier = ($new_gdp / ($gdp / 100) - 100) / 100 + 1;
        	$country_instance['average_income'] = $average_income  * $gdp_multiplier;
        	$country_instance['average_income_high'] = $average_income_high * $gdp_multiplier;
        	$country_instance['average_income_low'] = $average_income_low * $gdp_multiplier;

        	// Change employment
        	$country_instance['employment_rate'] = doubleval($country_instance['employment_rate']) * $gdp_multiplier;
        	$country_instance['employment_rate'] = randomizeData($country_instance['employment_rate']);

       	// Change polling a bit
        	/*
	        $hundredCheck = 0;
	        foreach ($country_instance['parties'] as $key => $party) {
	        	$gallup_change = 0;
	        	$gallup_total = 0;

	            // +-1% each way random
	            $gallup_change = rand(-1, 1);

	            if ($country_instance['parties'][$key]['gallup_percentage'] <= 1) {
	            	// 40% of up, 60% nothing 
	            	$effects = rand(1,10);
	            	if ($effects <= 4) {
	            		$gallup_change = 1;
	            	} else {
	            		$gallup_change = 0;
	            	}
	            }

	            // Round the results
	            $old_gallup_total = $country_instance['parties'][$key]['gallup_percentage'];
	        	$gallup_total = (int)$country_instance['parties'][$key]['gallup_percentage'] + $gallup_change;
	        	if ($gallup_total <= 1) {
	        		$gallup_total = 1;
	        	}
	        	$gallup_change = $gallup_total - $old_gallup_total;

	        	$hundredCheck = $hundredCheck + $gallup_total;

	        	$country_instance['parties'][$key]['gallup_percentage'] = $gallup_total;
	        	$country_instance['parties'][$key]['gallup_change'] = $gallup_change;
	        }

	        $hundredCheck = $hundredCheck - 100;
	       	if ($hundredCheck < 0) {
	       		// There are some gallup to be given away
	       		$i = 0;
	       		while ($i < abs($hundredCheck)) {
	       			// Who will this percentage point go to?
	                $goesTo = rand(0,(count($country_instance['parties']) - 1));
	                $country_instance['parties'][$goesTo]['gallup_percentage'] = $country_instance['parties'][$goesTo]['gallup_percentage'] + 1;
	                $country_instance['parties'][$goesTo]['gallup_change'] = $country_instance['parties'][$goesTo]['gallup_change'] + 1;
	       			$i = $i+1;
	       		}
	       	} elseif ($hundredCheck > 0) {
	       		// There are some gallup points to be taken away
	       		$i = 0;
	       		while ($i < $hundredCheck) {
	       			// Who will this percentage point go to?
	       			$goesTo = rand(0,(count($country_instance['parties']) - 1));
	       			if ($country_instance['parties'][$goesTo]['gallup_percentage'] > 1) {
	                	$country_instance['parties'][$goesTo]['gallup_percentage'] = $country_instance['parties'][$goesTo]['gallup_percentage'] - 1;
	                	$country_instance['parties'][$goesTo]['gallup_change'] = $country_instance['parties'][$goesTo]['gallup_change'] - 1;
	                	$i = $i+1;
	                }
	       		}
	       	}
			*/

        // TODO
	        // Generate actions by other parties
	        // Generate scandals

	    // Party funding
	       	$fundingPerMp = $country_instance['party_funding_per_mp_per_turn'];
	        foreach ($country_instance['parties'] as $key => $party) {
	       		$balance = (int)$party['party_instance']['balance'];
	       		$income = (int)$country_instance['party_funding_per_mp_per_turn'] * $party['number_of_seats'];
	       		$expenditure = (int)$party['party_instance']['fixed_expenditure_per_mp_per_turn'] * $party['number_of_seats'];
	       		$total = $income - $expenditure;
	       		$balance = $balance + $total;
	       		$country_instance['parties'][$key]['party_instance']['balance'] = $balance + $total;
	       	}

	    // Generate random events
	       	// 10% Chance that a random event happens
	       	$eventTriggersRand = rand(1,10); 
	       	if ($eventTriggersRand == 1) {
		        $loop = new WP_Query( 
		            array( 
		                    'post_type' => 'randomevents',
		                    'posts_per_page' => -1,
		                )
		        ); 
		        $eventList = Array();
		        while ( $loop->have_posts() ) : $loop->the_post(); 
	                $population_change = carbon_get_the_post_meta('population_change');
	                $gdp_change = carbon_get_the_post_meta('gdp_change');
	                $ideology_effects = carbon_get_the_post_meta('ideology_effects');
	                if ($gdp_change == 1) {
	                	$gdp_change = 1;
	                } else {
	                	$gdp_change = (double)$gdp_change;
	                }
	                if ($population_change == 1) {
	                	$population_change = 1;
	                } else {
	                	$population_change = (double)$population_change;
	                }
	                array_push($eventList, Array(
	                	"name" => get_the_title(),
	                	"ID" => get_the_ID(),
	                	"population_multiplier" => $population_change,
	                	"gdp_multiplier" => $gdp_change, 
	                	"ideology_effects" => $ideology_effects
	                ));
		        endwhile;

		        $eventToTrigger = rand(0, (count($eventList) - 1));
		        $theEvent = $eventList[$eventToTrigger];

		        $country_instance['gdp'] = round((int)$country_instance['gdp'] * (double)$theEvent['gdp_multiplier']);
		        $country_instance['population'] = round((int)$country_instance['population'] * (double)$theEvent['population_multiplier']);

		        $results['event'] = $theEvent;

		        // Change polling
		        $ideologies_add_happiness = Array();
		        $ideologies_lose_happiness = Array();
		        foreach ($theEvent['ideology_effects'][0]['ideologies_add_happiness'] as $key => $ideology) {
		        	$ideologyID = $ideology['id'];
		        	array_push($ideologies_add_happiness, $ideologyID);
		        }
		        foreach ($theEvent['ideology_effects'][0]['ideologies_lose_happiness'] as $key => $ideology) {
		        	$ideologyID = $ideology['id'];
		        	array_push($ideologies_lose_happiness, $ideologyID);
		        }

		        $hundredCheck = 0;
		        foreach ($country_instance['parties'] as $key => $party) {
		        	$gallup_change = 0;
		        	$partykey = $key;

		        	foreach ($party['ideology'] as $key => $ideology) {
		        		foreach ($ideologies_add_happiness as $add_popularity) {
			        		if ($add_popularity == $party['primary_ideology'][0]['id']) {
			        			$gallup_change = $gallup_change + 0.5;
			        		} else if ($ideology['id'] == $add_popularity) {
			        			$gallup_change = $gallup_change + 0.1;
			        		}
		        		}
		        		foreach ($ideologies_lose_happiness as $lose_popularity) {
			        		if ($lose_popularity == $party['primary_ideology'][0]['id']) {
			        			$gallup_change = $gallup_change - 0.5;
			        		} elseif ($ideology['id'] == $lose_popularity) {
			        			$gallup_change = $gallup_change - 0.1;
			        		}
		        		}
		        	}

		            // +-50% each way random
		            $gallup_change = $gallup_change * random(0.5, 1.5);

		            // Round the results
		            $gallup_change = round($gallup_change);

		            $gallup_total = (int)$country_instance['parties'][$partykey]['gallup_percentage'] + $gallup_change;
		            if ($gallup_total <= 1) {
		                $gallup_total = 1;
		            }

		        	$hundredCheck = $hundredCheck + $gallup_total;

		        	$country_instance['parties'][$partykey]['gallup_percentage'] = $gallup_total;
		        	$country_instance['parties'][$partykey]['gallup_change'] = $country_instance['parties'][$partykey]['gallup_change'] + $gallup_change;
		        }
		        $hundredCheck = $hundredCheck - 100;
		        if ($hundredCheck < 0) {
		            // There are some gallup to be given away
		            $i = 0;
		            while ($i < abs($hundredCheck)) {
		                // Who will this percentage point go to?
		                $goesTo = rand(0,(count($country_instance['parties']) - 1));
		                $country_instance['parties'][$goesTo]['gallup_percentage'] = $country_instance['parties'][$goesTo]['gallup_percentage'] + 1;
		                $country_instance['parties'][$goesTo]['gallup_change'] = $country_instance['parties'][$goesTo]['gallup_change'] + 1;
		                $i = $i+1;
		            }
		        } elseif ($hundredCheck > 0) {
		            // There are some gallup points to be taken away
		            $i = 0;
		            while ($i < $hundredCheck) {
		                // Who will this percentage point go to?
		                $goesTo = rand(0,(count($country_instance['parties']) - 1));
		                if ($country_instance['parties'][$goesTo]['gallup_percentage'] > 1) {
		                    $country_instance['parties'][$goesTo]['gallup_percentage'] = $country_instance['parties'][$goesTo]['gallup_percentage'] - 1;
		                    $country_instance['parties'][$goesTo]['gallup_change'] = $country_instance['parties'][$goesTo]['gallup_change'] - 1;
		                    $i = $i+1;
		                }
		            }
		        }
	       	}

	    // Create history
	        $turn_history = Array(
	        	"turn" => $currentTurn,
	        	"population" => $new_population,
	        	"gdp" => $new_gdp,
	        	"birth_rate" => $birth_rate,
	        	"death_rate" => $death_rate,
	        	"immigration_rate" => $immigration_rate,
	        	"emigration_rate" => $emigration_rate,
	        	"spending" => $spending,
	        	"income" => $taxes,
	        	"debt" => $current_debt,
	        	"debt_maintenance" => $debt_maintenance,
	        	"gallups" => $country_instance['parties'],
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
				"happiness" => $happiness
	        );
	        array_push($history_instance, $turn_history);

        // Update country instance
        update_post_meta($gameID, '_country_instance', json_encode($country_instance));
        update_post_meta($gameID, '_history_instance', json_encode($history_instance));
        update_post_meta($gameID, '_turn_results', json_encode($results));

	    // Adjust polling to goverment performance, TODO: change should probably be more mild
	    //adjustGovermentPollingBasedOnPerformance($gameID, false);

        // Update data on top of which to build polling next turn
		update_stats_at_previous_turn($gameID);

		// Back to the game
		header("Location: ".get_site_url()."/the_game/".$gameCode);
	}
	add_action( 'admin_post_nopriv_next_turn', 'game_next_turn_function' );
	add_action( 'admin_post_next_turn', 'game_next_turn_function' );
