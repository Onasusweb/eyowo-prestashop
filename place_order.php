<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/eyowo.php');

$eyowo = new eyowo();

$ref = $_GET['ref'];
$status = 0;
$id_cart = $cart->id;
$comment = 'Forwarded to Eyowo for Payment';

$customer = new Customer((int)$cart->id_customer);
$total = $cart->getOrderTotal(true, Cart::BOTH);

$eyowo->validateOrder((int)$cart->id, Configuration::get('PS_OS_PENDING'), $total, $eyowo->displayName, NULL, array(), NULL, false, $customer->secure_key);
$order = new Order((int)$eyowo->currentOrder);

$db = Db::getInstance(); 
$query = "INSERT INTO `"._DB_PREFIX_."order_eyowo_ref` (`id`, `id_cart`, `ref`, `status`, `comment`) 
        VALUES ('".NULL."', '".$id_cart."', '".$ref."', '".$status."', '".$comment."')";

$db->Execute($query);

include_once(dirname(__FILE__).'/../../footer.php');
?>