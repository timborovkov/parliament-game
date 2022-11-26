<?php
    /*
        Template name: The vote results template
    */
    get_header(); 

    session_start();

    $gameID = $_SESSION['gameID'];
    $gameCode  = $_SESSION['gameCode'];
    $legislationID  = $_SESSION['legislationID'];
    $policy_name = $_SESSION['policy_name'];
    $level = $_SESSION['level'];
    $proposed_by = $_SESSION['proposed_by'];
    $for = $_SESSION['for'];
    $against = $_SESSION['against'];
    $empty = $_SESSION['empty'];
    $vote_passed = $_SESSION['vote_passed'];
    $removal = $_SESSION['removal'];

    // remove all session variables
    session_unset();
    // destroy the session
    session_destroy();

    if ($removal == "true") {
        $removal = true;
    } else {
        $removal = false;
    }

    $player_name = get_post_meta($gameID, '_player_name', true);
    $player_party = get_post_meta($gameID, '_player_party_affiliation', true);
    $country = (int)get_post_meta($gameID, '_country', true);
    $country_name = get_the_title($country);
    $country_instance = json_decode(get_post_meta($gameID, '_country_instance', true), true);
    $parties = $country_instance['parties'];

    if ($removal) {
        $against_now = $against;
        $for_now = $for;
        $for = $against_now;
        $against = $for_now;
    }
?>

    <div class="container">
        <div class="row">
            <div class="col-12 text-center" style="padding: 20px 20px;">
                <?php
                    if ($vote_passed && !$removal): 
                ?>
                    <h3 class="text-success"><?= $policy_name; ?> bill passed and was signed in to law</h3>
                <?php
                    elseif($vote_passed && $removal): 
                ?>
                    <h3 class="text-danger"><?= $policy_name; ?> removal bill did not pass</h3>
                <?php
                    elseif(!$vote_passed && $removal): 
                ?>
                    <h3 class="text-success"><?= $policy_name; ?> removal bill was passed</h3>
                <?php
                    elseif(!$vote_passed && !$removal): 
                ?>
                    <h3 class="text-danger"><?= $policy_name; ?> bill did not pass</h3>
                <?php
                    endif;
                ?>
            </div>
            <div class="col-12 text-center">
                <h4>For: <?= $for; ?> | Empty: <?= $empty; ?> | Against: <?= $against; ?></h4>
            </div>
            <div class="col-12">
                <figure class="highcharts-figure">
                    <div id="parliament_chart"></div>
                </figure>
            </div>
            <div class="col-12 text-center">
                <a href="/the_game/<?= $gameCode; ?>" class="btn-primary btn">Back to the game</a>
            </div>
        </div>
        <div class="row" style="padding-top: 20px;">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Party name</th>
                        <th scope="col">Gallup change</th>
                        <th scope="col">Gallup total</th>
                        <th scope="col">Vote recomendation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($parties as $key => $party):
                    ?>
                        <tr>
                            <td><p><?= $party['name'] ?></p></td>
                            <td>
                                <?php
                                    if ($party['gallup_change'] > 0) {
                                        echo "<p class='text-primary'>+".$party['gallup_change']." % -points</p>";
                                    } elseif ($party['gallup_change'] < 0) {
                                        echo "<p class='text-danger'>".$party['gallup_change']." % -points</p>";
                                    } else {
                                        echo "<p>".$party['gallup_change']." % -points</p>";
                                    }
                                ?> 
                            </td> 
                            <td><p><?= $party['gallup_percentage'] ?>%</p></td>
                            <td><p><?php
                                if ($removal) {
                                    if ($party['party_line'] == 'for') {
                                        echo "against";
                                    } elseif ($party['party_line'] == 'against') {
                                        echo "for";
                                    } else {
                                        echo $party['party_line'];
                                    }
                                } else {
                                    echo $party['party_line'];;
                                }
                            ?></p></td>
                        </tr>
                    <?php
                        endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) { 
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
                            echo '["Aye", '.$for.', "#056608", "Votes in favour"],';
                            echo '["Empty", '.$empty.', "#c2c5cc", "Empty votes"],';
                            echo '["No", '.$against.', "#d92121", "Votes against"],';
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

<?php get_footer(); ?>