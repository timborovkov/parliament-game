<?php

function getStatsNow($gameID)
{
	$country_instance = json_decode(get_post_meta($gameID, '_country_instance', true), true);
	$statsNow = array(
		"gdp_per_capita" => round((int)$country_instance['gdp'] / (int)$country_instance['population']),
		"employment_rate" => (int)$country_instance['employment_rate'],
		"crime_level" => (int)$country_instance['crime_level'],
		"freedom_level" => (int)$country_instance['freedom_level'],
		"civil_rights_level" => (int)$country_instance['civil_rights_level'],
		"health_level" => (int)$country_instance['health_level'],
		"tourist_attractiveness_level" => (int)$country_instance['tourist_attractiveness_level'],
		"education_level" => (int)$country_instance['education_level'],
		"culture_level" => (int)$country_instance['culture_level'],
		"average_income" => (int)$country_instance['average_income'],
		"goverment_credit_rating" => get_post_meta($gameID, '_goverment_credit_rating', true),
		"happiness_score" => (int)get_post_meta($gameID, '_happiness_score', true),
		"inflation_add_per_turn" => (float)get_post_meta($gameID, '_inflation_add_per_turn', true),
		"inflation_multiplier_total" => (float)get_post_meta($gameID, '_inflation_multiplier_total', true),
	);
	return $statsNow;
}

function update_stats_at_previous_election($gameID)
{
	$statsNow = getStatsNow($gameID);
	update_post_meta($gameID, '_stats_at_previous_election', json_encode($statsNow));
	return true;
}

function update_stats_at_previous_turn($gameID)
{
	$statsNow = getStatsNow($gameID);
	update_post_meta($gameID, '_stats_at_previous_turn', json_encode($statsNow));
	return true;
}

