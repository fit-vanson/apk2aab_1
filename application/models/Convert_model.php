<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'third_party\faker-master\src\autoload.php';


//D:\xampp\htdocs\apk2aab_1\application\third_party\faker-master\src\autoload.php

class Convert_model extends CI_Model
{

	//input values
	public function input_values()
	{

		$data = array(
			'name' => $this->input->post('name', true),
			'email' => $this->input->post('email', true),
			'phone' => $this->input->post('phone', true),
//			'file' => $this->input->file('file', true)
		);

		return $data;
	}

	//add contact message
	public function add_convert_file()
	{
        $this->load->library('funct');
		$data = $this->input_values();
		//send email
		if ($this->general_settings->send_email_contact_messages == 1) {
			$email_data = array(
				'email_type' => 'convert',
				'message_name' => $data['name'],
				'message_email' => $data['email'],
				'message_text' => $data['phone']
			);
			$this->session->set_userdata('mds_send_email_data', json_encode($email_data));
		}
        $user = $this->get_user_by_email($data['email']);

        if(empty($user)){
            $this->load->library('bcrypt');
            $dataDB['username'] = uniqid();
            //secure password
            $dataDB['email'] = $data['email'];
            $dataDB['password'] = $this->bcrypt->hash_password('123456');
            $dataDB['role_id'] = 3;
            $dataDB['first_name'] = $data['name'];
            $dataDB['phone_number'] = $data['phone'];
            $dataDB['user_type'] = "registered";
            $dataDB["slug"] = $dataDB["username"];
            $dataDB['banned'] = 0;
            $dataDB['last_seen'] = date('Y-m-d H:i:s');
            $dataDB['created_at'] = date('Y-m-d H:i:s');
            $dataDB['token'] = generate_token();
            $dataDB['email_status'] = 1;
            if ($this->general_settings->email_verification == 1) {
                $dataDB['email_status'] = 0;
            }
            if ($this->general_settings->vendor_verification_system != 1) {
                $dataDB['role_id'] = 2;
            }
            if ($this->db->insert('users', $dataDB)) {
                $last_id = $this->db->insert_id();
                $user_id = $last_id;
            }
        }else{
            $user_id = $user->id;
        }
        $this->file_model->upload_convert_files($user_id);
        $this->funct->telegram_notification('new_convert',$data['email'],$data['phone'],$_FILES['file']['name'],$this->general_settings->telegram_chat_id,$this->general_settings->telegram_secure_key );
        return true;

	}




