<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CISendEmail
{
    public function __construct()
    {
		$this->CI =& get_instance();
    }

    public function send_email($username, $to, $subject, $message)
    {
        $this->CI->load->library('email');
        
        $config['protocol']    = 'smtp';
    
        $config['smtp_host']    = 'ssl://smtp.gmail.com';
        $config['smtp_port']    = '465';
        
        $config['smtp_crypto'] = 'tls';
        $config['smtp_host'] = 'smtp.gmail.com';    
        $config['smtp_port'] = '587';
        
        $config['smtp_timeout'] = '7';
        $sender_email = "excelsportdata@gmail.com";
        $config['smtp_user']    = $sender_email;
        $config['smtp_pass']    = 'GLORY95200?!a';
        $config['charset']    = 'utf-8';
        $config['newline']    = "\r\n";
        $config['crlf'] = "\r\n";
        $config['wordwrap']     = TRUE;
        $config['mailtype'] = 'html'; // or text
        $config['validation'] = TRUE; // bool whether to validate email or not      

        $this->CI->email->initialize($config);

        $this->CI->email->from($sender_email, $username);
        $this->CI->email->to($to); 
        $this->CI->email->subject($subject);
        $this->CI->email->message($message);    
        $result = false;
        try { 
          if($this->CI->email->send()) {
            $result = true;
          }
        } catch (Exception $e) {
          //alert the user.
        }
        return $result;
    }

}
