<?php
	$v = "";
	if ($this->input->post('reference_no')) {
		$v .= "&reference_no=" . $this->input->post('reference_no');
	}
	if ($this->input->post('biller')) {
		$v .= "&biller=" . $this->input->post('biller');
	}
	if ($this->input->post('user')) {
		$v .= "&user=" . $this->input->post('user');
	}
	if ($this->input->post('note')) {
		$v .= "&note=" . $this->input->post('note');
	}
	if ($this->input->post('start_date')) {
		$v .= "&start_date=" . $this->input->post('start_date');
	}
	if ($this->input->post('end_date')) {
		$v .= "&end_date=" . $this->input->post('end_date');
	}
	if(isset($date)){
		$v .= "&d=" . $date;
	}

?>
<script>
    $(document).ready(function () {
        function attachment(x) {
            if (x != null) {
                return '<a href="' + site.base_url + 'assets/uploads/' + x + '" target="_blank"><i class="fa fa-chain"></i></a>';
            }
            return x;
        }
		var oTable = $('#EXPData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('jobs/getJobs/'.$parent_id) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];

                var action = $('td:eq(9)', nRow);
                if (aData[8] == 'received') {
                    action.find('.btn-action').attr('disabled','disabled');
                } else {
                    action.find('.btn-action').removeAttr('disabled');
                }

                return nRow;
            },
            "aoColumns": [
				{
					"bSortable": false,
					"mRender": checkbox
				}, 
				{"mRender": fld}, 
				null, 
				null, 
				null, 
				{"mRender": formatQuantity}, 
				{"mRender": formatQuantity}, 
				{"mRender": row_status, "bSortable": false}, 
				{"mRender": row_status, "bSortable": false}, 
				{"bSortable": false}
			]
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference');?>]", filter_type: "text", data: []},
			{column_number: 3, filter_default_label: "[<?=lang('customer_name');?>]", filter_type: "text", data: []},
			{column_number: 4, filter_default_label: "[<?=lang('product_name');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('quantity');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('developed_quantity');?>]", filter_type: "text", data: []},
            {column_number: 7, filter_default_label: "[<?=lang('developed_status');?>]", filter_type: "text", data: []},
            {column_number: 8, filter_default_label: "[<?=lang('receive_status');?>]", filter_type: "text", data: []},
			/*{column_number: 7, filter_default_label: "[<?=lang('develop_status');?>]", filter_type: "select", data: [{value:'completed',label:'<?= lang('completed') ?>'},{value:'processing',label: '<?= lang('processing') ?>' },{value:'pending',label: '<?= lang('pending') ?>' }]},
			{column_number: 8, filter_default_label: "[<?=lang('receive_status');?>]", filter_type: "select", data: [{value:'received',label:'<?= lang('received') ?>'},{value:'pending',label: '<?= lang('pending') ?>' }]},*/
        ], "footer");
        

    });

</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>
<?php if ($Owner) {
    echo form_open('jobs/jobs_actions', 'id="action-form"');
} ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa fa-barcode"></i><?= lang('jobs'); ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
					<i class="icon fa fa-tasks tip" data-placement="left"  title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right" class="tasks-menus" role="menu" aria-labelledby="dLabel">
	
						<?php if($this->Admin || $this->Owner){ ?>
		
						<li><a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
                        
						<li><a href="#" id="pdf" data-action="export_pdf"><i class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?></a></li>
                        <li class="divider"></li>
                        <li><a href="#" class="bpo" title="<?= $this->lang->line("delete_jobs") ?>"
                               data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>"
                               data-html="true" data-placement="left"><i
                                    class="fa fa-trash-o"></i> <?= lang('delete_jobs') ?></a></li>						
						<?php }else{ ?>
							<li><a href="#" id="excel" data-action="receive_job"><i class="fa fa-file-excel-o"></i> <?= lang('receive_job') ?></a></li>
							<li><a href="#" class="bpo" title="<?= $this->lang->line("cancel_receive_job") ?>"
                               data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='cancel_receive_job'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>"
                               data-html="true" data-placement="left"><i
                                    class="fa fa-trash-o"></i> <?= lang('cancel_receive_job') ?></a></li>
									
							<?php if($this->GP['jobs-export']){ ?>
								
								<li><a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
								<li><a href="#" id="pdf" data-action="export_pdf"><i class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?></a></li>
								
							<?php } ?>
							
							<?php if($this->GP['jobs-delete']){ ?>
							
							<li class="divider"></li>
							<li><a href="#" class="bpo" title="<?= $this->lang->line("delete_jobs") ?>" data-content="<p><?= lang('r_u_sure') ?></p>
								<button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'>
								<?= lang('no') ?></button>" data-html="true" data-placement="left"><i class="fa fa-trash-o"></i> <?= lang('delete_jobs') ?></a></li>							
							<?php } ?>
						
                        <?php } ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table id="EXPData" cellpadding="0" cellspacing="0" border="0" class="table table-condensed table-bordered table-hover table-striped">
                        <thead>
							<tr class="active">
								<th style="min-width:30px; width: 30px; text-align: center;">
									<input class="checkbox checkft" type="checkbox" name="check"/>
								</th>
								<th><?php echo $this->lang->line("date"); ?></th>
								<th><?php echo $this->lang->line("reference"); ?></th>
								<th><?php echo $this->lang->line("customer_name"); ?></th>
								<th><?php echo $this->lang->line("product_name"); ?></th>
								<th><?php echo $this->lang->line("quantity"); ?></th>
								<th><?php echo $this->lang->line("developed_quantity"); ?></th>
								<th><?php echo $this->lang->line("develop_status"); ?></th>
								<th><?php echo $this->lang->line("receive_status"); ?></th>
								<th style="width:100px;"><?php echo $this->lang->line("actions"); ?></th>
							</tr>
                        </thead>
                        <tbody>
							
							<tr>
								<td colspan="5" class="dataTables_empty">
									<?= lang('loading_data_from_server') ?>
								</td>
							</tr>

                        </tbody>
                        <tfoot class="dtFilter">
							<tr class="active">
								<th style="min-width:30px; width: 30px; text-align: center;">
									<input class="checkbox checkft" type="checkbox" name="check"/>
								</th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th style="width:100px; text-align: center;"><?php echo $this->lang->line("actions"); ?></th>
							</tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>
<style type="text/css">
	table { white-space:nowrap; }
</style>

