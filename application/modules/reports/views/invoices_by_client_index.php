<script>
    $(function () {
        $('.simple-select').select2();

        <?php $this->layout->load_view('clients/script_select2_client_id.js'); ?>

        // Toggle on/off permissive search on clients names
        $('span#toggle_permissive_search_clients').click(function () {
            if ($('input#input_permissive_search_clients').val() == ('1')) {
                $.get("<?php echo site_url('clients/ajax/save_preference_permissive_search_clients'); ?>", {
                    permissive_search_clients: '0'
                });
                $('input#input_permissive_search_clients').val('0');
                $('span#toggle_permissive_search_clients i').removeClass('fa-toggle-on');
                $('span#toggle_permissive_search_clients i').addClass('fa-toggle-off');
            } else {
                $.get("<?php echo site_url('clients/ajax/save_preference_permissive_search_clients'); ?>", {
                    permissive_search_clients: '1'
                });
                $('input#input_permissive_search_clients').val('1');
                $('span#toggle_permissive_search_clients i').removeClass('fa-toggle-off');
                $('span#toggle_permissive_search_clients i').addClass('fa-toggle-on');
            }
        });

        $('#btn-q1').click(function () {
            const year = $('#year-select').val();
            $('#from_date').datepicker('setDate', new Date(year, 0, 1));
            $('#to_date').datepicker('setDate', new Date(year, 2, 31));
        });
        $('#btn-q2').click(function () {
            const year = $('#year-select').val();
            $('#from_date').datepicker('setDate', new Date(year, 3, 1));
            $('#to_date').datepicker('setDate', new Date(year, 5, 30));
        });
        $('#btn-q3').click(function () {
            const year = $('#year-select').val();
            $('#from_date').datepicker('setDate', new Date(year, 6, 1));
            $('#to_date').datepicker('setDate', new Date(year, 8, 30));
        });
        $('#btn-q4').click(function () {
            const year = $('#year-select').val();
            $('#from_date').datepicker('setDate', new Date(year, 9, 1));
            $('#to_date').datepicker('setDate', new Date(year, 11, 31));
        });
    });
</script>

<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('invoices_by_client'); ?></h1>
</div>

<div id="content">

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div id="report_options" class="panel panel-default">

                <div class="panel-heading">
                    <i class="fa fa-print"></i>
                    <?php _trans('report_options'); ?>
                </div>

                <div class="panel-body">

                    <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>"
                        <?php echo get_setting('reports_in_new_tab', false) ? 'target="_blank"' : ''; ?>>

                        <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
                               value="<?php echo $this->security->get_csrf_hash() ?>">

                        <div class="form-group has-feedback">
                            <label for="create_quote_client_id"><?php _trans('client'); ?></label>
                            <div class="input-group">
                                <select name="client_id" id="create_quote_client_id" class="client-id-select form-control"
                                        autofocus="autofocus">
                                    <?php if (!empty($client)) : ?>
                                        <option value="<?php echo $client->client_id; ?>"><?php _htmlsc(format_client($client)); ?></option>
                                    <?php endif; ?>
                                </select>
                                <span id="toggle_permissive_search_clients" class="input-group-addon" title="<?php _trans('enable_permissive_search_clients'); ?>" style="cursor:pointer;">
                                    <i class="fa fa-toggle-<?php echo get_setting('enable_permissive_search_clients') ? 'on' : 'off' ?> fa-fw" ></i>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php _trans('quarters'); ?></label>
                            <div class="form-inline">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default" id="btn-q1"><?php _trans('q1'); ?></button>
                                    <button type="button" class="btn btn-default" id="btn-q2"><?php _trans('q2'); ?></button>
                                    <button type="button" class="btn btn-default" id="btn-q3"><?php _trans('q3'); ?></button>
                                    <button type="button" class="btn btn-default" id="btn-q4"><?php _trans('q4'); ?></button>
                                </div>
                                <select id="year-select" class="form-control">
                                    <?php
                                    $currentYear = date('Y');
                                    for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="from_date">
                                <?php _trans('from_date'); ?>
                            </label>

                            <div class="input-group">
                                <input name="from_date" id="from_date" class="form-control datepicker">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="to_date">
                                <?php _trans('to_date'); ?>
                            </label>

                            <div class="input-group">
                                <input name="to_date" id="to_date" class="form-control datepicker">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                        <input type="submit" class="btn btn-success" name="btn_submit"
                               value="<?php _trans('run_report'); ?>">

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>
