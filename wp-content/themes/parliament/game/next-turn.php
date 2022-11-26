<form class="next_turn_form text-center" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="next_turn">
    <input type="hidden" name="gameCode" value="<?= $gameCode; ?>">
    <input type="hidden" name="gameID" value="<?= $gameID; ?>">

    <input type="submit" name="submit" value="Next turn" class="btn btn-primary theActualNextTurnButton">
</form>