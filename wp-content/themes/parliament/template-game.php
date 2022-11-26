<?php
/*
Template Name: Game
*/
?>
<?php get_header(); 

$countries = Array();

$loop = new WP_Query( 
    array( 
            'post_type' => 'country',
            'posts_per_page' => -1, 
        )
); 
while ( $loop->have_posts() ) : $loop->the_post(); 
    array_push($countries, Array(
        'name' => get_the_title(),
        'ID' => get_the_ID(),
        'parties' => carbon_get_the_post_meta('parties'),
    ));
endwhile;
?>

<?php while ( have_posts() ) : the_post(); ?>
    <?php
    if (get_post_thumbnail_id() != null && get_post_thumbnail_id() != "") {
        ?>
    <div class="other_page_hero" style="background: url('<?= wp_get_attachment_image_src(get_post_thumbnail_id(), 'full')[0]; ?>');">
        <br>
    </div>
        <?php
    }
    ?>
    <div class="container">
        <?php echo get_the_content(); ?>
        <div class="row">
            <div class="col-md-6">
                <h2>Start new game</h2>
                <?php
                    session_start();
                    if (isset($_SESSION['gameCode'])) {
                ?>

                <h3>Your new game has been created.</h3>
                <h5>The game code is:</h5>
                <h5 class="the_game_code"><?= $_SESSION['gameCode']; ?></h5>

                <?php
                    } else {
                ?>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="create_game">
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" class="form-control" name="creategame_email">
                    </div>
                    <div class="form-group">
                        <label>Your name</label>
                        <input type="text" class="form-control" name="creategame_player_name">
                    </div>
                    <div class="form-group">
                        <label>Pick country</label><br>
                        <select name="creategame_country">
                            <?php
                                foreach ($countries as $key => $country) {
                                    echo "<option value='".$country['ID']."'>".$country['name']."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pick your party</label><br>
                        <select name="creategame_party">
                            <?php
                                foreach ($countries as $key => $country) {
                                    echo "<optgroup label='".$country['name']."'>";
                                    foreach ($country['parties'] as $key => $party) {
                                        echo "<option value='".$party['name']."'>".$party['name']."</option>";
                                    }
                                    echo "</optgroup>";
                                }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="creategame_submit" class="btn btn-primary">Create game</button>
                </form>
                <?php
                    }
                ?>
            </div>
            <div class="col-md-6">
                <h2>Join existing game</h2>
                <form>
                    <div class="form-group">
                        <label>Game code</label>
                        <input type="text" class="form-control joining_gamecode">
                    </div>
                    <a onclick="joinGame();" class="btn btn-primary text-white">Join game</a>
                </form>
            </div>
        </div>
    </div>
<?php endwhile; ?>

<script type="text/javascript">
    function joinGame () {
        var gamecode = $('.joining_gamecode').val();
        if (gamecode != "" || gamecode == null) {
            var gameurl = '/the_game/'+gamecode;
            window.location.href = gameurl;
        } else {
            alert("Please enter the game code to start playing")
        }
    }
</script>

<?php
// remove all session variables
session_unset();
// destroy the session
session_destroy();

get_footer(); ?>