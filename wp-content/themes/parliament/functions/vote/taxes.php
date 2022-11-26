<?php

	/*
	*	Tax vote processing
	*/

	// Is tax adopted?
	function is_tax_adopted_function() {
        $gameID = $_POST['gameID']; 
        $taxID = $_POST['taxID']; 
        $taxes_instance = json_decode(get_post_meta($gameID, '_taxes_instance', true), true);
        // TODO
		$theTax = Array(
			"ID" => $taxID,
			"name" => get_the_title($taxID),
			"taxes_of_gdp" => get_post_meta($taxID, '_taxes_of_gdp', true),
			"happiness_score_effects" => get_post_meta($taxID, '_happiness_score_effects', true),
		);

        if (!empty($taxes_instance)) {
        	// Active taxes found, so loop through them and check if the right one exists, if does send it to client
			$isactive = false;
			$tax_instance = Array();
        	foreach($taxes_instance as $tax){
        		if ($tax['ID'] == $taxID) {
					$isactive = true;
					$tax_instance = $tax;
					break;
        		}
            }
            if ($isactive) {
            	echo json_encode(Array(
            		"theTax" => $theTax,
            		"tax_instance" => $tax_instance,
            		"isactive" => true
            	));
            } else {
            	echo json_encode(Array(
            		"isactive" => false,
            		"theTax" => $theTax,
            	));
            }
        } else {
        	// No active policies at all
	    	echo json_encode(Array(
	    		"isactive" => false,
	    		"theTax" => $theTax,
	    	));
        }
	}
	add_action( 'admin_post_nopriv_is_tax_adopted', 'is_tax_adopted_function' );
	add_action( 'admin_post_is_tax_adopted', 'is_tax_adopted_function' );

	// Put tax adoption / change / removal to vote
	function put_tax_to_vote_function() {

		$gameCode = $_POST['gameCode'];
		$gameID = $_POST['gameID'];
		$taxID = $_POST['taxID'];
		$level = $_POST['level'];
		$proposed_by = $_POST['proposed_by'];
		$removal = $_POST['remove'];

		if ($removal == "true") {
			$removal = true;
		} else {
			$removal = false;
		}

        $country_instance = json_decode(get_post_meta($gameID, '_country_instance', true), true);
        $taxes_instance = json_decode(get_post_meta($gameID, '_taxes_instance', true), true);
        $goverment = json_decode(get_post_meta($gameID, '_goverment', true), true);

		// Get policy name
		$policy_name = get_the_title($taxID);

        // Get current level of the policy
        $active_policy_instance = null;
        foreach ($taxes_instance as $key => $active_policy) {
            if ($active_policy['name'] == $policy_name) {
                $active_policy_instance = $active_policy;
            }
        }

        // If policy is adopdet and is not removal
            // Check if the level is increased or decreased
        $levelIncrease = false;
        $levelDecrease = false;
        $reform = false;
        if (!$removal && isset($active_policy_instance) && $active_policy_instance != null) {
            if ($active_policy_instance['level'] > $level) {
                // Active policy is leveled higher
                $levelDecrease = true;
            } elseif ($active_policy_instance['level'] < $level) {
                // Active policy is leveled lower
                $levelIncrease = true;
            } else {
                $reform = true;
            }
        }

        var_dump($levelIncrease);
        var_dump($levelDecrease);
        var_dump($reform);
        echo "<br><br>";

		// Get the legislation ideologies which like and don't this policy
		$ideology_effects = Array();
        $loop = new WP_Query( 
            array( 
                    'post_type' => 'taxes',
                    'posts_per_page' => -1,
                )
        ); 
        while ( $loop->have_posts() ) : $loop->the_post(); 
            if ($policy_name == get_the_title()) {
                $ideology_effects = carbon_get_the_post_meta('ideology_effects')[0];
                break;
            }
        endwhile;
        $ideologies_add_happiness = Array();
        $ideologies_lose_happiness = Array();
        foreach ($ideology_effects['ideologies_add_happiness'] as $key => $ideology) {
        	$ideologyID = $ideology['id'];
        	array_push($ideologies_add_happiness, $ideologyID);
        }
        foreach ($ideology_effects['ideologies_lose_happiness'] as $key => $ideology) {
        	$ideologyID = $ideology['id'];
        	array_push($ideologies_lose_happiness, $ideologyID);
        }

        // If level decreased ideology effects switch sides
        if ($levelDecrease) {
            $original_ideologies_lose_happiness = $ideologies_lose_happiness;
            $original_ideologies_add_happiness = $ideologies_add_happiness;
            $ideologies_lose_happiness = $original_ideologies_add_happiness;
            $ideologies_add_happiness = $original_ideologies_lose_happiness;
        }

        // Get votes for this proposal
        $for = 0;
        $against = 0;
        $empty = 0;
        $vote_passed = false;

        // Loop through parties
        foreach ($country_instance['parties'] as $key => $party) {
        	$partykey = $key;
        	$seats = $party['number_of_seats'];
        	$party_ideologies = $party['ideology'];
        	// Count pros and cons
        	$forOrAgainst = 0;
        	foreach ($ideologies_add_happiness as $key => $ideology_for) {
        		foreach ($party_ideologies as $key => $party_ideology) {
	        		if ($party_ideology['id'] == $ideology_for) {
	        			$forOrAgainst = $forOrAgainst + 5;
	        		}
	        	}
	        	if ($party['primary_ideology'][0]['id'] == $ideology_for) {
                    $forOrAgainst = $forOrAgainst + 10;
                }
        	}
        	foreach ($ideologies_lose_happiness as $key => $ideology_against) {
        		foreach ($party_ideologies as $key => $party_ideology) {
	        		if ($party_ideology['id'] == $ideology_against) {
	        			$forOrAgainst = $forOrAgainst - 1;
	        		}
	        	}
	        	if ($party['primary_ideology'][0]['id'] == $ideology_against) {
                    $forOrAgainst = $forOrAgainst - 10;
                }
        	}

        	// Account for policy level support
            // Affects for or against -7 to +7
            $level = intval($level);
            $levelToUse = $level - 50; // $level - half of amount of levels. Helps us building a quadratfunction for levels
            $forOrAgainstChange = round(sqrt(abs($levelToUse)));
            if ($levelToUse < 0) {
                $forOrAgainstChange = 0 - $forOrAgainstChange;
            }
            $forOrAgainst = $forOrAgainst + $forOrAgainstChange;

            // Get for or against absed on ideology, not taking goverment coalition or who proposed it in to a count
            $country_instance['parties'][$partykey]['ideologicalForOrAgainst'] = $forOrAgainst;

        	// Take goverment coalition in to a count
        	// Is this proposed by the goverment?
        	$proposed_by_goverment = false;
        	$this_party_in_goverment = false;
        	foreach ($goverment as $key => $gov_party) {
        		if ($proposed_by == $gov_party['name']) {
        			$proposed_by_goverment = true;
	        	}
	        	if ($party['name'] == $gov_party['name']) {
	        		$this_party_in_goverment = true;
	        	}
        	}
            if ($this_party_in_goverment) {
                if ($proposed_by_goverment) {
                    if ($removal) {
                        $forOrAgainst = $forOrAgainst - 10;
                    } else {
                        $forOrAgainst = $forOrAgainst + 10;
                    }
                }
            }

            // If proposed by this party add 20 support for the tax, to make sure that party votes for the policy it proposed
            if ($proposed_by == $party['name']) {
                if ($removal) {
                    $forOrAgainst = $forOrAgainst - 20;
                } else {
                    $forOrAgainst = $forOrAgainst + 20;
                }
            }

        	$partyIsForPolicy = false;
        	$partyline = "";
        	// Assign party delegates
        	if ($forOrAgainst > 0) {
        		$for = $for + $seats;
        		$partyIsForPolicy = true;
        		$partyline = "for";
        	} else if ($forOrAgainst == 0) {
        		// Split
        		$splitChoice = rand(1,10);
        		if ($splitChoice <= 2) {
        			// Split party is for (20% chance)
        			$for = $for + $seats;
        			$partyIsForPolicy = true;
        			$partyline = "for";
        		} else if ($splitChoice > 2 && $splitChoice <= 3) {
        			// Split party is voting empty (10% chance)
        			$empty = $empty + $seats;
        			$partyline = "empty";
        		} else if ($splitChoice > 3 && $splitChoice <= 10) {
        			// Split party is against (70% chance)
        			$against = $against + $seats;
        			$partyline = "against";
           		}
        	} else {
        		$against = $against + $seats;
        		$partyline = "against";
        	}

        	$country_instance['parties'][$partykey]['party_line'] = $partyline;

        	// Randomize votes, each delegate has 95% chance to vote with the party, 2% empty, 3% against
        	$i=0;
        	while ($i < $seats) {
        		$randResult = rand(1,100);
        		if ($randResult > 95) {
        			if ($randResult <= 97) {
        				if ($partyIsForPolicy) {
        					$for = $for - 1;
        					$empty = $empty + 1;
        				} else {
        					$against = $against - 1;
        					$empty = $empty + 1;
        				}
        			} elseif ($randResult >= 98) {
        				if ($partyIsForPolicy) {
        					$for = $for - 1;
        					$against = $against + 1;
        				} else {
        					$against = $against - 1;
        					$for = $for + 1;
        				}
           			}
        		}
        		$i = $i+1;
        	}
        }
        // Has the law passed?
        if ($for > $against) {
        	$vote_passed = true;
        }

        // Change gallups
        $hundredCheck = 0;
        foreach ($country_instance['parties'] as $key => $party) {
        	$gallup_change = 0;
        	$gallup_total = 0;
        	$partykey = $key;

            $ideology_betrayed = false;
            if ($party['party_line'] != 'empty') {
                // Party vote not empty
                if ($party['party_line'] == 'for') {
                    // Party voted in favour
                    if ($party['ideologicalForOrAgainst'] > 0) {
                        // Party ideologically should have voted in favour
                        if ($vote_passed) {
                            // The vote passed
                            if ($party['name'] == $proposed_by) {
                                // Proposed by this party, gain 6 in polls
                                $gallup_change = 6;
                            } else {
                                // Gain 4 popularity, as the party voted according to it's ideology and the vote passed bacause of it
                                $gallup_change = 4;
                            }
                        } else {
                            // The vote did not pass
                            if ($party['name'] == $proposed_by) {
                                // Proposed by this party, gain 3 in polls
                                $gallup_change = 3;
                            } else {
                                // Gain 2 popularity, as the party voted according to it's ideology but sadly the vote did not pass
                                $gallup_change = 2;
                            }
                        }
                    } else {
                        // Party ideologically should have voted against
                        $ideology_betrayed = true;
                        if ($vote_passed) {
                            // The vote passed
                            if ($party['name'] == $proposed_by) {
                                // Proposed by this party, lose 8 popularity, as the party betrayed it's ideology and the vote passed bacause of it
                                $gallup_change = -8;
                            } else {
                                // Lose 5 popularity, as the party betrayed it's ideology and the vote passed bacause of it
                                $gallup_change = -5;
                            }
                        } else {
                            // The vote did not pass
                            if ($party['name'] == $proposed_by) {
                                // Proposed by this party, lose 5 popularity, as the party betrayed it's ideology, luckily the vote did not pass
                                $gallup_change = -5;
                            } else {
                                // Lose 3 popularity, as the party betrayed it's ideology but luckily the vote did not pass
                                $gallup_change = -3;
                            }
                        }
                    }
                } elseif ($party['party_line'] == 'against') {
                    // Party voted in against
                    // No need to check if party who prosed this voted against, as it is imposible
                    if ($party['ideologicalForOrAgainst'] > 0) {
                        // Party ideologically should have voted in favour
                        $ideology_betrayed = true;
                        if ($vote_passed) {
                            // The vote passed
                                // Lose 3 popularity, as the party betrayed it's ideology, but the vote still passed
                                $gallup_change = -3;
                        } else {
                            // The vote did not pass
                                // Lose 5 popularity, as the party betrayed it's ideology, so the vote did not pass
                                $gallup_change = -5;
                        }
                    } else {
                        // Party ideologically should have voted against
                        if ($vote_passed) {
                            // The vote passed
                                // Gain 2 popularity, as the party voted according to it's ideology but sadly the vote passed
                                $gallup_change = 2;
                        } else {
                            // The vote did not pass
                                // Gain 4 popularity, as the party voted according to it's ideology and the vote did not pass
                                $gallup_change = 5;
                        }
                    }
                }
            }

            $country_instance['parties'][$partykey]['ideology_betrayed'] = $ideology_betrayed;

            // +-60% each way random
            $gallup_change = $gallup_change * random(0.4, 1.6);

        	// Round the results
        	$gallup_change = round($gallup_change);

        	$gallup_total = (int)$country_instance['parties'][$partykey]['gallup_percentage'] + $gallup_change;
        	if ($gallup_total <= 1) {
        		$gallup_total = 1;
        	}

        	$hundredCheck = $hundredCheck + $gallup_total;

        	$country_instance['parties'][$partykey]['gallup_percentage'] = $gallup_total;
        	$country_instance['parties'][$partykey]['gallup_change'] = $gallup_change;
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

        // Edit the game, so that the tax effects take place
        if ($vote_passed && !$removal) {
            // Adopt tax
            $adoptTaxResults = adoptTax($gameID, $taxID, $level, $proposed_by, $taxes_instance, $country_instance);
            $taxes_instance = $adoptTaxResults['taxes_instance'];
            $country_instance = $adoptTaxResults['country_instance'];
        } else {
        	if (!$vote_passed && $removal) {
				// If such tax is already active, remove it's effects
                $removeTaxResults = removeTax($gameID, $taxID, $taxes_instance, $country_instance);
                $taxes_instance = $removeTaxResults['taxes_instance'];
                $country_instance = $removeTaxResults['country_instance'];
        	}
        }

        // If this action was made by player, lower the actions left number
        if ($proposed_by == get_post_meta($gameID, '_player_party_affiliation', true)) {
        	update_post_meta($gameID, '_actions_left_this_turn', get_post_meta($gameID, '_actions_left_this_turn', true) - 1);
        }

        update_post_meta($gameID, '_country_instance', json_encode($country_instance));

		// Write to session
		session_start();

		// remove all session variables
		session_unset();

		$_SESSION['gameID'] = $gameID;
		$_SESSION['gameCode'] = $gameCode;
		$_SESSION['legislationID'] = $taxID;
		$_SESSION['policy_name'] = $policy_name;
		$_SESSION['level'] = $level;
		if ($removal) {
			$_SESSION['removal'] = "true";
		} else {
			$_SESSION['removal'] = "false";
		}
		$_SESSION['proposed_by'] = $proposed_by;
		$_SESSION['for'] = $for;
		$_SESSION['against'] = $against;
		$_SESSION['empty'] = $empty;
		$_SESSION['vote_passed'] = $vote_passed;

		header("Location: ".get_site_url()."/vote-results/");

	}
	add_action( 'admin_post_nopriv_put_tax_to_vote', 'put_tax_to_vote_function' );
	add_action( 'admin_post_put_tax_to_vote', 'put_tax_to_vote_function' );



