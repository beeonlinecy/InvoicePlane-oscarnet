<?php foreach ($company_notes as $company_note) : ?>
    <div class="panel panel-default small">
        <div class="panel-body">
            <?php echo nl2br(htmlsc($company_note->company_note)); ?>
        </div>
        <div class="panel-footer text-muted">
            <?php echo date_from_mysql($company_note->company_note_date, true); ?>
            <span data-id="<?php echo $company_note->company_note_id; ?>" class="delete_company_note pull-right btn btn-xs btn-danger">
                <i class="fa fa-trash-o"></i> <?php _trans('delete'); ?>
            </span>
        </div>
    </div>
<?php endforeach; ?>
