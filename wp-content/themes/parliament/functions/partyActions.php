<?php

	function run_party_action() {

		$gameID = $_POST['gameID'];
		$action = $_POST['theaction'];
		$own_party = $_POST['party']; // Party name
		$turn = $_POST['turn'];

        $country_instance = json_decode(get_post_meta($gameID, '_country_instance', true), true);

		$own_party_gallup_change = 0;
		$cost = 0;
		$income = 0;
		$not_enough_money;
		$note = "";

		// If this action was made by player, lower the actions left number
        if ($own_party == get_post_meta($gameID, '_player_party_affiliation', true)) {
        	update_post_meta($gameID, '_actions_left_this_turn', get_post_meta($gameID, '_actions_left_this_turn', true) - 1);
        }

		// Get own party instance
		$own_party_key;
		foreach ($country_instance['parties'] as $key => $party) {
			if ($party['name'] == $own_party) {
				$own_party_key = $key;
			}
		}

		// Get action results
		switch ($action) {
			case 'marketing_campaign':
				// Gallup effects
				$own_party_gallup_change = rand(-5, 10);
				// Campaign cost
				$cost = 70000;
				break;
			case 'speech':
				// Gallup effects
				$own_party_gallup_change = rand(-2, 4);
				// Campaign cost
				$cost = 30000;
				break;
			case 'fundraiser':
				// Gallup effects
				$own_party_gallup_change = rand(-3, 1);
				// Campaign cost
				$cost = 20000;
				if (!isset($country_instance['parties'][$own_party_key]['party_instance']['fundraiser_held'])) {
					$country_instance['parties'][$own_party_key]['party_instance']['fundraiser_held'] = $turn;
					$income = rand(0,200000);
				} else {
					if (($turn - (int)$country_instance['parties'][$own_party_key]['party_instance']['fundraiser_held']) >= 5) {
						$country_instance['parties'][$own_party_key]['party_instance']['fundraiser_held'] = $turn;
						$income = rand(0,200000);
					} else {
						// Can't run a fundraiser now
						$own_party_gallup_change = 0;
						$cost = 0;
					}
				}

				break;
		}

		// Does party have enough funds?
		if ($country_instance['parties'][$own_party_key]['party_instance']['balance'] > $cost) {
			$not_enough_money = false;
			// Take money from the balance
			$cost = $cost - $income;
			$country_instance['parties'][$own_party_key]['party_instance']['balance'] = $country_instance['parties'][$own_party_key]['party_instance']['balance'] - $cost;
			switch ($action) {
				case 'fundraiser':
					if ($income > 0) {
						$note = "Fundraiser held";
					} else {
						$note = "Wait ".( abs(($turn - (int)$country_instance['parties'][$own_party_key]['party_instance']['fundraiser_held']) - 5) )." turns until next fundraiser can be helt";
					}
					break;
				default:
					$note = "Done";

			}
		} else {
			$not_enough_money = true;
			$own_party_gallup_change = 0;
			$cost = 0;
			$note = "Not enough funds";
		}

		if (!$not_enough_money) {

			// Give gallup points to party
			$country_instance['parties'][$own_party_key]['gallup_percentage'] = $country_instance['parties'][$own_party_key]['gallup_percentage'] + $own_party_gallup_change;
			$country_instance['parties'][$own_party_key]['gallup_change'] = $own_party_gallup_change;

			// Gallup can't go below 1%
			if ($country_instance['parties'][$own_party_key]['gallup_percentage'] < 1) {
				$country_instance['parties'][$own_party_key]['gallup_percentage'] = 1;
			}

			$hundredCheck = 0;
			// Hundred check
			foreach ($country_instance['parties'] as $key => $party) {
				$hundredCheck += $party['gallup_percentage'];
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
		} else {
			$own_party_gallup_change = 0;
			$country_instance['parties'][$own_party_key]['gallup_change'] = 0;
		}

        echo json_encode(Array(
        	"gallup_change" => $country_instance['parties'][$own_party_key]['gallup_change'],
        	"gallup_percentage" => $country_instance['parties'][$own_party_key]['gallup_percentage'],
        	"not_enough_money" => $not_enough_money,
        	"action" => $action,
        	"cost" => $cost,
        	"note" => $note
        ));
        update_post_meta($gameID, '_country_instance', json_encode($country_instance));
	}
	add_action( 'admin_post_nopriv_run_party_action', 'run_party_action' );
	add_action( 'admin_post_run_party_action', 'run_party_action' );

?>