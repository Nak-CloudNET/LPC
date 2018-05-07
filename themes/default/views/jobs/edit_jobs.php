<div class="modal-dialog">
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
					<fieldset class="scheduler-border">
						<legend class="scheduler-border"><?= lang("detail_information") ?></legend>
						<table width="100%">
							<tr>
								<td style="width:20%;"><?= lang("date"); ?></td>
								<td>: <?= $this->erp->hrld($develop->date);?></td>
							</tr>
							<tr>
								<td><?= lang("reference_no"); ?></td>
								<td>: <?= $develop->reference_no;?></td>
							</tr>
							<tr>
								<td><?= lang("customer_name"); ?></td>
								<td>: <?= $develop->customer;?></td>
							</tr>
							<tr>
								<td><?= lang("product_name"); ?></td>
								<td>: <?= $develop->product_name;?></td>
							</tr>
							<tr>
								<td><?= lang("quantity"); ?></td>
								<td>: <?= $this->erp->formatQuantity($develop->quantity);?></td>
							</tr>
						</table>
						
						<input type="hidden" value="<?= $develop_id;?>" name="develop_id">
						<input type="hidden" value="<?= $develop->product_name;?>" name="product_name">
						<input type="hidden" value="<?= $develop->warehouse;?>" name="warehouse">
						<input type="hidden" value="<?= $develop->product_id;?>" name="product_id">
						<input type="hidden" value="<?= $develop->sale_id;?>" name="sale_id">
						<input type="hidden" value="<?= $develop->unit_price;?>" name="unit_price">
					</fieldset>
				</div>	
			</div>
			<div class="row">
				
				<div class="col-sm-6">
					<?php if ($Owner || $Admin) { ?>
						<div class="form-group" style="margin-left:1%">
							<?= lang("dates_studio", "dates_studio"); ?>
							<div class="controls">
								<?= form_input('date', (isset($_POST['date']) ? $_POST['date'] : $dev_item->date), 'class="form-control datetime" id="date"'); ?>
							</div>
						</div>
					<?php } ?>
				</div>
					
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%;margin-right:1%;">
						<?= lang("studio_machine","studio_machine"); ?>
						<div class="controls">
						<?php
							$getMarchine = array(""=>"");
							foreach($marchine as $marchines){
								$getMarchine[$marchines->id] = $marchines->name;
							}
							echo form_dropdown('machine', $getMarchine, $dev_item->machine_name ,'id="user_1" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("studio_machine") . '" required="required" style="width:100%;" ');
						?>
						</div>
					</div>
				</div>
				
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("no_photo_studio","no_photo_studio"); ?>
						<div class="controls">
							<input name="developed_quantity" type="text" value="<?= $this->erp->formatQuantity($dev_item->quantity); ?>" id="developed_quantity" class="pa form-control kb-pad developed_quantity" required="required"/>
						</div>
					</div>
				</div>
				
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%;margin-right:1%;">
						<?= lang("photowriter","photowriter"); ?>
						<div class="controls">
							<?php
							$photocreater = array(""=>"");
							foreach($computer as $creater){
								if($creater->name == 'photowriter'){
									$photocreater[$creater->id] = $creater->first_name.' '.$creater->last_name;
								}
							}
								echo form_dropdown('user_5', $photocreater, (isset($_POST['user_5']) ? $_POST['user_5'] : $dev_item->user_5) ,'id="user_5" class="form-control input-tip select" required="required" data-placeholder="" style="width:100%;" ');
							?>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("no_photo_blur","no_photo_blur"); ?>
						<div class="controls">
							<input name="quantity_index" type="text" id="developed_quantity" value="<?= $this->erp->formatQuantity($dev_item->quantity_index); ?>"  class="pa form-control kb-pad developed_quantity"/>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%;margin-right:1%;">
						<?= lang("computer","computer"); ?>
						<div class="controls">
						<?php
							$computers = array(""=>"");
							foreach($computer as $user1){
								if($user1->name == 'computer'){
									$computers[$user1->id] = $user1->first_name.' '.$user1->last_name;
								}
							}
							echo form_dropdown('user_1', $computers,  (isset($_POST['user_1']) ? $_POST['user_1'] : $dev_item->user_1) ,'id="user_1" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("computer") . '" required="required" style="width:100%;" ');
						?>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("studio_damage","studio_damage"); ?>
						<div class="controls">
							<input name="quantity_break" type="text" id="developed_quantity" value="<?=$this->erp->formatQuantity($dev_item->quantity_break); ?>" class="pa form-control kb-pad developed_quantity"/>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%;margin-right:1%;">
						<?= lang("developphoto","developphoto"); ?>
						<div class="controls">
							<?php
							$photomaker = array(""=>"");
							foreach($computer as $photo){
								if($photo->name == 'developphoto'){
									$photomaker[$photo->id] = $photo->first_name.' '.$photo->last_name;
								}
							}
								echo form_dropdown('user_2', $photomaker, (isset($_POST['user_2']) ? $_POST['user_2'] : $dev_item->user_2) ,'id="user_2" class="form-control input-tip select" data-placeholder="" required="required" style="width:100%;" ');
							?>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("studio_decor","studio_decor"); ?>
						<div class="controls">
							<?php
							$decor = array(""=>"");
							foreach($computer as $decore){
								if($decore->name == 'decor'){
									$decor[$decore->id] = $decore->first_name.' '.$decore->last_name;
								}
							}
								echo form_dropdown('user_3', $decor, (isset($_POST['user_3']) ? $_POST['user_3'] : $dev_item->user_3) ,'id="user_3" class="form-control input-tip select" data-placeholder="" style="width:100%;" ');
							?>
						</div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%;margin-right:1%;">
						<?= lang("photographer","photographer"); ?>
						<div class="controls">
							<?php
							$photographer = array(""=>"");
							foreach($computer as $grapher){
								if($grapher->name == 'photographer'){
									$photographer[$grapher->id] = $grapher->first_name.' '.$grapher->last_name;
								}
							}
								echo form_dropdown('user_4', $photographer, (isset($_POST['user_4']) ? $_POST['user_4'] : $dev_item->user_4) ,'id="user_4" class="form-control input-tip select" data-placeholder="" style="width:100%;" ');
							?>
						</div>
					</div>
				</div>
			</div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_products_develop', lang('edit_products_develop'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['erp'] = <?=$dp_lang?>;
</script>
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
