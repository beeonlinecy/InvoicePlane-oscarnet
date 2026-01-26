<?php
/*
 * InvoicePlane Expense Module - Settings Page
 */
?>

<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('expense_settings'); ?></h1>
</div>

<div id="content" class="content-expenses-settings">

    <?php echo form_open(site_url('expenses/settings'), ['id' => 'form-settings']); ?>

    <div class="row">
        <div class="col-md-6">
            <!-- General Settings -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php _trans('general_settings'); ?></h3>
                </div>
                <div class="panel-body">
                    
                    <div class="form-group">
                        <label for="expenses_next_number_prefix"><?php _trans('expense_number_prefix'); ?></label>
                        <input type="text" name="expenses_next_number_prefix" id="expenses_next_number_prefix" 
                               class="form-control" value="<?php echo get_setting('expenses_next_number_prefix', 'EXP'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="expenses_next_number"><?php _trans('next_expense_number'); ?></label>
                        <input type="number" name="expenses_next_number" id="expenses_next_number" 
                               class="form-control" value="<?php echo get_setting('expenses_next_number', 1); ?>" min="1">
                    </div>

                    <div class="form-group">
                        <label for="expenses_due_after_days"><?php _trans('due_after_days'); ?></label>
                        <input type="number" name="expenses_due_after_days" id="expenses_due_after_days" 
                               class="form-control" value="<?php echo get_setting('expenses_due_after_days', 30); ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label for="expenses_default_currency_code"><?php _trans('default_currency'); ?></label>
                        <input type="text" name="expenses_default_currency_code" id="expenses_default_currency_code" 
                               class="form-control" value="<?php echo get_setting('expenses_default_currency_code', 'USD'); ?>" maxlength="3">
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Tax Settings -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php _trans('tax_settings'); ?></h3>
                </div>
                <div class="panel-body">

                    <div class="form-group">
                        <label for="expenses_default_item_tax_rate"><?php _trans('default_item_tax_rate'); ?></label>
                        <select name="expenses_default_item_tax_rate" id="expenses_default_item_tax_rate" class="form-control">
                            <option value="0"><?php _trans('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate): ?>
                            <option value="<?php echo $tax_rate->tax_rate_id; ?>" 
                                <?php if (get_setting('expenses_default_item_tax_rate') == $tax_rate->tax_rate_id) echo 'selected'; ?>>
                                <?php echo $tax_rate->tax_rate_name; ?> (<?php echo $tax_rate->tax_rate_percent; ?>%)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted"><?php _trans('default_item_tax_rate_hint'); ?></small>
                    </div>

                    <div class="form-group">
                        <label for="expenses_default_expense_tax_rate"><?php _trans('default_expense_tax_rate'); ?></label>
                        <select name="expenses_default_expense_tax_rate" id="expenses_default_expense_tax_rate" class="form-control">
                            <option value="0"><?php _trans('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate): ?>
                            <option value="<?php echo $tax_rate->tax_rate_id; ?>" 
                                <?php if (get_setting('expenses_default_expense_tax_rate') == $tax_rate->tax_rate_id) echo 'selected'; ?>>
                                <?php echo $tax_rate->tax_rate_name; ?> (<?php echo $tax_rate->tax_rate_percent; ?>%)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted"><?php _trans('default_expense_tax_rate_hint'); ?></small>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="expenses_generate_number_for_new" value="1"
                                    <?php if (get_setting('expenses_generate_number_for_new', 1) == 1) echo 'checked'; ?>>
                                <?php _trans('generate_number_for_new_expenses'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="expenses_mark_as_paid_on_payment" value="1"
                                    <?php if (get_setting('expenses_mark_as_paid_on_payment', 1) == 1) echo 'checked'; ?>>
                                <?php _trans('mark_as_paid_when_fully_paid'); ?>
                            </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Default Text Settings -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php _trans('default_texts'); ?></h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expenses_default_terms"><?php _trans('default_terms'); ?></label>
                                <textarea name="expenses_default_terms" id="expenses_default_terms" 
                                          class="form-control" rows="4"><?php echo get_setting('expenses_default_terms', ''); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expenses_default_notes"><?php _trans('default_notes'); ?></label>
                                <textarea name="expenses_default_notes" id="expenses_default_notes" 
                                          class="form-control" rows="4"><?php echo get_setting('expenses_default_notes', ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-success" name="btn_save">
            <i class="fa fa-save"></i> <?php _trans('save_settings'); ?>
        </button>
    </div>

    <?php echo form_close(); ?>

</div>
