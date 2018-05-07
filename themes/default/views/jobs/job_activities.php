<script>
	$(document).ready(function () {
        CURI = '<?= site_url('jobs/job_activities'); ?>';
    });
</script>
<style>
	@media print {
        .fa {
            color: #EEE;
            display: none;
        }

        .small-box {
            border: 1px solid #CCC;
        }
    }
</style>
<?php
	$start_date=date('Y-m-d',strtotime($start));
	$rep_space_end=str_replace(' ','_',$end);
	$end_date=str_replace(':','-',$rep_space_end);
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
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            "aoColumns": [{
                "bSortable": false
            }, null, null, null, null, null, {"bSortable": false}],

        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('product_name');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('quantity');?>]", filter_type: "text", data: []},
			{column_number: 3, filter_default_label: "[<?=lang('quantity_break');?>]", filter_type: "text", data: []},
			{column_number: 4, filter_default_label: "[<?=lang('quantity_index');?>]", filter_type: "text", data: []},
			{column_number: 5, filter_default_label: "[<?=lang('total');?>]", filter_type: "text", data: []},
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
    echo form_open('jobs/activities_actions', 'id="action-form"');
} ?>

<ul id="myTab" class="nav nav-tabs no-print">
	<li class=""><a href="#job_activities" class="tab-grey"><?= lang('job_activities') ?></a></li>
	<li class=""><a href="#job_activities_detail" class="tab-grey"><?= lang('job_activities_detail') ?></a></li>	
</ul>

