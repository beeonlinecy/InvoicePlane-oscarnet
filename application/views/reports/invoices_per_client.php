<!DOCTYPE html>
<html lang="<?php echo trans('cldr'); ?>">
<head>
    <title><?php echo trans('invoices_per_client'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/reports.css" type="text/css">
</head>
<body>

<h3 class="report_title">
    <?php echo trans('invoices_per_client'); ?><br/>
    <small><?php echo $from_date . ' - ' . $to_date ?></small>
</h3>

<?php
$client_id = '';
$client_total = 0;
$total_row = function ($total) {
    ?>
    <tr>
        <th><?php echo trans('total'); ?></th>
        <th></th>
        <th class="amount"><?php echo format_currency($total); ?></th>
    </tr>
    <?php
};
?>
<table>
    <?php
        foreach ($results as $result) {  ?>
            <?php if ($client_id != $result->client_id) {
                if ($client_id !== '') {
                    $total_row($client_total);
                }
                $client_id = $result->client_id;
                $client_total = 0; ?>
                <tr>
                    <th><?php _htmlsc(format_client($result)); ?></th>
                    <th></th>
                    <th></th>
                </tr>
            <?php } ?>
            <?php $client_total += $result->invoice_total; ?>
            <tr>
                <td><?php echo date_from_mysql($result->invoice_date_created, true); ?></td>
                <td><?php echo $result->invoice_number; ?></td>
                <td class="amount"><?php echo format_currency($result->invoice_total); ?></td>
            </tr>
    <?php }
    if ($client_id !== '') {
        $total_row($client_total);
    } ?>
</table>

</body>
</html>
