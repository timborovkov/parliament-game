<div class="container game_page legislation">

    <h1>
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