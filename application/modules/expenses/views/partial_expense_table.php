<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-sm-1">
                    <input type="checkbox" id="select_all">
                </th>
                <th class="col-sm-2"><?php _trans('expense_number'); ?></th>
                <th class="col-sm-2"><?php _trans('category'); ?></th>
                <th class="col-sm-2"><?php _trans('date'); ?></th>
                <th class="col-sm-2"><?php _trans('due_date'); ?></th>
                <th class="col-sm-1"><?php _trans('amount'); ?></th>
                <th class="col-sm-1"><?php _trans('status'); ?></th>
                <th class="col-sm-1"><?php _trans('options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$expenses): ?>
            <tr>
                <td colspan="8" class="text-center">
                    <strong><?php _trans('no_results'); ?></strong>
                </td>
            </tr>
            <?php endif; ?>

            <?php foreach ($expenses as $expense): ?>
            <tr>
                <td>
                    <input type="checkbox" class="expense-checkbox" value="<?php echo $expense->expense_id; ?>">
                </td>
                <td>
                    <a href="<?php echo site_url('expenses/view/' . $expense->expense_id); ?>" class="expense_number">
                        <?php echo $expense->expense_number; ?>
                    </a>
                </td>
                <td>
                    <span style="color: <?php echo $expense->expense_category_color; ?>">
                        <i class="<?php echo $expense->expense_category_icon; ?>"></i>
                        <?php echo $expense->expense_category_name; ?>
                    </span>
                </td>
                <td>
                    <?php echo date_from_mysql($expense->expense_date_created); ?>
                </td>
                <td>
                    <?php echo date_from_mysql($expense->expense_date_due); ?>
                    <?php if ($expense->is_overdue && $expense->expense_status_id != 3): ?>
                        <span class="label label-danger">
                            <?php _trans('overdue'); ?> 
                            (<?php echo $expense->days_overdue; ?> <?php _trans('days'); ?>)
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo format_amount($expense->expense_total); ?>
                </td>
                <td>
                    <?php 
                    $status_class = '';
                    switch ($expense->expense_status_id) {
                        case 1: $status_class = 'label-warning'; break;
                        case 2: $status_class = 'label-info'; break;
                        case 3: $status_class = 'label-success'; break;
                    }
                    ?>
                    <span class="label <?php echo $status_class; ?>">
                        <?php 
                        switch ($expense->expense_status_id) {
                            case 1: _trans('new'); break;
                            case 2: _trans('confirmed'); break;
                            case 3: _trans('paid'); break;
                        }
                        ?>
                    </span>
                </td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('expenses/view/' . $expense->expense_id); ?>">
                                    <i class="fa fa-eye"></i> <?php _trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('expenses/edit/' . $expense->expense_id); ?>">
                                    <i class="fa fa-edit"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <?php if ($expense->expense_status_id == 1): ?>
                            <li>
                                <a href="<?php echo site_url('expenses/mark_confirmed/' . $expense->expense_id); ?>" 
                                   onclick="return confirm('<?php _trans('confirm_expense_status_change'); ?>');">
                                    <i class="fa fa-check"></i> <?php _trans('mark_confirmed'); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if ($expense->expense_status_id == 2): ?>
                            <li>
                                <a href="<?php echo site_url('expenses/mark_paid/' . $expense->expense_id); ?>" 
                                   onclick="return confirm('<?php _trans('confirm_expense_status_change'); ?>');">
                                    <i class="fa fa-money"></i> <?php _trans('mark_paid'); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo site_url('expenses/delete/' . $expense->expense_id); ?>" 
                                   onclick="return confirm('<?php _trans('confirm_delete'); ?>');">
                                    <i class="fa fa-trash"></i> <?php _trans('delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Select all checkbox
    $('#select_all').change(function() {
        $('.expense-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Filter functionality
    if (typeof filter_method !== 'undefined' && filter_method) {
        var filter_method = '<?php echo isset($filter_method) ? $filter_method : ''; ?>';
        if (filter_method) {
            $('#filter_results input[name="' + filter_method + '"]').keyup(function() {
                filter_table($(this).val());
            });
        }
    }
});

function filter_table(query) {
    var $table = $('#filter_results');
    var $rows = $table.find('tbody tr');
    
    if (query.length > 0) {
        $rows.hide();
        $rows.filter(function() {
            return $(this).text().toLowerCase().indexOf(query.toLowerCase()) != -1;
        }).show();
    } else {
        $rows.show();
    }
}
</script>
