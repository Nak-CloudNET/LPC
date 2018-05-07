<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_job_activity'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'class'=>'form-horizontal');
        echo form_open_multipart("jobs/edit_jobActivity", $attrib); ?>
        <div class="modal-body">
			<div class="row">
				<input type="hidden" value="<?= $dev_item->id;?>" name="sale_id">
				
				<div class="col-sm-12">
					<div class="form-group" style="margin-left:1%">
						<?= lang("ឈ្មោះផលិតផល","ឈ្មោះផលិតផល"); ?>​ : <?= $dev_item->product_name?>
						<div class="control">
							
						</div>
					</div>
				</div>
				
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("ចំនួនផ្តិតបានការ","ចំនួនផ្តិតបានការ"); ?>
						<div class="control">
							<input style="width:95%" name="developed_quantity" type="text" id="developed_quantity" value="<?=$dev_item->quantity?>" class="pa form-control kb-pad developed_quantity"
							   required="required"/>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("ផ្តិតខូច","ផ្តិតខូច"); ?>
						<div class="control">
							<input style="width:95%" name="quantity_break" type="text" id="developed_quantity" value="<?=$dev_item->quantity_break?>" class="pa form-control kb-pad developed_quantity"/>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("ចំនួនរូបអ៊ិនដិច","ចំនួនរូបអ៊ិនដិច"); ?>
						<div class="control">
							<input style="width:95%" name="quantity_index" type="text" id="developed_quantity" value="<?=$dev_item->quantity_index?>" class="pa form-control kb-pad developed_quantity"/>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("ឈ្មោះកុំព្យូទ័រ","ឈ្មោះកុំព្យូទ័រ"); ?>
						<div class="control">
						<?php
						$computers = array(""=>"");
						foreach($computer as $user1){
							if($user1->group_id == 13){
								$computers[$user1->id] = $user1->first_name.' '.$user1->last_name;
							}
						}
							echo form_dropdown('user_1', $computers, (isset($_POST['user_1']) ? $_POST['user_1'] : $dev_item->user_1) ,'id="user_1" class="form-control input-tip select" style="width:95%;" ');
						?>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("ឈ្មោះជាងផ្តិតរូប","ឈ្មោះជាងផ្តិតរូប"); ?>
						<div class="control">
						<?php
						$photomaker = array(""=>"");
						foreach($computer as $photo){
							if($photo->group_id == 14){
								$photomaker[$photo->id] = $photo->first_name.' '.$photo->last_name;
							}
						}
							echo form_dropdown('user_2', $photomaker, (isset($_POST['user_2']) ? $_POST['user_2'] : $dev_item->user_2) ,'id="user_2" class="form-control input-tip select" style="width:95%;" ');
						?>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("ឈ្មោះអ្នកតែង","ឈ្មោះអ្នកតែង"); ?>
						<div class="control">
						<?php
						$decor = array(""=>"");
						foreach($computer as $decore){
							if($decore->group_id == 15){
								$decor[$decore->id] = $decore->first_name.' '.$decore->last_name;
							}
						}
							echo form_dropdown('user_3', $decor, (isset($_POST['user_3']) ? $_POST['user_3'] : $dev_item->user_3) ,'id="user_3" class="form-control input-tip select" style="width:95%;" ');
						?>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("ឈ្មោះអ្នកថត","ឈ្មោះអ្នកថត"); ?>
						<div class="control">
						<?php
						$photographer = array(""=>"");
						foreach($computer as $grapher){
							if($grapher->group_id == 16){
								$photographer[$grapher->id] = $grapher->first_name.' '.$grapher->last_name;
							}
						}
							echo form_dropdown('user_4', $photographer, (isset($_POST['user_4']) ? $_POST['user_4'] : $dev_item->user_4) ,'id="user_4" class="form-control input-tip select" style="width:95%;" ');
						?>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group" style="margin-left:1%">
						<?= lang("ឈ្មោះអ្នកផ្តិត","ឈ្មោះអ្នកផ្តិត"); ?>
						<div class="control">
						<?php
						$photocreater = array(""=>"");
						foreach($computer as $creater){
							if($creater->group_id == 17){
								$photocreater[$creater->id] = $creater->username;
							}
						}
							echo form_dropdown('user_5', $photocreater, (isset($_POST['user_5']) ? $_POST['user_5'] : $dev_item->user_5) ,'id="user_5" class="form-control input-tip select" style="width:95%;" ');
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
