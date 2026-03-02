<div id="headerbar">
    <h1 class="headerbar-title"><?php echo sprintf(trans('expense') . ' #%s', $expense->expense_number); ?></h1>
    
    <div class="headerbar-item pull-right">
        <div class="btn-group">
            <?php if ($expense->expense_status_id == 1): ?>
            <a href="<?php echo site_url('expenses/mark_confirmed/' . $expense->expense_id); ?>" 
               class="btn btn-success btn-sm" 
               onclick="return confirm('<?php _trans('confirm_expense_status_change'); ?>');">
                <i class="fa fa-check"></i> <?php _trans('mark_confirmed'); ?>
            </a>
            <?php endif; ?>
            
            <?php if ($expense->expense_status_id == 2): ?>
            <a href="<?php echo site_url('expenses/mark_paid/' . $expense->expense_id); ?>" 
               class="btn btn-success btn-sm" 
               onclick="return confirm('<?php _trans('confirm_expense_status_change'); ?>');">
                <i class="fa fa-money"></i> <?php _trans('mark_paid'); ?>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo site_url('expenses/edit/' . $expense->expense_id); ?>" class="btn btn-primary btn-sm">
                <i class="fa fa-edit"></i> <?php _trans('edit'); ?>
            </a>
            
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                    <?php _trans('more'); ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#" onclick="window.print(); return false;">
                            <i class="fa fa-print"></i> <?php _trans('print'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo site_url('expenses/delete/' . $expense->expense_id); ?>" 
                           onclick="return confirm('<?php _trans('confirm_delete'); ?>');">
                            <i class="fa fa-trash"></i> <?php _trans('delete'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="headerbar-item pull-right visible-lg">
        <span class="label <?php echo $expense->expense_status_id == 1 ? 'label-warning' : ($expense->expense_status_id == 2 ? 'label-info' : 'label-success'); ?>">
            <?php 
            switch ($expense->expense_status_id) {
                case 1: _trans('new'); break;
                case 2: _trans('confirmed'); break;
                case 3: _trans('paid'); break;
            }
            ?>
        </span>
    </div>
</div>

<div class="content">

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">

            <div class="row">
                <div class="col-xs-12">
                    
                    <!-- Expense Info Panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <span style="color: <?php echo $this->db->where('expense_category_id', $expense->expense_category_id)->get('ip_expense_categories')->row()->expense_category_color; ?>">
                                    <i class="<?php echo $this->db->where('expense_category_id', $expense->expense_category_id)->get('ip_expense_categories')->row()->expense_category_icon; ?>"></i>
                                </span>
                                <?php _trans('expense_details'); ?>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _trans('expense_number'); ?></label>
                                        <p><?php echo $expense->expense_number; ?></p>
                                    </div>
                                    <div class="form-group">
                                        <label><?php _trans('expense_date'); ?></label>
                                        <p><?php echo date_from_mysql($expense->expense_date_created); ?></p>
                                    </div>
                                    <div class="form-group">
                                        <label><?php _trans('due_date'); ?></label>
                                        <p>
                                            <?php echo date_from_mysql($expense->expense_date_due); ?>
                                            <?php if ($expense->is_overdue && $expense->expense_status_id != 3): ?>
                                                <span class="label label-danger">
                                                    <?php _trans('overdue'); ?> 
                                                    (<?php echo $expense->days_overdue; ?> <?php _trans('days'); ?>)
                                                </span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _trans('expense_category'); ?></label>
                                        <p>
                                            <span style="color: <?php echo $this->db->where('expense_category_id', $expense->expense_category_id)->get('ip_expense_categories')->row()->expense_category_color; ?>">
                                                <i class="<?php echo $this->db->where('expense_category_id', $expense->expense_category_id)->get('ip_expense_categories')->row()->expense_category_icon; ?>"></i>
                                                <?php echo $this->db->where('expense_category_id', $expense->expense_category_id)->get('ip_expense_categories')->row()->expense_category_name; ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label><?php _trans('created_by'); ?></label>
                                        <p><?php echo $expense->user_name; ?></p>
                                    </div>
                                    <div class="form-group">
                                        <label><?php _trans('currency'); ?></label>
                                        <p><?php echo $expense->expense_currency_code; ?></p>
                                    </div>
                                    <div class="form-group">
                                        <label><?php _trans('payment_method'); ?></label>
                                        <p><?php echo $payment_method ? $payment_method->payment_method_name : _trans('none'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php _trans('expense_items'); ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php _trans('item'); ?></th>
                                            <th><?php _trans('description'); ?></th>
                                            <th class="text-right"><?php _trans('quantity'); ?></th>
                                            <th class="text-right"><?php _trans('price'); ?></th>
                                            <th class="text-right"><?php _trans('total'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!$items): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                <em><?php _trans('no_items_found'); ?></em>
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $item->item_name; ?></strong>
                                            </td>
                                            <td>
                                                <?php echo nl2br($item->item_description); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo format_amount($item->item_quantity); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo format_amount($item->item_price); ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo format_amount($item->item_quantity * $item->item_price); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Totals Panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php _trans('totals'); ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <?php $expense_amounts = $this->db->where('expense_id', $expense->expense_id)->get('ip_expense_amounts')->row(); ?>
                                        <tr>
                                            <td class="text-right"><strong><?php _trans('subtotal'); ?>:</strong></td>
                                            <td class="text-right"><?php echo format_amount($expense_amounts ? $expense_amounts->expense_item_subtotal : 0); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><strong><?php _trans('item_tax_total'); ?>:</strong></td>
                                            <td class="text-right"><?php echo format_amount($expense_amounts ? $expense_amounts->expense_item_tax_total : 0); ?></td>
                                        </tr>
                                        <?php if ($expense_tax_rates): ?>
                                        <?php foreach ($expense_tax_rates as $tax_rate): ?>
                                        <tr>
                                            <td class="text-right">
                                                <strong><?php echo $tax_rate->tax_rate_name; ?>:</strong>
                                            </td>
                                            <td class="text-right">
                                                <?php echo format_amount($tax_rate->expense_tax_rate_amount); ?>
                                                <?php if ($tax_rate->include_tax): ?>
                                                    <small class="text-muted"><?php _trans('included'); ?></small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td class="text-right"><strong><?php _trans('expense_tax_total'); ?>:</strong></td>
                                            <td class="text-right"><?php echo format_amount($expense_amounts ? $expense_amounts->expense_tax_total : 0); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr class="active">
                                            <td class="text-right"><strong><?php _trans('total'); ?>:</strong></td>
                                            <td class="text-right"><strong><?php echo format_amount($expense_amounts ? $expense_amounts->expense_total : 0); ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><strong><?php _trans('paid'); ?>:</strong></td>
                                            <td class="text-right"><?php echo format_amount($expense_amounts ? $expense_amounts->expense_paid : 0); ?></td>
                                        </tr>
                                        <tr class="warning">
                                            <td class="text-right"><strong><?php _trans('balance'); ?>:</strong></td>
                                            <td class="text-right"><strong><?php echo format_amount($expense_amounts ? $expense_amounts->expense_balance : 0); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Panel -->
                    <?php if (!empty($expense->expense_notes)): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php _trans('notes'); ?></h3>
                        </div>
                        <div class="panel-body">
                            <p><?php echo nl2br($expense->expense_notes); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Terms Panel 
                    <?php if (!empty($expense->expense_terms)): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php _trans('terms'); ?></h3>
                        </div>
                        <div class="panel-body">
                            <p><?php echo nl2br($expense->expense_terms); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                      --> 
                </div>
            </div>

        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    // Auto refresh page every 30 seconds if expense is not paid
    <?php if ($expense->expense_status_id != 3): ?>
    setTimeout(function() {
        location.reload();
    }, 30000);
    <?php endif; ?>
});
</script>
