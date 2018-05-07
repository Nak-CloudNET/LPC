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
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('jobs/get_machine_activities').'/?v=1'.$v; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox
            }, {"mRender": fld}, null, null, null, null, null, {"mRender": row_status}, {"bSortable": false}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "expense_link";
                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total = 0;
                for (var i = 0; i < aaData.length; i++) {
                    //total += parseFloat(aaData[aiDisplay[i]][4]);
                }
                //var nCells = nRow.getElementsByTagName('th');
                //nCells[4].innerHTML = formatPurDecimal(total);
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('reference');?>]", filter_type: "text", data: []},
			{column_number: 3, filter_default_label: "[<?=lang('customer_name');?>]", filter_type: "text", data: []},
			{column_number: 4, filter_default_label: "[<?=lang('product_name');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('quantity');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('developed_quantity');?>]", filter_type: "text", data: []},
			{column_number: 7, filter_default_label: "[<?=lang('status');?>]", filter_type: "text", data: []},
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
   
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa fa-barcode"></i><?= lang('marchine_activities'); ?></h2>
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
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" id="pdf" data-action="export_pdf" class="tip" title="<?= lang('download_pdf') ?>">
                        <i class="icon fa fa-file-pdf-o"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" id="image" class="tip" title="<?= lang('save_image') ?>">
                        <i class="icon fa fa-file-picture-o"></i>
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
                    <?php echo form_open("jobs/marchine_activities"); ?>
                    <div class="row">
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
                    </div>
                    <div class="form-group">
                        <div class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
                <div class="table-responsive">
                    <table id="EXPData__" cellpadding="0" cellspacing="0" border="0"
                           class="table table-condensed table-bordered table-hover table-striped">
                        <thead>
							<tr class="active">
								<th style="min-width:30px; width: 30px; text-align: center;">
									<input class="checkbox checkft" type="checkbox" name="check"/>
								</th>
								<th class="sorting"><?php echo $this->lang->line("ទំហំ"); ?></th>
								<th class="sorting"><?php echo $this->lang->line("ចំនួនផ្តិត"); ?></th>
								<th class="sorting"><?php echo $this->lang->line("ចំនួនខូច"); ?></th>
								<th class="sorting"><?php echo $this->lang->line("ចំនួនគំរូរ"); ?></th>
								<th class="sorting"><?php echo $this->lang->line("ចំនួនផ្តិតសរុប"); ?></th>
								<th class="sorting"><?php echo $this->lang->line("ទំហំក្រដាស់(ម)"); ?></th>
							</tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($this->input->POST('start_date')) {
                                $start_date = $this->input->POST('start_date');
                            } else {
                                $start_date = NULL;
                            }
                            if ($this->input->POST('end_date')) {
                                $end_date = $this->input->POST('end_date');
                            } else {
                                $end_date = NULL;
                            }
                            if ($start_date) {
                                $start_date = $this->erp->fld($start_date);
                                $end_date = $this->erp->fld($end_date);
                            }
                            $wheres = "";
                            if ($start_date && $start_date != "0000-00-00 00:00:00") {
                                $wheres = " and s.created_at > '$start_date' ";
                            }
                            if ($end_date && $end_date != "0000-00-00 00:00:00") {
                                $wheres = ($wheres != "" ? $wheres . " and s.created_at < '$end_date' " : $wheres);
                            }

                            $sdv = $this->db;
                                $sdv->select("
												sum(s.quantity) as quantity, 
												sum(s.quantity_index) as quantity_index, 
												sum(s.quantity_break) as quantity_break, 
												products.cf1,
												products.cf2
											")->from('sale_dev_items s');
                                $sdv->join("products","products.id = s.product_id","inner");
								$sdv->group_by("cf1"); 
								$sdv->group_by("cf2"); 
								if($wheres != "") {
                                    $sdv->where( '1=1' . $wheres);
                                }
                                $sdv = $sdv->get()->result();
                            $i      = 01;
                            $tq     = 0;
                            $tb     = 0;
                            $tin    = 0;
                            $tqua     = 0;
                            foreach ($sdv as $rows) {
								$totalqty = $rows->quantity + $rows->quantity_index + $rows->quantity_break;
                               ?>
                                <tr class="active">
                                    <th style="min-width:30px; width: 30px; text-align: center;">
                                        <?=$i++?>
                                    </th>
                                    <th><?=$rows->cf1 . 'x' . $rows->cf2?></th>
                                    <th><?= number_format($rows->quantity);?></th>
                                    <th><?= number_format($rows->quantity_break);?></th>
                                    <th><?= number_format($rows->quantity_index); ?></th>
                                    <th><?= number_format($totalqty); ?></th>
                                    <th><?= ((($rows->cf2*$totalqty))); ?></th>
                                </tr>
                               <?php
                               $tq += $rows->quantity;
                               $tb += $rows->quantity_break;
                               $tin += $rows->quantity_index;
                               $tqua += $totalqty;
                            }
                            ?>
                            <tr>
                                <td></td>
                                <th class="right"><?php echo $this->lang->line("សរុប"); ?></th>
                                <th><?=$tq?></th>
                                <th><?=$tb?></th>
                                <th><?=$tin?></th>
                                <th><?=$tqua?></th>
                                <th></th>
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
							</tr>
                        </tfoot>
                    </table>
                </div>

                <div class="table-responsive">
                    <table id="EXPData__1" cellpadding="0" cellspacing="0" border="0"
                           class="table table-condensed table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <td><?=$this->lang->line("ឈ្មោះម៉ាស៊ីន"); ?></td>
                                <td><?=$this->lang->line("លេខថ្មី"); ?></td>
                                <td><?=$this->lang->line("លេខចាស់"); ?></td>
                                <td><?=$this->lang->line("ចំនួនផ្តិត"); ?></td>
								<td><?=$this->lang->line("ចំនួនខូច"); ?></td>
								<td><?=$this->lang->line("ចំនួនគំរូរ"); ?></td>
                                <td><?=$this->lang->line("13(ម)"); ?></td>
                                <td><?=$this->lang->line("15(ម)"); ?></td>
                                <td><?=$this->lang->line("25(ម)"); ?></td>
                                <td><?=$this->lang->line("30(ម)"); ?></td>
                                <td><?=$this->lang->line("50(ម)"); ?></td>
                                <td><?=$this->lang->line("60(ម)"); ?></td>
                                <td><?=$this->lang->line("76(ម)"); ?></td>
                                <td><?=$this->lang->line("80(ម)"); ?></td>
                                <td><?=$this->lang->line("100(ម)"); ?></td>
                                <td><?=$this->lang->line("120(ម)"); ?></td>
                                <td><?=$this->lang->line("150(ម)"); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
							$tmp_machine_log = "(SELECT
													erp_marchine_logs.*
												FROM
													erp_marchine_logs 
												INNER JOIN (
													SELECT
														max(date) AS date,
														marchine_id
													FROM
														erp_marchine_logs
													GROUP BY
														marchine_id
												) AS a ON a.date = erp_marchine_logs.date and a.marchine_id = erp_marchine_logs.marchine_id) as ml";
		
							$demand = "sum(
													CASE
													WHEN p.cf1 = 13 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_13,
												sum(
													CASE
													WHEN p.cf1 = 15 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_15,
												sum(
													CASE
													WHEN p.cf1 = 25 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_25,
												sum(
													CASE
													WHEN p.cf1 = 30 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_30,
												sum(
													CASE
													WHEN p.cf1 = 50 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_50,
												sum(
													CASE
													WHEN p.cf1 = 60 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_60,
												sum(
													CASE
													WHEN p.cf1 = 76 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_76,
												sum(
													CASE
													WHEN p.cf1 = 80 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_80,
												sum(
													CASE
													WHEN p.cf1 = 100 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_100,
												sum(
													CASE
													WHEN p.cf1 = 120 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_120,
												sum(
													CASE
													WHEN p.cf1 = 150 THEN
														(
															 d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS d_150,";
							$blur = "sum(
									CASE
									WHEN p.cf1 = 13 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_13,
								sum(
									CASE
									WHEN p.cf1 = 15 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_15,
								sum(
									CASE
									WHEN p.cf1 = 25 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_25,
								sum(
									CASE
									WHEN p.cf1 = 30 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_30,
								sum(
									CASE
									WHEN p.cf1 = 50 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_50,
								sum(
									CASE
									WHEN p.cf1 = 60 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_60,
								sum(
									CASE
									WHEN p.cf1 = 76 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_76,
								sum(
									CASE
									WHEN p.cf1 = 80 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_80,
								sum(
									CASE
									WHEN p.cf1 = 100 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_100,
								sum(
									CASE
									WHEN p.cf1 = 120 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_120,
								sum(
									CASE
									WHEN p.cf1 = 150 THEN
										(
											 d.quantity_index
										) * p.cf2
									ELSE
										0
									END
								) AS i_150,";
								
							$this->db->select("
												m.`name`,
												m.`description`,
												m.`type`,
												ml.`old_number`,
												ml.`new_number`,
												sum(
													CASE
													WHEN p.cf1 = 13 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_13,
												sum(
													CASE
													WHEN p.cf1 = 15 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_15,
												sum(
													CASE
													WHEN p.cf1 = 25 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_25,
												sum(
													CASE
													WHEN p.cf1 = 30 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_30,
												sum(
													CASE
													WHEN p.cf1 = 50 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_50,
												sum(
													CASE
													WHEN p.cf1 = 60 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_60,
												sum(
													CASE
													WHEN p.cf1 = 76 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_76,
												sum(
													CASE
													WHEN p.cf1 = 80 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_80,
												sum(
													CASE
													WHEN p.cf1 = 100 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_100,
												sum(
													CASE
													WHEN p.cf1 = 120 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_120,
												sum(
													CASE
													WHEN p.cf1 = 150 THEN
														(
															d.quantity + d.quantity_index + d.quantity_break
														) * p.cf2
													ELSE
														0
													END
												) AS m_150, {$demand} {$blur}
												m.`13` as b_13,
												m.`15` as b_15,
												m.`25` as b_25,
												m.`30` as b_30,
												m.`50` as b_50,
												m.`60` as b_60,
												m.`76` as b_76,
												m.`80` as b_80,
												m.`100` as b_100,
												m.`120` as b_120,
												m.`150` as b_150,
												sum(d.quantity) as g_qty,
												sum(d.quantity_index) as i_qty,
												sum(d.quantity_break) as b_qty
											");
								$this->db->from('marchine m');
								$this->db->join("sale_dev_items d","d.machine_name = m.id","left");
								$this->db->join("products p","p.id = d.product_id","left");
								$this->db->join($tmp_machine_log,"ml.marchine_id = m.id","left");
								$this->db->group_by("m.id");
								$marchine = $this->db->get()->result();						
								foreach ($marchine as $rows) {
								   ?>
									<tr class="active">
										<th><?=$rows->name?></th>
										<th><?=$rows->new_number?></th>
										<th><?=$rows->old_number?></th>
										<th><?= round($rows->g_qty,0) ?></th>
										<th><?= round($rows->b_qty,0) ?></th>
										<th><?= round($rows->i_qty,0) ?></th>
										<th><?="[" . $rows->b_13 . "] [" .($rows->m_13)/100 . "] [" . ($rows->b_13 - (($rows->m_13)/100)) . "]" ?></th>
										<th><?="[" . $rows->b_15 . "] [" . ($rows->m_15)/100 . "] [" . ($rows->b_15 - (($rows->m_15)/100)) . "]" ?></th>
										<th><?="[" . $rows->b_25 . "] [" . ($rows->m_25)/100 . "] [" . ($rows->b_25 - (($rows->m_25)/100)) . "]" ?></th>
										<th><?="[" . $rows->b_30 . "] [" . ($rows->m_30)/100 . "] [" . ($rows->b_30 - (($rows->m_30)/100)) . "]" ?></th>
										<th><?="[" . $rows->b_50 . "] [" . ($rows->m_50)/100 . "] [" . ($rows->b_50 - (($rows->m_50)/100)) . "]" ?></th>
										<th><?="[" . $rows->b_60 . "] [" . ($rows->m_60)/100 . "] [" . ($rows->b_60 - (($rows->m_60)/100)) . "]" ?></th>
										<th><?="[" . $rows->b_76 . "] [" . ($rows->m_76)/100 . "] [" . ($rows->b_76 - (($rows->m_76)/100)) . "]" ?></th>
										<th><?="[" . $rows->b_80 . "] [" . ($rows->m_80)/100 . "] [" . ($rows->b_80 - (($rows->m_80)/100)) . "]" ?></th>
										<th><?="[" . $rows->b_100 . "] [" . ($rows->m_100)/100 . "] [" . ($rows->b_100 - (($rows->m_100)/100)) . "]" ?></th>
										<th><?="[" . $rows->b_120 . "] [" . ($rows->m_120)/100 . "] [" . ($rows->b_120 - (($rows->m_120)/100)) . "]" ?></th>
										<th><?="[" . $rows->b_150 . "] [" . ($rows->m_150)/100 . "] [" . ($rows->b_150 - (($rows->m_150)/100)) . "]" ?></th>
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
	#EXPData__1 { white-space: nowrap; width:100%; display:block; overflow: scroll; }
</style>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('jobs/marchine_activities/pdf')?>";
            return false;
        });
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    var img = canvas.toDataURL()
                    window.open(img);
                }
            });
            return false;
        });
    });
</script>


