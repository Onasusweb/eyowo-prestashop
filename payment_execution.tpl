{capture name=path}{l s='Eyowo' mod='eyowo'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order summation' mod='eyowo'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<h3>{l s='Interswitch, Visa, MasterCard & eTransact Card (Eyowo)' mod='eyowo'}</h3>

<form id="payment" action="{$gw}" method="post">
    <input type="hidden" name="eyw_walletcode" value="{$wallet}" />
    <input type="hidden" name="eyw_transactionref" value="{$ref}" />
    <input type="hidden" name="eyw_item_name_1" value="{$item_name}" />
    <input type="hidden" name="eyw_item_price_1" value="{$total*100}" />
    <input type="hidden" name="eyw_item_description_1" value="{$item_description}" />

    <input type="hidden" name="confirm" value="1" />
    <p>
            <img src="{$this_path}interswitch.jpg" alt="{l s='Interswitch, Visa, MasterCard & eTransact Card (Eyowo)' mod='eyowo'}" style="float:left; margin: 0px 10px 5px 0px;" />
            {l s='You have chosen the Interswitch, Visa, MasterCard & eTransact Card (Eyowo) method' mod='eyowo'}
            <br/><br />
            {l s='The total amount of your order is' mod='eyowo'}
            <span id="amount_{$currencies.0.id_currency}" class="price">{convertPrice price=$total}</span>
            {if $use_taxes == 1}
                {l s='(tax incl.)' mod='eyowo'}
            {/if}
    </p>
    <p>
            <br />
            <b>{l s='Please confirm your order by clicking \'I confirm my order\', you will be redirected to Eyowos Secure Payment Gateway to complete your transaction' mod='eyowo'}.</b>
    </p>
    <p class="cart_navigation">
            <a href="{$link->getPageLink('order.php', true)}?step=2" class="button_large">{l s='Other payment methods' mod='eyowo'}</a>
            <a id="button-confirm" class="exclusive_large">{l s='I confirm my order' mod='eyowo'}</a>
    </p>
</form>
<link href="{$this_path}eyowo.css" rel="stylesheet" type="text/css" media="all" />
<div id="spinner" class="globalspinner" style="display: none">
    <div id="spinnerdistance"></div>
    <div id="spinnercontent">
        <img src="{$this_path}loadingAnimation.gif" mod='eyowo'" border=0 />
    </div>
</div>

<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
    $.ajax({ 
        type: 'GET',
        url: '{$this_path}place_order.php?ref={$ref}',
        beforeSend: function() {
            $("#spinner").fadeIn("slow");
        },
        complete: function() {
            $("#spinner").fadeOut("slow");
        }, 
        success: function() {
            $('#payment').submit();
        }		
    });
});
//--></script> 