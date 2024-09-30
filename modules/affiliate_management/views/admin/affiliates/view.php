<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (is_client_logged_in()) : ?>
    <?php require_once('_view_content.php'); ?>
<?php else : ?>
    <?php init_head(); ?>
    <div id="wrapper">
        <?php require_once('_view_content.php'); ?>
    </div>

    <?php init_tail(); ?>
    </body>

    </html>
<?php endif; ?>