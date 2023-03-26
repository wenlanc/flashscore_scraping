<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Checkout extends CI_Controller {
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('url_helper');
    $this->load->library(array('session','ShoppingCart','CISendEmail'));
    $this->load->model(array('Public_model','Auth_model'));  
  }
  public function index()
  {
    $user = $this->session->userdata('logged_data'); // role_id = 1
    if( !isset($user))
      redirect( base_url().'user/login');
      
    $data['title'] = "CHECKOUT";
    $data['description'] = "";
    $data['navigation'] = "";
    $user = $this->session->userdata('logged_data');
    $data['user'] = $this->Auth_model->getUserInfoFromID($user["userid"]);
    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('common/sub_header', $data);
    $this->load->view('checkout');
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');
  }

  public function stripePost()
    {
        $user = $this->session->userdata('logged_data'); // role_id = 1
        if( !isset($user))
          redirect( base_url().'user/login');
          
        require_once APPPATH.'third_party/stripe/lib/Stripe.php';
        require_once APPPATH."third_party/stripe/config.php";
     
        if ($_POST) {
          \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
      
          try {
              //if (empty($_POST['street']) || empty($_POST['city']) || empty($_POST['zip']))
              //    throw new Exception("Fill out all required fields.");
              if (!isset($_POST['stripeToken']))
              {
                  //throw new Exception("The Stripe Token was not generated correctly");
                  $successMessage = "Failed on checkout!";
                  $this->session->set_flashdata('checkoutError', $successMessage);
                  redirect("checkout/index");
              } else {
                $country_name = $this->session->userdata("logged_data")["country_name"];
                $itemName = 'Data of sport game.';
                $itemAmount = $this->shoppingcart->getTotalCart($country_name);
                $itemAmount = $itemAmount * 1; 
                $result = \Stripe\Charge::create([
                    "amount" => $itemAmount*100,         // $_POST['donateValue']*100 ,
                    "currency" => "eur",    //$_POST['currency_code'],
                    "source"   => $_POST['stripeToken'], // obtained with Stripe.js
                    "description" => $itemName  //$_POST['email']  // $_POST['item_name'], $_POST['item_number']
                ]);

                $stripeResponse = $result->jsonSerialize();
                $amount = $stripeResponse["amount"] /100;
                if ($stripeResponse['amount_refunded'] == 0 && empty($stripeResponse['failure_code']) && $stripeResponse['paid'] == 1 && $stripeResponse['captured'] == 1 && $stripeResponse['status'] == 'succeeded') {
                    $customer_id = $this->Public_model->setCustomer($_POST);
                    $_POST["customer_id"] = $customer_id;
                    $_POST['paid_amount'] = $amount;
                    $_POST['payment_type'] = "CreditCard";
                    $_POST['proccessed'] = 1;
                    $_POST['tax'] = $this->shoppingcart->getTax($country_name);
                    $_POST['user_id']    = $this->session->userdata("logged_data")["userid"];
                    $order_id = $this->Public_model->setOrder($_POST);
                    $successMessage = "Successfully paid!";
                    
                    // Email sending...
                    $data=array();
                    $data["username"] = $this->session->userdata("logged_data")["username"];
                    $data['email'] = $this->session->userdata("logged_data")["email"];
                    $data["download_link"] = base_url()."myaccount/download";
                    $data["invoice_url"] = base_url()."myaccount/invoice";
                    $mesg = $this->load->view('email/payment_success',$data,true);
                    $this->cisendemail->send_email($data["username"], $data['email'], "excelsportdata.com", $mesg);
                    
                    // Clear shoppingcart
                    unset($_SESSION['shopping_cart']);
                    @delete_cookie('shopping_cart');
                    $this->session->set_flashdata('checkoutError', $successMessage);
                    redirect("download/index");
                }else{
                    $successMessage = "Failed";
                    $this->session->set_flashdata('checkoutError', $successMessage);
                    redirect("checkout/index");
                  }
                }
              }
              catch (Exception $e) {
                  $successMessage = "Failed";
                  $this->session->set_flashdata('checkoutError', $successMessage);
                  redirect("checkout/index");
              }
        }
    }

    public function paypalPost()
    {
        $user = $this->session->userdata('logged_data'); // role_id = 1
        if( !isset($user))
          redirect( base_url().'user/login');
          
        if(isset($_POST["email"]))
        {
            $enableSandbox = false;
            // PayPal settings. Change these to your account details and the relevant URLs
            // for your site.
            $paypalConfig = [
                'email' => "ebuildix@gmail.com", // receiving paypal account
                'return_url' => base_url().'checkout/paypal_successful',
                'cancel_url' => base_url().'checkout/paypal_cancelled',
                'notify_url' => base_url().'checkout/paypal_notify'
            ];
            $paypalUrl = $enableSandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
            // Product being purchased.
            $cartDetails = $this->shoppingcart->getCartItems();
            $country_name = $this->session->userdata("logged_data")["country_name"];
            $itemName = 'Data of sport game.';
            $itemAmount = $this->shoppingcart->getTotalCart($country_name);
            $itemAmount = $itemAmount * 1; 
             //$itemAmount = 0.1;
            // Set the PayPal account.
            $data['business'] = $paypalConfig['email'];
            // Set the PayPal return addresses.
            $data['return'] = stripslashes($paypalConfig['return_url']);
            $data['cancel_return'] = stripslashes($paypalConfig['cancel_url']);
            $data['notify_url'] = stripslashes($paypalConfig['notify_url']);
            $data['rm'] = '2';
            // Set the details about the product being purchased, including the amount
            // and currency so that these aren't overridden by the form data.
            $data['item_name'] = $itemName; // payment subject
            $data['item_number'] = ''; //$id; // payment subject
            $data['amount'] = $itemAmount;
            $data['currency_code'] = 'EUR';
            $data['payer_email'] = $_POST["email"];
            $data['cmd'] = '_xclick'; // _xclick,_cart,_donations
            // Add any custom fields for the query string.
            $custom_data = array();
            $custom_data['custom_name'] = $_POST['firstname'];
            $custom_data['custom_last_name'] = $_POST['lastname'];
            $custom_data['custom_address'] = $_POST['billingaddress'];
            $custom_data['custom_city'] = $_POST['city'];
            $custom_data['custom_country'] = $_POST['country'];
            $custom_data['custom_zipcode'] = $_POST['zipcode'];
            $data['custom'] = json_encode($custom_data);
            
            $input = array(
                'email' => trim($data['payer_email']),
                'first_name'  => $custom_data['custom_name'],
                'last_name'  => $custom_data['custom_last_name'],
                'town_city'  => $custom_data['custom_city'],
                'country'  => $custom_data['custom_country'],
                'address'  => $custom_data['custom_address'],
                'zip_code'  => $custom_data['custom_zipcode'],
                'register_date'  => date('Y-m-d H:i:s'),
                'role_id' => 2,
            );
            if (!$this->db->insert('user', $input)) {
                //log_message('error', print_r($this->db->error(), true));
                //show_error(lang('database_error'));
            }
            $customer_id = $this->db->insert_id();

            $input = array(
                'user_id'   =>  $this->session->userdata("logged_data")["userid"], 
                'customer_id' => $customer_id,
                'paid_amount' => 0,
                'proccessed' => 0,
                'payment_type' => "Paypal",
                'tax'          => $this->shoppingcart->getTax($country_name),
            );
           
            $order_id = $this->Public_model->setOrder($input);
            $data['item_number'] = $order_id; //$id; // payment subject
      
            // Build the query string from the data.
            $queryString = http_build_query($data);
      
            // Redirect to paypal IPN
            header('location:' . $paypalUrl . '?' . $queryString);
            exit();
        }
    }
    public function paypal_successful()
    {
    
       // Clear shoppingcart
       unset($_SESSION['shopping_cart']);
       @delete_cookie('shopping_cart');
              
        $result = "Successfully paid via PayPal.";
        $method = 'paypal';
        $this->session->set_flashdata('checkoutError', $result);
        //redirect(base_url()."checkout/index");
        redirect(base_url()."download/index");
    }
    public function paypal_cancelled()
    {
        $result = "Payment via PayPal cancelled.";
        $method = 'paypal';
        $this->session->set_flashdata('checkoutError', $result);
        redirect(base_url()."checkout/index");
    }
    public function paypal_notify()
    {
       //log_message('debug', json_encode($_POST), false);
        
        // Paypal response
        // {"mc_gross":"1.19","protection_eligibility":"Ineligible","address_status":"confirmed","payer_id":"3F8VTBMBNPET6","address_street":"Av. de la Pelouse, 87648672 Mayet","payment_date":"05:50:37 Sep 25, 2021 PDT","payment_status":"Pending","charset":"windows-1252","address_zip":"75002","first_name":"test","address_country_code":"FR","address_name":"test buyer","notify_version":"3.9","custom":"","payer_status":"verified","address_country":"France","address_city":"Paris","quantity":"1","verify_sign":"AycwAxu3Dcmn40VTTmFdkN5Vem32AKfVl9G9rn1cvFdWAzGActvV-Kc5","payer_email":"ebuildix-buyer@gmail.com","txn_id":"5MF90281CF1397937","payment_type":"instant","last_name":"buyer","address_state":"Alsace","receiver_email":"ebuildix@gmail.com","shipping_discount":"0.00","insurance_amount":"0.00","pending_reason":"unilateral","txn_type":"web_accept","item_name":"Data of sport game.","discount":"0.00","mc_currency":"EUR","item_number":"1234","residence_country":"FR","test_ipn":"1","shipping_method":"Default","transaction_subject":"","payment_gross":"","ipn_track_id":"c5c433ac099b0"}
      
      $rowpostdata = file_get_contents('php://input'); 
      $requestFromPaypal = $_POST;
      
      if($this->verifyTransaction($requestFromPaypal,$rowpostdata)){
        if(isset($requestFromPaypal["txn_id"]) && isset($requestFromPaypal["txn_type"]))
          {
              if (!$this->db->update('orders',array(
                'proccessed' => 1,
                'paid_amount' => $requestFromPaypal["mc_gross"], // payment_gross
                'payment_type' => "Paypal",
              //  'updated_at' => date('Y-m-d H:i:s')
              ), 'id = '.$requestFromPaypal["item_number"] )) {
                      //return -1;        
                }
          }
      }
    }

    function verifyTransaction($data, $raw_post_data) {
        
        // STEP 1: read POST data

        // Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.

        // Instead, read raw POST data from the input stream.

        $raw_post_array = explode('&', $raw_post_data);

        $myPost = array();

        foreach ($raw_post_array as $keyval) {

          $keyval = explode ('=', $keyval);

          if (count($keyval) == 2)

            $myPost[$keyval[0]] = urldecode($keyval[1]);

        }

        // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'

        $req = 'cmd=_notify-validate';

        if (function_exists('get_magic_quotes_gpc')) {

          $get_magic_quotes_exists = true;

        }

        foreach ($myPost as $key => $value) {

          if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {

            $value = urlencode(stripslashes($value));

          } else {

            $value = urlencode($value);

          }

          $req .= "&$key=$value";

        }

        $enableSandbox = false;
        $paypalUrl = $enableSandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
        
        // https://ipnpb.paypal.com/cgi-bin/webscr
        
        // Step 2: POST IPN data back to PayPal to validate

        $ch = curl_init($paypalUrl);

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

        // In wamp-like environments that do not come bundled with root authority certificates,

        // please download 'cacert.pem' from "https://curl.haxx.se/docs/caextract.html" and set

        // the directory path of the certificate as shown below:

        // curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');

        if ( !($res = curl_exec($ch)) ) {

          // error_log("Got " . curl_error($ch) . " when processing IPN data");

          curl_close($ch);

          exit;

        }
        curl_close($ch);
        // inspect IPN validation result and act accordingly

        if (strcmp ($res, "VERIFIED") == 0) {

          // The IPN is verified, process it
          return true;

        } else if (strcmp ($res, "INVALID") == 0) {

          // IPN invalid, log for manual investigation

        }


        /* return false; */ 

        $req = 'cmd=_notify-validate';
        foreach ($data as $key => $value) {
        
            //if (is_array($value)) {
            //    $paymentArray = explode(' ', $value[0]);
            //    $paymentCurrency = urlencode(stripslashes($paymentArray[0]));
            //    $paymentGross = urlencode(stripslashes($paymentArray[1]));
            //    $req .= '&mc_currency=' . $paymentCurrency . '&mc_gross=' . $paymentGross;
            //} else {
                $value = urlencode(stripslashes($value));
                $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
                $req .= "&$key=$value";
            //}
            
        }

        $enableSandbox = false;
        $paypalUrl = $enableSandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

        $ch = curl_init($paypalUrl);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $res = curl_exec($ch);

        if (!$res) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }

        $info = curl_getinfo($ch);

        // Check the http response
        $httpCode = $info['http_code'];
        if ($httpCode != 200) {
            throw new Exception("PayPal responded with http code $httpCode");
        }
        curl_close($ch);
        return $res === 'VERIFIED';
    }
}

?>