<div class="container">
    <div class="personal_data jumbotron">
        <h5><b><?= $player_name ?></b>, <?= $player_party ?></h5>
        <h4>Turn: <b class="turn_number"><?= $turn ?></b></h4>
        <p>1 year = 5 turns</p>
        <h4>Actions left this turn: <b><?= $actions_left_this_turn ?></b></h4>
        <h4>Country happiness: <b><?= $happiness ?></b></h4>
        <h4>Turns to election: <b><?= $turns_until_election ?></b></h4>
    </div>
</div>