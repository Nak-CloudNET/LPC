<?php
	function row_status($x){
		if($x == 'completed' || $x == 'paid' || $x == 'sent' || $x == 'received' || $x == 'deposit') {
			return '<div class="text-center"><span class="label label-success">'.lang($x).'</span></div>';
		}elseif($x == 'pending' || $x == 'book' || $x == 'free'){
			return '<div class="text-center"><span class="label label-warning">'.lang($x).'</span></div>';
		}elseif($x == 'partial' || $x == 'transferring' || $x == 'ordered'  || $x == 'busy'  || $x == 'processing'){
			return '<div class="text-center"><span class="label label-info">'.lang($x).'</span></div>';
		}elseif($x == 'due' || $x == 'returned' || $x == 'regular'){
			return '<div class="text-center"><span class="label label-danger">'.lang($x).'</span></div>';
		}else{
			return '<div class="text-center"><span class="label label-default">'.lang($x).'</span></div>';
		}
	}

?>
<div class="modal-dialog" style="width:80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >
                <i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel">Jobs List</h4>
        </div>
        <div class="modal-body">
			<script type="text/javascript">
				var lang = {paid: '<?=lang('paid');?>',processing:'<?=lang('processing')?>', pending: '<?=lang('pending');?>', completed: '<?=lang('completed');?>', ordered: '<?=lang('ordered');?>', received: '<?=lang('received');?>', partial: '<?=lang('partial');?>', sent: '<?=lang('sent');?>', r_u_sure: '<?=lang('r_u_sure');?>', due: '<?=lang('due');?>', returned: '<?=lang('returned');?>', transferring: '<?=lang('transferring');?>', active: '<?=lang('active');?>', inactive: '<?=lang('inactive');?>', unexpected_value: '<?=lang('unexpected_value');?>', select_above: '<?=lang('select_above');?>'};
			</script>
			<script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.min.js"></script>
			<script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.dtFilter.min.js"></script>
			<script type="text/javascript" src="<?= $assets ?>js/core.js"></script>
			<?php if ($Owner) {
				echo form_open('jobs/jobs_actions', 'id="action-form"');
			} ?>
				<div class="box">
					<div class="box-header">
						<h2 class="blue"><i class="fa fa-barcode"></i><?= lang('jobs'); ?></h2>
					</div>
					<div class="box-content">
						<div class="row">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table id="EXPData" cellpadding="0" cellspacing="0" border="0" class="table table-condensed table-bordered table-hover table-striped job_list">
										<thead>
											<tr class="active">
												<th><?php echo $this->lang->line("date"); ?></th>
												<th><?php echo $this->lang->line("reference"); ?></th>
												<th><?php echo $this->lang->line("customer_name"); ?></th>
												<th><?php echo $this->lang->line("product_name"); ?></th>
												<th><?php echo $this->lang->line("quantity"); ?></th>
												<th><?php echo $this->lang->line("developed_quantity"); ?></th>
												<th><?php echo $this->lang->line("status"); ?></th>
												<th><?php echo $this->lang->line("receive_status"); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
												foreach($result as $row)
												{
												  ?>
													<tr>
														<td><?php echo $row->date; ?></td>
														<td><?php echo $row->reference_no; ?></td>
														<td><?php echo $row->customer; ?></td>
														<td><?php echo $row->product_name; ?></td>
														<td><?php echo $row->fquantity; ?></td>
														<td><?php echo $row->dev_quantity; ?></td>
														<td><?php echo row_status($row->status); ?></td>
														<td><?php echo row_status($row->receive); ?></td>
													</tr>
												  <?php
												}
											?>
										</tbody>
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
			
        </div>
    </div>
</div>
<script>
	$(document).ready(function(){
		$('#EXPData').dataTable();
		
	});

</script>