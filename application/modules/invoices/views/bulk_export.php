<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('bulk_export'); ?></h1>
    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a class="btn btn-default" href="<?php echo site_url('invoices/index'); ?>">
                <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
            </a>
        </div>
    </div>
</div>

<div id="content">
    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <?php $this->layout->load_view('layout/alerts'); ?>

            <form method="post">
                <?php _csrf_field(); ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-print"></i> <?php _trans('bulk_export'); ?>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="from_date"><?php _trans('from_date'); ?></label>
                            <div class="input-group">
                                <input type="text" name="from_date" id="from_date"
                                       class="form-control datepicker">
                                <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="to_date"><?php _trans('to_date'); ?></label>
                            <div class="input-group">
                                <input type="text" name="to_date" id="to_date"
                                       class="form-control datepicker">
                                <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button class="btn btn-success" type="submit" name="btn_submit" value="1">
                            <i class="fa fa-download"></i> <?php _trans('download'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>