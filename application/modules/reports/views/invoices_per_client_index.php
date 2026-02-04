<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('invoices_per_client'); ?></h1>
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

                        <div class="form-group">
                            <label for="year"><?php _trans('year'); ?></label>
                            <select name="year" id="year" class="form-control">
                                <?php for ($i = 0; $i < 10; $i++) { ?>
                                    <option value="<?php echo date('Y') - $i; ?>">
                                        <?php echo date('Y') - $i; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-default" id="btn-this-year"><?php _trans('this_year'); ?></button>
                            <button type="button" class="btn btn-default" id="btn-last-year"><?php _trans('last_year'); ?></button>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-quarter" data-quarter="1">Q1</button>
                            <button type="button" class="btn btn-default btn-quarter" data-quarter="2">Q2</button>
                            <button type="button" class="btn btn-default btn-quarter" data-quarter="3">Q3</button>
                            <button type="button" class="btn btn-default btn-quarter" data-quarter="4">Q4</button>
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
        // Set datepicker format
        if ($.fn.datepicker) {
            $('.datepicker').datepicker({format: 'yyyy-mm-dd'});
        }

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
        }

        $('#btn-this-year').click(function () {
            var year = $('#year').val();
            var from_date = new Date(year, 0, 1);
            var to_date = new Date(year, 11, 31);
            $('#from_date').val(formatDate(from_date));
            $('#to_date').val(formatDate(to_date));
            if ($.fn.datepicker) {
                $('.datepicker').datepicker('update');
            }
        });

        $('#btn-last-year').click(function () {
            var year = $('#year').val() - 1;
            var from_date = new Date(year, 0, 1);
            var to_date = new Date(year, 11, 31);
            $('#from_date').val(formatDate(from_date));
            $('#to_date').val(formatDate(to_date));
            if ($.fn.datepicker) {
                $('.datepicker').datepicker('update');
            }
        });

        $('.btn-quarter').click(function () {
            var year = $('#year').val();
            var quarter = $(this).data('quarter');
            var from_date;
            var to_date;

            switch (quarter) {
                case 1:
                    from_date = new Date(year, 0, 1);
                    to_date = new Date(year, 2, 31);
                    break;
                case 2:
                    from_date = new Date(year, 3, 1);
                    to_date = new Date(year, 5, 30);
                    break;
                case 3:
                    from_date = new Date(year, 6, 1);
                    to_date = new Date(year, 8, 30);
                    break;
                case 4:
                    from_date = new Date(year, 9, 1);
                    to_date = new Date(year, 11, 31);
                    break;
            }

            $('#from_date').val(formatDate(from_date));
            $('#to_date').val(formatDate(to_date));
            if ($.fn.datepicker) {
                $('.datepicker').datepicker('update');
            }
        });
    });
</script>
