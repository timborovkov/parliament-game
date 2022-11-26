<div class="text-center">
    <p>Actions left this turn: <?php echo $actions_left_this_turn; ?> | Country happiness: <?php echo $happiness; ?> | Turns to election: <?= $turns_until_election ?></p>
</div>
<ul class="nav nav-pills justify-content-center">
    <li class="nav-item">
        <a class="nav-link" href="" onclick="event.preventDefault(); window.changePage('basic_info')">Basic Info</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="" onclick="event.preventDefault(); window.changePage('country_info')">Country Info</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="" onclick="event.preventDefault(); window.changePage('geopolitics')">Geopolitics</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="" onclick="event.preventDefault(); window.changePage('me_and_my_party')">Me & my party</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="" onclick="event.preventDefault(); window.changePage('legislation')">Legislation</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="" onclick="event.preventDefault(); window.changePage('taxes')">Taxes</a>
    </li>
    <li class="nav-item mt-3">
        <?php
        require('next-turn.php');
        ?>
    </li>
</ul>