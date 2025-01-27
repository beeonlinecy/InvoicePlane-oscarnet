<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th><?php _trans('status'); ?></th>
            <th><?php _trans('expense'); ?></th>
            <th><?php _trans('created'); ?></th>
            <th><?php _trans('due_date'); ?></th>
            <th><?php _trans('client_name'); ?></th>
            <th class="amount"><?php _trans('amount'); ?></th>
            <th class="amount last"><?php _trans('balance'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php
        $invoice_idx = 1;
        $invoice_count = count($expenses);
        $invoice_list_split = $invoice_count > 3 ? $invoice_count / 2 : 9999;
        foreach ($expenses as $expense) {
            // Disable read-only if not applicable
            if ($this->config->item('disable_read_only') == true) {
                $expense->is_read_only = 0;
            }
            // Convert the dropdown menu to a dropup if invoice is after the invoice split
            $dropup = $invoice_idx > $invoice_list_split ? true : false;
            ?>
            <tr>
                <td>
                    <span class="label <?php echo $expense_statuses[$expense->expense_status_id]['class']; ?>">
                        <?php echo $expense_statuses[$expense->expense_status_id]['label'];
                        if ($expense->invoice_sign == '-1') { ?>
                            &nbsp;<i class="fa fa-credit-invoice" title="<?php echo trans('credit_invoice') ?>"></i>
                        <?php } ?>
                        <?php if ($expense->is_read_only) { ?>
                            &nbsp;<i class="fa fa-read-only" title="<?php _trans('read_only') ?>"></i>
                        <?php } ?>
                        <?php if ($expense->invoice_is_recurring) { ?>
                            &nbsp;<i class="fa fa-refresh" title="<?php echo trans('recurring') ?>"></i>
                        <?php } ?>
                    </span>
                </td>

                <td>
                    <a href="<?php echo site_url('expenses/view/' . $expense->invoice_id); ?>"
                       title="<?php _trans('edit'); ?>">
                        <?php echo($expense->invoice_number ? $expense->invoice_number : $expense->invoice_id); ?>
                    </a>
                </td>

                <td>
                    <?php echo date_from_mysql($expense->invoice_date_created); ?>
                </td>

                <td>
                    <span class="<?php if ($expense->is_overdue) { ?>font-overdue<?php } ?>">
                        <?php echo date_from_mysql($expense->invoice_date_due); ?>
                    </span>
                </td>

                <td>
                    <a href="<?php echo site_url('clients/view/' . $expense->client_id); ?>"
                       title="<?php _trans('view_client'); ?>">
                        <?php _htmlsc(format_client($expense)); ?>
                    </a>
                </td>

                <td class="amount <?php if ($expense->invoice_sign == '-1') {
                    echo 'text-danger';
                }; ?>">
                    <?php echo format_currency($expense->invoice_total); ?>
                </td>

                <td class="amount last">
                    <?php echo format_currency($expense->invoice_balance); ?>
                </td>

                <td>
                    <div class="options btn-group<?php echo $dropup ? ' dropup' : ''; ?>">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if ($expense->is_read_only != 1) { ?>
                                <li>
                                    <a href="<?php echo site_url('expenses/view/' . $expense->invoice_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <li>
                                <a href="<?php echo site_url('expenses/generate_pdf/' . $expense->invoice_id); ?>"
                                   target="_blank">
                                    <i class="fa fa-print fa-margin"></i> <?php _trans('download_pdf'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('mailer/invoice/' . $expense->invoice_id); ?>">
                                    <i class="fa fa-send fa-margin"></i> <?php _trans('send_email'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="invoice-add-payment"
                                   data-invoice-id="<?php echo $expense->invoice_id; ?>"
                                   data-invoice-balance="<?php echo $expense->invoice_balance; ?>"
                                   data-invoice-payment-method="<?php echo $expense->payment_method; ?>">
                                    <i class="fa fa-money fa-margin"></i>
                                    <?php _trans('enter_payment'); ?>
                                </a>
                            </li>
                            <?php if (
                                $invoice->invoice_status_id == 1 ||
                                ($this->config->item('enable_invoice_deletion') === true && $expense->is_read_only != 1)
                            ) { ?>
                                <li>
                                    <form action="<?php echo site_url('invoices/delete/' . $expense->invoice_id); ?>"
                                          method="POST">
                                        <?php _csrf_field(); ?>
                                        <button type="submit" class="dropdown-button"
                                                onclick="return confirm('<?php _trans('delete_invoice_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                        </button>
                                    </form>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php
            $invoice_idx++;
        } ?>
        </tbody>

    </table>
</div>
