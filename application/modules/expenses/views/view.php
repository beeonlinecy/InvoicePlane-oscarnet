<?php
$cv = $this->controller->view_data["custom_values"];
?>

<script>
    $(function () {
        $('.item-task-id').each(function () {
            // Disable client chaning if at least one item already has a task id assigned
            if ($(this).val().length > 0) {
                $('#expense_change_client').hide();
                return false;
            }
        });

        $('.btn_add_product').click(function () {
            $('#modal-placeholder').load(
                "<?php echo site_url('products/ajax/modal_product_lookups'); ?>/" + Math.floor(Math.random() * 1000)
            );
        });

        $('.btn_add_task').click(function () {
            $('#modal-placeholder').load(
                "<?php echo site_url('tasks/ajax/modal_task_lookups/' . $expense_id); ?>/" +
                Math.floor(Math.random() * 1000)
            );
        });

        $('.btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        });

        <?php if (!$items) { ?>
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        <?php } ?>

        $('#btn_create_recurring').click(function () {
            $('#modal-placeholder').load(
                "<?php echo site_url('expenses/ajax/modal_create_recurring'); ?>",
                {
                    expense_id: <?php echo $expense_id; ?>
                }
            );
        });

        $('#expense_change_client').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('expenses/ajax/modal_change_client'); ?>", {
                expense_id: <?php echo $expense_id; ?>,
                client_id: "<?php echo $this->db->escape_str($expense->client_id); ?>",
            });
        });

        $('#btn_save_expense').click(function () {
            var items = [];
            var item_order = 1;
            $('#item_table .item').each(function () {
                var row = {};
                $(this).find('input,select,textarea').each(function () {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });
                row['item_order'] = item_order;
                item_order++;
                items.push(row);
            });
            $.post("<?php echo site_url('expenses/ajax/save'); ?>", {
                    expense_id: <?php echo $expense_id; ?>,
                    expense_number: $('#expense_number').val(),
                    expense_date_created: $('#expense_date_created').val(),
                    expense_date_due: $('#expense_date_due').val(),
                    expense_status_id: $('#expense_status_id').val(),
                    expense_password: $('#expense_password').val(),
                    items: JSON.stringify(items),
                    expense_discount_amount: $('#expense_discount_amount').val(),
                    expense_discount_percent: $('#expense_discount_percent').val(),
                    expense_terms: $('#expense_terms').val(),
                    custom: $('input[name^=custom],select[name^=custom]').serializeArray(),
                    payment_method: $('#payment_method').val(),
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        window.location = "<?php echo site_url('expenses/view'); ?>/" + <?php echo $expense_id; ?>;
                    } else {
                        $('#fullpage-loader').hide();
                        $('.control-group').removeClass('has-error');
                        $('div.alert[class*="alert-"]').remove();
                        var resp_errors = response.validation_errors,
                            all_resp_errors = '';
                        for (var key in resp_errors) {
                            $('#' + key).parent().addClass('has-error');
                            all_resp_errors += resp_errors[key];
                        }
                        $('#expense_form').prepend('<div class="alert alert-danger">' + all_resp_errors + '</div>');
                    }
                });
        });

        $('#btn_generate_pdf').click(function () {
            window.open('<?php echo site_url('expenses/generate_pdf/' . $expense_id); ?>', '_blank');
        });

        $(document).on('click', '.btn_delete_item', function () {
            var btn = $(this);
            var item_id = btn.data('item-id');

            // Just remove the row if no item ID is set (new row)
            if (typeof item_id === 'undefined') {
                $(this).parents('.item').remove();
            }

            $.post("<?php echo site_url('expenses/ajax/delete_item/' . $expense->expense_id); ?>", {
                    'item_id': item_id,
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);

                    if (response.success === 1) {
                        btn.parents('.item').remove();
                    } else {
                        btn.removeClass('btn-link').addClass('btn-danger').prop('disabled', true);
                    }
                });
        });

        <?php if ($expense->is_read_only != 1):
          if (get_setting('show_responsive_itemlist') == 1) { ?>
             function UpR(k) {
               var parent = k.parents('.item');
               var pos = parent.prev();
               parent.insertBefore(pos);
             }
             function DownR(k) {
               var parent = k.parents('.item');
               var pos = parent.next();
               parent.insertAfter(pos);
             }
             $(document).on('click', '.up', function () {
               UpR($(this));
             });
             $(document).on('click', '.down', function () {
               DownR($(this));
             });
        <?php } else { ?>
            var fixHelper = function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            };

            $('#item_table').sortable({
                items: 'tbody',
                helper: fixHelper,
            });
        <?php } ?>

        if ($('#expense_discount_percent').val().length > 0) {
            $('#expense_discount_amount').prop('disabled', true);
        }

        if ($('#expense_discount_amount').val().length > 0) {
            $('#expense_discount_percent').prop('disabled', true);
        }

        $('#expense_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#expense_discount_percent').prop('disabled', true);
            } else {
                $('#expense_discount_percent').prop('disabled', false);
            }
        });
        $('#expense_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#expense_discount_amount').prop('disabled', true);
            } else {
                $('#expense_discount_amount').prop('disabled', false);
            }
        });
        <?php endif; ?>

        <?php if ($expense->expense_is_recurring) : ?>
        $(document).on('click', '.js-item-recurrence-toggler', function () {
            var itemRecurrenceState = $(this).next('input').val();
            if (itemRecurrenceState === ('1')) {
                $(this).next('input').val('0');
                $(this).removeClass('fa-calendar-check-o text-success');
                $(this).addClass('fa-calendar-o text-muted');
            } else {
                $(this).next('input').val('1');
                $(this).removeClass('fa-calendar-o text-muted');
                $(this).addClass('fa-calendar-check-o text-success');
            }
        });
        <?php endif; ?>

    });
