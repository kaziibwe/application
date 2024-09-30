<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customemailandsmsnotifications_model extends CI_Model {
	
	protected $table = '';
    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix().'custom_templates';
    }

    public function get($id = '',$where=[])
    {
        if(!empty($where) || $where != ''){
            $this->db->where($where);
        }

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get($this->table)->row();
        }
        return $this->db->get($this->table)->result_array();
    }

    public function add($data){
    	$this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
	
    public function sendMail($request) {

        if (!has_permission('customemailandsmsnotifications', '', 'create')) {
            access_denied(_l('sms_title'));
        }

        if($request['customer_or_leads'] == "customers"){

            $to =  $this->db->select('tblcontacts.*');
            $this->db->from('tblcontacts');
            $this->db->where_in('userid',$request['select_customer']);
            $this->db->where('active', '1');
            $to = $this->db->get()->result();
            
        }else{

            $to =  $this->db->select('tblleads.*');
            $this->db->from('tblleads');
            $this->db->where_in('id',$request['select_lead']);
            $to = $this->db->get()->result();

        }

        if (get_option('email_protocol') == "mail" || get_option('email_protocol') == "smtp") {

           $this->load->config('email');
            // Simulate fake template to be parsed
            $template           = new StdClass();
            $template->message  = get_option('email_header') . $request['message'] . get_option('email_footer');
            $template->fromname = get_option('companyname');
            $template->subject  = $request['subject'];

            $template = parse_email_template($template);

            hooks()->do_action('before_send_test_smtp_email');
            $this->email->initialize();
            if (get_option('mail_engine') == 'phpmailer') {
                
                $this->email->set_debug_output(function ($err) {
                    if (!isset($GLOBALS['debug'])) {
                        $GLOBALS['debug'] = '';
                    }
                    $GLOBALS['debug'] .= $err . '<br />';

                    return $err;
                });
                $this->email->set_smtp_debug(3);

            }

            $this->email->set_newline(config_item('newline'));
            $this->email->set_crlf(config_item('crlf'));

            $this->email->from(get_option('smtp_email'), $template->fromname);
            
            foreach ($to as $key => $t) {

                $template->message  = get_option('email_header') . $request['message'] . get_option('email_footer');
                $template = parse_email_template($template);

                $company =  $this->db->select('tblclients.company');
                $this->db->from('tblclients');
                $this->db->where('userid', $t->userid);
                $company = $this->db->get()->result();
                $company = $company[0]->company;

                $dynamic_fields = array('{contact_firstname}','{contact_lastname}','{client_company}');

                foreach ($dynamic_fields as $key => $dynamic_field) {
                    
                    if ( str_contains($template->message,$dynamic_field) ) {
                        
                        switch ($dynamic_field) {

                            case '{contact_firstname}':
                                $template->message = str_replace($dynamic_field,$t->firstname,$template->message);
                                break;

                            case '{contact_lastname}':
                                $template->message = str_replace($dynamic_field,$t->lastname,$template->message);
                                break;

                            case '{client_company}':
                                $template->message = str_replace($dynamic_field,$company,$template->message);
                                break;

                        }

                    }

                    if ( str_contains($template->subject,$dynamic_field) ) {
                        
                        switch ($dynamic_field) {

                            case '{contact_firstname}':
                                $template->subject = str_replace($dynamic_field,$t->firstname,$template->subject);
                                break;

                            case '{contact_lastname}':
                                $template->subject = str_replace($dynamic_field,$t->lastname,$template->subject);
                                break;

                            case '{client_company}':
                                $template->subject = str_replace($dynamic_field,$company,$template->subject);
                                break;

                        }

                    }

                }
               
                $this->email->to($t->email);

                $file_tmp  = $_FILES['file_mail']['tmp_name'];
                $file_name = $_FILES['file_mail']['name'];
               
                $this->email->attach($file_tmp,'attachment', $file_name);

                $systemBCC = get_option('bcc_emails');

                if ($systemBCC != '') {
                    $this->email->bcc($systemBCC);
                }

                $this->email->subject($template->subject);
                $this->email->message($template->message);

                if ($this->email->send(true)) {
                    hooks()->do_action('smtp_test_email_success');
                    set_alert('success', _l('Message has been sent !'));

                    $activity_log_des = "Email sent to ".$t->email." , Message: ".$request['message'];

                    $data = array(
                            'description' => $activity_log_des,
                            'date' => gmdate('Y-m-d h:i:s \G\M\T'),
                            'staffid' => get_staff()->firstname." ".get_staff()->lastname,
                    );

                    $this->db->insert('tblactivity_log', $data);
                    $this->db->where('id', $request['id']);
                    $this->db->update('tblcustom_email_sms', [
                        'is_delivered' => 1,
                    ]);

                } else {

                    hooks()->do_action('smtp_test_email_failed');
                    set_alert('warning', _l('Message could not be sent!'));

                }
            }

        } else {

            $this->load->library('encryption');

            $fromPass   = $this->encryption->decrypt(get_option('smtp_password'));
            $fromMail   = get_option('smtp_email');
            $host   = get_option('smtp_host');
            $port   = get_option('smtp_port');
            $charset   = get_option('smtp_email_charset');
            $secure   = get_option('smtp_encryption');

            $emailHeader = get_option('email_header');

            $mail = new PHPMailer();

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->isSMTP();

            $mail->Host = $host;

            $mail->Port = $port;

            $mail->SMTPAuth = true;

            $mail->SMTPSecure = $secure;

            $mail->Username = $fromMail;

            $mail->Password = $fromPass;
            
            $mail->setFrom($fromMail, get_option('companyname'));

            foreach ($to as $key => $t) {

                $mail->addBCC($t->email);

                $mail->addReplyTo($fromMail);

                $file_tmp  = $_FILES['file_mail']['tmp_name'];
                $file_name = $_FILES['file_mail']['name'];
               
                $mail->AddAttachment($file_tmp, $file_name);

                $mail->isHTML(true);

                $mail->Subject = $request['subject'];

                $mail->Body = get_option('email_header')."<strong>Message</strong><br><p style='text-align:center'>".$request['message']."</p>".get_option('email_footer');

                if (!$mail->send()) {

                    set_alert('warning', _l('Message could not be sent!'));
                }
                else {
                    set_alert('success', _l('Message has been sent !'));

                    $activity_log_des = "Email sent to ".$t->email." , Message: ".$request['message'];

                    $data = array(
                            'description' => $activity_log_des,
                            'date' => gmdate('Y-m-d h:i:s \G\M\T'),
                            'staffid' => get_staff()->firstname." ".get_staff()->lastname,
                    );

                    $this->db->insert('tblactivity_log', $data);
                    $this->db->where('id', $request['id']);
                    $this->db->update('tblcustom_email_sms', [
                        'is_delivered' => 1,
                    ]);
                }
            }            
        }

    }

    public function sendSMS($request) {

        if (!has_permission('customemailandsmsnotifications', '', 'create')) {
             access_denied(_l('sms_title'));
        }

        if( $request['customer_or_leads'] == "customers") {

            $to =  $this->db->select('tblcontacts.*');
            $this->db->from('tblcontacts');
            $this->db->where_in('userid',$request['select_customer']);
            $to = $this->db->get()->result();

        } else {

            $to =  $this->db->select('tblleads.*');
            $this->db->from('tblleads');
            $this->db->where_in('id',$request['select_lead']);
            $to = $this->db->get()->result();

        }
                
        if (get_option('sms_twilio_active') == 1) {

            $this->twilioSms($request,$to);
        }
        else if (get_option('sms_clickatell_active') == 1) {

            $this->clickatellSms($request,$to);
            
        }
        else if (get_option('sms_msg91_active') == 1) {

            $this->msg91Sms($request,$to);
        }
    }   

    public function twilioSms($request,$to) {
        if (!has_permission('customemailandsmsnotifications', '', 'create')) {
             access_denied(_l('sms_title'));
        }
        $account_sid   = get_option('sms_twilio_account_sid');
        $auth_token   = get_option('sms_twilio_auth_token');
        $twilio_number   = get_option('sms_twilio_phone_number');

        $client = new Client($account_sid, $auth_token);

        foreach ($to as $key => $t) {
            $message = $client->messages->create(
                $t->phonenumber,
                array(
                    'from' => $twilio_number,
                    'body' => strip_tags($request['message'])
                )
            );

            if ($message->sid) {
                
                $activity_log_des = "SMS sent to ".$t->phonenumber." , Message: ".strip_tags($request['message']);

                $data = array(
                        'description' => $activity_log_des,
                        'date' => gmdate('Y-m-d h:i:s \G\M\T'),
                        'staffid' => get_staff()->firstname." ".get_staff()->lastname,
                );

                $this->db->insert('tblactivity_log', $data);
                $this->db->where('id', $request['id']);
                $this->db->update('tblcustom_email_sms', [
                    'is_delivered' => 1,
                ]);
                
                set_alert('success', _l('Message has been sent !'));
            }
            else {

                set_alert('warning', _l('Message could not be sent!'));
            }
        }

    }

    public function msg91Sms($request,$to) {
        
        foreach ($to as $key => $t) {

            $mobileNumber = $t->phonenumber;
            $message = urlencode(strip_tags($request['message']));

            if($this->sms_msg91->send($mobileNumber, $message)){
                
                $activity_log_des = "SMS sent to ".$t->phonenumber." , Message: ".strip_tags($request['message']);

                $data = array(
                        'description' => $activity_log_des,
                        'date' => gmdate('Y-m-d h:i:s \G\M\T'),
                        'staffid' => get_staff()->firstname." ".get_staff()->lastname,
                );

                $this->db->insert('tblactivity_log', $data);
                $this->db->where('id', $request['id']);
                $this->db->update('tblcustom_email_sms', [
                    'is_delivered' => 1,
                ]);
                set_alert('success', _l('Message has been sent !'));
            }
            else {

                set_alert('warning', _l('Message could not be sent!'));
            }
        }
    }
 
   public function clickatellSms($request, $to) {
    $clickatellApiKey = get_option('sms_clickatell_api_key');
    $clickatellApiUrl = 'https://platform.clickatell.com/messages/http/send';

    foreach ($to as $key => $t) {
        $company = $this->db->select('tblclients.company')
                            ->from('tblclients')
                            ->where('userid', $t->userid)
                            ->get()
                            ->row()
                            ->company;

        $dynamic_fields = array('{contact_firstname}', '{contact_lastname}', '{client_company}');

        foreach ($dynamic_fields as $key => $dynamic_field) {
            if (str_contains($request['message'], $dynamic_field)) {
                switch ($dynamic_field) {
                    case '{contact_firstname}' :
                        $request['message'] = str_replace($dynamic_field, $t->firstname, $request['message']);
                        break;

                    case '{contact_lastname}' :
                        $request['message'] = str_replace($dynamic_field, $t->lastname, $request['message']);
                        break;

                    case '{client_company}' :
                        $request['message'] = str_replace($dynamic_field, $company, $request['message']);
                        break;
                }
            }
        }

        $apiKey = urlencode($clickatellApiKey);
        $toNumber = urlencode($t->phonenumber);
        $content = urlencode(strip_tags($request['message']));
        if($content == ''){
        $content = "Hii";
        }
        $url = "{$clickatellApiUrl}?apiKey={$apiKey}&to={$toNumber}&content={$content}";

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 202) {
                $activity_log_des = "SMS sent to {$t->phonenumber}, Message: " . strip_tags($request['message']);
                $data = array(
                    'description' => $activity_log_des,
                    'date' => gmdate('Y-m-d h:i:s \G\M\T'),
                    'staffid' => get_staff()->firstname . " " . get_staff()->lastname,
                );

                $this->db->insert('tblactivity_log', $data);
                $this->db->where('id', $request['id']);
                $this->db->update('tblcustom_email_sms', [
                    'is_delivered' => 1,
                ]);
                set_alert('success', _l('Message has been sent!'));
            } else {
                set_alert('warning', _l('Message could not be sent!'));
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
            set_alert('warning', _l('Message could not be sent!'));
        }
    }
}
}

/* End of file Customemailandsmsnotifications_model.php */
