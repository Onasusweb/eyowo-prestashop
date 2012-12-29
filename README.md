Eyowo Prestashop Module
=======================

Eyowo (https://www.eyowo.com/) Prestashop (http://www.prestashop.com/) Module allows you to accept credit card payments on your website easily and quickly. If you have Eyowo merchant account setup, then this module is for you. It will allow you to do auth/capture transactions within the confines of the Eyowo payment system. This module is useful for e-commerce stores in Nigeria.

Getting Started
================

All you have to do is open eyowo.php in a text editor and change the value of wallet to yours:

       class Eyowo extends PaymentModule{
        public function __construct(){
            $this->name = 'eyowo';
            $this->tab = 'payments_gateways';
            $this->version = 1.0;
            $this->author = 'Finbarrs Oketunji';
            $this->need_instance = 0;
            $this->wallet = '6I1312W';  
            $this->gw = 'https://www.eyowo.com/gateway/pay';

License
================

Copyright (c) 2011 Finbarrs Oketunji
