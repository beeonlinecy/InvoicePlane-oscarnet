<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('receipt'); ?></title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'paymentplane'); ?>/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
</head>
<body>
<header class="clearfix">

    <div id="logo">
        <?php echo payment_logo_pdf(); ?>
    </div>

    <div id="client">
        <div>
            <b><?php _htmlsc(format_client($payment)); ?></b>
        </div>
        <?php if ($payment->client_vat_id) {
            echo '<div>' . trans('vat_id_short') . ': ' . htmlsc($payment->client_vat_id) . '</div>';
        }
        if ($payment->client_tax_code) {
            echo '<div>' . trans('tax_code_short') . ': ' . htmlsc($payment->client_tax_code) . '</div>';
        }
        if ($payment->client_address_1) {
            echo '<div>' . htmlsc($payment->client_address_1) . '</div>';
        }
        if ($payment->client_address_2) {
            echo '<div>' . htmlsc($payment->client_address_2) . '</div>';
        }
        if ($payment->client_city || $payment->client_state || $payment->client_zip) {
            echo '<div>';
            if ($payment->client_city) {
                echo htmlsc($payment->client_city) . ' ';
            }
            if ($payment->client_state) {
                echo htmlsc($payment->client_state) . ' ';
            }
            if ($payment->client_zip) {
                echo htmlsc($payment->client_zip);
            }
            echo '</div>';
        }
        if ($payment->client_country) {
            echo '<div>' . get_country_name(trans('cldr'), htmlsc($payment->client_country)) . '</div>';
        }

        echo '<br/>';

        if ($payment->client_phone) {
            echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($payment->client_phone) . '</div>';
        } ?>
    </div>
    <div id="company">
        <div><b><?php _htmlsc($payment->user_name); ?></b></div>
        <?php if ($payment->user_vat_id) {
            echo '<div>' . trans('vat_id_short') . ': ' . htmlsc($payment->user_vat_id) . '</div>';
        }
            if ($payment->user_tax_code) {
                echo '<div>' . trans('tax_code_short') . ': ' . htmlsc($payment->user_tax_code) . '</div>';
            }
            if ($payment->user_address_1) {
                echo '<div>' . htmlsc($payment->user_address_1) . '</div>';
            }
            if ($payment->user_address_2) {
                echo '<div>' . htmlsc($payment->user_address_2) . '</div>';
            }
            if ($payment->user_city || $payment->user_state || $payment->user_zip) {
                echo '<div>';
                if ($payment->user_city) {
                    echo htmlsc($payment->user_city) . ' ';
                }
                if ($payment->user_state) {
                    echo htmlsc($payment->user_state) . ' ';
                }
                if ($payment->user_zip) {
                    echo htmlsc($payment->user_zip);
                }
                echo '</div>';
            }
            if ($payment->user_country) {
                echo '<div>' . get_country_name(trans('cldr'), htmlsc($payment->user_country)) . '</div>';
            }

            echo '<br/>';

            if ($payment->user_phone) {
                echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($payment->user_phone) . '</div>';
            }
            if ($payment->user_fax) {
                echo '<div>' . trans('fax_abbr') . ': ' . htmlsc($payment->user_fax) . '</div>';
            }
            ?>
    </div>

</header>

