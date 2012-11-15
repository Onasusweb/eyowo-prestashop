Eyowo Prestashop Module
=======================

Eyowo Prestashop Module allows you to accept credit card payments on your website easily and quickly. If you have Eyowo merchant account setup, then this module is for you. It will allow you to do auth/capture transactions within the confines of the Eyowo payment system.

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
            $this->wallet = '6I1316Q';  
            $this->gw = 'https://www.eyowo.com/gateway/pay';

License
================

(The MIT License)

Copyright (c) 2012 Finbarrs Oketunji

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the 'Software'), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.