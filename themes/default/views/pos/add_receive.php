<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_receive'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("pos/add_receive/". $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
			<table class="table table-condensed table-bordered table-hover table-striped" style="font-size: 1.2em; font-weight: bold; margin-bottom: 0;">
				<thead>
					<tr>
						<th style="width:3%;"></th>
						<th><?= lang("product_code") ?></th>
						<th><?= lang("product_name") ?></th>
						<th><?= lang("quantity") ?></th>
						<th><?= lang("develop_qty") ?></th>
						<th style="width:180px;"><?= lang("date") ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($rows as $row){
							$date = new DateTime($row->receive_date);
							$dates = $date->format('d/m/Y H:i');

							echo '<tr>
									<td class="center">
										<input class="hidden" type="hidden" name="checked[]" value="'.$row->id.'"/>
									</td>
									<td>'.$row->product_code.'</td>
									<td>'.$row->product_name.'</td>
									<td>'.$row->quantity.'</td>
									<td>'.$row->develop_qty.'</td>
									<td>'.$dates.'</td>
								 </tr>';
						}
					?>
				</tbody>
			</table>
        </div>
        <div class="modal-footer">
		<?php if($dev_status->dev_status =='completed' && $dev_status->receive_status !='received'){
					echo form_submit('add_receive', lang('add_receive'), 'class="btn btn-primary" id="add_receive"');
				}else{
					echo form_submit('add_receive', lang('add_receive'), 'class="btn btn-primary" id="add_receive"disabled');
	       }
			 ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['erp'] = <?=$dp_lang?>;
</script>
<script type="text/javascript" src="<?= $assets ?>pos/js/parse-track-data.js"></script>
<?= $modal_js ?>
<script type="text/javascript" charset="UTF-8">
	$(document).ready(function () {
		$('input[type="checkbox"],[type="radio"]').not('.skip').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass: 'iradio_square-blue',
			increaseArea: '20%' // optional
		});
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
