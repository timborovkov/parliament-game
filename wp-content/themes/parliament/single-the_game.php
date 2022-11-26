<?php get_header(); ?>

<?php while (have_posts()) : the_post();
    $gameID = get_the_ID();
    $gameCode = get_the_title();

    $turn_results = json_decode(get_post_meta($gameID, '_turn_results', true), true);
    $show_election_results = false;

    $electionheld = get_post_meta($gameID, '_election_held', true);
    if ($electionheld == "true") {
        $electionheld = true;
    } else {
        $electionheld = false;
    }
    //$electionheld = false; // TODO Disable in production

    /* Process turn results */
    if (!empty($turn_results)) {
        // Trigger election if
        if ($turn_results['election']) {
            if (!$electionheld) {
                have_election_now_function($gameID);
                update_post_meta($gameID, '_election_held', "true");
            }
            $show_election_results = true;
        }
        // Trigger random events
        if (!empty($turn_results['event'])) {
            echo "<div class='container'>";
            echo "<h1>New event: " . $turn_results['event']['name'] . "</h1>";
            echo get_post($turn_results['event']['ID'])->post_content;
            echo "</div>";
        }
        // Trigger revolution if needed
        // TODO
        if ($turn_results['revolution']) {
            echo "<div class='container'>";
            echo "<h1>Revolution! You lose the game.</h1>";
            echo "</div>";
        }

        // Hyperinflation
        if ($turn_results['hyperinflation']) {
            echo "<div class='container'>";
            echo "<h1>Hyperinflation!</h1>";
            echo "<p>In economics, hyperinflation is very high and typically accelerating inflation. It quickly erodes the real value of the local currency, as the prices of all goods increase. This causes people to minimize their holdings in that currency as they usually switch to more stable foreign currencies, often the US Dollar. Prices typically remain stable in terms of other relatively stable currencies.</p>";
            echo "</div>";
        }

        // Trigger country debt default if needed
        // TODO: You lose the game (next_turn does not yet output this)

    } else {
        update_post_meta($gameID, '_election_held', "false");
    }

    $player_name = get_post_meta($gameID, '_player_name', true);
    $player_party = get_post_meta($gameID, '_player_party_affiliation', true);
    $turn = get_post_meta($gameID, '_turn', true);
    $turns_until_election = get_post_meta($gameID, '_turns_until_election', true);

    $country = (int)get_post_meta(get_the_ID(), '_country', true);
    $flag = get_post_meta($country, '_flag', true);
    $banner = get_post_meta($country, '_hero_banner', true);
    $country_name = get_the_title($country);
    $country_content = get_post($country)->post_content;
    $credit_rating = get_post_meta($gameID, '_goverment_credit_rating', true);
    $prime_minister = get_post_meta($gameID, '_prime_minister', true);
    $happiness = round(get_post_meta($gameID, '_happiness_score', true));

    $history_instance = json_decode(get_post_meta($gameID, '_history_instance', true), true);
    $policies_instance = json_decode(get_post_meta($gameID, '_policies_instance', true), true);
    $country_instance = json_decode(get_post_meta($gameID, '_country_instance', true), true);
    $myParty = get_post_meta($gameID, '_player_party_affiliation', true);
    $parties = $country_instance['parties'];
    $population = $country_instance['population'];
    $gdp = $country_instance['gdp'];

    foreach ($parties as $party) {
        if ($party['name'] == $myParty) {
            $myParty = $party;
        }
    }

    $actions_left_this_turn = get_post_meta($gameID, '_actions_left_this_turn', true);

    // Count inflation
    $inflationMultiplierTotal = (float)get_post_meta($gameID, '_inflation_multiplier_total', true);
    $adjustToInflation = $inflationMultiplierTotal + 1;