    //submit quote
    public function submit_quote($convert_request)
    {
        if (isset($_FILES['file'])) {
            if(isset($convert_request->file_aab)){
                delete_file_from_server("uploads/convert-files/".$convert_request->buyer_id.'/' . $convert_request->file_aab);
            }

            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $file_name = str_slug($this->general_settings->application_name) . "-file-demo-" . $convert_request->buyer_id . uniqid() . "." . $ext;
            if ($this->upload_model->convert_file_upload('file', $file_name,$convert_request->buyer_id)) {
                $data = array(
                    'file_aab' => $file_name,
                    'price_offered' => $this->input->post('price', true),
                    'price_currency' => $this->input->post('currency', true),
                    'status' => 'pending_quote',
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $data["price_offered"] = get_price($data["price_offered"], 'database');
                if (empty($data["price_offered"])) {
                    $data["price_offered"] = 0;
                }
                @$this->db->close();
                @$this->db->initialize();
                $this->db->where('id', $convert_request->id);
                return $this->db->update('convert_requests', $data);

            }
            if (empty($_FILES['file']['name'])) {
                exit();
            }
        }

        if (isset($_FILES['fileConvert'])) {
            if(isset($convert_request->file_aab_apk)){
                delete_file_from_server("uploads/convert-files/".$convert_request->buyer_id.'/' . $convert_request->file_aab_apk);
            }

            $extSuccess = pathinfo($_FILES['fileConvert']['name'], PATHINFO_EXTENSION);
            $file_name_Success = str_slug($this->general_settings->application_name) . "-success-" . $convert_request->buyer_id . uniqid() . "." . $extSuccess;

            if ($this->upload_model->convert_file_upload('file', $file_name_Success,$convert_request->buyer_id)) {
                $data = array(
                    'file_aab_apk' => $file_name_Success,
                    'updated_at' => date('Y-m-d H:i:s')
                );

                @$this->db->close();
                @$this->db->initialize();
                $this->db->where('id', $convert_request->id);
                return $this->db->update('convert_requests', $data);
            }

            if (empty($_FILES['fileConvert']['name'])) {
                exit();
            }
        }

        return false;
    }



    //reject file convert
    public function reject_file($file_request)
    {
        $this->load->library('funct');
        if (!empty($file_request) && $this->auth_user->id == $file_request->buyer_id) {
            $data = array(
                'status' => "rejected_quote",
                'updated_at' => date('Y-m-d H:i:s')
            );
            $this->db->where('id', $file_request->id);
            $this->funct->telegram_notification('reject-convert',$file_request->price_offered,$file_request->price_currency,$file_request->file_aab,$this->general_settings->telegram_chat_id,$this->general_settings->telegram_secure_key );

            return $this->db->update('convert_requests', $data);
        }
    }


    //accept file convert
    public function accept_file($file_request)
    {
        $this->load->library('funct');

        if (!empty($file_request) && $this->auth_user->id == $file_request->buyer_id) {
            $data = array(
                'status' => "pending_payment",
                'updated_at' => date('Y-m-d H:i:s')
            );
            $this->db->where('id', $file_request->id);
            $this->funct->telegram_notification('accept-convert',$file_request->price_offered,$file_request->price_currency,$file_request->file_aab,$this->general_settings->telegram_chat_id,$this->general_settings->telegram_secure_key );

            return $this->db->update('convert_requests', $data);
        }
    }


    //get contact messages
	public function get_contact_messages()
	{

		$query = $this->db->get('contacts');
		return $query->result();
	}

	//get contact message
	public function get_contact_message($id)
	{
		$id = clean_number($id);
		$this->db->where('id', $id);
		$query = $this->db->get('contacts');
		return $query->result();
	}

	//get last contact messages
	public function get_last_contact_messages()
	{
		$this->db->limit(5);
		$query = $this->db->get('contacts');
		return $query->result();
	}

	//delete contact message
	public function delete_contact_message($id)
	{
		$id = clean_number($id);
		$contact = $this->get_contact_message($id);

		if (!empty($contact)) {
			$this->db->where('id', $id);
			return $this->db->delete('contacts');
		}
		return false;
	}

    public function get_user_by_email($email)
    {
        return $this->db->select('users.*, (SELECT permissions FROM roles_permissions WHERE roles_permissions.id = users.role_id LIMIT 1) AS permissions')->where('users.email', remove_special_characters($email))->get('users')->row();
    }


    //get admin convert files count
    public function get_admin_convert_files_count()
    {
        $this->filter_convert_files();
        $query = $this->db->get('convert_requests');
        return $query->num_rows();
    }

    //get admin convert files
    public function get_admin_paginated_convert_files($per_page, $offset)
    {
        $this->filter_convert_files();
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($per_page, $offset);
        $query = $this->db->get('convert_requests');
        return $query->result();
    }

    //delete admin convert files
    public function delete_admin_convert_file($id)
    {
        if (is_admin()) {
            $id = clean_number($id);
            $quote_request = $this->get_convert_file($id);
            if (!empty($quote_request)) {
                $this->db->where('id', $id);
                return $this->db->delete('convert_requests');
            }
        }
    }

    //get quote request
    public function get_convert_file($id)
    {
        $id = clean_number($id);
        $this->db->where('id', $id);
        $query = $this->db->get('convert_requests');
        return $query->row();
    }


    public function filter_convert_files()
    {
        $status = input_get('status');
        $q = input_get('q');


        if ($status == "new_convert_request" || $status == "pending_quote" || $status == "pending_payment" || $status == "rejected_quote" || $status == "closed" || $status == "completed") {
            $this->db->where('convert_requests.status', $status);
        }
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('convert_requests.file_apk', $q);
            $this->db->or_like('convert_requests.id', $q);
            $this->db->group_end();
        }
    }


    //get quote requests count
    public function get_convert_files_requests_count($user_id)
    {
//        $this->db->join('products', 'quote_requests.product_id = products.id');
//        if ($this->general_settings->membership_plans_system == 1) {
//            $this->db->join('users', 'quote_requests.seller_id = users.id AND users.banned = 0 AND users.is_membership_plan_expired = 0');
//        }
//        $this->db->where('products.status', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0);
//        $this->db->where("((products.product_type = 'physical' AND products.stock > 0) OR products.product_type = 'digital')");
        $this->db->where('convert_requests.buyer_id', clean_number($user_id))->where('convert_requests.is_buyer_deleted', 0);
        return $this->db->count_all_results('convert_requests');
    }

    //get paginated quote requests
    public function get_paginated_convert_files_requests($user_id, $per_page, $offset)
    {
//        $this->db->select('quote_requests.*');
//        $this->db->join('products', 'quote_requests.product_id = products.id');
//        if ($this->general_settings->membership_plans_system == 1) {
//            $this->db->join('users', 'quote_requests.seller_id = users.id AND users.banned = 0 AND users.is_membership_plan_expired = 0');
//        }
//        $this->db->where('products.status', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0);
//        $this->db->where("((products.product_type = 'physical' AND products.stock > 0) OR products.product_type = 'digital')");
        $this->db->where('convert_requests.buyer_id', clean_number($user_id))->where('convert_requests.is_buyer_deleted', 0);
        $this->db->order_by('updated_at', 'DESC')->limit($per_page, $offset);
        return $this->db->get('convert_requests')->result();
    }


    //set bidding quotes as completed after purchase
    public function set_convert_quotes_as_completed_after_purchase()
    {
        $cart_items = $this->cart_model->get_sess_cart_items();
        if (!empty($cart_items)) {
            foreach ($cart_items as $cart_item) {
                if ($cart_item->purchase_type == 'bidding') {
                    $data = array(
                        'status' => 'completed',
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    $this->db->where('id', $cart_item->quote_request_id);
                    @$this->db->update('convert_requests', $data);
                }
            }
        }
    }

    //get digital sale by order id
    public function get_digital_sale_by_order_id($buyer_id, $product_id, $order_id)
    {

        $this->db->where('buyer_id', clean_number($buyer_id));
        $this->db->where('id', clean_number($product_id));
//        $this->db->where('order_id', clean_number($order_id));
        return $this->db->get('convert_requests')->row();
    }


}
