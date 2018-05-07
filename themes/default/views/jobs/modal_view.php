<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= $product->name; ?></h4>
        </div>
		<?php
			$tbody = '';
			foreach($date as $row){
				$tbody .= '<tr>
								<td>'.$row['product_name'].'</td>
								<td style="text-align:right">'.$this->erp->formatQuantity($row['quantity']).'</td>
								<td style="text-align:center">'.$row['created_at'].'</td>
								<td style="text-align:center">'.$row['reference_no'].'</td>
							</tr>';
			}
		?>
         <div>
              <table id="PRData" class="table table-bordered table-condensed table-striped">
				<thead>
					<tr>
						<th><?=  lang('product_name') ?></th>
						<th><?=  lang('quantity') ?></th>
						<th><?=  lang('date') ?></th>
						<th><?=  lang('invoice') ?></th>
					</tr>
				</thead>
				<tbody>
					<?= $tbody ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
