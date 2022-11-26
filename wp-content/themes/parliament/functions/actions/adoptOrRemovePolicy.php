<?php

function removePolicy($gameID, $legislationID, $policies_instance, $country_instance) {

    $i = 0;
    foreach($policies_instance as $policy){
        if ($policy['ID'] == $legislationID) {

            $gdp_per_turn_effects = get_post_meta($gameID, '_gdp_per_turn_effects', true) - $policy['gdp_per_turn_effects'];
            $birth_rate_per_turn_effects = get_post_meta($gameID, '_birth_rate_per_turn_effects', true) - $policy['birth_rate_per_turn_effects'];
            $death_rate_per_turn_effects = get_post_meta($gameID, '_death_rate_per_turn_effects', true) - $policy['death_rate_per_turn_effects'];
            $immigration_rate_per_turn_effects = get_post_meta($gameID, '_immigration_rate_per_turn_effects', true) - $policy['immigration_rate_per_turn_effects'];
            $emigration_rate_per_turn_effects = get_post_meta($gameID, '_emigration_rate_per_turn_effects', true) - $policy['emigration_rate_per_turn_effects'];
            $policy_budgetary_cost_per_capita = get_post_meta($gameID, '_policy_budgetary_cost_per_capita', true) - $policy['policy_budgetary_cost_per_capita'];
            $happiness_score = (int)get_post_meta($gameID, '_happiness_score', true) - (int)$policy['happiness_score'];
            $inflation_effects = (double)get_post_meta($gameID, '_inflation_add_per_turn', true) - (double)$policy['inflation_add_per_turn_change'];

            $employment_rate = (float)$country_instance['employment_rate'] - (float)$policy['employment_rate'];
            $crime_level = (float)$country_instance['crime_level'] - (float)$policy['crime_level'];
            $freedom_level = (float)$country_instance['freedom_level'] - (float)$policy['freedom_level'];
            $civil_rights_level = (float)$country_instance['civil_rights_level'] - (float)$policy['civil_rights_level'];
            $health_level = (float)$country_instance['health_level'] - (float)$policy['health_level'];
            $tourist_attractiveness_level = (float)$country_instance['tourist_attractiveness_level'] - (float)$policy['tourist_attractiveness_level'];
            $education_level = (float)$country_instance['education_level'] - (float)$policy['education_level'];
            $culture_level = (float)$country_instance['culture_level'] - (float)$policy['culture_level'];
            $average_income = (float)$country_instance['average_income'] - ((float)$country_instance['average_income'] * (float)$policy['average_income']);
            $average_income_high = (float)$country_instance['average_income_high'] - ((float)$country_instance['average_income'] * (float)$policy['average_income_high']);
            $average_income_low = (float)$country_instance['average_income_low'] - ((float)$country_instance['average_income'] * (float)$policy['average_income_low']);

            update_post_meta($gameID, '_gdp_per_turn_effects', $gdp_per_turn_effects);
            update_post_meta($gameID, '_birth_rate_per_turn_effects', $birth_rate_per_turn_effects);
            update_post_meta($gameID, '_death_rate_per_turn_effects', $death_rate_per_turn_effects);
            update_post_meta($gameID, '_immigration_rate_per_turn_effects', $immigration_rate_per_turn_effects);
            update_post_meta($gameID, '_emigration_rate_per_turn_effects', $emigration_rate_per_turn_effects);
            update_post_meta($gameID, '_policy_budgetary_cost_per_capita', $policy_budgetary_cost_per_capita);
            update_post_meta($gameID, '_happiness_score', $happiness_score);
            update_post_meta($gameID, '_inflation_add_per_turn', $inflation_effects);

            $country_instance['employment_rate'] = $employment_rate;
            $country_instance['crime_level'] = $crime_level;
            $country_instance['freedom_level'] = $freedom_level;
            $country_instance['civil_rights_level'] = $civil_rights_level;
            $country_instance['health_level'] = $health_level;
            $country_instance['tourist_attractiveness_level'] = $tourist_attractiveness_level;
            $country_instance['education_level'] = $education_level;
            $country_instance['culture_level'] = $culture_level;
            $country_instance['average_income'] = $average_income;
            $country_instance['average_income_high'] = $average_income_high;
            $country_instance['average_income_low'] = $average_income_low;

            unset($policies_instance[$i]);                        
            $policies_instance = array_values($policies_instance); 
        }
        $i = $i+1;
    }

    update_post_meta($gameID, '_policies_instance', json_encode($policies_instance));
    update_post_meta($gameID, '_country_instance', json_encode($country_instance));

    return Array(
        "policies_instance" => $policies_instance,
        "country_instance" => $country_instance,
    );
}

