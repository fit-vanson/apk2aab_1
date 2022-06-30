<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Convert_file_controller extends Home_Core_Controller
{
    /**
     * File Status
     *
     * 1. new_quote_request
     * 2. pending_quote
     * 3. pending_payment
     * 4. rejected_quote
     * 5. closed
     * 6. completed
     */

    public function __construct()
    {
        parent::__construct();
        if (!$this->auth_check) {
            redirect(lang_base_url());
        }
        if (!is_bidding_system_active()) {
            redirect(lang_base_url());
        }
        $this->load->model('convert_model');
        $this->rows_per_page = 15;
    }

    /**
     * Request Quote
     */
    public function request_quote()
    {
        $product_id = $this->input->post('product_id', true);
        $product = $this->product_model->get_active_product($product_id);
        if (!empty($product)) {
            if ($product->user_id == $this->auth_user->id) {
                $this->session->set_flashdata('product_details_error', trans("msg_quote_request_error"));
                redirect($this->agent->referrer());
                exit();
            }

            $this->db->where('product_id', clean_number($product_id))->where('buyer_id', $this->auth_user->id)->where('status', 'new_quote_request');
            $request = $this->db->get('quote_requests')->row();
            if (!empty($request)) {
                $this->session->set_flashdata('product_details_error', trans("already_have_active_request"));
                redirect($this->agent->referrer());
                exit();
            }
            
            $quote_id = $this->bidding_model->request_quote($product);
            if ($quote_id) {
                //send email
                $seller = get_user($product->user_id);
                if (!empty($seller) && $this->general_settings->send_email_bidding_system == 1) {
                    $email_data = array(
                        'email_type' => 'email_general',
                        'to' => $seller->email,
                        'subject' => trans("quote_request"),
                        'email_content' => trans("you_have_new_quote_request") . "<br>" . trans("quote") . ": " . "<strong>#" . $quote_id . "</strong>",
                        'email_link' => generate_dash_url("quote_requests"),
                        'email_button_text' => trans("view_details")
                    );
                    $this->session->set_userdata('mds_send_email_data', json_encode($email_data));
                }
            }
            $this->session->set_flashdata('product_details_success', trans("msg_quote_request_sent"));
        }
        redirect($this->agent->referrer());
    }

    /**
     * Accept Quote
     */
    public function accept_convert()
    {

        $id = $this->input->post('id', true);
        $convert_request = $this->convert_model->get_convert_file($id);
        if ($this->convert_model->accept_file($convert_request)) {
            //send email
            $seller = get_user($convert_request->seller_id);
            if (!empty($seller) && $this->general_settings->send_email_bidding_system == 1) {
                $email_data = array(
                    'email_type' => 'email_general',
                    'to' => $seller->email,
                    'subject' => trans("quote_request"),
                    'email_content' => trans("your_quote_accepted") . "<br>" . trans("quote") . ": " . "<strong>#" . $convert_request->id . "</strong>",
                    'email_link' => generate_dash_url("quote_requests"),
                    'email_button_text' => trans("view_details")
                );
                $this->session->set_userdata('mds_send_email_data', json_encode($email_data));
            }
        } else {
            $this->session->set_flashdata('error', trans("msg_error"));
        }
        redirect($this->agent->referrer());
    }

    /**
     * Reject Quote
     */
    public function reject_convert()
    {
        $id = $this->input->post('id', true);
        $convert_request = $this->convert_model->get_convert_file($id);

        if ($this->convert_model->reject_file($convert_request)) {
            //send email
            $seller = get_user($convert_request->seller_id);
            if (!empty($seller) && $this->general_settings->send_email_bidding_system == 1) {
                $email_data = array(
                    'email_type' => 'email_general',
                    'to' => $seller->email,
                    'subject' => trans("quote_request"),
                    'email_content' => trans("your_quote_rejected") . "<br>" . trans("quote") . ": " . "<strong>#" . $convert_request->id . "</strong>",
                    'email_link' => generate_dash_url("quote_requests"),
                    'email_button_text' => trans("view_details")
                );
                $this->session->set_userdata('mds_send_email_data', json_encode($email_data));
            }
        } else {
            $this->session->set_flashdata('error', trans("msg_error"));
        }
        redirect($this->agent->referrer());
    }

    /**
     * Quote Requests
     */
    public function convert_files_requests()
    {

        $data["user"] = $this->auth_user;
        $data['title'] = trans("convert_files");
        $data['description'] = trans("convert_files") . " - " . $this->app_name;
        $data['keywords'] = trans("convert_files") . "," . $this->app_name;
        
        $data['num_rows'] = $this->convert_model->get_convert_files_requests_count($this->auth_user->id);

        $pagination = $this->paginate(generate_url("convert_files_requests"), $data['num_rows'], $this->rows_per_page);
        $data['convert_files_requests'] = $this->convert_model->get_paginated_convert_files_requests($this->auth_user->id, $pagination['per_page'], $pagination['offset']);



        $this->load->view('partials/_header', $data);
        $this->load->view('convert/convert_files_requests', $data);
        $this->load->view('partials/_footer');

    }


    /**
     * Delete Quote Request
     */
    public function delete_quote_request()
    {
        $id = $this->input->post('id', true);
        $this->bidding_model->delete_quote_request($id);
        $this->bidding_model->delete_quote_request_if_both_deleted($id);
    }
}
