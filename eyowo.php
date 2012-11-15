<?php
    if ( !defined( '_PS_VERSION_' ) )
    exit;

    class Eyowo extends PaymentModule{
        public function __construct(){
            $this->name = 'eyowo';
            $this->tab = 'payments_gateways';
            $this->version = 1.0;
            $this->author = 'Finbarrs Oketunji';
            $this->need_instance = 0;
            $this->wallet = '6I1316Q';
            $this->gw = 'https://www.eyowo.com/gateway/pay';

            parent::__construct();

            $this->displayName = $this->l('Eyowo');
            $this->description = $this->l('Accept Interswitch, Visa, MasterCard & eTransact Cards.');
        }

        public function install(){
            if (parent::install() == false OR !$this->createEyowotbl() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn') OR !$this->registerHook('invoice') OR !$this->registerHook('PDFInvoice'))
                return false;
            return true;
        }
                
        public function uninstall(){
            if ( !parent::uninstall() )
            Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'eyowo`');
            parent::uninstall();
        }
        
        function createEyowotbl(){
            $db = Db::getInstance(); 
            $query = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."order_eyowo_ref` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `id_cart` INT NOT NULL ,
            `ref` VARCHAR(50) NOT NULL ,
            `status` INT NOT NULL ,
            `comment` TEXT NOT NULL ,
            KEY `ref` (`ref`)
            ) ENGINE = MYISAM ";

            $db->Execute($query);
            
            if (!Configuration::get('PS_OS_PENDING'))
		{
			$orderState = new OrderState();
			$orderState->name = array();
			foreach (Language::getLanguages() AS $language)
			{
                            $orderState->name[$language['id_lang']] = 'Pending';
			}
			$orderState->send_email = false;
			$orderState->color = '#dad0ff';
			$orderState->hidden = false;
			$orderState->delivery = false;
			$orderState->logable = true;
			$orderState->invoice = false;
                        
			if ($orderState->add())
				copy(dirname(__FILE__).'/pending.gif', dirname(__FILE__).'/../../img/os/'.(int)$orderState->id.'.gif');
			Configuration::updateValue('PS_OS_PENDING', (int)$orderState->id);
		}
                

            return true;
        }
        
        public function hookPayment($params){
            if (!$this->active)
                return ;
            global $smarty;

            $smarty->assign(array(
                'this_path' => $this->_path,
                'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
            ));

            return $this->display(__FILE__, 'eyowo.tpl');
        }
        
        public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;
                global $smarty;
                
                $ref = $_GET['ref'];
                
                $response = $this->get_url_contents($ref);
                $response = json_decode($response);
                $comment = $response->STATUS.': '.$response->STATUSREASON;
                
                if($response->STATUS == 'Aborted'){
                    $comment = $response->STATUS.': Transaction Aborted';
                }

                $smarty->assign(array(
                    'comment' => $comment,
                ));
                
                if($response->STATUS == 'Approved'){
                    return $this->display(__FILE__, 'success.tpl');
                }else{
                    return $this->display(__FILE__, 'fail.tpl');
                }
	}
        
        function hookPDFInvoice($params)
	{
            $id_order = $params['id_order'];
            $order = new Order((int)($id_order));
            $paymentdetails = Db::getInstance()->getRow('
                        SELECT * 
                        FROM `'._DB_PREFIX_.'order_eyowo_ref` 
                        WHERE `id_cart` = \''.$order->id_cart.'\'');
            
            if($paymentdetails){
                $params['pdf']->SetFont('helvetica', '', 8);
                $params['pdf']->SetXY(10, 17);
                $params['pdf']->Cell(0, 5, strtoupper($this->displayName).' REF # : '.$paymentdetails['ref'], 0, NULL, 'R');
            }
            
            return $pdf;
	}
        
        function hookInvoice($params)
	{
            global $smarty;

            $id_order = $params['id_order'];
            $order = new Order((int)($id_order));
            $paymentdetails = Db::getInstance()->getRow('
                        SELECT * 
                        FROM `'._DB_PREFIX_.'order_eyowo_ref` 
                        WHERE `id_cart` = \''.$order->id_cart.'\'');

             if($paymentdetails){
                $smarty->assign(array(
                    'paymentdetails'    => $paymentdetails,
                    'this_path'         => $this->_path,
                    ));
                return $this->display(__FILE__, 'invoice_block.tpl');
             }
 
	}
        
        
        public function execPayment($cart){
            if (!$this->active)
                return ;

            global $cookie, $smarty;
            
            $customer = new Customer((int)$cart->id_customer);
            $total = $cart->getOrderTotal(true, Cart::BOTH);
            $ref = strtoupper(uniqid()).'-'.$cart->id_customer;
            $item_name = Configuration::get('PS_SHOP_NAME');
            $item_description = 'Order #'.$cart->id.' From '.$customer->firstname.' ('.$customer->email.')';

            $smarty->assign(array(
                'total' => $total,
                'wallet' => $this->wallet,
                'gw' => $this->gw,
                'ref' => $ref,
                'item_name' => $item_name,
                'item_description' => $item_description,
                'this_path' => $this->_path,
                'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/'
            ));

            return $this->display(__FILE__, 'payment_execution.tpl');
        }
        
         public function get_url_contents($ref){
            $url = 'https://www.eyowo.com/api/gettransactionstatus?format=json&walletcode='.$this->wallet.'&transactionref='.$ref; 
            
            $ch = curl_init();
            $timeout = 0; // set to zero for no timeout
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
            $file_contents = curl_exec($ch);
            curl_close($ch);

            // return the results
            return $file_contents;
        }
        
    }
?>