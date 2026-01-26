<script>
    $(function () {
        // Display the create quote modal
        $('#modal_copy_expense').modal('show');

        // Select2 for all select inputs
        $(".simple-select").select2();

        <?php $this->layout->load_view('clients/script_select2_client_id.js'); ?>

        // Creates the expense
        $('#copy_expense_confirm').click(function () {
            $.post("<?php echo site_url('expenses/ajax/copy_expense'); ?>", {
                    expense_id: <?php echo $expense_id; ?>,
                    client_id: $('#copy_expense_client_id').val(),
                    expense_date_created: $('#expense_date_created_modal').val(),
                    expense_group_id: $('#expense_group_id').val(),
                    expense_password: $('#expense_password').val(),
                    expense_time_created: '<?php echo date('H:i:s') ?>',
                    user_id: $('#user_id').val(),
                    payment_method: $('#payment_method').val()
                },
                function (data) {
                    <?php echo IP_DEBUG ? 'console.log(data);' : ''; ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
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

<div id="modal_copy_expense" class="modal modal-lg" role="dialog" aria-labelledby="modal_copy_expense"
     aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('copy_expense'); ?></h4>
        </div>
        <div class="modal-body">

            <input type="hidden" name="user_id" id="user_id" class="form-control"
                   value="<?php echo $expense->user_id; ?>">
            <input type="hidden" name="payment_method" id="payment_method" class="form-control"
                   value="<?php echo $expense->payment_method; ?>">

            <div class="form-group">
                <label for="copy_expense_client_id"><?php _trans('client'); ?></label>
                <select name="client_id" id="copy_expense_client_id" class="client-id-select form-control" autofocus="autofocus">
                <?php if ( ! empty($client)) : ?>
                        <option value="<?php echo $client->client_id; ?>"><?php _htmlsc(format_client($client)); ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group has-feedback">
                <label for="expense_date_created_modal"><?php _trans('expense_date'); ?>: </label>

                <div class="input-group">
                    <input name="expense_date_created_modal" id="expense_date_created_modal" class="form-control datepicker"
                           value="<?php echo date_from_mysql(date('Y-m-d', time()), true) ?>">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="expense_password"><?php _trans('expense_password'); ?></label>
                <input type="text" name="expense_password" id="expense_password" class="form-control"
                       value="<?php echo get_setting('expense_pre_password') == '' ? '' : get_setting('expense_pre_password') ?>"
                       style="margin: 0 auto;" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="expense_group_id"><?php _trans('expense_group'); ?>: </label>
                <select name="expense_group_id" id="expense_group_id" class="form-control simple-select">
                    <?php foreach ($expense_groups as $expense_group) { ?>
                        <option value="<?php echo $expense_group->expense_group_id; ?>"
                            <?php check_select(get_setting('default_expense_group'), $expense_group->expense_group_id); ?>>
                            <?php _htmlsc($expense_group->expense_group_name); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success" id="copy_expense_confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
