<div class="container game_page taxes">
    <h1>
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