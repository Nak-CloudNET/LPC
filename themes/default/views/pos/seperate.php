<style>
	#tb1 tr th{
		        background-color: #428BCA;
				color: white;
				border-color: #357EBD;
				border-top: 1px solid #357EBD;
				text-align: center;
	}
	#tb2 tr th{
		       background-color: #428BCA;
				color: white;
				border-color: #357EBD;
				border-top: 1px solid #357EBD;
				text-align: center;
	}
	#tb2 th:nth-child(1) {
		width: 4%;
	}
	.error{
		color:red;
	}
	.error2{
		color:red;
	}
</style>
<div class="modal-dialog modal-lg">
    <div class="modal-content col-sm-12">
        <div class="modal-header col-sm-12">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="false"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_seperate'); ?></h4>
        </div>
       
        <div class="modal-body col-sm-12" >
				<p>Table : <?=$suspended_name->name?></p>
				<div class=" col-sm-8">
					
					<table id="tb1" class="table table-bordered table-hover table-striped">
						<tr>
							<th><input class="checkbox checkth2" type="checkbox" name="check"/></th>
							<th><?=lang('Product')?></th>
							<th><?=lang('price')?></th>
							<th><?=lang('price_kh')?></th>
							<th><?=lang('qty')?></th>
							<th><?=lang('discount')?></th>
							<th><?=lang('subtotal')?></th>
						</tr>
						<?php 
						if(is_array($suspended_items)){
							foreach($suspended_items as $row_item){
						?>
						<tr class="tr">
							<td><input class="checkbox checkft2" type="checkbox" name="check2[]" value="<?=$row_item->id?>"/></td>
							<td><?=$row_item->product_name?></td>
							<td><?=$row_item->unit_price?></td>
							<td><?=$row_item->unit_price*$exchange_rate->rate?></td>
							<td><input style="width:80px;" type="text" class="form-control quantity" name="quantity[]" value="<?=$this->erp->formatDecimal($row_item->quantity)?>" />
							<input style="width:80px;" type="hidden" class="form-control quantity2" name="quantity2[]" value="<?=$this->erp->formatDecimal($row_item->quantity)?>" />
							</td>
							<td><?=$row_item->discount?></td>
							<td><?=$row_item->subtotal?></td>
						</tr>
						<?php
							}
						} 
						?>
					</table>
					<p class="error"></p>
				</div>
				<div class=" col-sm-4">
					<table id="tb2"  class="table table-bordered table-hover table-striped">
						<tr>
							<th>#</th>
							<th><?=lang('table')?></th>
						</tr>
						<?php 
						if(is_array($suspended)){
							foreach($suspended as $table){
								
						?>
						<tr>
							<td><input class="checkbox checkft3" type="radio" name="check3[]" value="<?=$table->id?>" /></td>
							<?php if($table->status == 1){ ?>
							<td style="color:orange;"><?=$table->name?></td>
							<?php }else{ ?>
							<td><?=$table->name?></td>
							<?php } ?>
						</tr>
						<?php
						}
						} 
						?>
					</table>
					<p class="error2"></p>
				</div>
			
        </div>

        <div class="modal-footer col-sm-12">
				<button id="add_seperate" class="btn btn-primary"><?=lang('add_seperate')?></button>
        </div>
   
</div>
</div>

<?= $modal_js ?>
<script>
	
	$(document).ready(function() {
		$(document).on('ifChecked', '.checkth2', function(event) {
			$('.checkth2').iCheck('check');
			$('.checkft2').each(function() {
				$(this).iCheck('check');
			});
		});
		$(document).on('ifUnchecked', '.checkth2,.checkft2', function(event) {
			$('.checkth2,.checkft2').iCheck('uncheck');
			$('.checkft2').each(function() {
				$(this).iCheck('uncheck');
			});
		});
		
		$("#add_seperate").click(function(e){
			e.preventDefault();
			var i = 0;
			var k = 0;
			var items = [];
			
			var tab = "";
			var hasCheck = false;
			var has = false;
			var qty = 0;
			var qty2 = 0;
			var b  = false;
			var k = false;
			$.each($("input[name='check2[]']:checked"), function(){
				var str = $(this).closest('.tr');
				qty  = str.find('.quantity').val()-0;
				qty2  = str.find('.quantity2').val()-0;
				if(qty > qty2){
					b = true;
				}
				if(qty<=0){
					k = true;
				}
				//alert(qty2);
				items[i] = {'id':$(this).val(),'qty':qty};
				i++;
				hasCheck = true;
			});
			//return false;
			//console.log(items);
			if(k == true){
				$(".error").text("invalid number!");
				return false;
			}
			if(b == true){
				$(".error").text("New number cannot greater than old number!");
				return false;
			}
			if(hasCheck == false){
				//bootbox.alert('Please select!');
				$(".error").text("Please select items!");
				return false;
			}
			tab =  $("input[name='check3[]']:checked").val();
			//console.log(tab);
			if(!tab){
				$(".error2").text("Please select a table!");
				return false;
			}
			
			
			$.ajax({
				url: '<?= site_url('pos/add_seperate'); ?>',
				type : 'GET',
				dataType: "JSON",
				contentType : 'application/json;  charset=utf8',
				data: {
					items:items,
					tab:tab,
					id:'<?=$id?>'
				},
				success: function (data) {
					if(data){
						window.location = window.location.pathname;
					}
				},error: function(err){
					alert(JSON.stringify(err));
				}
			});
				
			
		});
		
		
});
</script>