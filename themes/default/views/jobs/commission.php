<style type="text/css" media="all">
	.table {
		overflow-x: scroll;
		max-width: 300%;
		min-height: 300px;
		display: block;
		cursor: pointer;
		white-space: nowrap;
	}
</style>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i><?= lang('commission') ; ?>
        </h2>
		<div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="javascript:void(0);" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="javascript:void(0);" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
				<li class="dropdown">
					<a href="#" id="pdf" data-action="export_pdf"  class="tip" title="<?= lang('download_pdf') ?>">
						<i class="icon fa fa-file-pdf-o"></i>
					</a>
				</li>
                <li class="dropdown">
						<a href="#" id="excel" data-action="export_excel"  class="tip" title="<?= lang('download_xls') ?>">
								<i class="icon fa fa-file-excel-o"></i>
						</a>
				</li>
            </ul>
        </div>
        

    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div id="form">
				<?php echo form_open('jobs/commission/', 'id="action-form"'); ?>
					<div class="row">
						<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="cat"><?= lang("categories"); ?></label>
                                <?php
								$cat[""] = "ALL";
                                foreach ($categories as $category) {
                                    $cat[$category->id] = $category->name;
                                }
                                echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ""), 'class="form-control" id="category" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("category") . '"');
                                ?>
                            </div>
                        </div>
						<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="cat"><?= lang("products"); ?></label>
                                <?php
                               
								$pro[""] = "ALL";
                                foreach ($products as $product) {
                                    $pro[$product->id] = $product->code.' / '.$product->name;
                                }
                                echo form_dropdown('product', $pro, (isset($_POST['product']) ? $_POST['product'] : ""), 'class="form-control" id="product" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("producte") . '"');
                                ?>
                            </div>
                        </div>						
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="cat"><?= lang("user"); ?></label>
                                <?php
                               
								$us[""] = "ALL";
								
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ""), 'class="form-control" id="user" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("user") . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("from_date", "from_date"); ?>
                                <?php echo form_input('from_date', (isset($_POST['from_date']) ? $_POST['from_date'] : ""), 'class="form-control date" id="from_date"'); ?>
                            </div>
                        </div>
						
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("to_date", "to_date"); ?>
                                <?php echo form_input('to_date', (isset($_POST['to_date']) ? $_POST['to_date'] : ""), 'class="form-control date" id="to_date"'); ?>
                            </div>
                        </div>	
						<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="cat"><?= lang("group"); ?></label>
                                <?php
                             
								$ug[""] = "ALL";
                                foreach ($groups as $group) {
                                    $ug[$group->id] = $this->lang->line($group->name);
                                }
                                echo form_dropdown('group', $ug, (isset($_POST['group']) ? $_POST['group'] : ""), 'class="form-control" id="group" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("group") . '"');
                                ?>
                            </div>
                        </div>	
                    </div>
					
					<div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>
					
                </div>
                <div class="clearfix"></div>
				<?php 	
					if($group_id =='' || $group_id == '9'){
						$photowriters = $this->jobs_model->getAllUsersByGroup(9,$user_id); 
					}else{
						$photowriters = array();
					}
					if($group_id =='' || $group_id == '10'){
						$computers = $this->jobs_model->getAllUsersByGroup(10,$user_id); 
					}else{
						$computers = array();
					}
					if($group_id =='' || $group_id == '11'){
						$developphotos = $this->jobs_model->getAllUsersByGroup(11,$user_id); 
					}else{
						$developphotos = array();
					}
					if($group_id =='' || $group_id == '8'){
						$photographers = $this->jobs_model->getAllUsersByGroup(8,$user_id); 
					}else{
						$photographers = array();
					}
					if($group_id =='' || $group_id == '12'){
						$studio_decors = $this->jobs_model->getAllUsersByGroup(12,$user_id); 
					}else{
						$studio_decors = array();
					}
					if($group_id =='' || $group_id == '17'){
						$salesmans = $this->jobs_model->getAllUsersByGroup(17,$user_id); 
					}else{
						$salesmans = array();
					}

					$num_photo = count($photowriters);
					$num_com = count($computers);
					$num_dev = count($developphotos);
					$num = count($photographers);
					$num_decor = count($studio_decors);
					$num_salesman = count($salesmans);


				?>
                <div style="width:100%;overflow:auto;">
                    <table class="table table-bordered table-condensed table-striped">
                        <thead>						
							<tr>
								<th rowspan="2" style="min-width:300px !important;"><?= lang("product_name") ?></th>
								<?php if($num_photo > 0){ ?>
									<th colspan="<?= $num_photo; ?>" style="min-width:250px !important;"><?= lang("photowriter") ?></th>
								<?php } if($num_com > 0){ ?>
									<th colspan="<?= $num_com; ?>" style="min-width:250px !important;"><?= lang("computer") ?></th>
								<?php } if($num_dev > 0){ ?>
									<th colspan="<?= $num_dev; ?>" style="min-width:250px !important;"><?= lang("developphoto") ?></th>
								<?php } if($num > 0){ ?>
									<th colspan="<?= $num; ?>" style="min-width:250px !important;"><?= lang("photographer") ?></th>
								<?php } if($num_decor > 0){ ?>
									<th colspan="<?= $num_decor; ?>" style="min-width:250px !important;"><?= lang("studio_decor") ?></th>
								<?php } if($num_salesman > 0){ ?>
									<th colspan="<?= $num_salesman; ?>" style="min-width:250px !important;"><?= lang("salesman") ?></th>
								<?php } ?> 			
							</tr>
							<tr>								
								<?php 
									foreach($photowriters as $photowriter){
										echo "<th style='width:100px !important; text-align:center;'>". $photowriter->first_name ."</th>";
									}
									
									foreach($computers as $computer){
										echo "<th style='width:100px !important; text-align:center;'>". $computer->first_name ."</th>";
									}
								
									foreach($developphotos as $developphoto){
										echo "<th style='width:100px !important; text-align:center;'>". $developphoto->first_name ."</th>";
									}
									
									foreach($photographers as $photographer){
										echo "<th style='width:100px !important; text-align:center;'>". $photographer->first_name ."</th>";
									}
									
									foreach($studio_decors as $studio_decor){
										echo "<th style='width:100px !important; text-align:center;'>". $studio_decor->first_name ."</th>";
									}
									foreach($salesmans as $saleman){
										echo "<th style='width:100px !important; text-align:center;'>". $saleman->first_name ."</th>";
									}
								?>
							</tr>
                        </thead>
						<?php
							$getUser1Com = $this->jobs_model->getUser1Com($from_date,$to_date,$product_id1);
							$getUser2Com = $this->jobs_model->getUser2Com($from_date,$to_date,$product_id1);
							$getUser3Com = $this->jobs_model->getUser3Com($from_date,$to_date,$product_id1);
							$getUser4Com = $this->jobs_model->getUser4Com($from_date,$to_date,$product_id1);
							$getUser5Com = $this->jobs_model->getUser5Com($from_date,$to_date,$product_id1);
							$getSaleComs = $this->jobs_model->getSalemanCom($from_date,$to_date,$product_id1);
							$array_user_1 = array();
							$array_user_2 = array();
							$array_user_3 = array();
							$array_user_4 = array();
							$array_user_5 = array();
							$array_salemans = array();
							foreach($getUser1Com as $rowUser1Com){
								$array_user_1[$rowUser1Com['product_id']][$rowUser1Com['user_1']] = $this->erp->formatQuantity($rowUser1Com['qty']);
							}
							foreach($getUser2Com as $rowUser2Com){
								$array_user_2[$rowUser2Com['product_id']][$rowUser2Com['user_2']] = $this->erp->formatQuantity($rowUser2Com['qty']);
							}
							foreach($getUser3Com as $rowUser3Com){
								$array_user_3[$rowUser3Com['product_id']][$rowUser3Com['user_3']] = $this->erp->formatQuantity($rowUser3Com['qty']);
							}
							foreach($getUser4Com as $rowUser4Com){
								$array_user_4[$rowUser4Com['product_id']][$rowUser4Com['user_4']] = $this->erp->formatQuantity($rowUser4Com['qty']);
							}
							foreach($getUser5Com as $rowUser5Com){
								$array_user_5[$rowUser5Com['product_id']][$rowUser5Com['user_5']] = $this->erp->formatQuantity($rowUser5Com['qty']);
							}
							foreach($getSaleComs as $getSaleCom){
								$array_salemans[$getSaleCom['product_id']][$getSaleCom['created_by']] = $this->erp->formatQuantity($getSaleCom['qty']);
							}
						////totalCommision
						
						$getTotalComUser1 = $this->jobs_model->getTotalComUser1($from_date,$to_date,$product_id1);
						$TotalComUser1 = array();
						foreach($getTotalComUser1 as $rowTotalComUser1){
							$TotalComUser1[$rowTotalComUser1['user_1']] = $rowTotalComUser1['total_commision'];
						}
						
						$getTotalComUser2 = $this->jobs_model->getTotalComUser2($from_date,$to_date,$product_id1);
						$TotalComUser2 = array();
						foreach($getTotalComUser2 as $rowTotalComUser2){
							$TotalComUser2[$rowTotalComUser2['user_2']] = $rowTotalComUser2['total_commision'];
						}
						
						$getTotalComUser3 = $this->jobs_model->getTotalComUser3($from_date,$to_date,$product_id1);
						$TotalComUser3 = array();
						foreach($getTotalComUser3 as $rowTotalComUser3){
							$TotalComUser3[$rowTotalComUser3['user_3']] = $rowTotalComUser3['total_commision'];
						}
						
						$getTotalComUser4 = $this->jobs_model->getTotalComUser4($from_date,$to_date,$product_id1);
						$TotalComUser4 = array();
						foreach($getTotalComUser4 as $rowTotalComUser4){
							$TotalComUser4[$rowTotalComUser4['user_4']] = $rowTotalComUser4['total_commision'];
						}
						
						$getTotalComUser5 = $this->jobs_model->getTotalComUser5($from_date,$to_date,$product_id1);
						$TotalComUser5 = array();
						foreach($getTotalComUser5 as $rowTotalComUser5){
							$TotalComUser5[$rowTotalComUser5['user_5']] = $rowTotalComUser5['total_commision'];
						}	

						$getTotalComSalesman = $this->jobs_model->getTotalComSalesman($from_date,$to_date,$product_id1);
						$TotalComSalemans = array();
						foreach($getTotalComSalesman as $getTotalComSaleman){
							$TotalComSalemans[$getTotalComSaleman['created_by']] = $getTotalComSaleman['total_commision'];
						}						
						
						$User5 = $this->jobs_model->getComUser5($from_date,$to_date,$product_id1);
						$comUser5 = array();
						$GrandTotalUser5 = array();
						foreach($User5 as $rowUser5){
							$comUser5[$rowUser5['user_5']] = $rowUser5['commission_user'];
							$GrandTotalUser5[$rowUser5['user_5']] = $rowUser5['sum_grand_total'];
						}
						
						$User1 = $this->jobs_model->getComUser1($from_date,$to_date,$product_id1);
						$comUser1 = array();
						$GrandTotalUser1 = array();
						foreach($User1 as $rowUser1){
							$comUser1[$rowUser1['user_1']] = $rowUser1['commission_user'];
							$GrandTotalUser1[$rowUser1['user_1']] = $rowUser1['sum_grand_total'];
						}
						
						$User2 = $this->jobs_model->getComUser2($from_date,$to_date,$product_id1);
						$comUser2 = array();
						$GrandTotalUser2 = array();
						foreach($User2 as $rowUser2){
							$comUser2[$rowUser2['user_2']] = $rowUser2['commission_user'];
							$GrandTotalUser2[$rowUser2['user_2']] = $rowUser2['sum_grand_total'];
						}
						
						$User3 = $this->jobs_model->getComUser3($from_date,$to_date,$product_id1);
						$comUser3 = array();
						$GrandTotalUser3 = array();
						foreach($User3 as $rowUser3){
							$comUser3[$rowUser3['user_3']] = $rowUser3['commission_user'];
							$GrandTotalUser3[$rowUser3['user_3']] = $rowUser3['sum_grand_total'];
						}
						
						$User4 = $this->jobs_model->getComUser4($from_date,$to_date,$product_id1);
						$comUser4 = array();
						$GrandTotalUser4 = array();
						foreach($User4 as $rowUser4){
							$comUser4[$rowUser4['user_4']] = $rowUser4['commission_user'];
							$GrandTotalUser4[$rowUser4['user_4']] = $rowUser4['sum_grand_total'];
						}
						
						$getComSalesman = $this->jobs_model->getComSalesman($from_date,$to_date,$product_id1);
						$comSaleman = array();
						$GrandTotalSal = array();
						foreach($getComSalesman as $getComSaleman){
							$comSaleman[$getComSaleman['created_by']] = $getComSaleman['commission_user'];
							$GrandTotalSal[$getComSaleman['created_by']] = $getComSaleman['sum_grand_total'];
						}
						?>
						<tbody>
							<?php

								$getSaleDevItem = $this->jobs_model->getSaleDevItem($from_date,$to_date,$product_id1,$cate_id1);
								$total_pho_writer = 0;
								$total_computer = 0;
								$total_dev_photo = 0;
								$total_photo = 0;
								$total_studio = 0;
								$test = 0;
								foreach($getSaleDevItem as $rowSaleDev){
									echo '<tr>';
											echo '<td>'.$rowSaleDev['product_name'].'</td>';											
											foreach($photowriters as $photowriter){
												$ph_writer = $array_user_5[$rowSaleDev['product_id']][$photowriter->id];
												if($ph_writer > 0){
													echo '<td group_user="user_5" user_id="'.$photowriter->id.'" id="'.$rowSaleDev['product_id'].'" class="pop_detail" style="text-align:right;">'.$ph_writer.' <span style="color: red;">('. $this->erp->formatMoney($ph_writer * $rowSaleDev['photowriter']) .')</span>'.'</td>';
												}else{
													echo '<td></td>';
												}

											}
											
											foreach($computers as $com){
												$computer = $array_user_1[$rowSaleDev['product_id']][$com->id];
												if($computer > 0){
													echo '<td group_user="user_1" user_id="'.$com->id.'" id="'.$rowSaleDev['product_id'].'" class="pop_detail" style="text-align:right;">'.$computer.' <span style="color: red;">('. $this->erp->formatMoney($computer * $rowSaleDev['computer']) .')</span>'.'</td>';
												}else{
													echo '<td></td>';
												}
											}
											
											foreach($developphotos as $dev_pho){
												$developphoto = $array_user_2[$rowSaleDev['product_id']][$dev_pho->id];
												if($developphoto > 0){
													echo '<td group_user="user_2" user_id="'.$dev_pho->id.'" id="'.$rowSaleDev['product_id'].'" class="pop_detail" style="text-align:right;">'.$developphoto.' <span style="color: red;">('. $this->erp->formatMoney($rowSaleDev['developphoto'] * $developphoto) .')</span>'.'</td>';
												}else{
													echo '<td></td>';
												}
												
											}
											
											foreach($photographers as $photo){
												$photographer = $array_user_4[$rowSaleDev['product_id']][$photo->id];
												if($photographer > 0){
													echo '<td group_user="user_4" user_id="'.$photo->id.'" id="'.$rowSaleDev['product_id'].'" class="pop_detail" style="text-align:right;">'.$photographer.' <span style="color: red;">('. $this->erp->formatMoney($rowSaleDev['photographer'] * $photographer) .')</span>'.'</td>';
												}else{
													echo '<td></td>';
												}
											} 
											
											foreach($studio_decors as $studio_dec){
												$studio_decor = $array_user_3[$rowSaleDev['product_id']][$studio_dec->id];
												if($studio_decor > 0 ){
													echo '<td group_user="user_3" user_id="'.$studio_dec->id.'" id="'.$rowSaleDev['product_id'].'" class="pop_detail" style="text-align:right;">'.$studio_decor.' <span style="color: red;">('. $this->erp->formatMoney($rowSaleDev['studio_decor'] * $studio_decor) .')</span>'.'</td>';
												}else{
													echo '<td></td>';
												}
												
											}
										
											foreach($salesmans as $salesman){
												$saleman = $array_salemans[$rowSaleDev['product_id']][$salesman->id];
												if($saleman > 0 ){
													echo '<td group_user="created_by" user_id="'.$salesman->id.'" id="'.$rowSaleDev['product_id'].'" class="pop_detail" style="text-align:right;">'.$saleman.' <span style="color: red;">('. $this->erp->formatMoney($rowSaleDev['salesman'] * $saleman) .')</span>'.'</td>';
												}else{
													echo '<td></td>';
												}
												
											}
									echo '</tr>';
								}
							
							?>
                        </tbody>						
						<tfoot>
							<tr style="font-weight:bold;">
								<td><b>Total Commission By Items</b></td>
								<?php
							
									foreach($photowriters as $photowriter){
										echo '<td style="text-align:right;">'.$this->erp->formatMoney($TotalComUser5[$photowriter->id]).'</td>';
									} 
									
									foreach($computers as $com){
										echo '<td style="text-align:right;">'.$this->erp->formatMoney($TotalComUser1[$com->id]).'</td>';
									} 
									
									foreach($developphotos as $dev_pho){
										echo '<td style="text-align:right;">'.$this->erp->formatMoney($TotalComUser2[$dev_pho->id]).'</td>';
									}
									
									foreach($photographers as $photo){
										echo '<td style="text-align:right;">'.$this->erp->formatMoney($TotalComUser4[$photo->id]).'</td>';
									}
									
									foreach($studio_decors as $studio_dec){
										echo '<td style="text-align:right;">'.$this->erp->formatMoney($TotalComUser3[$studio_dec->id]).'</td>';
									}
									foreach($salesmans as $salesman){
										echo '<td style="text-align:right;">'.$this->erp->formatMoney($TotalComSalemans[$salesman->id]).'</td>';
									}
									
									
								?>
							</tr>
							<tr style="font-weight:bold;">
								<td><b>Total Commission By Users</b></td>
								<?php
							
									foreach($photowriters as $photowriter){
										echo '<td style="text-align:right;">'. $this->erp->formatMoney($GrandTotalUser5[$photowriter->id]) .'<span style="color: red;"> ('. $this->erp->formatMoney($comUser5[$photowriter->id]) .')</span>'.'</td>';
									} 
									
									foreach($computers as $com){
										echo '<td style="text-align:right;">'. $this->erp->formatMoney($GrandTotalUser1[$com->id]) .'<span style="color: red;"> ('.$this->erp->formatMoney($comUser1[$com->id]).')</span>'.'</td>';
									} 
									
									foreach($developphotos as $dev_pho){
										echo '<td style="text-align:right;">'. $this->erp->formatMoney($GrandTotalUser2[$dev_pho->id]) .'<span style="color: red;"> ('.$this->erp->formatMoney($comUser2[$dev_pho->id]).')</span>'.'</td>';
									}
									
									foreach($photographers as $photo){
										echo '<td style="text-align:right;">'. $this->erp->formatMoney($GrandTotalUser4[$photo->id]) .'<span style="color: red;"> ('.$this->erp->formatMoney($comUser4[$photo->id]).')</span>'.'</td>';
									}
									
									foreach($studio_decors as $studio_dec){
										echo '<td style="text-align:right;">'. $this->erp->formatMoney($GrandTotalUser3[$studio_dec->id]) .'<span style="color: red;"> ('.$this->erp->formatMoney($comUser3[$studio_dec->id]).')</span>'.'</td>';
									}
									foreach($salesmans as $salesman){
										echo '<td style="text-align:right;">'. $this->erp->formatMoney($GrandTotalSal[$salesman->id]) .'<span style="color: red;"> ('.$this->erp->formatMoney($comSaleman[$salesman->id]).')</span>'.'</td>';
									}
								?>
							</tr>
							
						</tfoot>
                    </table>
                </div>
				<div class=" text-right">
					<div class="dataTables_paginate paging_bootstrap">
						<?= $pagination; ?>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#form').hide();
    $('.toggle_down').click(function () {
        $("#form").slideDown();
        return false;
    });
    $('.toggle_up').click(function () {
        $("#form").slideUp();
        return false;
    });
	
	$('.pop_detail').click(function(){
		var product_id = $(this).attr('id');
		var group_user =$(this).attr('group_user');
		var user_id = $(this).attr('user_id');
		var from_date = '<?= $from_date ?>';
		var to_date = '<?= $to_date ?>';
		$('#myModal').modal({remote: site.base_url + 'jobs/modal_view/'+product_id+'/'+group_user+'/'+user_id+'/'+from_date+'/'+to_date+''});
        $('#myModal').modal('show');
	});
	
</script>

