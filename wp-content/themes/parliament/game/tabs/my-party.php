<div class="container game_page me_and_my_party">
    <h1 class="w-100 text-center">
        Me & my party
    </h1>
    <ul class="nav nav-pills justify-content-center">
        <li class="nav-item">
            <a class="nav-link" href="" onclick="event.preventDefault();window.meAndMyParty('fundraiser');">Fundraiser</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="" onclick="event.preventDefault();window.meAndMyParty('speech');">Speech or Event</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="" onclick="event.preventDefault();window.meAndMyParty('marketing_campaign');">Marketing campaign</a>
        </li>
    </ul>
    <div class="partyActionBlock"></div>
    <div class="partyActionResults jumbotron" style="display:none;">
        <h3>Results</h3>
        <h4 class="note"></h4>
        <p>Money: <b class="moneyResults"></b></p>
        <p>Polls change: <b class="pollsChangeResults"></b></p>
        <p>Polls total: <b class="pollsTotalResults"></b></p>
    </div>

    <h2 class="w-100 text-center">Budget statement</h2>

    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Category</th>
                <th scope="col">Sum (USD)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = ($country_instance['party_funding_per_mp_per_turn'] * $myParty['number_of_seats']) - ($myParty['party_instance']['fixed_expenditure_per_mp_per_turn'] * $myParty['number_of_seats']);
            ?>
            <tr>
                <td>Fixed expenditure per turn</td>
                <td>- <?= formatUSD($myParty['party_instance']['fixed_expenditure_per_mp_per_turn'] * $myParty['number_of_seats']); ?> USD</td>
            </tr>
            <tr>
                <td>Income per turn</td>
                <td>+ <?= formatUSD($country_instance['party_funding_per_mp_per_turn'] * $myParty['number_of_seats']); ?> USD</td>
            </tr>
            <tr class="table-primary">
                <td>TOTAL</td>
                <td><?= formatUSD($total); ?> USD</td>
            </tr>
            <tr class="table-success">
                <td>IN THE BANK</td>
                <td><?= formatUSD($myParty['party_instance']['balance']); ?> USD</td>
            </tr>
        </tbody>
    </table>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {

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
    });
</script>