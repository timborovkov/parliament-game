<div class="container game_page country_info">
    <h1 class="w-100 text-center">
        Country info
    </h1>
    <p>Credit rating: <?= $credit_rating; ?></p>
    <p>Prime minister: <?= $prime_minister; ?></p>

    <?php
    if (!empty($history_instance[0])) {
    ?>
        <div class="history">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="political-tab" data-toggle="tab" href="#political" role="tab" aria-controls="political" aria-selected="true">Political</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="economical-tab" data-toggle="tab" href="#economical" role="tab" aria-controls="economical" aria-selected="false">Economical</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="goverment-tab" data-toggle="tab" href="#goverment" role="tab" aria-controls="goverment" aria-selected="false">Goverment finance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="demographics-tab" data-toggle="tab" href="#demographics" role="tab" aria-controls="demographics" aria-selected="false">Demographics</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="other-tab" data-toggle="tab" href="#other" role="tab" aria-controls="other" aria-selected="false">Social metrics</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="political" role="tabpanel" aria-labelledby="political-tab">
                    <h2 class="w-100 text-center">Parties</h2>
                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Logo</th>
                                <th scope="col">Party name</th>
                                <th scope="col">Leader</th>
                                <th scope="col">Seats</th>
                                <th scope="col">Gallup</th>
                                <th scope="col">Share of votes <br>in the last election</th>
                                <th scope="col">In the goverment?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($parties as $key => $party) :
                            ?>
                                <tr>
                                    <td>
                                        <img src="<?= wp_get_attachment_image_src($party['logo'], 'large')[0]; ?>" height="50">
                                    </td>
                                    <td>
                                        <p><?= $party['name'] ?></p>
                                    </td>
                                    <td>
                                        <p><?= $party['leader'] ?></p>
                                    </td>
                                    <td>
                                        <p><?= $party['number_of_seats'] ?></p>
                                    </td>
                                    <td>
                                        <p><?= $party['gallup_percentage'] ?> %</p>
                                    </td>
                                    <td>
                                        <p><?= $party['last_election_share'] ?> %</p>
                                    </td>
                                    <td>
                                        <p>
                                            <?php
                                            if ($party['goverment'] == "true") {
                                                echo "Yes";
                                            }
                                            ?>
                                        </p>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="economical" role="tabpanel" aria-labelledby="economical-tab">

                    <h2 class="w-100 text-center">GDP (annual in Billions USD)</h2>

                    <canvas id="gdpChart"></canvas>

                    <h2 class="w-100 text-center">GDP per capita (annual in USD)</h2>

                    <canvas id="gdp_per_capitaChart"></canvas>

                    <h2 class="w-100 text-center">Employment rate (%)</h2>

                    <canvas id="employment_rateChart"></canvas>

                    <h2 class="w-100 text-center">Average monthly income in USD</h2>

                    <canvas id="average_incomeChart"></canvas>

                    <h2 class="w-100 text-center">Average monthly income of top 10% in USD</h2>

                    <canvas id="average_income_highChart"></canvas>

                    <h2 class="w-100 text-center">Average monthly income of bottom 10% in USD</h2>

                    <canvas id="average_income_lowChart"></canvas>
                </div>
                <div class="tab-pane fade" id="goverment" role="tabpanel" aria-labelledby="goverment-tab">

                    <h2 class="w-100 text-center">Budget statement (annual)</h2>

                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Category</th>
                                <th scope="col">Sum (USD)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 10 * $population * $adjustToInflation;
                            foreach ($policies_instance as $key => $policy) {
                                echo "<tr>";
                                echo "<td>" . $policy['name'] . "</td>";
                                echo "<td>- " . formatUSD($population * $policy['policy_budgetary_cost_per_capita'] * $adjustToInflation) . " USD</td>";
                                echo "</tr>";
                                $total = $total + $population * $policy['policy_budgetary_cost_per_capita'];
                            }
                            $total = ($total * $adjustToInflation) - $country_instance['gdp'] * get_post_meta($gameID, '_taxes_of_gdp', true);
                            $total = $total + end($history_instance)['debt_maintenance'];
                            ?>
                            <tr>
                                <td>Goverment upkeep</td>
                                <td>- <?= formatUSD($population * 10 * $adjustToInflation); ?> USD</td>
                            </tr>
                            <tr>
                                <td>Goverment debt maintenance</td>
                                <td>- <?= formatUSD(end($history_instance)['debt_maintenance']); ?> USD</td>
                            </tr>
                            <tr>
                                <td>Tax income</td>
                                <td>+ <?= formatUSD($country_instance['gdp'] * get_post_meta($gameID, '_taxes_of_gdp', true)); ?> USD</td>
                            </tr>
                            <tr class="table-primary">
                                <td>TOTAL</td>
                                <td><?= formatUSD(0 - $total); ?> USD</td>
                            </tr>
                            <tr class="table-danger">
                                <td>DEBT</td>
                                <td><?= formatUSD(get_post_meta($gameID, '_goverment_debt', true)); ?> USD</td>
                            </tr>
                            <tr class="table-success">
                                <td>GDP</td>
                                <td><?= formatUSD($country_instance['gdp']); ?> USD</td>
                            </tr>
                        </tbody>
                    </table>

                    <h2 class="w-100 text-center">Spending (per turn in USD)</h2>

                    <canvas id="spendingChart"></canvas>

                    <h2 class="w-100 text-center">Income (per turn in USD)</h2>

                    <canvas id="incomeChart"></canvas>

                    <h2 class="w-100 text-center">Debt (total in USD)</h2>

                    <canvas id="debtChart"></canvas>

                    <h2 class="w-100 text-center">Debt maintenance cost (per turn in USD)</h2>

                    <canvas id="debt_maintenanceChart"></canvas>
                </div>
                <div class="tab-pane fade" id="demographics" role="tabpanel" aria-labelledby="demographics-tab">

                    <h2 class="w-100 text-center">Population (Millions)</h2>

                    <canvas id="populationChart"></canvas>

                    <h2 class="w-100 text-center">Birth rate (% population change per turn)</h2>

                    <canvas id="birth_rateChart"></canvas>

                    <h2 class="w-100 text-center">Death rate (% population change per turn)</h2>

                    <canvas id="death_rateChart"></canvas>

                    <h2 class="w-100 text-center">Immigration rate (% population change per turn)</h2>

                    <canvas id="immigration_rateChart"></canvas>

                    <h2 class="w-100 text-center">Emigration rate (% population change per turn)</h2>

                    <canvas id="emigration_rateChart"></canvas>
                </div>
                <div class="tab-pane fade" id="other" role="tabpanel" aria-labelledby="other-tab">

                    <h2 class="w-100 text-center">Happiness level</h2>

                    <canvas id="happinessChart"></canvas>

                    <h2 class="w-100 text-center">Crime level 0-100</h2>

                    <canvas id="crime_levelChart"></canvas>

                    <h2 class="w-100 text-center">Freedom level 0-100</h2>

                    <canvas id="freedom_levelChart"></canvas>

                    <h2 class="w-100 text-center">Civil rights level 0-100</h2>

                    <canvas id="civil_rights_levelChart"></canvas>

                    <h2 class="w-100 text-center">Health level 0-100</h2>

                    <canvas id="health_levelChart"></canvas>

                    <h2 class="w-100 text-center">Tourist attractiveness level 0-100</h2>

                    <canvas id="tourist_attractiveness_levelChart"></canvas>

                    <h2 class="w-100 text-center">Education level 0-100</h2>

                    <canvas id="education_levelChart"></canvas>

                    <h2 class="w-100 text-center">Culture attractiveness level 0-100</h2>

                    <canvas id="culture_levelChart"></canvas>
                </div>
            </div>
        </div>
    <?php
    } else {
        echo ("<h2 class='w-100 text-center'>No historical data available</h2>");
    }
    ?>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        // Load charts 
        <?php
        $labels = "";
        foreach ($history_instance as $history_turn) {
            $turn_number = strval($history_turn['turn']);
            $labels = $labels . "'" . $turn_number . "'" . ",";
        }
        $labels = rtrim($labels, ",");
        if (!empty($history_instance[0]) && $history_instance != null) {

            function printChart($parID, $history_instance, $labels, $max = null)
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

                if ($parID == "population") {
                    $hisdataset = "";
                    foreach ($history_instance as $history_turn) {
                        $hisdataset = $hisdataset . round((float)$history_turn[$parID] / 1000000, 2) . ",";
                    }
                }

                $hisdataset = rtrim($hisdataset, ",");

                if ($max) {
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
                                    min: 0, // minimum value
                                    max: " . $max . " // maximum value
                                }
                              }]
                            }
                        }
                    });
                    ";
                } else {
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
                                    min: 0, // minimum value
                                }
                              }]
                            }
                        }
                    });
                    ";
                }
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

            printChart('average_income', $history_instance, $labels);
            printChart('average_income_high', $history_instance, $labels);
            printChart('average_income_low', $history_instance, $labels);

            printChart('employment_rate', $history_instance, $labels, 100);
            printChart('crime_level', $history_instance, $labels, 100);
            printChart('freedom_level', $history_instance, $labels, 100);
            printChart('civil_rights_level', $history_instance, $labels, 100);
            printChart('health_level', $history_instance, $labels, 100);
            printChart('tourist_attractiveness_level', $history_instance, $labels, 100);
            printChart('education_level', $history_instance, $labels, 100);
            printChart('culture_level', $history_instance, $labels, 100);
            printChart('happiness', $history_instance, $labels, 100);

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
    });
</script>