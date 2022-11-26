<div class="container game_page me_and_my_party">

    <h1>
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