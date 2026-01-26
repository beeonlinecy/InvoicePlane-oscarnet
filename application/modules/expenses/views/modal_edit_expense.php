<div class="modal-dialog" style="width: 90%; max-width: 1200px;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-times"></i>
            </button>
            <h4 class="modal-title"><?php echo sprintf(trans('edit_expense') . ' #%s', $expense->expense_number); ?></h4>
        </div>

        <?php echo form_open(); ?>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">

                    <!-- Basic Info -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php _trans('basic_info'); ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_number"><?php _trans('expense_number'); ?></label>
                                        <input type="text" name="expense_number" id="expense_number" 
                                               class="form-control" value="<?php echo $expense->expense_number; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_category_id"><?php _trans('expense_category'); ?> *</label>
                                        <select name="expense_category_id" id="expense_category_id" class="form-control" required>
                                            <option value=""><?php _trans('select_category'); ?></option>
                                            <?php foreach ($expense_categories as $category): ?>
                                            <option value="<?php echo $category->expense_category_id; ?>" 
                                                    style="color: <?php echo $category->expense_category_color; ?>"
                                                    <?php echo ($expense->expense_category_id == $category->expense_category_id) ? 'selected' : ''; ?>>
                                                <i class="<?php echo $category->expense_category_icon; ?>"></i>
                                                <?php echo $category->expense_category_name; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="user_id"><?php _trans('created_by'); ?> *</label>
                                        <select name="user_id" id="user_id" class="form-control" required>
                                            <option value=""><?php _trans('select_user'); ?></option>
                                            <?php foreach ($users as $user): ?>
                                            <option value="<?php echo $user->user_id; ?>" 
                                                    <?php echo ($expense->user_id == $user->user_id) ? 'selected' : ''; ?>>
                                                <?php echo $user->user_name; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_status_id"><?php _trans('status'); ?></label>
                                        <select name="expense_status_id" id="expense_status_id" class="form-control">
                                            <?php foreach ($expense_statuses as $status_id => $status): ?>
                                            <option value="<?php echo $status_id; ?>" 
                                                    <?php echo ($expense->expense_status_id == $status_id) ? 'selected' : ''; ?>>
                                                <?php echo $status['label']; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_date_created"><?php _trans('expense_date'); ?> *</label>
                                        <div class="input-group">
                                            <input type="text" name="expense_date_created" id="expense_date_created" 
                                                   class="form-control datepicker" required
                                                   value="<?php echo $expense->expense_date_created; ?>">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_date_due"><?php _trans('due_date'); ?> *</label>
                                        <div class="input-group">
                                            <input type="text" name="expense_date_due" id="expense_date_due" 
                                                   class="form-control datepicker" required
                                                   value="<?php echo $expense->expense_date_due; ?>">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_currency_code"><?php _trans('currency'); ?></label>
                                        <input type="text" name="expense_currency_code" id="expense_currency_code" 
                                               class="form-control" value="<?php echo $expense->expense_currency_code; ?>" maxlength="3">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_rate"><?php _trans('exchange_rate'); ?></label>
                                        <input type="number" name="expense_rate" id="expense_rate" 
                                               class="form-control" value="<?php echo $expense->expense_rate; ?>" 
                                               step="0.0001" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <?php _trans('expense_items'); ?>
                                <button type="button" class="btn btn-sm btn-primary pull-right" onclick="addItem()">
                                    <i class="fa fa-plus"></i> <?php _trans('add_item'); ?>
                                </button>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="items-table">
                                    <thead>
                                        <tr>
                                            <th><?php _trans('item'); ?></th>
                                            <th><?php _trans('description'); ?></th>
                                            <th style="width: 10%;"><?php _trans('quantity'); ?></th>
                                            <th style="width: 15%;"><?php _trans('price'); ?></th>
                                            <th style="width: 15%;"><?php _trans('total'); ?></th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-tbody">
                                        <?php 
                                        $this->load->model('expenses/mdl_expense_items');
                                        $items = $this->mdl_expense_items->where('expense_id', $expense->expense_id)->get()->result();
                                        $itemCounter = 0;
                                        
                                        if (!$items): ?>
                                        <tr class="item-row">
                                            <td>
                                                <input type="text" name="items[1][item_name]" class="form-control" required>
                                            </td>
                                            <td>
                                                <textarea name="items[1][item_description]" class="form-control" rows="2"></textarea>
                                            </td>
                                            <td>
                                                <input type="number" name="items[1][item_quantity]" class="form-control" 
                                                       value="1" step="0.01" min="0">
                                            </td>
                                            <td>
                                                <input type="number" name="items[1][item_price]" class="form-control" 
                                                       value="0" step="0.01" min="0">
                                            </td>
                                            <td>
                                                <span class="item-total">0.00</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php else: 
                                            foreach ($items as $item): 
                                            $itemCounter++;
                                            $itemAmount = $this->db->where('expense_item_id', $item->expense_item_id)->get('ip_expense_item_amounts')->row();
                                        ?>
                                        <tr class="item-row">
                                            <td>
                                                <input type="text" name="items[<?php echo $itemCounter; ?>][item_name]" 
                                                       class="form-control" required
                                                       value="<?php echo $item->item_name; ?>">
                                                <input type="hidden" name="items[<?php echo $itemCounter; ?>][expense_item_id]" 
                                                       value="<?php echo $item->expense_item_id; ?>">
                                            </td>
                                            <td>
                                                <textarea name="items[<?php echo $itemCounter; ?>][item_description]" 
                                                          class="form-control" rows="2"><?php echo $item->item_description; ?></textarea>
                                            </td>
                                            <td>
                                                <input type="number" name="items[<?php echo $itemCounter; ?>][item_quantity]" 
                                                       class="form-control" value="<?php echo $item->item_quantity; ?>" 
                                                       step="0.01" min="0">
                                            </td>
                                            <td>
                                                <input type="number" name="items[<?php echo $itemCounter; ?>][item_price]" 
                                                       class="form-control" value="<?php echo $item->item_price; ?>" 
                                                       step="0.01" min="0">
                                            </td>
                                            <td>
                                                <span class="item-total"><?php echo number_format($itemAmount->item_total, 2); ?></span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php 
                                            endforeach; 
                                        endif; 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tax Rates Panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <?php _trans('expense_taxes'); ?>
                                <button type="button" class="btn btn-sm btn-primary pull-right" onclick="addTax()">
                                    <i class="fa fa-plus"></i> <?php _trans('add_tax'); ?>
                                </button>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tax-table">
                                    <thead>
                                        <tr>
                                            <th><?php _trans('tax_rate'); ?></th>
                                            <th><?php _trans('include_item_tax'); ?></th>
                                            <th><?php _trans('include_tax'); ?></th>
                                            <th style="width: 15%;"><?php _trans('amount'); ?></th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tax-tbody">
                                        <?php 
                                        $taxCounter = 0;
                                        if ($expense_tax_rates): 
                                            foreach ($expense_tax_rates as $expense_tax_rate): 
                                            $taxCounter++;
                                        ?>
                                        <tr class="tax-row">
                                            <td>
                                                <select name="tax_rates[<?php echo $taxCounter; ?>][tax_rate_id]" class="form-control" required>
                                                    <option value=""><?php _trans('select_tax_rate'); ?></option>
                                                    <?php foreach ($tax_rates as $tax_rate): ?>
                                                    <option value="<?php echo $tax_rate->tax_rate_id; ?>" 
                                                            <?php echo ($expense_tax_rate->tax_rate_id == $tax_rate->tax_rate_id) ? 'selected' : ''; ?>>
                                                        <?php echo $tax_rate->tax_rate_name; ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type="hidden" name="tax_rates[<?php echo $taxCounter; ?>][expense_tax_rate_id]" 
                                                       value="<?php echo $expense_tax_rate->expense_tax_rate_id; ?>">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="tax_rates[<?php echo $taxCounter; ?>][include_item_tax]" 
                                                       value="1" <?php echo $expense_tax_rate->include_item_tax ? 'checked' : ''; ?>>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="tax_rates[<?php echo $taxCounter; ?>][include_tax]" 
                                                       value="1" <?php echo $expense_tax_rate->include_tax ? 'checked' : ''; ?>>
                                            </td>
                                            <td>
                                                <span class="tax-amount"><?php echo number_format($expense_tax_rate->expense_tax_rate_amount, 2); ?></span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeTax(this)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php 
                                            endforeach; 
                                        endif; 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notes and Terms -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php _trans('notes_and_terms'); ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="expense_notes"><?php _trans('notes'); ?></label>
                                <textarea name="expense_notes" id="expense_notes" class="form-control" rows="3"
                                          placeholder="<?php _trans('expense_notes_placeholder'); ?>"><?php echo $expense->expense_notes; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="expense_terms"><?php _trans('terms'); ?></label>
                                <textarea name="expense_terms" id="expense_terms" class="form-control" rows="3"
                                          placeholder="<?php _trans('expense_terms_placeholder'); ?>"><?php echo $expense->expense_terms; ?></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php _trans('cancel'); ?>
                </button>
                <button type="submit" class="btn btn-primary" name="btn_submit" value="1">
                    <?php _trans('save'); ?>
                </button>
            </div>
        </div>

        <?php echo form_close(); ?>

    </div>
