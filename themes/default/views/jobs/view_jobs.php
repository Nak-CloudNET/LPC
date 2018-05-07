<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_products_develop'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'class'=>'form-horizontal');
        echo form_open_multipart("jobs/edit_jobs", $attrib); ?>
        <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<?php
						$results_detail = $this->db->query("SELECT
																erp_sale_dev_items.*,
																erp_users.username
															FROM
																erp_sale_dev_items
															LEFT JOIN erp_users on erp_users.id = erp_sale_dev_items.created_by
															WHERE erp_sale_dev_items.sale_id={$develop->sale_id}")->result(); ?>
															
					<table class="table table-condensed table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th><?= $this->lang->line("Nº"); ?></th>
								<th><?= $this->lang->line("date"); ?></th>	
								<th><?= $this->lang->line("product_name"); ?></th>
								<th><?= $this->lang->line("developed_quantity"); ?></th>
								<th><?= $this->lang->line("quantity_break");?> </th>
								<th><?= $this->lang->line("quantity_index");?> </th>
								<th><?= $this->lang->line("user");?> </th>
								<th><?= $this->lang->line("status");?> </th>								
							</tr>
							<tbody>
								<?php 
								if(count($results_detail) > 0){
								foreach($results_detail as $key => $result_detail){  ?>
									<tr>
										<td class="center"><?= ($key+1); ?></td>
										<td><?= $this->erp->hrld($result_detail->created_at); ?></td>
										<td class="center"><?= $result_detail->product_name ?></td>
										<td class="center"><?= $result_detail->quantity ?></td>
										<td class="center"><?= $result_detail->quantity_break ?></td>
										<td class="center"><?= $result_detail->quantity_index ?></td>
										<td><?= $result_detail->username; ?></td>
										
										<?php
											$delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_jobs") . "</b>' data-content=\"<p>"
												. lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('jobs/delete_jobs/'.$result_detail->id.'') . "'>"
												. lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
												. lang('delete_jobs') . "</a>";
										?>
										
										<td class="center">
											<div class="btn-group text-left"><button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><?= lang("actions") ?><span class="caret"></span></button>
												<ul class="dropdown-menu pull-right" role="menu">
													<li><a href="<?= site_url("jobs/edit_jobs/".$result_detail->id)?>" data-toggle="modal" data-target="#myModal2"><i class="fa fa fa-edit"></i> <?= lang("edit")?></a></li>
													<li><?= $delete_link; ?></li>
												</ul>
											</div>
										</td>
									</tr>
								<?php } 
								}else { ?>
									<tr>
										<td colspan="5" class="dataTables_empty">
											<?= lang('loading_data_from_server') ?>
										</td>
									</tr>
								<?php } ?>
							</tbody>
							
						</thead>
					</table>
						
				</div>	
			</div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $.fn.datetimepicker.dates['erp'] = <?=$dp_lang?>;
        $("#date").datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            language: 'erp',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        }).datetimepicker('update', new Date());
    });
</script>
