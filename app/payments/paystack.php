<?php
use Tygh\Registry;

class cs_cart_paystack_plugin_tracker {
  var $public_key;
  var $plugin_name;
  function __construct($plugin, $pk){
      //configure plugin name
      //configure public key
      $this->plugin_name = $plugin;
      $this->public_key = $pk;
  }

 

  function log_transaction_success($trx_ref){
      //send reference to logger along with plugin name and public key
      $url = "https://plugin-tracker.paystackintegrations.com/log/charge_success";

      $fields = array(
          'plugin_name'  => $this->plugin_name,
          'transaction_reference' => $trx_ref,
          'public_key' => $this->public_key
      );

      $fields_string = http_build_query($fields);

      $ch = curl_init();

      curl_setopt($ch,CURLOPT_URL, $url);
      curl_setopt($ch,CURLOPT_POST, true);
      curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

      //execute post
      $result = curl_exec($ch);
      //  echo $result;
  }
}

function fn_paystack_adjust_amount($price, $payment_currency){
    $currencies = Registry::get('currencies');

    if (array_key_exists($payment_currency, $currencies)) {
        if ($currencies[$payment_currency]['is_primary'] != 'Y') {
            $price = fn_format_price($price / $currencies[$payment_currency]['coefficient']);
        }
    } else {
        return false;
    }

    return $price;
}

function fn_paystack_place_order($original_order_id){
    $cart = & $_SESSION['cart'];
    $auth = & $_SESSION['auth'];

    list($order_id, $process_payment) = fn_place_order($cart, $auth);

    $data = array (
        'order_id' => $order_id,
        'type' => 'S',
        'data' => TIME,
    );
    db_query('REPLACE INTO ?:order_data ?e', $data);

    $data = array (
        'order_id' => $order_id,
        'type' => 'E', // extra order ID
        'data' => $original_order_id,
    );
    db_query('REPLACE INTO ?:order_data ?e', $data);

    return $order_id;
}


if (!defined('BOOTSTRAP')) { die('Access denied'); }

// Return from payment
if (defined('PAYMENT_NOTIFICATION')) {
    if ($mode == 'return' && !empty($_REQUEST['merchant_order_id'])) {
        if (isset($view) === false){
            $view = Registry::get('view');
        }

        $view->assign('order_action', __('placing_order'));
        $view->display('views/orders/components/placing_order.tpl');
        fn_flush();
        $code = $_REQUEST['merchant_order_id'];
        $merchant_order_id = fn_paystack_place_order($_REQUEST['merchant_order_id']);
        $amount = $_REQUEST['amount'];

        if(!empty($merchant_order_id) and !empty($amount)){
          if (fn_check_payment_script('paystack.php', $merchant_order_id, $processor_data)) {
                $mode = $processor_data['processor_params']['paystack_mode'];
                if ($mode == 'test') {
                  $key  = $processor_data['processor_params']['paystack_tsk'];
                }else{
                  $key  = $processor_data['processor_params']['paystack_lsk'];
                }
                // $key_secret = $processor_data['processor_params']['key_secret'];
                $order_info = fn_get_order_info($merchant_order_id);

                $pp_response = array();
                $success = false;
                $error = "";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"https://api.paystack.co/transaction/verify/".$code);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $headers = [
                    'Authorization: Bearer '.$key,
                ];

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $response = curl_exec($ch);
                $verification = json_decode($response);
                if (curl_errno($ch)) {   // should be 0
                    // curl ended with an error
                    $cerr = curl_error($ch);
                    curl_close($ch);
                    throw new Exception("Curl failed with response: '" . $cerr . "'.");
                }
                curl_close ($ch);
                if(($verification->status===false) || (!property_exists($verification, 'data')) || ($verification->data->status !== 'success')){
                  $success = false;
                  $error = "";
                }else{
                  if ($amount == ($verification->data->amount/100)) {
                    $success = true;
                  }else{
                    $success = false;
                    $error = "Invalid Amount";
                  }
                }


                if($success === true){

                  //PSTK - Logger
                  $mode = $processor_data['processor_params']['paystack_mode'];
                  if ($mode == 'test') {
                    $pk  = $processor_data['processor_params']['paystack_tpk'];
                  }else{
                    $pk  = $processor_data['processor_params']['paystack_lpk'];
                  }
                  $pstk_logger = new cs_cart_paystack_plugin_tracker('cs-cart', $pk);
                  $pstk_logger->log_transaction_success($code);

                  //

                    $pp_response['order_status'] = 'P';
                    $pp_response['transaction_id'] = $code;
                    $pp_response['Status'] = 'Payment Successful';
                    fn_finish_payment($merchant_order_id, $pp_response);
                    fn_order_placement_routines('route', $merchant_order_id);
                }else {
                    $pp_response['order_status'] = 'O';
                    $pp_response['transaction_id'] = $code;
                    $pp_response['Status'] = 'Payment Failed';

                    fn_finish_payment($merchant_order_id, $pp_response);
                    fn_set_notification('E', __('error'), 'Payment Failed #'.$code);
                    fn_order_placement_routines('checkout_redirect');
                }

            }
        }
        else {
            fn_set_notification('E', __('error'), 'Payment Unsuccessful'.$_REQUEST['merchant_order_id']);
            fn_order_placement_routines('checkout_redirect');
        }
    }
    exit;
}else {


    //$url = fn_url("payment_notification.return?payment=paystack", BOOTSTRAP, 'current');
    $url = fn_url("payment_notification.return?payment=paystack");
    $currency = fn_get_secondary_currency();
    $maintotal = $order_info['total']+$order_info['payment_surcharge'];
    $mode = $processor_data['processor_params']['paystack_mode'];
    if ($mode == 'test') {
      $key  = $processor_data['processor_params']['paystack_tpk'];
    }else{
      $key  = $processor_data['processor_params']['paystack_lpk'];
    }
    $html = '
          <form action="'.$url.'" method="POST" target="_parent">
            <input type="hidden" name="paystack_payment_id" id="paystack_payment_id" />
            <input type="hidden" name="merchant_order_id" id="order_id" value="'.$order_id.'"/>
            <input type="hidden" name="amount" value="'.$maintotal.'"/>
            <script
              src="https://js.paystack.co/v1/inline.js"
              data-key="'.$key.'"
              data-email="'.$order_info['email'].'"
              data-currency= "'.$currency.'";
              data-amount="'.($maintotal*100).'"
              data-ref="'.$order_id.'" 
              data-metadata="'.`{"custom_fields": [{"display_name": "Plugin","variable_name": "plugin","value": "cs-cart"}]}`.'"
            >
            </script>
          </form>';

echo <<<EOT
    {$html}
</body>
</html>
EOT;
exit;
}

?>
