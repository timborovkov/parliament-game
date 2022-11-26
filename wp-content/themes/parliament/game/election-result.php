<?php
if ($show_election_results) :
?>
    <div class="container election_results">
        <h2 class="w-100 text-center">Election results</h2>
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Logo</th>
                    <th scope="col">Party name</th>
                    <th scope="col">Leader</th>
                    <th scope="col">Seats</th>
                    <th scope="col">Previous seats</th>
                    <th scope="col">Gallup</th>
                    <th scope="col">Share of votes</th>
                    <th scope="col">Previous share of votes</th>
                    <th scope="col">Amount of votes</th>
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
                            <p><?= $party['old_number_of_seats'] ?></p>
                        </td>
                        <td>
                            <p><?= $party['gallup_percentage'] ?> %</p>
                        </td>
                        <td>
                            <p><?= $party['votePercent'] ?> %</p>
                        </td>
                        <td>
                            <p><?= $party['old_share_of_votes'] ?> %</p>
                        </td>
                        <td>
                            <p><?= $party['amountOfVotes'] ?></p>
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
<?php
endif;
?>