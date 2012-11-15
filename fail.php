<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/eyowo.php');

$eyowo = new eyowo();

$ref = $_GET['transactionref'];

$id_cart = Db::getInstance()->getValue('
    SELECT `id_cart` 
    FROM `'._DB_PREFIX_.'order_eyowo_ref` 
    WHERE `ref` = \''.$ref.'\'');

$cart = new Cart((int)($id_cart));
$customer = new Customer((int)$cart->id_customer);

if (Validate::isLoadedObject($cart) AND $cart->OrderExists()){
    $id_order = Db::getInstance()->getValue('
    SELECT `id_order` 
    FROM `'._DB_PREFIX_.'orders` 
    WHERE `id_cart` = \''.(int)$cart->id.'\'');
    
    $status = Db::getInstance()->getValue('
        SELECT `status` 
        FROM `'._DB_PREFIX_.'order_eyowo_ref` 
        WHERE `ref` = \''.$ref.'\'');
    
    if($status == 0){
        $response = $eyowo->get_url_contents($ref);
        $response = json_decode($response);
        $comment = $response->STATUS.': '.$response->STATUSREASON;
        
        $db = Db::getInstance(); 
        
        //Status ID's
        //Pending = 0;
        //Approved = 1;
        //Failed = 2;
        $history = new OrderHistory();
        $history->id_order = $id_order;
        
        switch ($response->STATUS){
            case 'Pending';
                break;
            case 'Aborted';
                $status_id = 2;
                $comment = $response->STATUS.': Transaction Aborted';
                $order_state = 'PS_OS_ERROR';
                $history->changeIdOrderState(Configuration::get($order_state), $id_order);
                $history->addWithemail(true, array());
                $query = "UPDATE `"._DB_PREFIX_."order_eyowo_ref` SET `status` = '".$status_id."', `comment` =  '".$comment."' WHERE `ref` = '".$ref."'";
                $db->Execute($query);
                break;
            case 'Approved';
                Tools::redirectLink(__PS_BASE_URI__.'modules/eyowo/success.php?transactionref='.$ref);
                break;
            default :
                $status_id = 2;
                $order_state = 'PS_OS_ERROR';
                $history->changeIdOrderState(Configuration::get($order_state), $id_order);
                $history->addWithemail(true, array());
                $query = "UPDATE `"._DB_PREFIX_."order_eyowo_ref` SET `status` = '".$status_id."', `comment` =  '".$comment."' WHERE `ref` = '".$ref."'";
                $db->Execute($query);
                break;
        }
    }
    Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)($cart->id).'&id_module='.(int)$eyowo->id.'&id_order='.(int)$id_order.'&ref='.$ref);
    
}else{
     Tools::redirectLink(__PS_BASE_URI__.'order.php');
}

include_once(dirname(__FILE__).'/../../footer.php');
?>