</script>

<?php
echo $modal_delete_expense;
echo $modal_add_expense_tax;
if ($this->config->item('disable_read_only') == true) {
    $expense->is_read_only = 0;
}
?>

<div id="headerbar">
    <h1 class="headerbar-title">
        <?php
        echo trans('expense') . ' ';
        echo($expense->expense_number ? '#' . $expense->expense_number : $expense->expense_id);
        ?>
    </h1>

    <div class="headerbar-item pull-right <?php if ($expense->is_read_only != 1 || $expense->expense_status_id != 4) { ?>btn-group<?php } ?>">

        <div class="options btn-group btn-group-sm">
            <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-caret-down no-margin"></i> <?php _trans('options'); ?>
            </a>
            <ul class="dropdown-menu">
                <?php if ($expense->is_read_only != 1) { ?>
                    <li>
                        <a href="#add-expense-tax" data-toggle="modal">
                            <i class="fa fa-plus fa-margin"></i> <?php _trans('add_expense_tax'); ?>
                        </a>
                    </li>
                <?php } ?>
                <li>
                    <a href="#" id="btn_create_credit" data-expense-id="<?php echo $expense_id; ?>">
                        <i class="fa fa-minus fa-margin"></i> <?php _trans('create_credit_expense'); ?>
                    </a>
                </li>
                <?php if ($expense->expense_balance != 0) : ?>
                    <li>
                        <a href="#" class="expense-add-payment"
                           data-expense-id="<?php echo $expense_id; ?>"
                           data-expense-balance="<?php echo $expense->expense_balance; ?>"
                           data-expense-payment-method="<?php echo $expense->payment_method; ?>"
                           data-payment-cf-exist="<?php echo $payment_cf_exist ?? ''; ?>">
                            <i class="fa fa-credit-card fa-margin"></i>
                            <?php _trans('enter_payment'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-expense-id="<?php echo $expense_id; ?>">
                        <i class="fa fa-print fa-margin"></i>
                        <?php _trans('download_pdf'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('mailer/expense/' . $expense->expense_id); ?>">
                        <i class="fa fa-send fa-margin"></i>
                        <?php _trans('send_email'); ?>
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#" id="btn_create_recurring"
                       data-expense-id="<?php echo $expense_id; ?>">
                        <i class="fa fa-refresh fa-margin"></i>
                        <?php _trans('create_recurring'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_expense"
                       data-expense-id="<?php echo $expense_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php _trans('copy_expense'); ?>
                    </a>
                </li>
                <?php if ($expense->expense_status_id == 1 || ($this->config->item('enable_expense_deletion') === true && $expense->is_read_only != 1)) { ?>
                    <li>
                        <a href="#delete-expense" data-toggle="modal">
                            <i class="fa fa-trash-o fa-margin"></i>
                            <?php _trans('delete'); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <?php if ($expense->is_read_only != 1 || $expense->expense_status_id != 4) { ?>
            <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_expense">
                <i class="fa fa-check"></i> <?php _trans('save'); ?>
            </a>
        <?php } ?>
    </div>

    <div class="headerbar-item expense-labels pull-right">
        <?php if ($expense->expense_is_recurring) { ?>
            <span class="label label-info">
                <i class="fa fa-refresh"></i>
                <?php _trans('recurring'); ?>
            </span>
        <?php } ?>
        <?php if ($expense->is_read_only == 1) { ?>
            <span class="label label-danger">
                <i class="fa fa-read-only"></i> <?php _trans('read_only'); ?>
            </span>
        <?php } ?>
    </div>

</div>

<div id="content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <div id="expense_form">
        <div class="expense">

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-5">

                    <h3>
                        <a href="<?php echo site_url('companies/view/' . $expense->company_id); ?>">
                            <?php _htmlsc(format_client($expense)) ?>
                        </a>
                        <?php if ($expense->expense_status_id == 1 && !$expense->creditexpense_parent_id) { ?>
                            <span id="expense_change_company" class="fa fa-edit cursor-pointer small"
                                  data-toggle="tooltip" data-placement="bottom"
                                  title="<?php _trans('change_client'); ?>"></span>
                        <?php } ?>
                    </h3>
                    <br>
                    <div class="company-address">
                        <?php $this->layout->load_view('companies/partial_company_address', ['company' => $expense]); ?>
                    </div>
                    <?php if ($expense->company_phone || $expense->company_email) : ?>
                        <hr>
                    <?php endif; ?>
                    <?php if ($expense->company_phone): ?>
                        <div>
                            <?php _trans('phone'); ?>:&nbsp;
                            <?php _htmlsc($expense->company_phone); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($expense->company_email): ?>
                        <div>
                            <?php _trans('email'); ?>:&nbsp;
                            <?php _auto_link($expense->company_email); ?>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="col-xs-12 visible-xs"><br></div>

                <div class="col-xs-12 col-sm-5 col-sm-offset-1 col-md-6 col-md-offset-1">
                    <div class="details-box panel panel-default panel-body">
                        <div class="row">

                            <?php if ($expense->expense_sign == -1) { ?>
                                <div class="col-xs-12">
                                    <div class="alert alert-warning small">
                                        <i class="fa fa-credit-expense"></i>&nbsp;
                                        <?php
                                        echo trans('credit_expense_for_expense') . ' ';
                                        $parent_expense_number = $this->mdl_expenses->get_parent_expense_number($expense->creditexpense_parent_id);
                                        echo anchor('/expenses/view/' . $expense->creditexpense_parent_id, $parent_expense_number);
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="col-xs-12 col-md-6">

                                <div class="expense-properties">
                                    <label><?php _trans('expense'); ?> #</label>
                                    <input type="text" id="expense_number" class="form-control input-sm"
                                        <?php if ($expense->expense_number) : ?>
                                            value="<?php echo $expense->expense_number; ?>"
                                        <?php else : ?>
                                            placeholder="<?php _trans('not_set'); ?>"
                                        <?php endif; ?>
                                        <?php if ($expense->is_read_only == 1) {
                                            echo 'disabled="disabled"';
                                        } ?>>
                                </div>

                                <div class="expense-properties has-feedback">
                                    <label><?php _trans('date'); ?></label>

                                    <div class="input-group">
                                        <input name="expense_date_created" id="expense_date_created"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($expense->expense_date_created); ?>"
                                            <?php if ($expense->is_read_only == 1) {
                                                echo 'disabled="disabled"';
                                            } ?>>
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="expense-properties has-feedback">
                                    <label><?php _trans('due_date'); ?></label>

                                    <div class="input-group">
                                        <input name="expense_date_due" id="expense_date_due"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($expense->expense_date_due); ?>"
                                            <?php if ($expense->is_read_only == 1) {
                                                echo 'disabled="disabled"';
                                            } ?>>
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Custom fields -->
                                <?php foreach ($custom_fields as $custom_field): ?>
                                    <?php if ($custom_field->custom_field_location != 1) {
                                        continue;
                                    } ?>
                                    <?php print_field($this->mdl_expenses, $custom_field, $cv); ?>
                                <?php endforeach; ?>

                            </div>

                            <div class="col-xs-12 col-md-6">

                                <div class="expense-properties">
                                    <label>
                                        <?php _trans('status');
                                        if ($expense->is_read_only != 1 || $expense->expense_status_id != 4) {
                                            echo ' <span class="small">(' . trans('can_be_changed') . ')</span>';
                                        } ?>
                                    </label>
                                    <select name="expense_status_id" id="expense_status_id"
                                            class="form-control input-sm simple-select" data-minimum-results-for-search="Infinity"
                                        <?php if ($expense->is_read_only == 1 && $expense->expense_status_id == 4) {
                                            echo 'disabled="disabled"';
                                        } ?>>
                                        <?php foreach ($expense_statuses as $key => $status) { ?>
                                            <option value="<?php echo $key; ?>"
                                                    <?php if ($key == $expense->expense_status_id) { ?>selected="selected"<?php } ?>>
                                                <?php echo $status['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="expense-properties">
                                    <label><?php _trans('payment_method'); ?></label>
                                    <select name="payment_method" id="payment_method"
                                            class="form-control input-sm simple-select"
                                        <?php if ($expense->is_read_only == 1 && $expense->expense_status_id == 4) {
                                            echo 'disabled="disabled"';
                                        } ?>>
                                        <option value="0"><?php _trans('select_payment_method'); ?></option>
                                        <?php foreach ($payment_methods as $payment_method) { ?>
                                            <option <?php check_select($expense->payment_method,
                                                $payment_method->payment_method_id) ?>
                                                value="<?php echo $payment_method->payment_method_id; ?>">
                                                <?php echo $payment_method->payment_method_name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="expense-properties">
                                    <label><?php _trans('expense_password'); ?></label>
                                    <input type="text" id="expense_password" class="form-control input-sm"
                                           value="<?php _htmlsc($expense->expense_password); ?>"
                                        <?php if ($expense->is_read_only == 1) {
                                            echo 'disabled="disabled"';
                                        } ?>>
                                </div>
                            </div>

                            <?php if ($expense->expense_status_id != 1) { ?>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label for="expense-guest-url"><?php _trans('guest_url'); ?></label>
                                        <div class="input-group">
                                            <input type="text" id="expense-guest-url" readonly class="form-control"
                                                   value="<?php echo site_url('guest/view/expense/' . $expense->expense_url_key) ?>">
                                            <span class="input-group-addon to-clipboard cursor-pointer"
                                                  data-clipboard-target="#expense-guest-url">
                                                <i class="fa fa-clipboard fa-fw"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>

            </div>

            <br>

            <?php if (get_setting('show_responsive_itemlist') == 1) {
                    $this->layout->load_view('expenses/partial_itemlist_responsive');
                  } else {
                    $this->layout->load_view('expenses/partial_itemlist_table');
                  }
            ?>

            <hr/>

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default no-margin">
                        <div class="panel-heading">
                            <?php _trans('expense_terms'); ?>
                        </div>
                        <div class="panel-body">
                            <textarea id="expense_terms" name="expense_terms" class="form-control" rows="3"
                                <?php if ($expense->is_read_only == 1) {
                                    echo 'disabled="disabled"';
                                } ?>
                            ><?php _htmlsc($expense->expense_terms); ?></textarea>
                        </div>
                    </div>

                    <div class="col-xs-12 visible-xs visible-sm"><br></div>

                </div>
                <div class="col-xs-12 col-md-6">

                    <?php $this->layout->load_view('upload/dropzone-expense-html'); ?>

                </div>
            </div>

            <?php if ($custom_fields): ?>
                <div class="row">
                    <div class="col-xs-12">

                        <hr>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <?php _trans('custom_fields'); ?>
                            </div>
                            <div class="panel-body">
                                <div class="row">

                                    <div class="col-xs-12 col-md-6">
                                        <?php $i = 0; ?>
                                        <?php foreach ($custom_fields as $custom_field): ?>
                                            <?php if ($custom_field->custom_field_location != 0) {
                                                continue;
                                            } ?>
                                            <?php $i++; ?>
                                            <?php if ($i % 2 != 0): ?>
                                                <?php print_field($this->mdl_expenses, $custom_field, $cv); ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="col-xs-12 col-md-6">
                                        <?php $i = 0; ?>
                                        <?php foreach ($custom_fields as $custom_field): ?>
                                            <?php if ($custom_field->custom_field_location != 0) {
                                                continue;
                                            } ?>
                                            <?php $i++; ?>
                                            <?php if ($i % 2 == 0): ?>
                                                <?php print_field($this->mdl_expenses, $custom_field, $cv); ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<?php $this->layout->load_view('upload/dropzone-expense-scripts'); ?>
