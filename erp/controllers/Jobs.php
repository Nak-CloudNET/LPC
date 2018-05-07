<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Jobs extends MY_Controller
{
	
    function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            redirect('login');
        }
		$this->lang->load('settings', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->model('settings_model');		
        $this->lang->load('job', $this->Settings->language);
        $this->load->library('form_validation');
        $this->load->model('jobs_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->popup_attributes = array('width' => '900', 'height' => '600', 'window_name' => 'erp_popup', 'menubar' => 'yes', 'scrollbars' => 'yes', 'status' => 'no', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
		
		if(!$this->Owner && !$this->Admin){
            $gp = $this->site->checkPermissions();
            $this->permission = $gp[0];
            $this->permission[] = $gp[0];
        } else {
            $this->permission[] = NULL;
        }
    }

	//////////////////////////////
	
    public function index()
    {
        $this->erp->checkPermissions('index', true, 'jobs');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['result'] = $this->jobs_model->getJobs();
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('jobs')));
        $meta = array('page_title' => lang('jobs'), 'bc' => $bc);
        $this->page_construct('jobs/jobs', $meta, $this->data);
    }
	
	
	function modal_view($product_id,$group_user,$user_id,$from_date,$to_date)
    {
        $this->erp->checkPermissions('index', TRUE);
		$this->data['date'] = $this->jobs_model->getDevByUser($product_id,$group_user,$user_id,$from_date,$to_date);
        $this->load->view($this->theme.'jobs/modal_view', $this->data);
    }
	
	function job_list($warehouse_id = NULL){
		$this->erp->checkPermissions('index');
		$this->data['table'] = $warehouse_id;
		$this->data['warehouse_id'] = $warehouse_id;
		
		$this->db->select(
					$this->db->dbprefix('sale_items').".id as id ,
					".$this->db->dbprefix('sales').".date, 
					reference_no, 
					customer ,
					".$this->db->dbprefix('sale_items').".product_name,
					".$this->db->dbprefix('sale_items').".quantity as fquantity, 
						IFNULL(
							sum(
								" .$this->db->dbprefix('sale_dev_items') . ".quantity
							),
							0
						) AS dev_quantity , 
						(
							CASE
							WHEN IFNULL(
								sum(
									" .$this->db->dbprefix('sale_dev_items') . ".quantity
								),
								0
							) = 0 THEN
								'pending'
							ELSE
								CASE
							WHEN sum(
								" .$this->db->dbprefix('sale_dev_items') . ".quantity
							) < " .$this->db->dbprefix('sale_items') . ".quantity THEN
								'processing'
							ELSE
								'completed'
							END
							END
						) AS status,
						(CASE WHEN 
							".$this->db->dbprefix('sale_items').".received = 1
							THEN
								'received'
							ELSE
								'pending'
							END
						) as receive
						")
            ->from('sales')
            ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
			->join('sale_dev_items', 'sale_dev_items.product_id=sale_items.product_id AND sale_dev_items.sale_id = sales.id', 'left')
			->join('products', 'products.id = sale_items.product_id', 'left')
			->join('categories', 'categories.id = products.category_id', 'left')
			->where('categories.jobs',1);
			if($warehouse_id)
			{
				$this->db->where('sales.warehouse_id',$warehouse_id);
			}
			$this->db->group_by('sale_items.sale_id')
			->group_by('sale_items.product_id');
			$result = $this->db->get()->result();
			$this->data['result'] = $result;
			//var_dump($this->data['result']);exit;
		$this->load->view('default/views/jobs/view_list', $this->data);
	}
	
	
	public function view_jobs($id = NULL)
	{
		$this->data['modal_js'] = $this->site->modal_js();
		$this->data['develop_id'] = $id;
		$this->data['computer'] = $this->jobs_model->getComputerUser();
		$this->data['marchine'] = $this->jobs_model->getAllMarchine();
		$this->data['develop'] = $this->jobs_model->getDevelopedProduct($id);
		$this->data['page_title'] = $this->lang->line("product_develop");
		$this->load->view($this->theme . 'jobs/view_jobs', $this->data);
	}

	public function getJobs()
	{
		$this->erp->checkPermissions('index');
        $this->load->library('datatables');
		if($this->Admin || $this->Owner){
				$add_link = anchor('jobs/add_jobs/$1', '<i class="fa fa-file-text-o"></i> ' . lang('add_jobs'), 'data-toggle="modal" data-target="#myModal2"');
				$edit_link = anchor('jobs/view_jobs/$1', '<i class="fa fa-edit"></i> ' . lang('edit_jobs'), 'data-toggle="modal" data-target="#myModal"');
				$delete_link = anchor('jobs/view_jobs/$1', '<i class="fa fa-edit"></i> ' . lang('delete_jobs'), 'data-toggle="modal" data-target="#myModal"');
		}else{
				if($this->GP['jobs-add']){
					$add_link = anchor('jobs/add_jobs/$1', '<i class="fa fa-file-text-o"></i> ' . lang('add_jobs'), 'data-toggle="modal" data-target="#myModal2"');
				}
				if($this->GP['jobs-edit']){
					$edit_link = anchor('jobs/view_jobs/$1', '<i class="fa fa-edit"></i> ' . lang('edit_jobs'), 'data-toggle="modal" data-target="#myModal"');
				}
				if($this->GP['jobs-delete']){
					$delete_link = anchor('jobs/view_jobs/$1', '<i class="fa fa-edit"></i> ' . lang('delete_jobs'), 'data-toggle="modal" data-target="#myModal"');
				}
		}
			$action_link = '<div class="text-center"><div class="btn-group text-left">'
								. '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle btn-action" data-toggle="dropdown">'
								. lang('actions') . ' <span class="caret"></span></button>
									<ul class="dropdown-menu pull-right" role="menu">
										<li>' . $add_link . '</li>
										<li>' . $edit_link . '</li>
										<li>' . $delete_link . '</li>
									</ul>
								</div></div>';

        $this->datatables
            ->select(
					$this->db->dbprefix('sale_items').".id as id ,
					".$this->db->dbprefix('sales').".date, 
					reference_no, 
					customer ,
					".$this->db->dbprefix('sale_items').".product_name,
					".$this->db->dbprefix('sale_items').".first_quantity as fquantity, 
						IFNULL(
							(
								" .$this->db->dbprefix('v_sale_dev_items') . ".quantity
							),
							0
						) AS dev_quantity , 
						(
							CASE
							WHEN IFNULL(
								(
									" .$this->db->dbprefix('v_sale_dev_items') . ".quantity
								),
								0
							) = 0 THEN
								'pending'
							ELSE
								CASE
							WHEN (
								" .$this->db->dbprefix('v_sale_dev_items') . ".quantity
							) < " .$this->db->dbprefix('sale_items') . ".first_quantity THEN
								'processing'
							ELSE
								'completed'
							END
							END
						) AS status,
						(CASE WHEN 
							".$this->db->dbprefix('sale_items').".received = 1
							THEN
								'received'
							ELSE
								'pending'
							END
						) as receive
						")
            ->from('sales')
            ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
			->join('v_sale_dev_items', 'v_sale_dev_items.product_id=sale_items.product_id AND v_sale_dev_items.sale_id = sales.id', 'left')
			->join('products', 'products.id = sale_items.product_id', 'left')
			->join('categories', 'categories.id = products.category_id', 'left')
			->where('categories.jobs',1)
			->group_by('sale_items.sale_id')
			->group_by('sale_items.product_id')
			->order_by('sale_items.id', 'DESC')
            ->add_column("Actions", $action_link, "id");
        echo $this->datatables->generate();
	}
	
	
	
	public function add_jobs($id = null)
	{
		$this->erp->checkPermissions('add', true, 'jobs');
		$this->form_validation->set_rules('developed_quantity', lang("developed_quantity"), 'required');
        $this->form_validation->set_rules('user_2', lang("user_2"), 'required');
		$this->form_validation->set_rules('user_1', lang("user_1"), 'required');
		$this->form_validation->set_rules('user_5', lang("user_5"), 'required');
		$this->form_validation->set_rules('machine', lang("machine"), 'required');
		if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->erp->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
			$product_id = $this->input->post('product_id');
			$products = $this->site->getProductAllByID($product_id);
			$category_id = $products->category_id;
			$total = $this->input->post('unit_price') * $this->input->post('developed_quantity');
            $data = array(
                'created_at' => $date,
				'created_by' => $this->session->userdata('user_id'),
                'sale_id' => $this->input->post('sale_id'),
                'product_id' => $product_id,
                'product_name' => $this->input->post('product_name'),
                'category_id' => $category_id,
                'warehouse_id' => $this->input->post('warehouse'),
				'machine_name' => $this->input->post('machine'),
				'quantity_break' => $this->input->post('quantity_break'),
				'quantity_index' => $this->input->post('quantity_index'),
				'user_1' => $this->input->post('user_1'),
				'user_2' => $this->input->post('user_2'),
				'user_3' => $this->input->post('user_3'),
				'user_4' => $this->input->post('user_4'),
				'user_5' => $this->input->post('user_5'),
				'unit_price' => $this->input->post('unit_price'),
				'quantity' => $this->input->post('developed_quantity'),
				'grand_total' => $total
            );
			
        } elseif ($this->input->post('add_products_develop')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('jobs');
        }
		 
		if ($this->form_validation->run() == true && $this->jobs_model->addDevelop($data)) {
            $this->session->set_flashdata('message', lang("jobs_added"));
            redirect('jobs');
        }else{
			$this->data['modal_js'] = $this->site->modal_js();
			$this->data['develop_id'] = $id;
			$this->data['computer'] = $this->jobs_model->getComputerUser();
			$this->data['marchine'] = $this->jobs_model->getAllMarchine();
			$this->data['develop'] = $this->jobs_model->getDevelopedProduct($id);
			$this->data['page_title'] = $this->lang->line("product_develop");
			$this->load->view($this->theme . 'jobs/add_jobs', $this->data);
		}
	}
	
	public function edit_jobs($id = null)
	{
		$this->erp->checkPermissions('edit', true, 'jobs');
		$this->form_validation->set_rules('developed_quantity', lang("developed_quantity"), 'required');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->erp->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }			
			$total = $this->input->post('unit_price') * $this->input->post('developed_quantity');
			$sale_id = $this->input->post('sale_id');
			$develop_id = $this->input->post('develop_id');
			
            $data = array(
                'created_at' => $date,
                'updated_by' => $this->session->userdata('user_id'),
                'updated_at' => $date,
                'sale_id' => $sale_id,
                'product_id' => $this->input->post('product_id'),
                'product_name' => $this->input->post('product_name'),
                'warehouse_id' => $this->input->post('warehouse'),
				'machine_name' => $this->input->post('machine'),
				'quantity_break' => $this->input->post('quantity_break'),
				'quantity_index' => $this->input->post('quantity_index'),
				'user_1' => $this->input->post('user_1'),
				'user_2' => $this->input->post('user_2'),
				'user_3' => $this->input->post('user_3'),
				'user_4' => $this->input->post('user_4'),
				'user_5' => $this->input->post('user_5'),
				'unit_price'	=> $this->input->post('unit_price'),
				'quantity' => $this->input->post('developed_quantity'),
				'grand_total' => $total
            );
			
        } elseif ($this->input->post('edit_products_develop')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('products/product_develop');
        }
		if ($this->form_validation->run() == true && $this->jobs_model->updateDevelopItem($develop_id,$data)) {
            $this->session->set_flashdata('message', lang("jobs_updated"));
            redirect('jobs');
        }else{
			
			$this->data['develop_id'] = $id;
			$this->data['modal_js'] = $this->site->modal_js();
			$this->data['computer'] = $this->jobs_model->getComputerUser();
			$this->data['dev_item'] = $this->jobs_model->getDevItems($id);
			$this->data['develop'] = $this->jobs_model->getDevelopedProductByDev($id);
			$this->data['marchine'] = $this->jobs_model->getAllMarchine();
			$this->data['page_title'] = $this->lang->line("product_develop");
			$this->load->view($this->theme . 'jobs/edit_jobs', $this->data);
		}
	}
	
	public function delete_jobs($id = null)
    {
		$this->erp->checkPermissions('delete', true, 'jobs');
		$dev_items = $this->jobs_model->getSaleDevItemById($id);
		$data = array(
			'sale_id' => $dev_items->sale_id,
			'product_id' => $dev_items->product_id
		);
        if ($this->jobs_model->deleteDevelopDev($id, $data)) {
            echo lang("products_develop_deleted");
        }
    }
	
	public function jobs_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {


            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    foreach ($_POST['val'] as $id) {
                        $this->jobs_model->deleteDevelop($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("purchases_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'combine') {

                    $html = $this->combine_pdf($_POST['val']);

                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('jobs'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('customer_name'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('product_name'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('quantity'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('developed_quantity'));
					$this->excel->getActiveSheet()->SetCellValue('G1', lang('status'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $jobs = $this->jobs_model->getJobsByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->erp->hrld($jobs->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $jobs->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $jobs->customer);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $jobs->product_name);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $jobs->fquantity);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $jobs->dev_quantity);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $jobs->status);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'jobs_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php";
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }elseif ($this->input->post('form_action') == 'receive_job') {
					foreach ($_POST['val'] as $id) {
                        $this->jobs_model->receiveJob($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("job_received"));
                    redirect($_SERVER["HTTP_REFERER"]);
				}else if ($this->input->post('form_action') == 'cancel_receive_job') {
					foreach ($_POST['val'] as $id) {
							$this->jobs_model->cancelJob($id);
						}
					$this->session->set_flashdata('message', $this->lang->line("cancel_receive_job"));
					redirect($_SERVER["HTTP_REFERER"]);
				}
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_purchase_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	public function jobs_by_csv()
	{
		$this->erp->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/import_csv");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('code', 'name', 'category_code', 'unit', 'cost', 'price', 'alert_quantity', 'tax_rate', 'tax_method', 'subcategory_code', 'variants', 'cf1', 'cf2', 'cf3', 'cf4', 'cf5', 'cf6');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                //$this->erp->print_arrays($final);
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if ($this->products_model->getProductByCode(trim($csv_pr['code']))) {
                        $this->session->set_flashdata('error', lang("check_product_code") . " (" . $csv_pr['code'] . "). " . lang("code_already_exist") . " " . lang("line_no") . " " . $rw);
                        redirect("products/import_csv");
                    }
                    if ($catd = $this->products_model->getCategoryByCode(trim($csv_pr['category_code']))) {
                        $pr_code[] = trim($csv_pr['code']);
                        $pr_name[] = trim($csv_pr['name']);
                        $pr_cat[] = $catd->id;
                        $pr_variants[] = trim($csv_pr['variants']);
                        $pr_unit[] = trim($csv_pr['unit']);
                        $tax_method[] = $csv_pr['tax_method'] == 'exclusive' ? 1 : 0;
                        $prsubcat = $this->products_model->getSubcategoryByCode(trim($csv_pr['subcategory_code']));
                        $pr_subcat[] = $prsubcat ? $prsubcat->id : NULL;
                        $pr_cost[] = trim($csv_pr['cost']);
                        $pr_price[] = trim($csv_pr['price']);
                        $pr_aq[] = trim($csv_pr['alert_quantity']);
                        $tax_details = $this->products_model->getTaxRateByName(trim($csv_pr['tax_rate']));
                        $pr_tax[] = $tax_details ? $tax_details->id : NULL;
                        $cf1[] = trim($csv_pr['cf1']);
                        $cf2[] = trim($csv_pr['cf2']);
                        $cf3[] = trim($csv_pr['cf3']);
                        $cf4[] = trim($csv_pr['cf4']);
                        $cf5[] = trim($csv_pr['cf5']);
                        $cf6[] = trim($csv_pr['cf6']);
                    } else {
                        $this->session->set_flashdata('error', lang("check_category_code") . " (" . $csv_pr['category_code'] . "). " . lang("category_code_x_exist") . " " . lang("line_no") . " " . $rw);
                        redirect("products/import_csv");
                    }

                    $rw++;
                }
            }

            $ikeys = array('code', 'name', 'category_id', 'unit', 'cost', 'price', 'alert_quantity', 'tax_rate', 'tax_method', 'subcategory_id', 'variants', 'cf1', 'cf2', 'cf3', 'cf4', 'cf5', 'cf6');

            $items = array();
            foreach (array_map(null, $pr_code, $pr_name, $pr_cat, $pr_unit, $pr_cost, $pr_price, $pr_aq, $pr_tax, $tax_method, $pr_subcat, $pr_variants, $cf1, $cf2, $cf3, $cf4, $cf5, $cf6) as $ikey => $value) {
                $items[] = array_combine($ikeys, $value);
            }
        }

        if ($this->form_validation->run() == true && $this->products_model->add_products($items)) {
            $this->session->set_flashdata('message', lang("products_added"));
            redirect('products');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('jobs'), 'page' => lang('jobs')), array('link' => '#', 'page' => lang('import_jobs_by_csv')));
            $meta = array('page_title' => lang('import_jobs_by_csv'), 'bc' => $bc);
            $this->page_construct('jobs/import_csv', $meta, $this->data);

        }
	}
	
	////////////////////////////////////
	
	public function job_activities($start_date = NULL, $end_date = NULL)
	{
		
		$this->erp->checkPermissions('index', true, 'jobs');
		$this->load->model('reports_model');
		$this->load->model('companies_model');
		
		if ($start_date) {
            $start = $this->db->escape(urldecode($start_date));
        }else{
			$start = $this->db->escape(urldecode(date('Y-m-d h:i:ss')));
		}
        if ($end_date) {
            $end = $this->db->escape(urldecode($end_date));
        }else{
			$end = $this->db->escape(urldecode(date('Y-m-d h:i:ss')));
		}
		$str_sql = "";
		if($_GET['product_id']){
			$str_sql .="AND sale_dev_items.product_id ='".$_GET['product_id']."'";
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
			$str_sql .="AND sale_dev_items.created_by ='".$_GET['user']."'";
		}
		if($_GET['biller']){
			$str_sql .="AND sales.biller_id ='".$_GET['biller']."'";
		}
		if($_GET['warehouse'] && $_GET['warehouse'] !='0'){
			$str_sql .="AND sale_dev_items.warehouse_id ='".$_GET['warehouse']."'";
		}
		
		/* $start_date = $this->input->get("start_date");
		$end_date = $this->input->get("end_date"); */
		
		if($start_date && $end_date){
			$str_sql .= "AND date(sale_dev_items.created_at) BETWEEN '{$this->erp->fld($start_date)}' AND '{$this->erp->fld($end_date)}'";
		}
		$resultss = $this->db->query("SELECT
											sale_dev_items.*, 
											sales.reference_no,
											sales.date,
											sales.customer_id,
											sales.saleman_by
										FROM
											erp_sale_dev_items AS sale_dev_items
										LEFT JOIN erp_sales as sales ON sales.id = sale_dev_items.sale_id
										WHERE 1=1 {$str_sql}
										GROUP BY sale_id")->result();
		$this->data['resultss'] = $resultss;
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$this->data['start'] = urldecode($start_date);
        $this->data['end'] = urldecode($end_date);
		$this->data['users'] = $this->reports_model->getStaff();
		$this->data['agencies'] = $this->site->getAllUsers();
		$this->data['products'] = $this->site->getProducts();
		
		$this->data['customer'] = $this->companies_model->getCustomerSuggestionsForDropdown(15);
		
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
		
		 if ($this->Owner || $this->Admin) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');

            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
        }
		
		$this->data['results'] = $this->jobs_model->getJobActivities(urldecode($start_date),urldecode($end_date));
		//$this->erp->print_arrays($this->data['results']);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('job_activities')));
        $meta = array('page_title' => lang('job_activities'), 'bc' => $bc);
        $this->page_construct('jobs/job_activities', $meta, $this->data);
	}
	function commission()
    {
        $this->load->library("pagination");
		$config = array();
		//$config['suffix'] = "?v=1".$suffix_uri;
		$uri = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $config["base_url"] = site_url("reports/product_dailyinout/") ;
		$config["total_rows"] = $row_nums;
		$config["ob_set"] = $uri;
        $config["per_page"] =20; 
		$config["uri_segment"] = 3;
		$config['full_tag_open'] = '<ul class="pagination pagination-sm">';
		$config['full_tag_close'] = '</ul>';
		$config['next_tag_open'] = '<li class="next">';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a>';
		$config['cur_tag_close'] = '</a></li>';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';		
		$this->pagination->initialize($config);
		$this->data["pagination"] = $this->pagination->create_links(); 
		
		if ($this->input->post('category')) {
            $category_id = $this->input->post('category');
        }else{
            $category_id = null;
        }
        if ($this->input->post('product')) {
            $product_id = $this->input->post('product');
        }else{
            $product_id = null;
        }
		if ($this->input->post('user')) {
            $user_id = $this->input->post('user');
        }else{
            $user_id = null;
        }
		if ($this->input->post('group')) {
            $group_id = $this->input->post('group');
        }else{
            $group_id = null;
        }
		
        if ($this->input->post('from_date')) {
            $from_date =  $this->erp->fld($this->input->post('from_date'));
        }else{
            $from_date = null;
        }
        if ($this->input->post('to_date')) {
            $to_date = $this->erp->fld($this->input->post('to_date'));
        }else{
            $to_date=null;
        }
		
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		
		$this->data['from_date']    = trim($from_date);
		$this->data['to_date'] 	  	= trim($to_date);
		$this->data['categories']   = $this->site->getAllCategories();
		$this->data['products']     = $this->site->getAllProducts();
		$this->data['users']     	= $this->jobs_model->getAllUsers();
		$this->data['warehouses']   = $this->site->getAllWarehouseProducts();
		$this->data['commissions']   = $this->jobs_model->getAllCommission();
		$this->data['groups']		= $this->jobs_model->getGroup();
		if($product_id == null){
             $this->data['product_id1'] = '';
        }else{
            $this->data['product_id1'] = $product_id;
        }
        if($from_date == null){
             $this->data['from_date1'] = '';
        }else{
            $this->data['from_date1'] = trim($from_date);
        }
        if($to_date == null){
             $this->data['to_date1'] = '';
        }else{
            $this->data['to_date1'] = trim($to_date);
        }
        if($category_id == null){
             $this->data['cate_id1'] = '';
        }else{			
            $this->data['cate_id1'] = $category_id;
        }
		if($user_id == null){
             $this->data['user_id'] = '';
        }else{			
            $this->data['user_id'] = $user_id;
        }
		if($group_id == null){
             $this->data['group_id'] = '';
        }else{			
            $this->data['group_id'] = $group_id;
        }
		
		
		
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('jobs')));
        $meta = array('page_title' => lang('commission'), 'bc' => $bc);
        $this->page_construct('jobs/commission', $meta, $this->data);
    }
	
	public function edit_jobActivity($id = null)
	{
		$this->erp->checkPermissions('edit', true, 'jobs');
		$this->form_validation->set_rules('developed_quantity', lang("developed_quantity"), 'required');
		$ids = $id;
        if ($this->form_validation->run() == true) {
			$ids = $this->input->post('sale_id');
            if ($this->Owner || $this->Admin) {
                $date = $this->erp->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = array(
				'user_1' => $this->input->post('user_1'),
				'user_2' => $this->input->post('user_2'),
				'user_3' => $this->input->post('user_3'),
				'user_4' => $this->input->post('user_4'),
				'user_5' => $this->input->post('user_5'),
				'quantity_break' => $this->input->post('quantity_break'),
				'quantity_index' => $this->input->post('quantity_index'),
				'quantity' => $this->input->post('developed_quantity')
            );
			
        } elseif ($this->input->post('edit_products_develop')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('jobs/job_activities');
        }
		
		if ($this->form_validation->run() == true && $this->jobs_model->updateDevelopItem($ids,$data)) {
            $this->session->set_flashdata('message', lang("job_activities_updated"));
            redirect('jobs/job_activities');
        }else{
			$this->data['modal_js'] = $this->site->modal_js();
			$this->data['computer'] = $this->jobs_model->getComputerUser();
			$this->data['dev_item'] = $this->jobs_model->getDevItems($id);
			$this->data['page_title'] = $this->lang->line("product_develop");
			$this->load->view($this->theme . 'jobs/edit_jobActivity', $this->data);
		}
	}
	
	public function activities_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    foreach ($_POST['val'] as $id) {
                        $this->jobs_model->deleteDevelop($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("purchases_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'combine') {

                    $html = $this->combine_pdf($_POST['val']);

                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('jobs_activities'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('quantity_break'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('quantity_index'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_quantity'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $jobs = $this->jobs_model->getActivitiesByProduct($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $jobs->product_name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $jobs->pquantity);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $jobs->qty_break);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $jobs->qty_index);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $jobs->tquantity);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'jobs_activities_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php";
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_purchase_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	/////////////////////////////////////
	
	public function job_employees($start_date = NULL, $end_date = NULL)
	{
		$this->erp->checkPermissions('index', true, 'jobs');
		if (!$start_date) {
            
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
           
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$this->data['start'] = urldecode($start_date);
        $this->data['end'] = urldecode($end_date);
		$this->data['result'] = $this->jobs_model->getJobEmployees(urldecode($start_date),urldecode($end_date));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('job_employees')));
        $meta = array('page_title' => lang('job_employees'), 'bc' => $bc);
        $this->page_construct('jobs/job_employees', $meta, $this->data);
	}
	
	public function getJobEmployees($start = NULL, $end = NULL)
	{        
	    $edit_link = '';
	    if($this->Admin || $this->Owner){		   
			$add_link = anchor('jobs/add_jobs/$1', '<i class="fa fa-file-text-o"></i> ' . lang('add_jobs'), 'data-toggle="modal" data-target="#myModal2"');
			$edit_link = anchor('jobs/edit_jobs/$1', '<i class="fa fa-edit"></i> ' . lang('edit_jobs'), 'data-toggle="modal" data-target="#myModal"');			
			$delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_jobs") . "</b>' data-content=\"<p>"
			. lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('jobs/delete_jobs/$1') . "'>"
			. lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
			. lang('delete_jobs') . "</a>";
	    }else{
		   
		   if($this->GP['jobs-add']){
			   $add_link = anchor('jobs/add_jobs/$1', '<i class="fa fa-file-text-o"></i> ' . lang('add_jobs'), 'data-toggle="modal" data-target="#myModal2"');
		   }
		   
		   if($this->GP['jobs-edit']){
			   $edit_link = anchor('jobs/edit_jobs/$1', '<i class="fa fa-edit"></i> ' . lang('edit_jobs'), 'data-toggle="modal" data-target="#myModal"');
		   }
		   
		   if($this->GP['jobs-delete']){
			   $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_jobs") . "</b>' data-content=\"<p>"
			. lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('jobs/delete_jobs/$1') . "'>"
			. lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
			. lang('delete_jobs') . "</a>";
		   }		   
	    }
		$detail_link = anchor('jobs/get_jobemployee/$1', '<i class="fa fa-file-text-o"></i> ' . lang('view_jobs'), 'data-toggle="modal" data-target="#myModal2"');        
        $action = '<div class="text-center"><div class="btn-group text-left">'
					. '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
					. lang('actions') . ' <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" role="menu">
						<li>' . $detail_link . '</li>
						<li>' . $add_link . '</li>
						<li>' . $edit_link . '</li>
						<li>' . $delete_link . '</li>
					</ul></div></div>';
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('users').".id as id, 
				username, 
				(select sum(quantity) from erp_sale_dev_items where user_1=erp_users.id) AS user1,
				(select sum(quantity) from erp_sale_dev_items where user_2=erp_users.id) AS user2,
				(select sum(quantity) from erp_sale_dev_items where user_3=erp_users.id) AS user3,
				(select sum(quantity) from erp_sale_dev_items where user_4=erp_users.id) AS user4,
				(select sum(quantity) from erp_sale_dev_items where user_5=erp_users.id) AS user5,
				(sum(grand_total) / (SELECT each_sale FROM erp_settings)) as sum")
            ->from('users')
			->join('sale_dev_items','users.id = sale_dev_items.user_1 or
									users.id = sale_dev_items.user_2 or
									users.id = sale_dev_items.user_3 or
									users.id = sale_dev_items.user_4 or
									users.id = sale_dev_items.user_5', 'left')->group_by('users.id');
        if (!$this->Owner && !$this->Admin) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }
		if($start && $end){
			$this->datatables->where($this->db->dbprefix('sale_dev_items').'.created_at BETWEEN "' . $start . '" and "' . $end . '"');
		}
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
	}

	public function employee_actions()
	{
		
		if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    foreach ($_POST['val'] as $id) {
                        $this->jobs_model->deleteDevelop($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("purchases_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'combine') {
                    $html = $this->combine_pdf($_POST['val']);
					
                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('jobs_employees'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('cashier'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('computer_name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('printer'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('printer_man'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('decor'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('take_photo'));
					$this->excel->getActiveSheet()->SetCellValue('H1', lang('money'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $jobs = $this->jobs_model->exportEmployeeByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $jobs->username);
                        $this->excel->getActiveSheet()->setCellValueExplicit('B' . $row, $jobs->user1, PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValueExplicit('C' . $row, $jobs->user2,PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->excel->getActiveSheet()->setCellValueExplicit('D' . $row, $jobs->user3,PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->excel->getActiveSheet()->setCellValueExplicit('E' . $row, $jobs->user4,PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValueExplicit('F' . $row, $jobs->user5,PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValueExplicit('H' . $row, $jobs->sum,PHPExcel_Cell_DataType::TYPE_STRING);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'jobs_employees_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php";
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_purchase_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
	}
	
	public function get_jobemployee($id = null)
	{
		$this->erp->checkPermissions('jobs');
		$this->form_validation->set_rules('developed_quantity', lang("developed_quantity"), 'required');
        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->erp->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }			
			$total = $this->input->post('unit_price') * $this->input->post('developed_quantity');			
            $data = array(
                'created_at' => $date,				
                'sale_id' => $this->input->post('sale_id'),
                'product_id' => $this->input->post('product_id'),
                'product_name' => $this->input->post('product_name'),
                'warehouse_id' => $this->input->post('warehouse'),
				'machine_name' => $this->input->post('machine'),
				'quantity_break' => $this->input->post('quantity_break'),
				'quantity_index' => $this->input->post('quantity_index'),
				'user_1' => $this->input->post('user_1'),
				'user_2' => $this->input->post('user_2'),
				'user_3' => $this->input->post('user_3'),
				'user_4' => $this->input->post('user_4'),
				'user_5' => $this->input->post('user_5'),
				'unit_price'	=> $this->input->post('unit_price'),
				'quantity' => $this->input->post('developed_quantity'),
				'grand_total' => $total
            );
        } elseif ($this->input->post('add_products_develop')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('jobs');
        }
		if ($this->form_validation->run() == true && $this->jobs_model->addDevelop($data)) {
            $this->session->set_flashdata('message', lang("jobs_added"));
            redirect('jobs');
        }else{
			$this->data['modal_js'] = $this->site->modal_js();
			$this->data['develop_id'] = $id;
			$this->data['employee'] = $this->jobs_model->getAllEmployee($id);
			$this->data['page_title'] = $this->lang->line("get_jobemployee");
			$this->load->view($this->theme . 'jobs/get_jobemployee', $this->data);
		}
	}

	///////////////////////////////////
	
	public function marchines()
	{
		$this->erp->checkPermissions('index', true, 'jobs');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('jobs')));
        $meta = array('page_title' => lang('list_machines'), 'bc' => $bc);
        $this->page_construct('jobs/marchines', $meta, $this->data);
	}
	
	public function getMarchine()
    {
		if($this->Admin || $this->Owner){
			$add_link = "<a class=\"tip\" title='" . $this->lang->line("add_marchine_logs") . "' href='" . site_url('jobs/add_computer/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus-circle\"></i></a>";
		
			$edit_link = "<a class=\"tip\" title='" . $this->lang->line("edit_marchine") . "' href='" . site_url('jobs/edit_marchine/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a>";
			
			$delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_marchine") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('jobs/delete_marchine/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";
		}else{
			if($this->GP['jobs-add']){
				$add_link = "<a class=\"tip\" title='" . $this->lang->line("add_marchine_logs") . "' href='" . site_url('jobs/add_computer/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus-circle\"></i></a>";
			}
			
			if($this->GP['jobs-edit']){
				$edit_link = "<a class=\"tip\" title='" . $this->lang->line("edit_marchine") . "' href='" . site_url('jobs/edit_marchine/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a>";
			}
			
			if($this->GP['jobs-delete']){
				$delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_marchine") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('jobs/delete_marchine/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";
			}
			
		}
		
		
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('marchine').".id as id,".$this->db->dbprefix('marchine').".name,".$this->db->dbprefix('companies').".company,".$this->db->dbprefix('marchine').".type,description")
            ->from("marchine")
			->join("companies", "companies.id = marchine.biller_id")
			->add_column("Actions", "<center> ".$add_link." ". $edit_link ." ". $delete_link ."</center>", "id");
        echo $this->datatables->generate();
    }
	
	public function add_marchine()
	{
		$this->erp->checkPermissions('add', true, 'jobs');
		$this->form_validation->set_rules('marchine_name', lang("marchine_name"), 'required');
		if ($this->form_validation->run() == true) {
			$data = array(
				'name'        => $this->input->post('marchine_name'), 
				'biller_id'   => $this->input->post('biller'),
				'type'        => $this->input->post('type'),
				'description' => $this->input->post('description'),
				'status'	  => $this->input->post('status'),
				'`13`'		  => $this->input->post('13s'),
				'`15`'		  => $this->input->post('15s'),
				'`25`'		  => $this->input->post('25s'),
				'`30`'		  => $this->input->post('30s'),
				'`50`'		  => $this->input->post('50s'),
				'`60`'		  => $this->input->post('60s'),
				'`76`'		  => $this->input->post('76s'),
				'`80`'		  => $this->input->post('80s'),
				'`100`'		  => $this->input->post('100s'),
				'`120`'		  => $this->input->post('120s'),
				'`150`'		  => $this->input->post('150s')
				
			);
			//$this->erp->print_arrays($data);
			$this->jobs_model->addMarchine($data);
			$this->session->set_flashdata('message', lang("marchine_added"));
			redirect('jobs/marchines');
		}
		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$this->data['biller'] = $this->jobs_model->getBiller();
		$this->data['modal_js'] = $this->site->modal_js();
		$this->load->view($this->theme . 'jobs/add_marchine', $this->data);
	}
	
	public function edit_marchine($id = null)
	{
		$this->erp->checkPermissions('edit', true, 'jobs');
		$this->form_validation->set_rules('marchine_name', lang("marchine_name"), 'required');
		if ($this->form_validation->run() == true) {
			$id_marchine = $this->input->post('id_marchine');
			$data = array(
				'name'        => $this->input->post('marchine_name'), 
				'biller_id'   => $this->input->post('biller'),
				'type'        => $this->input->post('type'),
				'description' => $this->input->post('description'),
				'status'	  => $this->input->post('status'),
				'`13`'		  => $this->input->post('13s'),
				'`15`'		  => $this->input->post('15s'),
				'`25`'		  => $this->input->post('25s'),
				'`30`'		  => $this->input->post('30s'),
				'`50`'		  => $this->input->post('50s'),
				'`60`'		  => $this->input->post('60s'),
				'`76`'		  => $this->input->post('76s'),
				'`80`'		  => $this->input->post('80s'),
				'`100`'		  => $this->input->post('100s'),
				'`120`'		  => $this->input->post('120s'),
				'`150`'		  => $this->input->post('150s')
			);
			//$this->erp->print_arrays($data);
			$this->jobs_model->editMarchine($id_marchine, $data);
			$this->session->set_flashdata('message', lang("marchine_updateed"));
			redirect('jobs/marchines');
		}
		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$this->data['marchine_id'] = $id;
		$this->data['biller'] = $this->jobs_model->getBiller();
		$this->data['marchine'] = $this->jobs_model->getMarchines($id);
		$this->data['modal_js'] = $this->site->modal_js();
		$this->load->view($this->theme . 'jobs/edit_marchine', $this->data);
	}
	
	public function delete_marchine($id = null)
    {
        $this->erp->checkPermissions('delete', true, 'jobs');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if ($this->jobs_model->deleteMarchine($id)) {
            echo lang("marchine_deleted");
        }
    }
	
	public function marchine_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    foreach ($_POST['val'] as $id) {
                        $this->jobs_model->deleteMarchine($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("marchines_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('marchines'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('branch'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('type'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('description'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $marchine = $this->jobs_model->getMarchineByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $marchine->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $marchine->company);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $marchine->type);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $marchine->description);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'marchines_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php";
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_marchine_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	/////////////////////////////////
	
    public function marchine_activities($pdf = NULL)
    {
        $this->erp->checkPermissions('index', true, 'jobs');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('machine_activities')));
        if ($pdf) {
            $html = $this->load->view($this->theme . 'jobs/machine_activities', $this->data, true);
            $name = lang("machine_activities") . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_mactivities_text") . '</p>', '', $html);
            $this->erp->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $meta = array('page_title' => lang('machine_activities'), 'bc' => $bc);
        $this->page_construct('jobs/machine_activities', $meta, $this->data);
    }

    public function get_machine_activities()
	{
        if ($this->input->get('user')) {
            $user_query = $this->input->get('user');
        } else {
            $user_query = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = NULL;
        }
        if ($this->input->get('note')) {
            $note = $this->input->get('note');
        } else {
            $note = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->erp->fld($start_date);
            $end_date = $this->erp->fld($end_date);
        }
        
		if($this->Admin || $this->Owner){
			$add_link = anchor('jobs/add_jobs/$1', '<i class="fa fa-file-text-o"></i> ' . lang('add_jobs'), 'data-toggle="modal" data-target="#myModal2"');
			$edit_link = anchor('jobs/edit_jobs/$1', '<i class="fa fa-edit"></i> ' . lang('edit_jobs'), 'data-toggle="modal" data-target="#myModal"');	
			$delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_jobs") . "</b>' data-content=\"<p>"
			. lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('jobs/delete_jobs/$1') . "'>"
			. lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
			. lang('delete_jobs') . "</a>";
		}else{
			if($this->GP['jobs-add']){
				$add_link = anchor('jobs/add_jobs/$1', '<i class="fa fa-file-text-o"></i> ' . lang('add_jobs'), 'data-toggle="modal" data-target="#myModal2"');
			}
			
			if($this->GP['jobs-edit']){
				$edit_link = anchor('jobs/edit_jobs/$1', '<i class="fa fa-edit"></i> ' . lang('edit_jobs'), 'data-toggle="modal" data-target="#myModal"');
			}
			
			if($this->GP['jobs-delete']){
				$delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line("delete_jobs") . "</b>' data-content=\"<p>"
				. lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('jobs/delete_jobs/$1') . "'>"
				. lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
				. lang('delete_jobs') . "</a>";
			}
		}
		
		
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $add_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
        </div></div>';

        $this->load->library('datatables');

        $this->datatables
            ->select("sdi.product_name, sdi.quantity, sdi.quantity_index, sdi.quantity_break, sdi.quantity_index, (sdi.quantity_index + sdi.quantity_break + sdi.quantity_index ) as totalqty, sdi.quantity")
            ->from('sale_dev_items sdi');

        if (!$this->Owner && !$this->Admin) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }
        if ($user_query) {
            $this->datatables->where('expenses.created_by', $user_query);
        }
        if ($reference_no) {
            $this->datatables->where('expenses.reference', $reference_no);
        }
        if ($biller) {
            $this->datatables->where('expenses.biller_id', $biller);
        }
        if ($note) {
            $this->datatables->where("expenses.note LIKE '%" . $note . "%'");
        }
        if ($start_date) {
            $this->datatables->where($this->db->dbprefix('expenses').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

	////////////////////////////
	
	public function add_computer($id = null)
	{
		$this->erp->checkPermissions('jobs');
		$this->form_validation->set_rules('marchine_name', lang("marchine_name"), 'required');
		if ($this->form_validation->run() == true) {
			$check_id = $this->input->post('marchine_name');
			$check_marchine = $this->jobs_model->checks_marchine($check_id);
			if($check_marchine){
				$data = array(
					'marchine_id' => $this->input->post('marchine_name'),
					'old_number'  => $check_marchine->new_number,
					'new_number'  => $this->input->post('number'),
					'date'        => $this->erp->fld(trim($this->input->post('date'))),
				);
			}else{
				$data = array(
					'marchine_id'  => $this->input->post('marchine_name'), 
					'new_number'   => $this->input->post('number'),
					'date'        => $this->erp->fld(trim($this->input->post('date'))),
				);
			}
			$this->jobs_model->addMarchine_log($data);
			$this->session->set_flashdata('message', lang("marchine_log_added"));
			redirect('jobs/marchines');
		}
		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$this->data['marchine_id'] = $id;
		$this->data['marchine'] = $this->jobs_model->getAllMarchine();
		$this->data['modal_js'] = $this->site->modal_js();
		$this->load->view($this->theme . 'jobs/add_computer', $this->data);
	}
}