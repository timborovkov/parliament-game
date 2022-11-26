<div class="container game_page legislation">

    <h1 class="w-100 text-center">
        Legislation
    </h1>

    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Legislation</th>
                <th scope="col">Details</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $loop = new WP_Query(
                array(
                    'post_type' => 'policies',
                    'posts_per_page' => -1,
                )
            );
            while ($loop->have_posts()) : $loop->the_post();
            ?>
                <tr>
                    <td style="width: 180px;"><?= get_the_title(); ?></td>
                    <td>
                        <?= get_the_content(); ?>
                    </td>
                    <td><a href="" onclick="event.preventDefault(); window.policyactions(<?= get_the_ID(); ?>)">Actions</a></td>
                </tr>
            <?php
            endwhile;
            ?>
        </tbody>
    </table>
    <div class="jumbotron policyactionspopup" style="display: none;"></div>
</div>

<form class="submit_legislation_adoption_form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="put_legislation_to_vote">
    <input type="hidden" name="gameCode" value="<?= $gameCode; ?>">
    <input type="hidden" name="gameID" value="<?= $gameID; ?>">
    <input type="hidden" name="proposed_by" value="<?= $player_party; ?>">
    <input type="hidden" name="legislationID" class="legislationID" value="">
    <input type="hidden" name="level" class="level" value="">
    <input type="hidden" name="remove" class="remove" value="false">
</form>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
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
                                <p>Current cost: ` + formatUSD(Math.round(data['policy_instance']['policy_budgetary_cost_per_capita'] * window.population * window.adjustToInflation)) + ' USD (annually), ' + formatUSD(Math.round(data['policy_instance']['policy_budgetary_cost_per_capita'] * window.adjustToInflation)) + ' USD per capita ' + `</p>
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
                                <p>Maximum cost: ` + formatUSD(Math.round(data['thePolicy']['policy_budgetary_cost_per_capita'] * window.population * window.adjustToInflation)) + ' USD (annually), ' + formatUSD(Math.round(data['thePolicy']['policy_budgetary_cost_per_capita'] * window.adjustToInflation)) + ' USD per capita ' + `</p>
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
            var estimatedCostPerCapita = estimatedCostPerCapitaPerLevel * policyLevel * window.adjustToInflation;
            var estimatedCost = estimatedCostPerCapita * window.population;
            $('.policyEffects').html('Estimated cost: ' + formatUSD(Math.round(estimatedCost)) + ' USD (annually), ' + formatUSD(Math.round(estimatedCostPerCapita)) + ' USD per capita ');

            // Calculate happiness
            var estimatedHappinessPerLevel = happiness_score_effects / 100;
            var estimatedHappiness = Math.round(estimatedHappinessPerLevel * policyLevel);
            $('.policyEffects').html($('.policyEffects').html() + '<br> Happiness effects: ' + estimatedHappiness);

            // Display the level
            $('.policyEffects').html($('.taxEffects').html() + '<br> Level: ' + taxLevel);
        }
    });
</script>