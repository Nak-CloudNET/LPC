<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Jobs_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	
	
	public function addDevelop($data)
	{
		
		/*$q = $this->db->query("SELECT
							unit_price,
							quantity,
							first_quantity
						FROM
							erp_sale_items
						WHERE product_id = '".$data['product_id']."'
						AND sale_id = '".$data['sale_id']."'
						")->row();
		if($data['quantity']){
			$dev_qty = $this->getQuantityDevItem($data['sale_id']);
			$itemDatas = array(
				'quantity' => $dev_qty->dev_quantity ? $dev_qty->dev_quantity + $data['quantity'] : $data['quantity']
			);
			
			$this->db->update('sale_items', $itemDatas, array('sale_id'=>$data['sale_id'],'product_id'=>$data['product_id']));
			$this->db->query("UPDATE erp_sales SET 
									total = total+".(($data['quantity'] - $q->quantity) * $q->unit_price).",
									grand_total = grand_total+".(($data['quantity'] - $q->quantity) * $q->unit_price).",
									payment_status = 'partial'
								WHERE id='".$data['sale_id']."'");
		}*/
		if ($this->db->insert('sale_dev_items', $data)) {
            $sale_dev_id = $this->db->insert_id();
			$dev_qty = $this->getQuantityDevItem($data['sale_id'], $data['product_id']);
			$itemDatas = array(
				'quantity' => $dev_qty->dev_quantity,
				'subtotal' => $dev_qty->dev_subtotal
			);
			$this->db->update('sale_items', $itemDatas, array('sale_id'=>$data['sale_id'],'product_id'=>$data['product_id']));
			
			$total_subtotal = $this->getTotalSubtotalItems($data['sale_id']);
			$saleDatas = array(
				'grand_total' => $total_subtotal->total_subtotal,
				'total' 	  => $total_subtotal->total_subtotal,
				'total_items' => $total_subtotal->total_items
			);
			$this->db->update('sales', $saleDatas, array('id'=>$data['sale_id']));			
            return true;
        }
	}
	
	public function getQuantityDevItem($sale_id = NULL, $product_id = NULL)
	{
		$this->db->select("SUM(COALESCE(quantity, 0)) as dev_quantity, SUM(COALESCE(grand_total, 0)) as dev_subtotal");
		$q = $this->db->get_where('sale_dev_items', array('sale_id' => $sale_id, 'product_id' => $product_id));
		if($q->num_rows() > 0){
            return $q->row();
		}
		return FALSE;
	}
	public function getGroup(){
		$q = $this->db->query("SELECT
							id,
							`name`
						FROM
							`erp_groups`
						WHERE
							`id` IN ('12', '11', '8', '10', '9', '17')");
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;			
	}
	
	public function getTotalSubtotalItems($sale_id)
	{
		$this->db->select("SUM(COALESCE(subtotal, 0)) as total_subtotal, SUM(COALESCE(quantity, 0)) as total_items");
		$q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
		if($q->num_rows() > 0){
            return $q->row();
		}
		return FALSE;
	}
	
	public function getAllUsersByGroup($id,$userid) {
		$this->db->select("id, first_name, last_name, username, group_id, commission");
		if($userid){
			$where = array('group_id' => $id, 'id' => $userid);
		}else{
			$where = array('group_id' => $id);
		}
        $q = $this->db->get_where('users', $where);
        if ($q->num_rows() > 0) { 
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllUsersByUserAndGroup($user_id, $group_id){
		$where = '';
		if($group_id != ''){
			$where .=' AND (erp_users.group_id)="'.$group_id.'"';
		}
		if($user_id != ''){
			$where .=' AND (erp_users.id)="'.$user_id.'"';
		}
		$q = $this->db->query("SELECT
									id, first_name, last_name, username, group_id, commission
								FROM
									`erp_users`
								WHERE 1=1
									".$where."
								");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getComputerUser()
	{
		$q = $this->db->query("SELECT
									erp_users.id,
									erp_groups.name,
									erp_users.username,
									erp_users.first_name,
									erp_users.last_name,
									erp_users.group_id
								FROM
									erp_users left join erp_groups 
									on erp_groups.id = erp_users.group_id");		
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
	}
	
	public function getDevelopedProduct($id)
	{
		$this->db->select($this->db->dbprefix('sale_items').".id as id ," .$this->db->dbprefix('sales') . ".date, reference_no, customer ," . $this->db->dbprefix('sale_items') . ".product_name, product_id, sale_id, unit_price, quantity,".$this->db->dbprefix('sales').".warehouse_id as warehouse")
            ->from('sales')
            ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
			->where('sale_items.id',$id);
		$q = $this->db->get();
		if($q->num_rows() > 0){
            return $q->row();
		}
		return FALSE;
	}
	
	public function getAllUsers()
	{
		
		$q = $this->db->query("SELECT
							id,
							`first_name`,
							last_name
						FROM
							`erp_users`
						WHERE
							`group_id` IN ('12', '11', '8', '10', '9', '17')");
		if ($q->num_rows() > 0) { 
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
		return FALSE;
	}
	
	public function getDevelopedProductByDev($id)
	{
		$this->db->select(
				$this->db->dbprefix('sale_items').".id as id ,
				" .$this->db->dbprefix('sales') . ".date, 
				reference_no, 
				customer,
				" . $this->db->dbprefix('sale_items') . ".product_name, 
				" . $this->db->dbprefix('sale_items') . ".product_id, 
				" . $this->db->dbprefix('sale_items') . ".sale_id, 
				" . $this->db->dbprefix('sale_items') . ".unit_price, 
				" . $this->db->dbprefix('sale_items') . ".quantity,
				".$this->db->dbprefix('sales').".warehouse_id as warehouse")
            ->from('sales')
            ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
			->join('sale_dev_items', 'sale_dev_items.sale_id=sales.id', 'left')
			->where('sale_dev_items.id',$id);
		$q = $this->db->get();
		if($q->num_rows() > 0){
            return $q->row();
		}
		return FALSE;
	}
	
	public function getJobActivities($start = NULL, $end = NULL)
	{
		$this->db->select("id, 
						product_name, 
						product_id,
						sum(quantity) as pquantity, 
						sum(quantity_break) as qty_break, 
						sum(quantity_index) as qty_index, 
						sum(quantity + quantity_break + quantity_index) as tquantity")
				->from('sale_dev_items')
				->group_by('product_id');
		if (!$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
		if($start && $end){
			$this->db->where($this->db->dbprefix('sale_dev_items').'.created_at BETWEEN "' . $start . '" and "' . $end . '"');
		}				
		return $this->db->get()->result();		
	}
	
	public function getJobs()
	{
		$this->db->select($this->db->dbprefix('sale_items').".id as id ," .$this->db->dbprefix('sales') . ".date, reference_no, customer ," . $this->db->dbprefix('sale_items') . ".product_name,".$this->db->dbprefix('sale_items').".quantity as fquantity, 
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
			->where('categories.jobs',1)
			->group_by('sale_items.sale_id')
			->group_by('sale_items.product_id');
		return $this->db->get()->result();	
	}
	
	public function getDevItem($id)
	{
		$this->db->select($this->db->dbprefix('sale_items').".id as id ," .$this->db->dbprefix('sales') . ".date, reference_no, customer ," . $this->db->dbprefix('sale_items') . ".product_name,".$this->db->dbprefix('sale_items').".quantity as fquantity,".$this->db->dbprefix('sale_dev_items').".quantity as dev_quantity, machine_name, quantity_break, quantity_index, created_at, user_1, user_2, user_3, user_4, user_5")
            ->from('sales')
            ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
			->join('sale_dev_items', 'sale_dev_items.sale_id=sale_items.sale_id', 'left')
            ->where('sale_items.id',$id);
		$q = $this->db->get();
		if($q->num_rows() > 0){
            return $q->row();
		}
		return FALSE;
	}
	
	public function getDevItems($id)
	{
		$this->db->select("*")
            ->from('sale_dev_items')
            ->where('sale_dev_items.id',$id);
		$q = $this->db->get();
		if($q->num_rows() > 0){
            return $q->row();
		}
		return FALSE;
	}
	
	public function updateDevelop($id, $data = array())
    {
        if ($this->db->update('sale_dev_items', $data, array('sale_id' => $id))) {
            return true;
        }
        return false;
    }
	
	public function updateDevelopItem($id, $data = array())
    {
       
		if ($this->db->update('sale_dev_items', $data, array('id' => $id))) {
			$dev_qty = $this->getQuantityDevItem($data['sale_id'], $data['product_id']);
			$itemDatas = array(
				'quantity' => $dev_qty->dev_quantity,
				'subtotal' => $dev_qty->dev_subtotal
			);
			$this->db->update('sale_items', $itemDatas, array('sale_id'=>$data['sale_id'],'product_id'=>$data['product_id']));
			
			$total_subtotal = $this->getTotalSubtotalItems($data['sale_id']);
			$saleDatas = array(
				'grand_total' => $total_subtotal->total_subtotal,
				'total' 	  => $total_subtotal->total_subtotal,
				'total_items' => $total_subtotal->total_items
			);
			$this->db->update('sales', $saleDatas, array('id'=>$data['sale_id']));
            return true;
        }
        return false;
    }
	
	public function getSaleIdByID($id)
	{
		$q = $this->db->query("SELECT
									sale_id
								FROM
									erp_sale_items
								WHERE
									id = $id");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}
	
	public function deleteDevelop($id)
    {
        if ($this->db->delete('sale_dev_items', array('sale_id' => $id))) {
            return true;
        }
        return FALSE;
    }
	
	public function deleteDevelopDev($id , $data)
    {
        if ($this->db->delete('sale_dev_items', array('id' => $id))) {
			$dev_qty = $this->getQuantityDevItem($data['sale_id'], $data['product_id']);
			$itemDatas = array(
				'quantity' => $dev_qty->dev_quantity,
				'subtotal' => $dev_qty->dev_subtotal
			);
			$this->db->update('sale_items', $itemDatas, array('sale_id'=>$data['sale_id'],'product_id'=>$data['product_id']));
			
			$total_subtotal = $this->getTotalSubtotalItems($data['sale_id']);
			$saleDatas = array(
				'grand_total' => $total_subtotal->total_subtotal,
				'total' 	  => $total_subtotal->total_subtotal,
				'total_items' => $total_subtotal->total_items
			);
			$this->db->update('sales', $saleDatas, array('id'=>$data['sale_id']));
            return true;
        }
        return FALSE;
    }
	
	public function getSaleDevItemById($id)
	{
		$this->db->select("*");
		$q = $this->db->get_where('sale_dev_items', array('id' => $id));
		if($q->num_rows() > 0){
            return $q->row();
		}
		return FALSE;
	}
	
	public function receiveJob($id)
    {
        if ($this->db->update('sale_items',array('received'=>'1'), array('id' => $id))) {
            return true;
        }
        return FALSE;
    }	
	
	public function cancelJob($id)
    {
        if ($this->db->update('sale_items',array('received'=>'0'), array('id' => $id))) {
            return true;
        }
        return FALSE;
    }	
	
	public function getAllEmployee($id)
	{
		$this->db->select($this->db->dbprefix('sale_dev_items').".created_at," .$this->db->dbprefix('sales') . ".reference_no, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ',".$this->db->dbprefix('users').".last_name) as uName,".$this->db->dbprefix('sales').".customer,".$this->db->dbprefix('sale_dev_items').".grand_total as total")
            ->from('sale_dev_items')
            ->join('sales', 'sales.id=sale_dev_items.sale_id', 'left')
			->join('users', 'users.id=sale_dev_items.created_by', 'left')
            ->where('sale_dev_items.user_1 = "'.$id.'" or sale_dev_items.user_2 = "'.$id.'" or sale_dev_items.user_3 = "'.$id.'" or sale_dev_items.user_4 = "'.$id.'" or sale_dev_items.user_5 = "'.$id.'"');
		$q = $this->db->get();
		if($q->num_rows() > 0){
            return $q->result();
		}
		return FALSE;
	}
	
	public function getSaleDevItem($from_date,$today,$product_id,$cate_id1){
		$where = '';
		if($from_date!=''){
			$where .=' AND date(erp_sales.date)>="'.$from_date.'"';
		}
		if($today!=''){
			$where .=' AND date(erp_sales.date)<="'.$today.'"';
		}
		if($product_id != ''){
			$where .=' AND (erp_sale_items.product_id)="'.$product_id.'"';
		}
		if($cate_id1 != ''){
			$where .=' AND (erp_sale_items.category_id)="'.$cate_id1.'"';
		}
		$q = $this->db->query("SELECT
									erp_sale_items.product_id,
									erp_sale_items.product_name,
									erp_commission.photowriter,
									erp_commission.computer,
									erp_commission.developphoto,
									erp_commission.photographer,
									erp_commission.studio_decor,
									erp_commission.salesman
								FROM
									`erp_sale_items`
								INNER JOIN erp_sales ON erp_sales.id = erp_sale_items.sale_id
								LEFT JOIN 
									erp_commission ON erp_commission.product_id = erp_sale_items.product_id
								WHERE 1=1
									".$where."
								GROUP BY
									erp_sale_items.product_id
								");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	

	public function getTotalComUser1($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
									sum(total_commision) as total_commision,
									user_1
								FROM
									(
										SELECT
											COALESCE (
												erp_commission.computer,
												0
											) * (
												SUM(
													COALESCE (
														erp_sale_dev_items.quantity,
														0
													)
												)
											) AS total_commision,
											erp_sale_dev_items.user_1
										FROM
											`erp_sale_dev_items`
										LEFT JOIN erp_commission ON erp_commission.product_id = erp_sale_dev_items.product_id
										WHERE 1=1
											".$where."
										GROUP BY
											erp_sale_dev_items.user_1,
											erp_sale_dev_items.product_id
									) as tmp
								GROUP BY
									user_1
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	
	public function getComUser5($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>= "'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<= "'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)= "'.$product_id.'"';
		}
		
		$q = $this->db->query("SELECT
									erp_sale_dev_items.user_5,
									SUM(grand_total) AS sum_grand_total,
									(
										(
											SUM(grand_total) * COALESCE(erp_users.commission, 0)
										) 
									) AS commission_user
								FROM
									`erp_sale_dev_items`
								LEFT JOIN 
									erp_users ON erp_users.id = erp_sale_dev_items.user_5
								WHERE 1=1
									".$where."
								GROUP BY
									user_5
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	
	public function getComUser1($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
									erp_sale_dev_items.user_1,
									SUM(grand_total) AS sum_grand_total,
									(
										(
											SUM(grand_total) * COALESCE(erp_users.commission, 0)
										) 
									) AS commission_user
								FROM
									`erp_sale_dev_items`
								LEFT JOIN erp_users ON erp_users.id = erp_sale_dev_items.user_1
								WHERE 1=1
									".$where."
								GROUP BY
									user_1
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	
	public function getComUser2($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
									erp_sale_dev_items.user_2,
									SUM(grand_total) AS sum_grand_total,
									(
										(
											SUM(grand_total) * COALESCE(erp_users.commission, 0)
										) 
									) AS commission_user
								FROM
									`erp_sale_dev_items`
								LEFT JOIN erp_users ON erp_users.id = erp_sale_dev_items.user_2
								WHERE 1=1
									".$where."
								GROUP BY
									user_2
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	public function getComSalesman($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sales.date)>="'.$from_date.'"';
			$where .=' AND date(erp_sales.date)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
									erp_sales.created_by,
									SUM(grand_total) AS sum_grand_total,
									(
										(
											SUM(grand_total) * COALESCE(erp_users.commission, 0)
										) 
									) AS commission_user
								FROM
									`erp_sale_items`
								INNER JOIN erp_sales ON erp_sales.id = erp_sale_items.sale_id
								LEFT JOIN erp_users ON erp_users.id = erp_sales.created_by
								WHERE 1=1
									".$where."
								GROUP BY
									erp_sales.created_by
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	
	public function getComUser3($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
									erp_sale_dev_items.user_3,
									SUM(grand_total) AS sum_grand_total,
									(
										(
											SUM(grand_total) * COALESCE(erp_users.commission, 0)
										) 
									) AS commission_user
								FROM
									`erp_sale_dev_items`
								LEFT JOIN erp_users ON erp_users.id = erp_sale_dev_items.user_3
								WHERE 1=1
									".$where."
								GROUP BY
									user_3
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	
	public function getComUser4($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
									erp_sale_dev_items.user_4,
									SUM(grand_total) AS sum_grand_total,
									(
										(
											SUM(grand_total) * COALESCE(erp_users.commission, 0)
										) 
									) AS commission_user
								FROM
									`erp_sale_dev_items`
								LEFT JOIN erp_users ON erp_users.id = erp_sale_dev_items.user_4
								WHERE 1=1
									".$where."
								GROUP BY
									user_4
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	
	public function getTotalComUser2($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
									sum(total_commision) as total_commision,
									user_2
								FROM
									(
										SELECT
											COALESCE (
												erp_commission.developphoto,
												0
											) * (
												SUM(
													COALESCE (
														erp_sale_dev_items.quantity,
														0
													)
												)
											) AS total_commision,
											erp_sale_dev_items.user_2
										FROM
											`erp_sale_dev_items`
										LEFT JOIN erp_commission ON erp_commission.product_id = erp_sale_dev_items.product_id
										WHERE 1=1
											".$where."
										GROUP BY
											erp_sale_dev_items.user_2,
											erp_sale_dev_items.product_id
									) as tmp
								GROUP BY
									user_2
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	
	public function getTotalComUser3($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
									sum(total_commision) as total_commision,
									user_3
								FROM
									(
										SELECT
											COALESCE (
												erp_commission.studio_decor,
												0
											) * (
												SUM(
													COALESCE (
														erp_sale_dev_items.quantity,
														0
													)
												)
											) AS total_commision,
											erp_sale_dev_items.user_3
										FROM
											`erp_sale_dev_items`
										LEFT JOIN erp_commission ON erp_commission.product_id = erp_sale_dev_items.product_id
										WHERE 1=1
											".$where."
										GROUP BY
											erp_sale_dev_items.user_3,
											erp_sale_dev_items.product_id
									) as tmp
								GROUP BY
									user_3
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	public function getTotalComUser4($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
									sum(total_commision) as total_commision,
									user_4
								FROM
									(
										SELECT
											COALESCE (
												erp_commission.photographer,
												0
											) * (
												SUM(
													COALESCE (
														erp_sale_dev_items.quantity,
														0
													)
												)
											) AS total_commision,
											erp_sale_dev_items.user_4
										FROM
											`erp_sale_dev_items`
										LEFT JOIN erp_commission ON erp_commission.product_id = erp_sale_dev_items.product_id
										WHERE 1=1
											".$where."
										GROUP BY
											erp_sale_dev_items.user_4,
											erp_sale_dev_items.product_id
									) as tmp
								GROUP BY
									user_4
							");
							
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	public function getTotalComUser5($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
		
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("
								SELECT
									sum(total_commision) as total_commision,
									user_5
								FROM
									(
										SELECT
											COALESCE (
												erp_commission.photowriter,
												0
											) * (
												SUM(
													COALESCE (
														erp_sale_dev_items.quantity,
														0
													)
												)
											) AS total_commision,
											erp_sale_dev_items.user_5
										FROM
											`erp_sale_dev_items`
										LEFT JOIN erp_commission ON erp_commission.product_id = erp_sale_dev_items.product_id
										WHERE 1=1
											".$where."
										GROUP BY
											erp_sale_dev_items.user_5,
											erp_sale_dev_items.product_id
									) as tmp
								GROUP BY
									user_5
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	public function getTotalComSalesman($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sales.date)>="'.$from_date.'"';
			$where .=' AND date(erp_sales.date)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
									sum(total_commision) as total_commision,
									created_by
								FROM
									(
										SELECT
											COALESCE (
												erp_commission.salesman,
												0
											) * (
												SUM(
													COALESCE (
														erp_sale_items.quantity,
														0
													)
												)
											) AS total_commision,
											erp_sales.created_by
										FROM
											`erp_sale_items`
										INNER JOIN erp_sales ON erp_sales.id = erp_sale_items.sale_id	
										LEFT JOIN erp_commission ON erp_commission.product_id = erp_sale_items.product_id
										WHERE 1=1
											".$where."
										GROUP BY
											erp_sales.created_by,
											erp_sale_items.product_id
									) as tmp
								GROUP BY
									created_by
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	public function getUser1Com($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
								erp_sale_dev_items.product_id,
								SUM(
									COALESCE (
										erp_sale_dev_items.quantity,
										0
									)
								) AS qty,
								erp_sale_dev_items.user_1
							FROM
								`erp_sale_dev_items`
							WHERE 1=1
								".$where."
							GROUP BY
								erp_sale_dev_items.product_id,
								erp_sale_dev_items.user_1
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	public function getUser2Com($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
								erp_sale_dev_items.product_id,
								SUM(
									COALESCE (
										erp_sale_dev_items.quantity,
										0
									)
								) AS qty,
								erp_sale_dev_items.user_2
							FROM
								`erp_sale_dev_items`
							WHERE 1=1
									".$where."
							GROUP BY
								erp_sale_dev_items.product_id,
								erp_sale_dev_items.user_2
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	public function getUser3Com($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
								erp_sale_dev_items.product_id,
								SUM(
									COALESCE (
										erp_sale_dev_items.quantity,
										0
									)
								) AS qty,
								erp_sale_dev_items.user_3
							FROM
								`erp_sale_dev_items`
							WHERE 1=1
									".$where."
							GROUP BY
								erp_sale_dev_items.product_id,
								erp_sale_dev_items.user_3
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	public function getUser4Com($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
								erp_sale_dev_items.product_id,
								SUM(
									COALESCE (
										erp_sale_dev_items.quantity,
										0
									)
								) AS qty,
								erp_sale_dev_items.user_4
							FROM
								`erp_sale_dev_items`
							WHERE 1=1
									".$where."
							GROUP BY
								erp_sale_dev_items.product_id,
								erp_sale_dev_items.user_4
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	
	public function getUser5Com($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$today.'"';
		}
		if($product_id!=''){
		
			$where .=' AND (erp_sale_dev_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("SELECT
								erp_sale_dev_items.product_id,
								SUM(
									COALESCE (
										erp_sale_dev_items.quantity,
										0
									)
								) AS qty,
								erp_sale_dev_items.user_5
							FROM
								`erp_sale_dev_items`
							WHERE 1=1
									".$where."
							GROUP BY
								erp_sale_dev_items.product_id,
								erp_sale_dev_items.user_5
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	public function getSalemanCom($from_date,$today,$product_id){
		$where = '';
		if($from_date!='' && $today!=''){
			$where .=' AND date(erp_sales.date)>="'.$from_date.'"';
			$where .=' AND date(erp_sales.date)<="'.$today.'"';
		}
		if($product_id!=''){
			$where .=' AND (erp_sale_items.product_id)="'.$product_id.'"';
		}
		$q = $this->db->query("
								SELECT
									erp_sale_items.product_id,
									sum(erp_sale_items.quantity) AS qty,
									erp_sales.created_by
								FROM
									`erp_sales`
								INNER JOIN erp_sale_items ON erp_sale_items.sale_id = erp_sales.id
								WHERE 1=1
									".$where."
								GROUP BY
									erp_sale_items.product_id,
									erp_sales.created_by
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	
	public function getDevByUser($product_id,$group_user,$user_id,$from_date,$to_date){
		$where = '';
		if($from_date!='' && $to_date!=''){
			$where .=' AND date(erp_sale_dev_items.created_at)>="'.$from_date.'"';
			$where .=' AND date(erp_sale_dev_items.created_at)<="'.$to_date.'"';
		}
		if($product_id!=''){
			$where .=' AND erp_sale_dev_items.product_id="'.$product_id.'"';
		}
		if($user_id!=''){
			$where .=' AND erp_sale_dev_items.'.$group_user.'="'.$user_id.'"';
		}
		$q = $this->db->query("SELECT
									erp_sale_dev_items.product_name,
									erp_sale_dev_items.quantity,
									erp_sale_dev_items.created_at,
									erp_sales.reference_no
								FROM
									`erp_sale_dev_items`
								INNER JOIN erp_sales ON erp_sales.id = erp_sale_dev_items.sale_id	
								WHERE 1=1
								AND erp_sale_dev_items.quantity > 0
									".$where."
							");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
	}
	
	
	
	
	public function getBiller()
	{
		$q = $this->db->query("SELECT
									id,
									company
								FROM
									erp_companies
								WHERE
									group_name = 'biller'");
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
	}
	
	public function addMarchine($data)
	{
		if ($this->db->insert('marchine', $data)) {
            $convert_id = $this->db->insert_id();
            return true;
        }
	}
	
	public function getMarchines($id)
	{
		$q = $this->db->query("SELECT
									name,
									description,
									type,
									biller_id,
									status,
									`13` as first, `15` as second, `25` as third, `30` as fourth, `50` as sixth, `60` as seventh, `76` as eighth, `80` as nineth, `100` as tenth, `120` as eleven, `150` as tween
								FROM
									erp_marchine
								WHERE
									id = '$id'");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}
	
	public function editMarchine($id, $data = array())
    {
        if ($this->db->update('marchine', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }
	
	public function deleteMarchine($id)
    {
        if ($this->db->delete('marchine', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
	
	public function getMarchineByID($id)
    {
		$this->db
				->select($this->db->dbprefix('marchine').".id as id,".$this->db->dbprefix('marchine').".name,".$this->db->dbprefix('companies').".company,".$this->db->dbprefix('marchine').".type,description")
				->from("marchine")
				->join("companies", "companies.id = marchine.biller_id")
				->where('marchine.id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllMarchine()
	{
		$q = $this->db->query("SELECT
									id,
									name
								FROM
									erp_marchine
								");
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
	}
	
	public function addMarchine_log($data)
	{
		if ($this->db->insert('marchine_logs', $data)) {
            $convert_id = $this->db->insert_id();
            return true;
        }
	}
	
	public function checks_marchine($id)
	{
		$this->db->select('id, new_number')
				 ->from('marchine_logs')
				 ->where('marchine_id',$id)
				 ->order_by('date desc')
				 ->limit(1);
		$q = $this->db->get();
		if($q->num_rows() > 0){
			 return $q->row();
		}
		return FALSE;
	}
	
	
	public function getJobsByID($id)
	{
		$this->db
			->select($this->db->dbprefix('sale_items').".id as id ," .$this->db->dbprefix('sales') . ".date, reference_no, customer ," . $this->db->dbprefix('sale_items') . ".product_name,".$this->db->dbprefix('sale_items').".quantity as fquantity,".$this->db->dbprefix('sale_dev_items').".quantity as dev_quantity , COALESCE(CASE WHEN erp_sale_items.quantity > erp_sale_dev_items.quantity THEN 'processing' WHEN erp_sale_items.quantity <= erp_sale_dev_items.quantity THEN 'completed' ELSE 'pending' END, 0) AS status")
            ->from('sales')
            ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
			->join('sale_dev_items', 'sale_dev_items.sale_id=sale_items.sale_id', 'left')
			->join('products', 'products.id = sale_items.product_id', 'left')
			->join('categories', 'categories.id = products.category_id', 'left')
			->where(array('categories.auto_delivery'=>1, 'sale_items.id'=>$id))
            ->group_by('sales.id');
		$q = $this->db->get();
		if($q->num_rows() > 0){
			 return $q->row();
		}
		return FALSE;
	}
	
	public function getAllCommission() {
		$this->db->select("commission.*, products.id as product_id, products.name as product_name, products.category_id as cat_id");
		$this->db->join('products','products.id = commission.product_id', 'left');
        $q = $this->db->get('commission');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getActivitiesByID($id)
	{
		$this->db
				->select("id, product_name, sum(quantity) as pquantity, sum(quantity_break) as qty_break, sum(quantity_index) as qty_index, sum(quantity + quantity_break + quantity_index) as tquantity")
				->from('sale_dev_items')
				->where('id', $id)
				->group_by('product_id');
		$q = $this->db->get();
		if($q->num_rows() > 0){
			 return $q->row();
		}
		return FALSE;
	}
	
	public function getActivitiesByProduct($id)
	{
		$this->db->select("id, 
						product_name,
						sum(quantity) as pquantity, 
						sum(quantity_break) as qty_break, 
						sum(quantity_index) as qty_index, 
						sum(quantity + quantity_break + quantity_index) as tquantity")
				->from('sale_dev_items')
				->where('product_id', $id)
				->group_by('product_id');
		$q = $this->db->get();
		if($q->num_rows() > 0){
			 return $q->row();
		}
		return FALSE;
	}
	
	public function getJobEmployees($start=null, $end=null)
	{
		$this->db
            ->select($this->db->dbprefix('users').".id as id, CONCAT(".$this->db->dbprefix('users').".first_name, ' ',".$this->db->dbprefix('users').".last_name) as name, 
			IFNULL(sum(".$this->db->dbprefix('sale_dev_items').".quantity),0) AS user1,
			IFNULL(sum(".$this->db->dbprefix('sale_dev_items').".quantity),0) AS user2,
			IFNULL(sum(".$this->db->dbprefix('sale_dev_items').".quantity),0) AS user3,
			IFNULL(sum(".$this->db->dbprefix('sale_dev_items').".quantity),0) AS user4,
			IFNULL(sum(".$this->db->dbprefix('sale_dev_items').".quantity),0) AS user5,
			IFNULL((sum(grand_total) / (SELECT each_sale FROM erp_settings)),0)as sum")
            ->from('users')
			->join('sale_dev_items','users.id = sale_dev_items.user_1 or users.id = sale_dev_items.user_2 or users.id = sale_dev_items.user_3 or users.id = sale_dev_items.user_4 or users.id = sale_dev_items.user_5', 'left')
			->group_by('users.id')
			->order_by('first_name');
			
        if (!$this->Owner && !$this->Admin) {
            $this->db->where('created_by', $this->session->userdata('user_id'));
        }
		if($start && $end){
			$this->db->where($this->db->dbprefix('sale_dev_items').'.created_at BETWEEN "' . $start . '" and "' . $end . '"');
		}
		return $this->db->get()->result();
	}
	
	public function getEmployeeByID($id)
	{
		$this->db
			->select($this->db->dbprefix('users').".id as id, CONCAT(".$this->db->dbprefix('users').".first_name, ' ',".$this->db->dbprefix('users').".last_name) as name, COALESCE(CASE WHEN erp_users.id = erp_sale_dev_items.user_1 THEN 'true' ELSE 'false' END, 0) AS user1, COALESCE(CASE WHEN erp_users.id = erp_sale_dev_items.user_2 THEN 'true' ELSE 'false' END, 0) AS user2, COALESCE(CASE WHEN erp_users.id = erp_sale_dev_items.user_3 THEN 'true' ELSE 'false' END, 0) AS user3, COALESCE(CASE WHEN erp_users.id = erp_sale_dev_items.user_4 THEN 'true' ELSE 'false' END, 0) AS user4, COALESCE(CASE WHEN erp_users.id = erp_sale_dev_items.user_5 THEN 'true' ELSE 'false' END, 0) AS user5, sum(grand_total) as sum")
            ->from('users')
			->join('sale_dev_items','users.id = sale_dev_items.user_1 or users.id = sale_dev_items.user_2 or users.id = sale_dev_items.user_3 or users.id = sale_dev_items.user_4 or users.id = sale_dev_items.user_5', 'left')
			->where('users.id', $id)
			->group_by('users.ids');
		$q = $this->db->get();
		if($q->num_rows() > 0){
			 return $q->row();
		}
		return FALSE;
	}
	
	public function exportEmployeeByID($id=null)
	{
		$this->db
			->select($this->db->dbprefix('users').".id as id, 
				username, 
				IFNULL((select sum(quantity) from erp_sale_dev_items where user_1=erp_users.id),0) AS user1,
				IFNULL((select sum(quantity) from erp_sale_dev_items where user_2=erp_users.id),0) AS user2,
				IFNULL((select sum(quantity) from erp_sale_dev_items where user_3=erp_users.id),0) AS user3,
				IFNULL((select sum(quantity) from erp_sale_dev_items where user_4=erp_users.id),0) AS user4,
				IFNULL((select sum(quantity) from erp_sale_dev_items where user_5=erp_users.id),0) AS user5,
				IFNULL((sum(grand_total) / (SELECT each_sale FROM erp_settings)),0) as sum
				")
            ->from('users')
			->join('sale_dev_items','users.id = sale_dev_items.user_1 or
									users.id = sale_dev_items.user_2 or
									users.id = sale_dev_items.user_3 or
									users.id = sale_dev_items.user_4 or
									users.id = sale_dev_items.user_5', 'left')
			->where('users.id', $id)
			->group_by('users.id');
		$q = $this->db->get();
		if($q->num_rows() > 0){
			 return $q->row();
		}
		return FALSE;
	}
	
}
