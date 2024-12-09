<?php must_have_access(); ?>

<?php if (get_option('payment_gw_shop/payments/gateways/flutterwave', 'payments')): ?>
    <div class="d-flex align-items-center mb-3">
        <span class="badge bg-green me-2"></span>
        <p class="text-success mb-0"><?php _e("Activated") ?> </p>
    </div>
<?php endif; ?>


<div class="form-group d-flex align-items-center justify-content-between">
    <div>
        <label class="form-label d-block"><?php _e("Additional message to the user") ?></label>
        <small class="text-muted d-block mb-2"><?php _e('Show additional message when Flutterwave is selected.'); ?></small>

    </div>
    <div class="form-check form-check-single form-switch" style="width: unset;">

        <input type="checkbox" id="flutterwave_show_msg1" name="flutterwave_show_msg" class="mw_option_field form-check-input" data-value-unchecked="n" data-value-checked="y" data-bs-toggle="collapse" data-bs-target="#show-additional-message-to-the-user" data-option-group="payments" value="y" <?php if (get_option('flutterwave_show_msg', 'payments') == 'y'): ?> checked="checked" <?php endif; ?>>

    </div>
</div>

<div class="form-group collapse <?php if (get_option('flutterwave_show_msg', 'payments') == 'y'): ?> show <?php endif; ?>" id="show-additional-message-to-the-user">
    <label class="form-label d-block"><?php _e('Message'); ?></label>
    <textarea name="flutterwave_msg" class="mw_option_field form-control" placeholder="<?php _e("e.g. Thank you for your order!"); ?>" data-option-group="payments"><?php print get_option('flutterwave_msg', 'payments') ?></textarea>
</div>

<div class="form-group d-flex align-items-center justify-content-between">
    <div>
        <label class="form-label d-block"><?php _e("Enable Flutterwave Testmode") ?></label>
        <small class="text-muted d-block mb-2"><?php _e('Only use Test Mode for testing purpose'); ?></small>

    </div>
    <div class="form-check form-check-single form-switch" style="width: unset;">

        <input type="checkbox" id="flutterwave_testmode" name="flutterwave_testmode" class="mw_option_field form-check-input" data-value-unchecked="n" data-value-checked="y" data-option-group="payments" value="y" <?php if (get_option('flutterwave_testmode', 'payments') == 'y'): ?> checked="checked" <?php endif; ?>>

    </div>
</div>

<div class="form-group">
    <label class="form-label"><?php _e("Flutterwave Live Public Key"); ?>: </label>
    <input type="text" class="mw_option_field form-control" name="flutterwave_public_key" placeholder="FLWPUBK-...-X" data-option-group="payments" value="<?php print get_option('flutterwave_public_key', 'payments'); ?>">
</div>

<div class="form-group">
    <label class="form-label"><?php _e("Flutterwave Live Secret Key"); ?>: </label>

    <input type="text" class="mw_option_field form-control" name="flutterwave_secret_key" placeholder="FLWSECK-...-X" data-option-group="payments" value="<?php print get_option('flutterwave_secret_key', 'payments'); ?>">
</div>

<div class="form-group">
    <label class="form-label"><?php _e("Flutterwave Currency"); ?>: </label>

    <input type="text" class="mw_option_field form-control" name="flutterwave_currency" placeholder="USD" data-option-group="payments" value="<?php print get_option('flutterwave_currency', 'payments'); ?>">
</div>

<div class="form-group">
    <label class="form-label"><?php _e("Site Title"); ?>: </label>

    <input type="text" class="mw_option_field form-control" name="flutterwave_title" placeholder="My Store Name" data-option-group="payments" value="<?php print get_option('flutterwave_title', 'payments'); ?>">
</div>