<div class="tab-content">
	<div id="job_activities" class="tab-pane fade in">
	
		<div class="box">
			<div class="box-header">
				<h2 class="blue"><i class="fa fa-barcode"></i><?= lang('job_activities'); ?></h2>			
				<div class="box-icon">
					<div class="form-group choose-date hidden-xs">
						<div class="controls">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								<input type="text"
									   value="<?= ($start > 0 && $end > 0 ? $start .' - '. $end : date('d/m/Y 00:00') . ' - ' . date('d/m/Y 23:59')) ?>"
									   name ="daterange[]" id="daterange" class="form-control">
								<span class="input-group-addon"><i class="fa fa-chevron-down"></i></span>
							</div>
						</div>
					</div>
				</div>
				
				<div class="box-icon">
					<ul class="btn-tasks">
						<li class="dropdown">
							<a data-toggle="dropdown" class="dropdown-toggle" href="#">
							<i class="icon fa fa-tasks tip" data-placement="left"  title="<?= lang("actions") ?>"></i></a>
							<ul class="dropdown-menu pull-right" class="tasks-menus" role="menu" aria-labelledby="dLabel">								
								<?php if($this->Admin || $this->Owner){ ?>							
									<li><a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
									<li><a href="#" id="pdf" data-action="export_pdf"><i class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?></a></li>
								<?php }else{ ?>
									<?php if($this->GP['jobs-export']){ ?>
									<li><a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
									<li><a href="#" id="pdf" data-action="export_pdf"><i class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?></a></li>
									<?php } ?>
								<?php } ?>
							</ul>
						</li>
					</ul>
				</div>
			</div>
	<?php if ($Owner) { ?>
		<div style="display: none;">
			<input type="hidden" name="form_action" value="" id="form_action"/>
			<?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
		</div>
		<?= form_close() ?>
	<?php } ?>

		<div class="box-content">
			<div class="row">
				<div class="col-lg-12">
					<p class="introtext"><?= lang('list_results'); ?></p>

						<div class="table-responsive">
							<table id="EXPData" cellpadding="0" cellspacing="0" border="0" class="table table-condensed table-bordered table-hover table-striped">
								<thead>
									<tr class="active">
										<th style="min-width:3%; width: 3%; text-align: center;">
											<input class="checkbox checkft" type="checkbox" name="check"/>
										</th>
										<th class="col-xs-2"><?php echo $this->lang->line("product_name"); ?></th>
										<th class="col-xs-2"><?php echo $this->lang->line("quantity"); ?></th>
										<th class="col-xs-2"><?php echo $this->lang->line("quantity_break"); ?></th>
										<th class="col-xs-2"><?php echo $this->lang->line("quantity_index"); ?></th>
										<th class="col-xs-2"><?php echo $this->lang->line("total_quantity"); ?></th>
										<th style="width:100px;"><?php echo $this->lang->line("actions"); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$body = '';
										foreach($results as $row){
											$edit_link = '';
											if($this->Admin || $this->Owner){
											   $edit_link = anchor('jobs/edit_jobActivity/'.$row->product_id.'', '<i class="fa fa-edit"></i> ' . lang('edit_jobs'), 'data-toggle="modal" data-target="#myModal"');
											}else{
											   echo $this->GP['jobs-edit'];
											   if($this->GP['jobs-edit']){
												   $edit_link = anchor('jobs/edit_jobActivity/'.$row->product_id.'', '<i class="fa fa-edit"></i> ' . lang('edit_jobs'), 'data-toggle="modal" data-target="#myModal"');
											   }
											   
											}
											$action = '<div class="text-center"><div class="btn-group text-left">'
											. '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
											. lang('actions') . ' <span class="caret"></span></button>
											<ul class="dropdown-menu pull-right" role="menu">
												<li>' . $edit_link . '</li>
											</ul></div></div>';
											$body .='<tr>
														<td><center><input class="checkbox multi-select" type="checkbox" name="val[]" value="'.$row->product_id.'" /></center></td>
														<td>'.$row->product_name.'</td>
														<td>'.$row->pquantity.'</td>
														<td>'.$row->qty_break.'</td>
														<td>'.$row->qty_index.'</td>
														<td>'.$row->tquantity.'</td>
														<td>'.$action.'</td>
													</tr>';
										}
										echo $body;
									?>
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
										<th style="width:100px; text-align: center;"><?php echo $this->lang->line("actions"); ?></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div id="job_activities_detail" class="tab-pane fade in">
		<div class="box">
			<div class="box-header">
				<h2 class="blue">
					<i class="fa-fw fa fa-folder-open"></i>
					<?= lang('jobs_activities_detail'); ?>
				</h2>
				<div class="box-icon">
					<ul class="btn-tasks">
						<li class="dropdown">
							<a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
								<i class="icon fa fa-toggle-up"></i>
							</a>
						</li>
						<li class="dropdown">
							<a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
								<i class="icon fa fa-toggle-down"></i>
							</a>
						</li>
					</ul>
				</div>
				
			</div>
			<div class="box-content">
				<div class="row">
					<div class="col-lg-12">
						<p class="introtext"><?= lang('list_results'); ?></p>
							
						<?php echo form_open("jobs/job_activities#job_activities_detail", ' method="GET" id="myform"'); ?>
						<div class="form">
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label" for="product_id"><?= lang("product"); ?></label>
									<?php
									$pr[""] = "";
									foreach ($products as $product) {
										$pr[$product->id] = $product->name . " | " . $product->code ;
									}
									echo form_dropdown('product_id', $pr, (isset($_GET['product_id']) ? $_GET['product_id'] : ""), 'class="form-control" id="product_id" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("product") . '"');
									?>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label" for="customer"><?= lang("customer"); ?></label>
									  <?php
									$cus[''] ='';
									foreach($customer as $c){
										$cus[$c->id] = $c->text;
									}
									echo form_dropdown('customer',$cus, (isset($_GET['customer']) ? $_GET['customer'] : ""),'class="form-control" id="customer" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"' ); 
                                    ?>
								</div>
							</div>
							<div class="col-sm-d-4">
								<div class="form-group">
								<?= lang("saleman", "saleman"); ?>
									<?php 
										$salemans['0'] = lang("all");
										foreach($agencies as $agency){
											$salemans[$agency->id] = $agency->username;
										}
										echo form_dropdown('saleman', $salemans, (isset($_GET['saleman']) ? $_GET['saleman'] : ""), 'id="saleman" class="form-control saleman"');
									?>
								</select>
								<?php
								?>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label" for="reference_no"><?= lang("reference_no"); ?></label>
									<?php echo form_input('reference_no', (isset($_GET['reference_no']) ? $_GET['reference_no'] : ""), 'class="form-control tip" id="reference_no"'); ?>

								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label" for="user"><?= lang("created_by"); ?></label>
									<?php
									$us[""] = "";
									foreach ($users as $user) {
										$us[$user->id] = $user->first_name . " " . $user->last_name;
									}
									echo form_dropdown('user', $us, (isset($_GET['user']) ? $_GET['user'] : ""), 'class="form-control" id="user" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("user") . '"');
									?>
								</div>
							</div>
                        
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label" for="biller"><?= lang("biller"); ?></label>
									<?php
									$bl[""] = "";
									foreach ($billers as $biller) {
										$bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
									}
									echo form_dropdown('biller', $bl, (isset($_GET['biller']) ? $_GET['biller'] : ""), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("biller") . '"');
									?>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label" for="warehouse"><?= lang("warehouse"); ?></label>
									<?php
									$wh[""] = "";
									foreach ($warehouses as $warehouse) {
										$wh[$warehouse->id] = $warehouse->name;
									}
									echo form_dropdown('warehouse', $wh, (isset($_GET['warehouse']) ? $_GET['warehouse'] : ""), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
									?>
								</div>
							</div>
							<!--
							 <div class="col-sm-4">
								<div class="form-group">
									<?= lang("start_date", "start_date"); ?>
									<?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control datetime" id="start_date"'); ?>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<?= lang("end_date", "end_date"); ?>
									<?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control datetime" id="end_date"'); ?>
								</div>
							</div>
							-->
							<div class="col-sm-1">
								<div class="form-group">
								<label class="control-label" for="warehouse"><?= lang(""); ?><br></label>
									<div class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
									
								</div>
							</div>
							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label" for="warehouse"><?= lang(""); ?><br></label>
									<div class="controls"> <?php echo form_button('submit_report', $this->lang->line("Reset"), 'class="btn btn-danger" id="Reset"'); ?> </div>
								</div>
							</div>
						</div>	
						<?php echo form_close(); ?>		
					</div>
						
						
						<div class="table-responsive">
							<table id="shipmentsTable" class="table table-condensed table-bordered table-hover table-striped">
								<thead>
									<tr>
										<th style="min-width:3%; width: 3%; text-align: center;">
											<input class="checkbox checkth" type="checkbox" name="check"/>
										</th>
										<th style="min-width:150px;" class="sorting"><?= $this->lang->line("date"); ?></th>	
										<th style="min-width:150px;" class="sorting"><?= $this->lang->line("product_name"); ?></th>
										<th style="min-width:150px;" class="sorting"><?= $this->lang->line("developed_quantity"); ?></th>
										<th style="min-width:150px;" class="sorting"><?= $this->lang->line("quantity_break");?> </th>
										<th style="min-width:150px;" class="sorting"><?= $this->lang->line("quantity_index");?> </th>
										<th style="min-width:150px;" class="sorting"><?= $this->lang->line("user");?> </th>								
										<th><?= $this->lang->line("actions"); ?></th>
									</tr>
								</thead>
								
								<?php 
									if(count($resultss) > 0){								
									foreach($resultss as $result){
										
										$start_date = $this->input->get("start_date");
										$end_date = $this->input->get("end_date");
										$str_sql = "";
										
										if($start_date && $end_date){
											$str_sql .= "AND date(created_at) BETWEEN '{$this->erp->fld($start_date)}' AND '{$this->erp->fld($end_date)}'";
										}
										if($_GET['product_id']){
											$str_sql .="AND erp_sale_dev_items.product_id ='".$_GET['product_id']."'";
										}
										if($_GET['customer']){
											$str_sql .="AND sales.customer_id ='".$_GET['customer']."'";
										}
										if($_GET['saleman']){
											$str_sql .="AND sales.saleman_by ='".$_GET['saleman']."'";
										}
										if($_GET['reference_no']){
											$str_sql .="AND sales.reference_no ='".$_GET['reference_no']."'";
										}
										if($_GET['user']){
											$str_sql .="AND erp_sale_dev_items.created_by ='".$_GET['user']."'";
										}
										if($_GET['biller']){
											$str_sql .="AND sales.biller_id ='".$_GET['biller']."'";
										}
										if($_GET['warehouse'] && $_GET['warehouse'] !='0'){
											$str_sql .="AND erp_sale_dev_items.warehouse_id ='".$_GET['warehouse']."'";
										} 
										$results_detail = $this->db->query("SELECT
																				erp_sale_dev_items.*,
																				erp_users.username
																			FROM
																				erp_sale_dev_items
																			LEFT JOIN erp_users on erp_users.id = erp_sale_dev_items.created_by
																			LEFT JOIN erp_sales AS sales ON sales.id = erp_sale_dev_items.sale_id
																			WHERE erp_sale_dev_items.sale_id={$result->sale_id} {$str_sql}																			
																			")->result();
								?>
									<tr style="font-weight:bold;">
										<td>									
											<td><?= $result->reference_no; ?> <i class="fa fa-hand-o-right" aria-hidden="true"></i></td>
										</td>
									</tr>
									
									<?php foreach($results_detail as $result_detail){ ?>
											
											<tr>
												<td></td>
												<td><?= $this->erp->hrld($result_detail->created_at); ?></td>
												<td class="center"><?= $result_detail->product_name ?></td>
												<td class="center"><?= $result_detail->quantity ?></td>
												<td class="center"><?= $result_detail->quantity_break ?></td>
												<td class="center"><?= $result_detail->quantity_index ?></td>
												<td><?= $result_detail->username; ?></td>
												<td class="center">
													<div class="btn-group text-left">
													<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><?= lang("actions") ?><span class="caret"></span></button>
														<ul class="dropdown-menu pull-right" role="menu">					        	
															<li><a href="<?= site_url("jobs/edit_jobs/".$result_detail->id)?>" data-toggle="modal" data-target="#myModal2"><i class="fa fa fa-edit"></i> <?= lang("edit")?></a></li>
														</ul>
													</div>
												</td>																				
											</tr>							
									<?php } ?>
									
									<?php }
								}else { ?>
								
								
								<tbody>
									<tr>
										<td colspan="5" class="dataTables_empty">
											<?= lang('loading_data_from_server') ?>
										</td>
									</tr>
								</tbody>
								
								<?php }  ?>
								
								
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
										<th style="width:100px; text-align: center;"></th>
										
									</tr>
									
								</tfoot>
								
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>
<script language="javascript">
    $(document).ready(function () {        
        $('#delete').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });
        $('#excel').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });
        $('#pdf').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });
		
		$('.form').hide();
        $('.toggle_down').click(function () {
            $(".form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $(".form").slideUp();
            return false;
        });
		$('#Reset').click(function(){
			window.open('jobs/job_activities','_self');
		});
    });
	
	
</script>