</div>

<script>
var itemCounter = <?php echo $itemCounter; ?>;
var taxCounter = <?php echo $taxCounter; ?>;

function addItem() {
    itemCounter++;
    var row = `
        <tr class="item-row">
            <td>
                <input type="text" name="items[${itemCounter}][item_name]" class="form-control" required>
            </td>
            <td>
                <textarea name="items[${itemCounter}][item_description]" class="form-control" rows="2"></textarea>
            </td>
            <td>
                <input type="number" name="items[${itemCounter}][item_quantity]" class="form-control" 
                       value="1" step="0.01" min="0">
            </td>
            <td>
                <input type="number" name="items[${itemCounter}][item_price]" class="form-control" 
                       value="0" step="0.01" min="0">
            </td>
            <td>
                <span class="item-total">0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    $('#items-tbody').append(row);
    
    // Recalculate totals
    calculateTotals();
}

function removeItem(button) {
    $(button).closest('tr').remove();
    calculateTotals();
}

function addTax() {
    taxCounter++;
    var row = `
        <tr class="tax-row">
            <td>
                <select name="tax_rates[${taxCounter}][tax_rate_id]" class="form-control" required>
                    <option value=""><?php _trans('select_tax_rate'); ?></option>
                    <?php foreach ($tax_rates as $tax_rate): ?>
                    <option value="<?php echo $tax_rate->tax_rate_id; ?>">
                        <?php echo $tax_rate->tax_rate_name; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td class="text-center">
                <input type="checkbox" name="tax_rates[${taxCounter}][include_item_tax]" value="1">
            </td>
            <td class="text-center">
                <input type="checkbox" name="tax_rates[${taxCounter}][include_tax]" value="1">
            </td>
            <td>
                <span class="tax-amount">0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeTax(this)">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    $('#tax-tbody').append(row);
}

function removeTax(button) {
    $(button).closest('tr').remove();
    calculateTotals();
}

function calculateTotals() {
    var subtotal = 0;
    $('.item-row').each(function() {
        var quantity = parseFloat($(this).find('input[name*="[item_quantity]"]').val()) || 0;
        var price = parseFloat($(this).find('input[name*="[item_price]"]').val()) || 0;
        var total = quantity * price;
        
        $(this).find('.item-total').text(total.toFixed(2));
        subtotal += total;
    });
    
    // Update total display (this would need to be implemented on the server)
    console.log('Subtotal:', subtotal.toFixed(2));
}

// Initialize datepickers
$(document).ready(function() {
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });
    
    // Calculate totals when inputs change
    $(document).on('change', 'input[name*="[item_quantity]"], input[name*="[item_price]"]', function() {
        calculateTotals();
    });
});
</script>
