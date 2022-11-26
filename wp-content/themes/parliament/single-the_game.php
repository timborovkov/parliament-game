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

    <script type="text/javascript">
        window.population = <?= $population; ?>;
        window.gdp = <?= $gdp; ?>;
        window.adjustToInflation = <?= $adjustToInflation; ?>;

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
                    text: "<?= $country_name; ?>'s parliament"
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