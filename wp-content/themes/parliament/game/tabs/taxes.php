<div class="container game_page taxes">
    <h1 class="w-100 text-center">
        Taxes
    </h1>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Tax</th>
                <th scope="col">Details</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $loop = new WP_Query(
                array(
                    'post_type' => 'taxes',
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
                    <td><a href="" onclick="event.preventDefault(); window.taxactions(<?= get_the_ID(); ?>)">Actions</a></td>
                </tr>
            <?php
            endwhile;
            ?>
        </tbody>
    </table>
    <div class="jumbotron taxactionspopup" style="display: none;"></div>
</div>

<form class="submit_tax_adoption_form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="put_tax_to_vote">
    <input type="hidden" name="gameCode" value="<?= $gameCode; ?>">
    <input type="hidden" name="gameID" value="<?= $gameID; ?>">
    <input type="hidden" name="proposed_by" value="<?= $player_party; ?>">
    <input type="hidden" name="taxID" class="taxID" value="">
    <input type="hidden" name="level" class="level" value="">
    <input type="hidden" name="remove" class="remove" value="false">
</form>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
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
                                <p>Current income: ` + formatUSD(parseInt(data['tax_instance']['taxes_of_gdp'] * window.gdp)) + `USD (annually)</p>
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
                                <p>Maximum income: ` + formatUSD(parseInt(data['theTax']['taxes_of_gdp'] * window.gdp)) + ` USD (annually)</p>
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
            var estimatedIncome = parseFloat(taxes_of_gdp) * parseFloat(window.gdp);
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
    });
</script>