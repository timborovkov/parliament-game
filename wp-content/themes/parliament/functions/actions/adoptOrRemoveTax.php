<?php

function removeTax($gameID, $taxID, $taxes_instance, $country_instance) {

    // If such tax is already active, remove it's effects
    $i = 0;
    foreach($taxes_instance as $tax){
        if ($tax['ID'] == $taxID) {
            $gdp_per_turn_effects = get_post_meta($gameID, '_gdp_per_turn_effects', true) - $tax['gdp_per_turn_effects'];
            $death_rate_per_turn_effects = get_post_meta($gameID, '_death_rate_per_turn_effects', true) - $tax['death_rate_per_turn_effects'];
            $birth_rate_per_turn_effects = get_post_meta($gameID, '_birth_rate_per_turn_effects', true) - $tax['birth_rate_per_turn_effects'];
            $immigration_rate_per_turn_effects = get_post_meta($gameID, '_immigration_rate_per_turn_effects', true) - $tax['immigration_rate_per_turn_effects'];
            $emigration_rate_per_turn_effects = get_post_meta($gameID, '_emigration_rate_per_turn_effects', true) - $tax['emigration_rate_per_turn_effects'];
            $happiness_score = (int)get_post_meta($gameID, '_happiness_score', true) - (int)$tax['happiness_score'];
            $taxes_of_gdp = (float)get_post_meta($gameID, '_taxes_of_gdp', true) - (float)$tax['taxes_of_gdp'];
            $inflation_effects = (double)get_post_meta($gameID, '_inflation_add_per_turn', true) - (double)$tax['inflation_add_per_turn_change'];

            $employment_rate = (float)$country_instance['employment_rate'] - (float)$tax['employment_rate'];
            $crime_level = (float)$country_instance['crime_level'] - (float)$tax['crime_level'];
            $freedom_level = (float)$country_instance['freedom_level'] - (float)$tax['freedom_level'];
            $civil_rights_level = (float)$country_instance['civil_rights_level'] - (float)$tax['civil_rights_level'];
            $health_level = (float)$country_instance['health_level'] - (float)$tax['health_level'];
            $tourist_attractiveness_level = (float)$country_instance['tourist_attractiveness_level'] - (float)$tax['tourist_attractiveness_level'];
            $education_level = (float)$country_instance['education_level'] - (float)$tax['education_level'];
            $culture_level = (float)$country_instance['culture_level'] - (float)$tax['culture_level'];
            $average_income = (float)$country_instance['average_income'] - ((float)$country_instance['average_income'] * (float)$tax['average_income']);
            $average_income_high = (float)$country_instance['average_income_high'] - ((float)$country_instance['average_income'] * (float)$tax['average_income_high']);
            $average_income_low = (float)$country_instance['average_income_low'] - ((float)$country_instance['average_income'] * (float)$tax['average_income_low']);

            update_post_meta($gameID, '_gdp_per_turn_effects', $gdp_per_turn_effects);
            update_post_meta($gameID, '_death_rate_per_turn_effects', $death_rate_per_turn_effects);
            update_post_meta($gameID, '_birth_rate_per_turn_effects', $birth_rate_per_turn_effects);
            update_post_meta($gameID, '_immigration_rate_per_turn_effects', $immigration_rate_per_turn_effects);
            update_post_meta($gameID, '_emigration_rate_per_turn_effects', $emigration_rate_per_turn_effects);
            update_post_meta($gameID, '_happiness_score', $happiness_score);
            update_post_meta($gameID, '_taxes_of_gdp', $taxes_of_gdp);
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

            unset($taxes_instance[$i]);                       
            $taxes_instance = array_values($taxes_instance); 
        }
        $i = $i+1;
    }

    update_post_meta($gameID, '_taxes_instance', json_encode($taxes_instance));
    update_post_meta($gameID, '_country_instance', json_encode($country_instance));

    return Array(
        "taxes_instance" => $taxes_instance,
        "country_instance" => $country_instance,
    );
}

