<?php

$flutterwave_show_msg = (get_option('flutterwave_show_msg', 'payments')) == 'y';
$flutterwave_msg = get_option('flutterwave_msg', 'payments');
?>
<?php if ($flutterwave_show_msg == true): ?>

  <div>
    <p class="alert alert-warning"><small>
        <?php if ($flutterwave_msg == true): ?>
          <?php print $flutterwave_msg; ?>
        <?php else: ?>
          <strong> *
            <?php _e("Note"); ?>
          </strong>
          <?php _e("Your shopping cart will be emptied when you complete the order"); ?>
        <?php endif; ?>
      </small> </p>
  </div>
<?php endif; ?>