<main>

    <div class="payment-details clearfix">
        <table>
            <tr>
                <td><?php echo trans('payment_date') . ':'; ?></td>
                <td><?php echo date_from_mysql(htmlsc($payment->payment_date_created), true); ?></td>
            </tr>
            <tr>
                <td><?php echo trans('due_date') . ': '; ?></td>
                <td><?php echo date_from_mysql(htmlsc($payment->payment_date_due), true); ?></td>
            </tr>
            <tr>
                <td><?php echo trans('amount_due') . ': '; ?></td>
                <td><?php echo format_currency(htmlsc($payment->payment_balance)); ?></td>
            </tr>
            <?php if ($payment_method) { ?>
                <tr>
                    <td><?php echo trans('payment_method') . ': '; ?></td>
                    <td><?php _htmlsc($payment_method->payment_method_name); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <h1 class="payment-title"><?php echo trans('payment') . ' ' . htmlsc($payment->payment_number); ?></h1>

    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name"><?php _trans('item'); ?></th>
            <th class="item-desc"><?php _trans('description'); ?></th>
            <th class="item-amount text-right"><?php _trans('qty'); ?></th>
            <th class="item-price text-right"><?php _trans('price'); ?></th>
            <?php if ($show_item_discounts) { ?>
                <th class="item-discount text-right"><?php _trans('discount'); ?></th>
            <?php } ?>
            <th class="item-total text-right"><?php _trans('total'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
            foreach ($items as $item) { ?>
            <tr>
                <td><?php _htmlsc($item->item_name); ?></td>
                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                <td class="text-right">
                    <?php echo format_quantity(htmlsc($item->item_quantity)); ?>
                    <?php if ($item->item_product_unit) { ?>
                        <br>
                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                    <?php } ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency(htmlsc($item->item_price)); ?>
                </td>
                <?php if ($show_item_discounts) { ?>
                    <td class="text-right">
                        <?php echo format_currency(htmlsc($item->item_discount)); ?>
                    </td>
                <?php } ?>
                <td class="text-right">
                    <?php echo format_currency(htmlsc($item->item_total)); ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
        <tbody class="payment-sums">

        <tr>
            <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right">
                <?php _trans('subtotal'); ?>
            </td>
            <td class="text-right"><?php echo format_currency(htmlsc($payment->payment_item_subtotal)); ?></td>
        </tr>

        <?php if ($payment->payment_item_tax_total > 0) { ?>
            <tr>
                <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right">
                    <?php _trans('item_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency(htmlsc($payment->payment_item_tax_total)); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($payment_tax_rates as $payment_tax_rate) { ?>
            <tr>
                <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right">
                    <?php echo htmlsc($payment_tax_rate->payment_tax_rate_name) . ' (' . format_amount($payment_tax_rate->payment_tax_rate_percent) . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency(htmlsc($payment_tax_rate->payment_tax_rate_amount)); ?>
                </td>
            </tr>
        <?php } ?>

        <?php if ($payment->payment_discount_percent != '0.00') { ?>
            <tr>
                <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_amount(htmlsc($payment->payment_discount_percent)); ?>%
                </td>
            </tr>
        <?php } ?>
        <?php if ($payment->payment_discount_amount != '0.00') { ?>
            <tr>
                <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency(htmlsc($payment->payment_discount_amount)); ?>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right">
                <b><?php _trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency(htmlsc($payment->payment_total)); ?></b>
            </td>
        </tr>
        <tr>
            <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right">
                <?php _trans('paid'); ?>
            </td>
            <td class="text-right">
                <?php echo format_currency(htmlsc($payment->payment_paid)); ?>
            </td>
        </tr>
        <tr>
            <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right">
                <b><?php _trans('balance'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency(htmlsc($payment->payment_balance)); ?></b>
            </td>
        </tr>
        </tbody>
    </table>
</main>

<?php if (get_setting('qr_code')) { ?>
    <table class="payment-qr-code-table">
        <tr>
            <td>
                <div>
                    <strong><?php _trans('qr_code_settings_recipient'); ?>:</strong>
                    <?php echo get_setting('qr_code_recipient'); ?>
                </div>
                <div>
                    <strong><?php _trans('qr_code_settings_iban'); ?>:</strong>
                    <?php echo get_setting('qr_code_iban'); ?>
                </div>
                <div>
                    <strong><?php _trans('qr_code_settings_bic'); ?>:</strong>
                    <?php echo get_setting('qr_code_bic'); ?>
                </div>
                <div>
                    <strong><?php _trans('qr_code_settings_remittance_text'); ?>:</strong>
                    <?php echo parse_template($payment, get_setting('qr_code_remittance_text')); ?>
                </div>
            </td>
            <td class="text-right">
                <?php echo payment_qrcode(htmlsc($payment->payment_id)); ?>
            </td>
        </tr>
    </table>
<?php } ?>

<div class="payment-terms">
    <?php if ($payment->payment_terms) { ?>
        <div class="notes">
            <b><?php _trans('terms'); ?></b><br/>
            <?php echo nl2br(htmlsc($payment->payment_terms)); ?>
        </div>
    <?php } ?>
</div>

<htmlpagefooter name="footer">
    <footer>
        <?php _trans('page'); ?> {PAGENO} / {nbpg}
    </footer>
</htmlpagefooter>

</body>
</html>