function adoptPolicy($gameID, $legislationID, $level, $proposed_by, $policies_instance, $country_instance) {

	// If such policy is already active, remove it's effects
    $removePolicyResults = removePolicy($gameID, $legislationID, $policies_instance, $country_instance);
    $policies_instance = $removePolicyResults['policies_instance'];
    $country_instance = $removePolicyResults['country_instance'];

	// Get addable policy effects
	$gdp_per_turn_effects_add = get_post_meta($legislationID, '_gdp_per_turn_effects', true);
	$birth_rate_per_turn_effects_add = get_post_meta($legislationID, '_birth_rate_per_turn_effects', true);
	$death_rate_per_turn_effects_add = get_post_meta($legislationID, '_death_rate_per_turn_effects', true);
	$immigration_rate_per_turn_effects_add = get_post_meta($legislationID, '_immigration_rate_per_turn_effects', true);
	$emigration_rate_per_turn_effects_add = get_post_meta($legislationID, '_emigration_rate_per_turn_effects', true);
	$policy_budgetary_cost_per_capita_add = get_post_meta($legislationID, '_policy_budgetary_cost_per_capita', true);
	$happiness_score_add = get_post_meta($legislationID, '_happiness_score_effects', true);
    $employment_rate_add = get_post_meta($legislationID, '_employment_rate_change', true);
    $crime_level_add = get_post_meta($legislationID, '_crime_level_change', true);
    $freedom_level_add = get_post_meta($legislationID, '_freedom_level_change', true);
    $civil_rights_level_add = get_post_meta($legislationID, '_civil_rights_level_change', true);
    $health_level_add = get_post_meta($legislationID, '_health_level_change', true);
    $tourist_attractiveness_level_add = get_post_meta($legislationID, '_tourist_attractiveness_level_change', true);
    $education_level_add = get_post_meta($legislationID, '_education_level_change', true);
    $culture_level_add = get_post_meta($legislationID, '_culture_level_change', true);
    $average_income_add = (double)get_post_meta($legislationID, '_average_income_change', true) - 1;
    $average_income_high_add = (double)get_post_meta($legislationID, '_average_income_high_change', true) - 1;
    $average_income_low_add = (double)get_post_meta($legislationID, '_average_income_low_change', true) -1;
    $inflation_effects_add = get_post_meta($legislationID, '_inflation_add_per_turn_change', true);

	// Take introduced policy level in to a count
	$gdp_per_turn_effects_add = doubleval($gdp_per_turn_effects_add) / 100 * (int)$level;
	$birth_rate_per_turn_effects_add = doubleval($birth_rate_per_turn_effects_add) / 100 * (int)$level;
	$death_rate_per_turn_effects_add = doubleval($death_rate_per_turn_effects_add) / 100 * (int)$level;
	$immigration_rate_per_turn_effects_add = doubleval($immigration_rate_per_turn_effects_add) / 100 * (int)$level;
	$emigration_rate_per_turn_effects_add = doubleval($emigration_rate_per_turn_effects_add) / 100 * (int)$level;
	$policy_budgetary_cost_per_capita_add = doubleval($policy_budgetary_cost_per_capita_add) / 100 * (int)$level;
	$happiness_score_add = doubleval($happiness_score_add) / 100 * (int)$level;
    $employment_rate_add = doubleval($employment_rate_add) / 100 * (int)$level;
    $crime_level_add = doubleval($crime_level_add) / 100 * (int)$level;
    $freedom_level_add = doubleval($freedom_level_add) / 100 * (int)$level;
    $civil_rights_level_add = doubleval($civil_rights_level_add) / 100 * (int)$level;
    $health_level_add = doubleval($health_level_add) / 100 * (int)$level;
    $tourist_attractiveness_level_add = doubleval($tourist_attractiveness_level_add) / 100 * (int)$level;
    $education_level_add = doubleval($education_level_add) / 100 * (int)$level;
    $culture_level_add = doubleval($culture_level_add) / 100 * (int)$level;
    $average_income_add = doubleval($average_income_add) / 100 * (int)$level;
    $average_income_high_add = doubleval($average_income_high_add) / 100 * (int)$level;
    $average_income_low_add = doubleval($average_income_low_add) / 100 * (int)$level;
    $inflation_effects_add = doubleval($inflation_effects_add) / 100 * (int)$level;

	// Randomize by around 30% each way
	$gdp_per_turn_effects_add = $gdp_per_turn_effects_add * random(0.650, 1.350);
	$birth_rate_per_turn_effects_add = $birth_rate_per_turn_effects_add * random(0.650, 1.350);
	$death_rate_per_turn_effects_add = $death_rate_per_turn_effects_add * random(0.650, 1.350);
	$immigration_rate_per_turn_effects_add = $immigration_rate_per_turn_effects_add * random(0.650, 1.350);
	$emigration_rate_per_turn_effects_add = $emigration_rate_per_turn_effects_add * random(0.650, 1.350);
	$policy_budgetary_cost_per_capita_add = $policy_budgetary_cost_per_capita_add * random(0.750, 1.350);
    $employment_rate_add = $employment_rate_add * random(0.650, 1.350);
    $crime_level_add = $crime_level_add * random(0.650, 1.350);
    $freedom_level_add = $freedom_level_add * random(0.650, 1.350);
    $civil_rights_level_add = $civil_rights_level_add * random(0.650, 1.350);
    $health_level_add = $health_level_add * random(0.650, 1.350);
    $tourist_attractiveness_level_add = $tourist_attractiveness_level_add * random(0.650, 1.350);
    $education_level_add = $education_level_add * random(0.650, 1.350);
    $culture_level_add =$culture_level_add * random(0.650, 1.350);
    $average_income_add = $average_income_add * random(0.650, 1.350);
    $average_income_high_add = $average_income_high_add * random(0.650, 1.350);
    $average_income_low_add = $average_income_low_add * random(0.650, 1.350);
    $inflation_effects_add = $inflation_effects_add * random(0.650, 1.350);

	// Count total policy effects
	$gdp_per_turn_effects = doubleval(get_post_meta($gameID, '_gdp_per_turn_effects', true)) + $gdp_per_turn_effects_add;
	$birth_rate_per_turn_effects = doubleval(get_post_meta($gameID, '_birth_rate_per_turn_effects', true)) + $birth_rate_per_turn_effects_add;
	$death_rate_per_turn_effects = doubleval(get_post_meta($gameID, '_death_rate_per_turn_effects', true)) + $death_rate_per_turn_effects_add;
	$immigration_rate_per_turn_effects = doubleval(get_post_meta($gameID, '_immigration_rate_per_turn_effects', true)) + $immigration_rate_per_turn_effects_add;
	$emigration_rate_per_turn_effects = doubleval(get_post_meta($gameID, '_emigration_rate_per_turn_effects', true)) + $emigration_rate_per_turn_effects_add;
	$policy_budgetary_cost_per_capita = doubleval(get_post_meta($gameID, '_policy_budgetary_cost_per_capita', true)) + $policy_budgetary_cost_per_capita_add;
	$happiness_score = doubleval(get_post_meta($gameID, '_happiness_score', true)) + $happiness_score_add;
    $inflation_effects = doubleval(get_post_meta($gameID, '_inflation_add_per_turn', true)) + $inflation_effects_add;

    $employment_rate = $country_instance['employment_rate'] + $employment_rate_add;
    $crime_level = $country_instance['crime_level'] + $crime_level_add;
    $freedom_level = $country_instance['freedom_level'] + $freedom_level_add;
    $civil_rights_level = $country_instance['civil_rights_level'] + $civil_rights_level_add;
    $health_level = $country_instance['health_level'] + $health_level_add;
    $tourist_attractiveness_level = $country_instance['tourist_attractiveness_level'] + $tourist_attractiveness_level_add;
    $education_level = $country_instance['education_level'] + $education_level_add;
    $culture_level = $country_instance['culture_level'] + $culture_level_add;
    $average_income = $country_instance['average_income'] + ($country_instance['average_income'] * $average_income_add);
    $average_income_high = $country_instance['average_income_high'] + ($country_instance['average_income_high'] * $average_income_high_add);
    $average_income_low = $country_instance['average_income_low'] + ($country_instance['average_income_low'] * $average_income_low_add);

	// Update the game
	update_post_meta($gameID, '_gdp_per_turn_effects', $gdp_per_turn_effects);
	update_post_meta($gameID, '_birth_rate_per_turn_effects', $birth_rate_per_turn_effects);
	update_post_meta($gameID, '_death_rate_per_turn_effects', $death_rate_per_turn_effects);
	update_post_meta($gameID, '_immigration_rate_per_turn_effects', $immigration_rate_per_turn_effects);
	update_post_meta($gameID, '_emigration_rate_per_turn_effects', $emigration_rate_per_turn_effects);
	update_post_meta($gameID, '_policy_budgetary_cost_per_capita', $policy_budgetary_cost_per_capita);
	update_post_meta($gameID, '_happiness_score', $happiness_score);
    update_post_meta($gameID, '_inflation_add_per_turn', $inflation_effects);

    $country_instance['employment_rate'] = $employment_rate;
    $country_instance['crime_level'] = $crime_level;
    $country_instance['freedom_level'] = $freedom_level;
    $country_instance['civil_rights_level'] = $civil_rights_level;
    $country_instance['health_level'] = $health_level;
    $country_instance['tourist_attractiveness_level'] = $tourist_attractiveness_level;
    $country_instance['education_level'] = $education_level;
    $country_instance['culture_level'] = $culture_level;
    $country_instance['average_income'] = $average_income;
    $country_instance['average_income_high'] = $average_income_high;
    $country_instance['average_income_low'] = $average_income_low;

	$policy_instance_add = Array(
		"ID" => $legislationID,
		"name" => get_the_title($legislationID),
		"level" => $level,
		"proposed_by" => $proposed_by,
		"gdp_per_turn_effects" => $gdp_per_turn_effects_add,
		"birth_rate_per_turn_effects" => $birth_rate_per_turn_effects_add,
		"death_rate_per_turn_effects" => $death_rate_per_turn_effects_add,
		"immigration_rate_per_turn_effects" => $immigration_rate_per_turn_effects_add,
		"emigration_rate_per_turn_effects" => $emigration_rate_per_turn_effects_add,
		"policy_budgetary_cost_per_capita" => $policy_budgetary_cost_per_capita_add,
		"happiness_score" => $happiness_score_add,
        "employment_rate" => $employment_rate_add,
        "crime_level" => $crime_level_add,
        "freedom_level" => $freedom_level_add,
        "civil_rights_level" => $civil_rights_level_add,
        "health_level" => $health_level_add,
        "tourist_attractiveness_level" => $tourist_attractiveness_level_add,
        "education_level" => $education_level_add,
        "culture_level" => $culture_level_add,
        "average_income" => $average_income_add,
        "average_income_high" => $average_income_high_add,
        "average_income_low" => $average_income_low_add,
        "inflation_add_per_turn_change" => $inflation_effects
	);
	array_push($policies_instance, $policy_instance_add);

    update_post_meta($gameID, '_policies_instance', json_encode($policies_instance));
    update_post_meta($gameID, '_country_instance', json_encode($country_instance));

    return Array(
        "policies_instance" => $policies_instance,
        "country_instance" => $country_instance,
    );
}