?>
    <div class="other_page_hero" style="background: url('<?= wp_get_attachment_image_src($banner, 'full')[0]; ?>');">
        <br>
    </div>

    <?php
    require_once('game/election-result.php');
    require_once('game/navigation.php');

    require_once('game/tabs/basic-info.php');
    require_once('game/tabs/country-info.php');
    require_once('game/tabs/geopolitics.php');
    require_once('game/tabs/my-party.php');
    require_once('game/tabs/legislation.php');
    require_once('game/tabs/taxes.php');

    require_once('game/jumbotron.php');
    require('game/next-turn.php');
    ?>

    <form class="submit_legislation_adoption_form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="put_legislation_to_vote">
        <input type="hidden" name="gameCode" value="<?= $gameCode; ?>">
        <input type="hidden" name="gameID" value="<?= $gameID; ?>">
        <input type="hidden" name="proposed_by" value="<?= $player_party; ?>">
        <input type="hidden" name="legislationID" class="legislationID" value="">
        <input type="hidden" name="level" class="level" value="">
        <input type="hidden" name="remove" class="remove" value="false">
    </form>

    <form class="submit_tax_adoption_form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="put_tax_to_vote">
        <input type="hidden" name="gameCode" value="<?= $gameCode; ?>">
        <input type="hidden" name="gameID" value="<?= $gameID; ?>">
        <input type="hidden" name="proposed_by" value="<?= $player_party; ?>">
        <input type="hidden" name="taxID" class="taxID" value="">
        <input type="hidden" name="level" class="level" value="">
        <input type="hidden" name="remove" class="remove" value="false">
    </form>

    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {

            if (<?php
                if ($actions_left_this_turn <= 0) {
                    echo "true";
                } else {
                    echo "false";
                }
                ?>) {
                $('.next_turn_form .theActualNextTurnButton').click();
            }

            var population = <?= $population; ?>;
            var gdp = <?= $gdp; ?>;
            var adjustToInflation = <?= $adjustToInflation; ?>;

            // Own party
            window.meAndMyParty = function(action) {
                var content = "";
                switch (action) {
                    case "fundraiser":
                        content = "<div class='jumbotron'><h3>Fundraiser</h3><br><p>Cost: 20 000 USD | Potential income: 0 - 200 000 USD | Gallup effects:  -3% - +1 %-points</p><br><a onclick='window.runPartyAction(\"" + action + "\")' class='btn btn-outline-green'>Run a fundraiser</a></div>";
                        break;
                    case "speech":
                        content = "<div class='jumbotron'><h3>Speech or event</h3><br><p>Cost: 30 000 USD | Gallup effects:  -2% - +4 %-points</p><br><a onclick='window.runPartyAction(\"" + action + "\")' class='btn btn-outline-green'>Host event / Give a speech</a></div>";
                        break;
                    case "marketing_campaign":
                        content = "<div class='jumbotron'><h3>Marketing campaign</h3><br><p>Cost: 70 000 USD | Gallup effects:  -4% - +8 %-points</p><br><a onclick='window.runPartyAction(\"" + action + "\")' class='btn btn-outline-green'>Run campaign</a></div>";
                        break;
                }
                $('.partyActionBlock').html(content);
            }
            window.runPartyAction = function(action) {
                event.preventDefault();
                $.ajax({
                    url: '<?php echo esc_url(admin_url('admin-post.php')); ?>',
                    type: 'post',
                    data: {
                        "action": "run_party_action",
                        "gameID": "<?= $gameID; ?>",
                        "theaction": action,
                        "party": "<?= $myParty['name']; ?>",
                        "turn": "<?= $turn ?>"
                    },
                    success: function(data) {
                        var data = JSON.parse(data);

                        $('.partyActionResults .note').html(data['note']);

                        if (data['cost'] > 0) {
                            $('.partyActionResults .moneyResults').html("- " + formatUSD(data['cost']) + " USD");
                        } else {
                            $('.partyActionResults .moneyResults').html("+ " + formatUSD(Math.abs(data['cost'])) + " USD");
                        }
                        if (data['gallup_change'] >= 0) {
                            $('.partyActionResults .pollsChangeResults').html("+ " + data['gallup_change'] + " %-points");
                        } else {
                            $('.partyActionResults .pollsChangeResults').html("- " + Math.abs(data['gallup_change']) + " %-points");
                        }

                        $('.partyActionResults .pollsTotalResults').html("+ " + data['gallup_percentage'] + " %");

                        $('.partyActionResults').show();

                        setTimeout(function() {
                            $('.partyActionResults').fadeOut(500);
                            location.reload();
                        }, 4000);
                    }
                });
            }

            // Process taxation
            window.taxactions = function(taxID) {
                // Check if adopted
                $.ajax({
                    url: '<?php echo esc_url(admin_url('admin-post.php')); ?>',
                    type: 'post',
                    data: {
                        "action": "is_tax_adopted",
                        "taxID": taxID,
                        "gameID": "<?= $gameID; ?>"
                    },
                    success: function(data) {
                        var data = JSON.parse(data);
                        console.log(data);
                        if (data['isactive']) {
                            // The tax is active
                            $('.taxactionspopup').html(`
                                <h2 class="w-100 text-center">` + data['tax_instance']['name'] + `</h2>
                                <h4 class="text-success">This tax is active</h4>
                                <p>Current level: ` + data['tax_instance']['level'] + `</p>
                                <p>Current income: ` + formatUSD(parseInt(data['tax_instance']['taxes_of_gdp'] * gdp)) + `USD (annually)</p>
                                <p>Current happiness effects: ` + data['tax_instance']['happiness_score'] + `</p>
                                <p><b>Tax level:</b></p>
                                <input type="range" min="1" max="100" value="` + data['tax_instance']['level'] + `" class="form-control-range changeTaxLevel" onchange="window.changeTaxLevel(` + taxID + `, ` + data['theTax']['taxes_of_gdp'] + `, ` + data['theTax']['happiness_score_effects'] + `)">
                                <p class="taxEffects"></p>
                                <a class="btn btn-primary putToVoteChange text-white" onclick="window.putToVoteTaxFunc(` + taxID + `)">Put to vote a change to the tax level</a>
                                <a class="btn btn-outline-primary putToVoteRemoval" onclick="window.putToVoteTaxRemovalFunc(` + taxID + `, ` + data['tax_instance']['level'] + `)">Put to vote removal of the tax</a>
                            `);
                        } else {
                            // The tax is not active
                            $('.taxactionspopup').html(`
                                <h2 class="w-100 text-center">` + data['theTax']['name'] + `</h2>
                                <h4 class="text-primary">This tax not adopted</h4>
                                <p>Maximum income: ` + formatUSD(parseInt(data['theTax']['taxes_of_gdp'] * gdp)) + ` USD (annually)</p>
                                <p>Maximum happiness effects: ` + data['theTax']['happiness_score_effects'] + `</p>
                                <p><b>Tax level:</b></p>
                                <input type="range" min="1" max="100" value="0" class="form-control-range changeTaxLevel" onchange="window.changeTaxLevel(` + taxID + `, ` + data['theTax']['taxes_of_gdp'] + `, ` + data['theTax']['happiness_score_effects'] + `)">
                                <p class="taxEffects"></p>
                                <a class="btn btn-primary putToVoteAdoption text-white" onclick="window.putToVoteTaxFunc(` + taxID + `)">Put to vote adoption of the tax</a>
                            `);
                        }
                        $('.taxactionspopup').show();
                    }
                })
            }

            window.putToVoteTaxFunc = function(policyID) {
                var policyLevel = $('.changeTaxLevel').val();
                if (policyLevel == 0 || policyLevel == null) {
                    alert('Please choose policy level');
                } else {
                    // Confirmation
                    if (confirm("Are you sure you want to put this tax to vote?")) {
                        // Fill the variables
                        $('.submit_tax_adoption_form input.taxID').val(policyID);
                        $('.submit_tax_adoption_form input.level').val(policyLevel);
                        $('.submit_tax_adoption_form input.removal').val("false");

                        // Submit
                        $('.submit_tax_adoption_form').submit();
                    }
                }
            }
            window.putToVoteTaxRemovalFunc = function(policyID, currentLevel) {
                // Confirmation
                if (confirm("Are you sure you want to put this tax's removal to vote?")) {
                    // Fill the variables
                    $('.submit_tax_adoption_form input.taxID').val(policyID);
                    $('.submit_tax_adoption_form input.level').val(currentLevel);
                    $('.submit_tax_adoption_form input.remove').val("true");

                    // Submit
                    $('.submit_tax_adoption_form').submit();
                }
            }
            window.changeTaxLevel = function(taxID, taxes_of_gdp, happiness_score_effects) {
                var taxLevel = $('.changeTaxLevel').val();

                // Calculate income
                var estimatedIncome = parseFloat(taxes_of_gdp) * parseFloat(gdp);
                var estimatedIncomePerLevel = parseFloat(estimatedIncome) / 100;
                var estimatedIncomeTotal = parseFloat(estimatedIncomePerLevel) * taxLevel;
                $('.taxEffects').html('Estimated income: ' + formatUSD(parseInt(estimatedIncomeTotal)) + ' USD (annually)');

                // Calculate happiness
                var estimatedHappinessPerLevel = happiness_score_effects / 100;
                var estimatedHappiness = Math.round(estimatedHappinessPerLevel * taxLevel);
                $('.taxEffects').html($('.taxEffects').html() + '<br> Happiness effects: ' + estimatedHappiness);

                // Display the level
                $('.taxEffects').html($('.taxEffects').html() + '<br> Level: ' + taxLevel);
            }


            // Process legislation
            window.policyactions = function(policyID) {
                // Check if adopted
                $.ajax({
                    url: '<?php echo esc_url(admin_url('admin-post.php')); ?>',
                    type: 'post',
                    data: {
                        "action": "is_policy_adopted",
                        "policyID": policyID,
                        "gameID": "<?= $gameID; ?>"
                    },
                    success: function(data) {
                        var data = JSON.parse(data);
                        if (data['isactive']) {
                            // The policy is active
                            $('.policyactionspopup').html(`
                                <h2 class="w-100 text-center">` + data['policy_instance']['name'] + `</h2>
                                <h4 class="text-success">This policy is active</h4>
                                <p>Current level: ` + data['policy_instance']['level'] + `</p>
                                <p>Current cost: ` + formatUSD(Math.round(data['policy_instance']['policy_budgetary_cost_per_capita'] * population * adjustToInflation)) + ' USD (annually), ' + formatUSD(Math.round(data['policy_instance']['policy_budgetary_cost_per_capita'] * adjustToInflation)) + ' USD per capita ' + `</p>
                                <p>Current happiness effects: ` + data['policy_instance']['happiness_score'] + `</p>
                                <p><b>Policy level:</b></p>
                                <input type="range" min="1" max="100" value="` + data['policy_instance']['level'] + `" class="form-control-range changePolicyLevel" onchange="window.changePolicyLevel(` + policyID + `, ` + data['thePolicy']['policy_budgetary_cost_per_capita'] + `, ` + data['thePolicy']['happiness_score_effects'] + `)">
                                <p class="policyEffects"></p>
                                <a class="btn btn-primary putToVoteChange text-white" onclick="window.putToVoteFunc(` + policyID + `)">Put to vote a change to the policy level</a>
                                <a class="btn btn-outline-primary putToVoteRemoval" onclick="window.putToVoteRemovalFunc(` + policyID + `, ` + data['policy_instance']['level'] + `)">Put to vote removal of the policy</a>
                            `);
                            $('.changePolicyLevel').val();
                        } else {
                            // The policy is not active
                            $('.policyactionspopup').html(`
                                <h2 class="w-100 text-center">` + data['thePolicy']['name'] + `</h2>
                                <h4 class="text-primary">This policy not adopted</h4>
                                <p>Maximum cost: ` + formatUSD(Math.round(data['thePolicy']['policy_budgetary_cost_per_capita'] * population * adjustToInflation)) + ' USD (annually), ' + formatUSD(Math.round(data['thePolicy']['policy_budgetary_cost_per_capita'] * adjustToInflation)) + ' USD per capita ' + `</p>
                                <p>Maximum happiness effects: ` + data['thePolicy']['happiness_score_effects'] + `</p>
                                <p><b>Policy level:</b></p>
                                <input type="range" min="1" max="100" value="0" class="form-control-range changePolicyLevel" onchange="window.changePolicyLevel(` + policyID + `, ` + data['thePolicy']['policy_budgetary_cost_per_capita'] + `, ` + data['thePolicy']['happiness_score_effects'] + `)">
                                <p class="policyEffects"></p>
                                <a class="btn btn-primary putToVoteAdoption text-white" onclick="window.putToVoteFunc(` + policyID + `)">Put to vote adoption of the policy</a>
                            `);
                        }
                        $('.policyactionspopup').show();
                    }
                })
            }

            window.putToVoteFunc = function(policyID) {
                var policyLevel = $('.changePolicyLevel').val();
                if (policyLevel == 0 || policyLevel == null) {
                    alert('Please choose policy level');
                } else {
                    // Confirmation
                    if (confirm("Are you sure you want to put this legislation to vote?")) {
                        // Fill the variables
                        $('.submit_legislation_adoption_form input.legislationID').val(policyID);
                        $('.submit_legislation_adoption_form input.level').val(policyLevel);
                        $('.submit_legislation_adoption_form input.removal').val("false");

                        // Submit
                        $('.submit_legislation_adoption_form').submit();
                    }
                }
            }
            window.putToVoteRemovalFunc = function(policyID, currentLevel) {
                // Confirmation
                if (confirm("Are you sure you want to put this legislation's removal to vote?")) {
                    // Fill the variables
                    $('.submit_legislation_adoption_form input.legislationID').val(policyID);
                    $('.submit_legislation_adoption_form input.level').val(currentLevel);
                    $('.submit_legislation_adoption_form input.remove').val("true");

                    // Submit
                    $('.submit_legislation_adoption_form').submit();
                }
            }
            window.changePolicyLevel = function(policyID, policy_budgetary_cost_per_capita, happiness_score_effects) {
                var policyLevel = $('.changePolicyLevel').val();

                // Calculate cost
                var estimatedCostPerCapitaPerLevel = policy_budgetary_cost_per_capita / 100;
                var estimatedCostPerCapita = estimatedCostPerCapitaPerLevel * policyLevel * adjustToInflation;
                var estimatedCost = estimatedCostPerCapita * population;
                $('.policyEffects').html('Estimated cost: ' + formatUSD(Math.round(estimatedCost)) + ' USD (annually), ' + formatUSD(Math.round(estimatedCostPerCapita)) + ' USD per capita ');

                // Calculate happiness
                var estimatedHappinessPerLevel = happiness_score_effects / 100;
                var estimatedHappiness = Math.round(estimatedHappinessPerLevel * policyLevel);
                $('.policyEffects').html($('.policyEffects').html() + '<br> Happiness effects: ' + estimatedHappiness);

                // Display the level
                $('.policyEffects').html($('.taxEffects').html() + '<br> Level: ' + taxLevel);
            }

            // Load charts 
            <?php
            $labels = "";
            foreach ($history_instance as $history_turn) {
                $turn_number = strval($history_turn['turn']);
                $labels = $labels . "'" . $turn_number . "'" . ",";
            }
            $labels = rtrim($labels, ",");
            if (!empty($history_instance[0]) && $history_instance != null) {

                function printChart($parID, $history_instance, $labels)
                {
                    $hisdataset = "";
                    foreach ($history_instance as $history_turn) {
                        $hisdataset = $hisdataset . $history_turn[$parID] . ",";
                    }
                    if ($parID == "gdp") {
                        $hisdataset = "";
                        foreach ($history_instance as $history_turn) {
                            $hisdataset = $hisdataset . round((float)$history_turn[$parID] / 1000000000, 2) . ",";
                        }
                    }

                    $hisdataset = rtrim($hisdataset, ",");

                    echo "
                            var ctx = document.getElementById('" . $parID . "Chart').getContext('2d');
                            var chart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: [" . $labels . "],
                                    datasets: [{
                                        label: '" . $parID . "',
                                        borderColor: '#0F1833',
                                        data: [" . $hisdataset . "]
                                    }]
                                },
                                // Start from 0
                                options: {
                                    title: {
                                      display: false
                                    },
                                    scales: {
                                      yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                      }]
                                    }
                                }
                            });
                            ";
                }

                printChart('gdp', $history_instance, $labels);
                printChart('population', $history_instance, $labels);
                printChart('birth_rate', $history_instance, $labels);
                printChart('death_rate', $history_instance, $labels);
                printChart('immigration_rate', $history_instance, $labels);
                printChart('emigration_rate', $history_instance, $labels);
                printChart('spending', $history_instance, $labels);
                printChart('income', $history_instance, $labels);
                printChart('debt', $history_instance, $labels);
                printChart('debt_maintenance', $history_instance, $labels);

                printChart('employment_rate', $history_instance, $labels);
                printChart('crime_level', $history_instance, $labels);
                printChart('freedom_level', $history_instance, $labels);
                printChart('civil_rights_level', $history_instance, $labels);
                printChart('health_level', $history_instance, $labels);
                printChart('tourist_attractiveness_level', $history_instance, $labels);
                printChart('education_level', $history_instance, $labels);
                printChart('culture_level', $history_instance, $labels);
                printChart('average_income', $history_instance, $labels);
                printChart('average_income_high', $history_instance, $labels);
                printChart('average_income_low', $history_instance, $labels);
                printChart('happiness', $history_instance, $labels);

                $hisdataset = "";
                foreach ($history_instance as $history_turn) {
                    $hisdataset = $hisdataset . (int)((int)$history_turn['gdp'] / (int)$history_turn['population']) . ",";
                }
                $hisdataset = rtrim($hisdataset, ",");
            ?>
                var ctx = document.getElementById('gdp_per_capitaChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [<?= $labels ?>],
                        datasets: [{
                            label: 'GDP per capita USD',
                            borderColor: '#0F1833',
                            data: [<?= $hisdataset ?>]
                        }]
                    },
                    options: {
                        title: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
                <?php
                $party_names_and_colours = array();
                foreach ($country_instance['parties'] as $party) {
                    array_push($party_names_and_colours, array(
                        'name' => $party['name'],
                        'colour' => $party['colour'],
                    ));
                }

                $percentages = array();
                foreach ($history_instance as $history_turn) {
                    foreach ($history_turn['gallups'] as $key => $party) {
                        if (!isset($percentages[$key])) {
                            $percentages[$key] = array();
                        }
                        array_push($percentages[$key], (int)$party['gallup_percentage']);
                    }
                }
                ?>
                var ctx = document.getElementById('partyPollsChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [<?= $labels; ?>],
                        datasets: [<?php
                                    $i = 0;
                                    foreach ($party_names_and_colours as $party_name_and_colour) {
                                        $data = "";
                                        foreach ($percentages[$i] as $percentage) {
                                            $data = $data . $percentage . ",";
                                        }
                                        $data = rtrim($data, ",");
                                        echo "{";
                                        echo "fill: false,";
                                        echo 'label: "' . $party_name_and_colour['name'] . '",';
                                        echo 'borderColor: "' . $party_name_and_colour['colour'] . '",';
                                        echo 'data: [' . $data . '],';
                                        echo "},";
                                        $i++;
                                    }
                                    ?>]
                    },
                    options: {
                        title: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            <?php
            }
            ?>

            $('.game_page').hide();
            $('.game_page.basic_info').show();

            window.changePage = function(pageToSee) {
                $('.game_page').hide();
                $('.game_page.' + pageToSee).show();
            }

            Highcharts.chart('parliament_chart', {
                chart: {
                    type: 'item'
                },
                title: {
                    text: '<?= $country_name; ?>´s parliament'
                },
                legend: {
                    labelFormat: '{name} <span style="opacity: 0.4">{y}</span>'
                },
                series: [{
                    name: 'Representatives',
                    keys: ['name', 'y', 'color', 'label'],
                    data: [
                        <?php
                        foreach ($parties as $party) {
                            echo '["' . $party['name'] . '", ' . $party['number_of_seats'] . ', "' . $party['colour'] . '", "' . $party['name'] . '"],';
                        }
                        ?>
                    ],
                    dataLabels: {
                        enabled: true,
                        format: '{point.label}'
                    },
                    // Circular options
                    center: ['50%', '88%'],
                    size: '120%',
                    startAngle: -100,
                    endAngle: 100
                }]
            });
        });
    </script>
<?php endwhile; ?>
<?php get_footer(); ?>