function adoptTax($gameID, $taxID, $level, $proposed_by, $taxes_instance, $country_instance) {

	// If such tax is already active, remove it's effects
    $removeTaxResults = removeTax($gameID, $taxID, $taxes_instance, $country_instance);
    $taxes_instance = $removeTaxResults['taxes_instance'];
    $country_instance = $removeTaxResults['country_instance'];

    // Get addable tax effects
    $gdp_per_turn_effects_add = get_post_meta($taxID, '_gdp_per_turn_effects', true);
    $death_rate_per_turn_effects_add = get_post_meta($taxID, '_death_rate_per_turn_effects', true);
    $birth_rate_per_turn_effects_add = get_post_meta($taxID, '_birth_rate_per_turn_effects', true);
    $immigration_rate_per_turn_effects_add = get_post_meta($taxID, '_immigration_rate_per_turn_effects', true);
    $emigration_rate_per_turn_effects_add = get_post_meta($taxID, '_emigration_rate_per_turn_effects', true);
    $happiness_score_add = get_post_meta($taxID, '_happiness_score_effects', true);
    $taxes_of_gdp_add = get_post_meta($taxID, '_taxes_of_gdp', true);
    $employment_rate_add = get_post_meta($taxID, '_employment_rate_change', true);
    $crime_level_add = get_post_meta($taxID, '_crime_level_change', true);
    $freedom_level_add = get_post_meta($taxID, '_freedom_level_change', true);
    $civil_rights_level_add = get_post_meta($taxID, '_civil_rights_level_change', true);
    $health_level_add = get_post_meta($taxID, '_health_level_change', true);
    $tourist_attractiveness_level_add = get_post_meta($taxID, '_tourist_attractiveness_level_change', true);
    $education_level_add = get_post_meta($taxID, '_education_level_change', true);
    $culture_level_add = get_post_meta($taxID, '_culture_level_change', true);
    $average_income_add = (double)get_post_meta($taxID, '_average_income_change', true) - 1;
    $average_income_high_add = (double)get_post_meta($taxID, '_average_income_high_change', true) - 1;
    $average_income_low_add = (double)get_post_meta($taxID, '_average_income_low_change', true) -1;
    $inflation_effects_add = get_post_meta($taxID, '_inflation_add_per_turn_change', true);

    // Take introduced policy level in to a count
    $gdp_per_turn_effects_add = doubleval($gdp_per_turn_effects_add) / 100 * (int)$level;
    $death_rate_per_turn_effects_add = doubleval($death_rate_per_turn_effects_add) / 100 * (int)$level;
    $birth_rate_per_turn_effects_add = doubleval($birth_rate_per_turn_effects_add) / 100 * (int)$level;
    $immigration_rate_per_turn_effects_add = doubleval($immigration_rate_per_turn_effects_add) / 100 * (int)$level;
    $emigration_rate_per_turn_effects_add = doubleval($emigration_rate_per_turn_effects_add) / 100 * (int)$level;
    $happiness_score_add = doubleval($happiness_score_add) / 100 * (int)$level;
    $taxes_of_gdp_add = doubleval($taxes_of_gdp_add) / 100 * (int)$level;
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
    $death_rate_per_turn_effects_add = $death_rate_per_turn_effects_add * random(0.650, 1.350);
    $birth_rate_per_turn_effects_add = $birth_rate_per_turn_effects_add * random(0.650, 1.350);
    $immigration_rate_per_turn_effects_add = $immigration_rate_per_turn_effects_add * random(0.650, 1.350);
    $emigration_rate_per_turn_effects_add = $emigration_rate_per_turn_effects_add * random(0.650, 1.350);
    $taxes_of_gdp_add = doubleval($taxes_of_gdp_add * random(0.850, 1.150));
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
    $death_rate_per_turn_effects = doubleval(get_post_meta($gameID, '_death_rate_per_turn_effects', true)) + $death_rate_per_turn_effects_add;
    $birth_rate_per_turn_effects = doubleval(get_post_meta($gameID, '_birth_rate_per_turn_effects', true)) + $birth_rate_per_turn_effects_add;
    $immigration_rate_per_turn_effects = doubleval(get_post_meta($gameID, '_immigration_rate_per_turn_effects', true)) + $immigration_rate_per_turn_effects_add;
    $emigration_rate_per_turn_effects = doubleval(get_post_meta($gameID, '_emigration_rate_per_turn_effects', true)) + $emigration_rate_per_turn_effects_add;
    $happiness_score = doubleval(get_post_meta($gameID, '_happiness_score', true)) + $happiness_score_add;
    $taxes_of_gdp = doubleval(get_post_meta($gameID, '_taxes_of_gdp', true)) + $taxes_of_gdp_add;
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
    update_post_meta($gameID, '_death_rate_per_turn_effects', $death_rate_per_turn_effects);
    update_post_meta($gameID, '_birth_rate_per_turn_effects', $birth_rate_per_turn_effects);
    update_post_meta($gameID, '_immigration_rate_per_turn_effects', $immigration_rate_per_turn_effects);
    update_post_meta($gameID, '_emigration_rate_per_turn_effects', $emigration_rate_per_turn_effects);
    update_post_meta($gameID, '_happiness_score', $happiness_score);
    update_post_meta($gameID, '_taxes_of_gdp', $taxes_of_gdp);
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

    $tax_instance_add = Array(
        "ID" => $taxID,
        "name" => get_the_title($taxID),
        "level" => $level,
        "proposed_by" => $proposed_by,
        "gdp_per_turn_effects" => $gdp_per_turn_effects_add,
        "death_rate_per_turn_effects" => $death_rate_per_turn_effects_add,
        "birth_rate_per_turn_effects" => $birth_rate_per_turn_effects_add,
        "immigration_rate_per_turn_effects" => $immigration_rate_per_turn_effects_add,
        "emigration_rate_per_turn_effects" => $emigration_rate_per_turn_effects_add,
        "happiness_score" => $happiness_score_add,
        "taxes_of_gdp" => $taxes_of_gdp_add,
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
    array_push($taxes_instance, $tax_instance_add);

    update_post_meta($gameID, '_taxes_instance', json_encode($taxes_instance));
    update_post_meta($gameID, '_country_instance', json_encode($country_instance));

    return Array(
        "taxes_instance" => $taxes_instance,
        "country_instance" => $country_instance,
    );
}

