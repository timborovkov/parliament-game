<?php

	function have_election_now_function($gameID) {

		adjustGovermentPollingBasedOnPerformance($gameID, true);

        $country_instance = json_decode(get_post_meta($gameID, '_country_instance', true), true);
        $goverment = json_decode(get_post_meta($gameID, '_goverment', true), true);
       	$population = $country_instance['population'];

       	// Get votes
       	$seatsInParliament = $country_instance['number_of_seats_in_parliament'];
       	$seatsCheck = 0;
        foreach ($country_instance['parties'] as $key => $party) {
        	$partyName = $party['name'];

        	$votePercent = round($party['gallup_percentage'] + random(-1.0, 1.0)) / 100;

        	$seats = round($seatsInParliament * $votePercent);
        	if ($seats < 1) {
        		$seats = 1;
        	}

        	$seatsCheck = $seatsCheck + $seats;

			$country_instance['parties'][$key]['old_number_of_seats'] = (int)$country_instance['parties'][$key]['number_of_seats'];
			$country_instance['parties'][$key]['old_share_of_votes'] = (int)$country_instance['parties'][$key]['last_election_share'];
        	$country_instance['parties'][$key]['number_of_seats'] = $seats;
        }        
       	
        // Take away or give away surplus seats
        $seatsCheck = $seatsCheck - $seatsInParliament;
       	if ($seatsCheck < 0) {
       		// There are some gallup to be given away
       		$i = 0;
       		while ($i < abs($seatsCheck)) {
       			// Who will this percentage point go to?
                $goesTo = rand(0,(count($country_instance['parties']) - 1));
                $country_instance['parties'][$goesTo]['number_of_seats'] = $country_instance['parties'][$goesTo]['number_of_seats'] + 1;
       			$i = $i+1;
       		}
       	} elseif ($seatsCheck > 0) {
       		// There are some gallup points to be taken away
       		$i = 0;
       		while ($i < $seatsCheck) {
       			// Who will this percentage point go to?
       			$goesTo = rand(0,(count($country_instance['parties']) - 1));
       			if ($country_instance['parties'][$goesTo]['gallup_percentage'] > 1) {
                	$country_instance['parties'][$goesTo]['number_of_seats'] = $country_instance['parties'][$goesTo]['number_of_seats'] - 1;
                	$i = $i+1;
                }
       		}
       	}
       	// Finalize election results
        foreach ($country_instance['parties'] as $key => $party) {
        	$partyName = $party['name'];
        	$election_turnout = random(0.50, 0.70);
        	$votePercent = $party['gallup_percentage'];
        	$amountOfVotes = round($population * $votePercent / 100 * $election_turnout);

        	$country_instance['parties'][$key]['votePercent'] = $votePercent;
        	$country_instance['parties'][$key]['last_election_share'] = $votePercent;
        	$country_instance['parties'][$key]['amountOfVotes'] = $amountOfVotes;
        	$country_instance['election_turnout'] = $election_turnout * 100;
        }

        // Form the goverment
	        $biggestParty = null;
	        $biggestSeats = 0;
	        foreach ($country_instance['parties'] as $party) {
	        	if ($party['number_of_seats'] > $biggestSeats) {
	        		$biggestParty = $party;
	        		$biggestSeats = $party['number_of_seats'];
	        	}
	        }
	        // Get goverment score
	        $the_potential_goverment = Array();
	        foreach ($country_instance['parties'] as $key => $party) {
	        	if ($party['name'] != $biggestParty['name']) {
		        	$prosCons = 0;
		        	foreach ($party['ideology'] as $ideology) {
		        		foreach ($biggestParty['ideology'] as $biggestIdeology) {
		        			if ($ideology['id'] == $biggestIdeology['id']) {
		        				$prosCons++;
		        			}
		        		}
		        		if ($ideology['id'] == $biggestParty['primary_ideology'][0]['id']) {
		        			$prosCons = $prosCons + 5;
		        		}
		        	}
		        	$party['govermentProsCons'] = $prosCons;
		        	$party['goverment'] = 'false';
		        	$party['key'] = $key;
		        	$country_instance['parties'][$key]['goverment'] = 'false';
		        	array_push($the_potential_goverment, $party);
	        	}
	        }
	        $newGoverment = Array($biggestParty);
	        $govermentSeats = $biggestParty['number_of_seats'];

	        // Check if the biggest party's seats are enough to form the goverment
	        if ($govermentSeats <= ($seatsInParliament / 2)):
		        while (true) {
		        	$mostScore = 0;
		        	$partyToAddToTheGoverment;
		        	$partyToAddToTheGovermentKey;
		        	foreach ($the_potential_goverment as $key => $party) {
			        	if ($party['govermentProsCons'] >= $mostScore) {
			        		$partyToAddToTheGoverment = $party;
			        		$partyToAddToTheGovermentKey = $key;
			        		$mostScore = $party['govermentProsCons'];
			        	}
			        }
			        $govermentSeats = $govermentSeats + $partyToAddToTheGoverment['number_of_seats'];
		        	$country_instance['parties'][$partyToAddToTheGovermentKey]['goverment'] = 'true';
			        array_push($newGoverment, $partyToAddToTheGoverment);
			        unset($the_potential_goverment[$partyToAddToTheGovermentKey]); 
			        $the_potential_goverment = array_values($the_potential_goverment);

			        if ($govermentSeats > ($seatsInParliament / 2)) {
			        	break;
			        }
		        }
		    endif;

	        // Update country_instance
	        foreach ($country_instance['parties'] as $key => $party) {
	        	$isInNewGoverment = false;
	        	foreach ($newGoverment as $govParty) {
	        		if ($govParty['name'] == $party['name']) {
	        			$isInNewGoverment = true;
	        		}
	        	}
	        	if ($isInNewGoverment) {
		        	$country_instance['parties'][$key]['goverment'] = 'true';
	        	}
	        }

        // Update the prime minister
        update_post_meta($gameID, '_prime_minister', $biggestParty['leader']);

        // Update instances
        update_post_meta($gameID, '_goverment', json_encode($newGoverment));
        update_post_meta($gameID, '_country_instance', json_encode($country_instance));

        // Update stats at election
        update_stats_at_previous_election($gameID);
	}




