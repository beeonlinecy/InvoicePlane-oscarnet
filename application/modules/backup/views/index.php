<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('backups'); ?></h1>

    <div class="headerbar-item pull-right">
        <form action="<?php echo site_url('backup/create'); ?>" method="POST">
            <?php _csrf_field(); ?>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i> <?php _trans('create_backup'); ?>
            </button>
        </form>
    </div>
</div>

<div id="content" class="table-content">
    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th><?php _trans('backup_name'); ?></th>
                <th><?php _trans('backup_size'); ?></th>
                <th><?php _trans('backup_date'); ?></th>
                <th><?php _trans('options'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($backups)) { ?>
                <tr>
                    <td colspan="4"><?php _trans('no_backups_found'); ?></td>
                </tr>
            <?php } else { ?>
                <?php foreach ($backups as $backup) { ?>
                    <tr>
                        <td><?php _htmlsc($backup->name); ?></td>
                        <td>
                            <?php
                            echo $backup->size >= 1048576
                                ? number_format($backup->size / 1048576, 2) . ' MB'
                                : number_format($backup->size / 1024, 2) . ' KB';
                    ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i:s', $backup->created_at); ?></td>
                        <td>
                            <a class="btn btn-default btn-sm"
                               href="<?php echo site_url('backup/download/' . rawurlencode($backup->id)); ?>">
                                <i class="fa fa-download"></i> <?php _trans('download'); ?>
                            </a>
                            <form action="<?php echo site_url('backup/delete/' . rawurlencode($backup->id)); ?>"
                                  method="POST" class="inline">
                                <?php _csrf_field(); ?>
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                    <i class="fa fa-trash-o"></i> <?php _trans('delete'); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
