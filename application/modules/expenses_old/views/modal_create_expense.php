<script>
    $(function () {
        // Display the create expense modal
        $('#create-expense').modal('show');

        // Enable select2 for all selects
        $('.simple-select').select2();

        <?php $this->layout->load_view('companies/script_select2_company_id.js'); ?>

        // Toggle on/off permissive search on clients names
        $('#toggle_permissive_search_companies').click(function () {
            if ($('input#input_permissive_search_companies').val() == ('1')) {
                $.get("<?php echo site_url('companies/ajax/save_preference_permissive_search_companies'); ?>", {
                    permissive_search_companies: '0'
                });
                $('input#input_permissive_search_companies').val('0');
                $('span#toggle_permissive_search_companies i').removeClass('fa-toggle-on');
                $('span#toggle_permissive_search_companies i').addClass('fa-toggle-off');
            } else {
                $.get("<?php echo site_url('companies/ajax/save_preference_permissive_search_companies'); ?>", {
                    permissive_search_companies: '1'
                });
                $('input#input_permissive_search_companies').val('1');
                $('span#toggle_permissive_search_companies i').removeClass('fa-toggle-off');
                $('span#toggle_permissive_search_companies i').addClass('fa-toggle-on');
            }
        });

        // Creates the expense
        $('#expense_create_confirm').click(function () {
            // Posts the data to validate and create the invoice;
            // will create the new client if necessary
            $.post("<?php echo site_url('expenses/ajax/create'); ?>", {
                    company_id: $('#create_expense_company_id').val(),
                    expense_date_created: $('#expense_date_created').val(),
                    expense_group_id: $('#expense_group_id').val(),
                    expense_time_created: '<?php echo date('H:i:s') ?>',
                    expense_password: $('#expense_password').val(),
                    user_id: '<?php echo $this->session->userdata('user_id'); ?>',
                    payment_method: $('#payment_method_id').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        // The validation was successful and invoice was created
                        window.location = "<?php echo site_url('expenses/view'); ?>/" + response.expense_id;
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                });
        });
    });

</script>

<div id="create-expense" class="modal modal-lg"
     role="dialog" aria-labelledby="modal_create_expense" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('create_expense'); ?></h4>
        </div>
        <div class="modal-body">

            <input class="hidden" id="payment_method_id"
                   value="<?php echo get_setting('invoice_default_payment_method'); ?>">

            <input class="hidden" id="input_permissive_search_companies"
                   value="<?php echo get_setting('enable_permissive_search_companies'); ?>">

            <div class="form-group has-feedback">
                <label for="create_expense_company_id"><?php _trans('company'); ?></label>
                <div class="input-group">
                    <select name="company_id" id="create_expense_company_id" class="company-id-select form-control"
                            autofocus="autofocus">
                        <?php if (!empty($company)) : ?>
                            <option value="<?php echo $company->company_id; ?>"><?php _htmlsc(format_company($company)); ?></option>
                        <?php endif; ?>
                    </select>
                    <span id="toggle_permissive_search_companies" class="input-group-addon"
                          title="<?php _trans('enable_permissive_search_companies'); ?>" style="cursor:pointer;">
                        <i class="fa fa-toggle-<?php echo get_setting('enable_permissive_search_clients') ? 'on' : 'off' ?> fa-fw"></i>
                    </span>
                </div>
            </div>

            <div class="form-group has-feedback">
                <label for="expense_date_created"><?php _trans('expense_date'); ?></label>

                <div class="input-group">
                    <input name="expense_date_created" id="expense_date_created"
                           class="form-control datepicker"
                           value="<?php echo date(date_format_setting()); ?>" required>
                    <span class="input-group-addon">
                    <i class="fa fa-calendar fa-fw"></i>
                </span>
                </div>
            </div>
            <!--
            <div class="form-group">
                <label for="expense_group_id"><?php //_trans('expense_group'); ?></label>
                <select name="expense_group_id" id="expense_group_id"
                	class="form-control simple-select" data-minimum-results-for-search="Infinity" required>
                    <?php //foreach ($expense_groups as $expense_group) { ?>
                        <option value="<?php //echo $expense_group->expense_group_id; ?>"
                                <?php //if (get_setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php //} ?>>
                            <?php //_htmlsc($expense_group->expense_group_name); ?>
                        </option>
                    <?php //} ?>
                </select>
            </div> -->

            <div class="form-group">
                <label for="expense_total"><?php _trans('expense_total'); ?></label>
                <input type="text" name="expense_total" id="expense_total" class="form-control amount"
                       value="0<?php //echo get_setting('expense_pre_password') == '' ? '' : get_setting('expense_pre_password'); ?>"
                       style="margin: 0 auto;" autocomplete="off">
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success ajax-loader" id="expense_create_confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
