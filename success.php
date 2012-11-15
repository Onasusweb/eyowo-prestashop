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
$total = $cart->getOrderTotal(true, Cart::BOTH);

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

        if($response->STATUS == 'Approved'){
            $db = Db::getInstance(); 
            $query = "UPDATE `"._DB_PREFIX_."order_eyowo_ref` SET `status` = 1, `comment` =  '".$comment."' WHERE `ref` = '".$ref."'";
            $db->Execute($query);
            
            $history = new OrderHistory();
            $history->id_order = $id_order;
            $history->changeIdOrderState(Configuration::get('PS_OS_PAYMENT'), $id_order);
            $history->addWithemail(true, array());
        }else{
            Tools::redirectLink(__PS_BASE_URI__.'modules/eyowo/fail.php?transactionref='.$ref);
        }
    }
    Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)($cart->id).'&id_module='.(int)$eyowo->id.'&id_order='.(int)$id_order.'&ref='.$ref);
    
}else{
     Tools::redirectLink(__PS_BASE_URI__.'order.php');
}

include_once(dirname(__FILE__).'/../../footer.php');
?>