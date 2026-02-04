<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('payment_history'); ?></h1>
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
                                <input name="from_date" id="from_date"
                                       class="form-control datepicker">
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

<script>
    $(function () {
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