function adjustGovermentPollingBasedOnPerformance($gameID)
{
	$country_instance = json_decode(get_post_meta($gameID, '_country_instance', true), true);

	// $statsPrevTurn = json_decode(get_post_meta($gameID, '_stats_at_previous_turn', true), true);
	$statsPrevTurn = json_decode(get_post_meta($gameID, '_stats_at_previous_election', true), true);

	$statsNow = getStatsNow($gameID);

	$performanceScore = 0; // Total difference in levels
	foreach ($statsPrevTurn as $key => $oldstat) {
		$theDifference = 0;
		if ($key == 'goverment_credit_rating') {
			// This is goverment credit rating, result is string
			$oldCreditRatingAsNumber = 0;
			switch ($oldstat) {
				case 'AAA':
					$oldCreditRatingAsNumber = 6;
					break;
				case 'AA':
					$oldCreditRatingAsNumber = 5;
					break;
				case 'A+':
					$oldCreditRatingAsNumber = 4;
					break;
				case 'BB+':
					$oldCreditRatingAsNumber = 3;
					break;
				case 'CCC':
					$oldCreditRatingAsNumber = 2;
					break;
				case 'D':
					$oldCreditRatingAsNumber = 1;
					break;
			}
			$newCreditRatingAsNumber = 0;
			switch ($statsNow[$key]) {
				case 'AAA':
					$newCreditRatingAsNumber = 6;
					break;
				case 'AA':
					$newCreditRatingAsNumber = 5;
					break;
				case 'A+':
					$newCreditRatingAsNumber = 4;
					break;
				case 'BB+':
					$newCreditRatingAsNumber = 3;
					break;
				case 'CCC':
					$newCreditRatingAsNumber = 2;
					break;
				case 'D':
					$newCreditRatingAsNumber = 1;
					break;
			}
			$theDifference = round(($newCreditRatingAsNumber - $oldCreditRatingAsNumber) * 50);
		} elseif ($key == 'crime_level') {
			// This is crime level, the lower the better
			$theDifference = $oldstat - $statsNow[$key];
		} elseif ($key == 'average_income') {
			// This is income, just basic difference is not going to cut it
			$theDifference = round(($statsNow[$key] - $oldstat) / 40);
		} elseif ($key == 'gdp_per_capita') {
			// This is income, just basic difference is not going to cut it
			$theDifference = round(($statsNow[$key] - $oldstat) / 40);
		} elseif ($key == 'happiness_score') {
			// This is happiness, should have higher effect
			$theDifference = round(($statsNow[$key] - $oldstat) * 2);
		} elseif ($key == 'inflation_add_per_turn') {
			// This is just inflation, don't do anything
		} elseif ($key == 'inflation_multiplier_total') {
			// This is just inflation, don't do anything
		} else {
			// This is just basic leveled stats
			$theDifference = $statsNow[$key] - $oldstat;
		}
		$performanceScore += $theDifference;
	}

	$changeGallupsBy = 0;
	// If this is election, we need to adjust the polling a lot
	if ($performanceScore < 200 && $performanceScore >= 0) {
		// Normal growth, tiny change in polling
		$changeGallupsBy = 1;
	} elseif ($performanceScore >= 200) {
		// Wow, this is really great performance!
		$changeGallupsBy = 2;
	} elseif ($performanceScore < 0 && $performanceScore >= -300) {
		// The country got worse, but not too bad
		$changeGallupsBy = -1;
	} elseif ($performanceScore < -300) {
		// Yeah it's really bad
		$changeGallupsBy = -2;
	}

	$hundredCheck = 0;
	foreach ($country_instance['parties'] as $key => $party) {
		if ($party['goverment'] == 'true') {
			$gallupChangeForThisParty = round($changeGallupsBy * random(0.5, 1.5));
			$country_instance['parties'][$key]['gallup_change'] = $country_instance['parties'][$key]['gallup_change'] + $gallupChangeForThisParty;
			$country_instance['parties'][$key]['gallup_percentage'] =  $country_instance['parties'][$key]['gallup_percentage'] + $gallupChangeForThisParty;
			if ($country_instance['parties'][$key]['gallup_percentage'] <= 1) {
				$country_instance['parties'][$key]['gallup_percentage'] = 1;
			}
		}
		$hundredCheck = $hundredCheck + $country_instance['parties'][$key]['gallup_percentage'];
	}

	$hundredCheck = $hundredCheck - 100;
	if ($hundredCheck < 0) {
		// There are some gallup to be given away
		$i = 0;
		while ($i < abs($hundredCheck)) {
			// Who will this percentage point go to?
			$goesTo = rand(0, (count($country_instance['parties']) - 1));
			if ($country_instance['parties'][$goesTo]['goverment'] == 'true') {
				// This party is in the goverment, so it does not deserve the point
			} else {
				$country_instance['parties'][$goesTo]['gallup_percentage'] = $country_instance['parties'][$goesTo]['gallup_percentage'] + 1;
				$country_instance['parties'][$goesTo]['gallup_change'] = $country_instance['parties'][$goesTo]['gallup_change'] + 1;
				$i = $i + 1;
			}
		}
	} elseif ($hundredCheck > 0) {
		// There are some gallup points to be taken away
		$i = 0;
		while ($i < $hundredCheck) {
			// Who will this percentage point go to?
			$goesTo = rand(0, (count($country_instance['parties']) - 1));
			if ($country_instance['parties'][$goesTo]['goverment'] == 'true') {
				// This party is in the goverment, so it should keep it's point
			} else {
				if ($country_instance['parties'][$goesTo]['gallup_percentage'] > 1) {
					$country_instance['parties'][$goesTo]['gallup_percentage'] = $country_instance['parties'][$goesTo]['gallup_percentage'] - 1;
					$country_instance['parties'][$goesTo]['gallup_change'] = $country_instance['parties'][$goesTo]['gallup_change'] - 1;
					$i = $i + 1;
				}
			}
			$i = $i + 1;
		}
	}

	update_post_meta($gameID, '_country_instance', json_encode($country_instance));
}
