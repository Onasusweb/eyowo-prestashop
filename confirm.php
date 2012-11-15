<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/eyowo.php');

if (!$cookie->isLogged())
    Tools::redirect('authentication.php?back=order.php');

$eyowo = new eyowo();

if(!$cart->id)
    Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$eyowo->active)
    Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

echo $eyowo->execPayment($cart);
 
include_once(dirname(__FILE__).'/../../footer.php');
?>