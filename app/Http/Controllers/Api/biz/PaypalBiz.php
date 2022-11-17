<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2019/7/23
 * Time: 15:27
 */

namespace app\api\biz;


use core\helper\JsonHelper;
use core\helper\LogHelper;
use core\helper\SysHelper;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use ResultPrinter;
use think\Exception;
use think\facade\Log;

require_once dirname(dirname(dirname(__DIR__))) . '/vendor/paypal/rest-api-sdk-php/sample/common.php';

class PaypalBiz
{
    public $apiContext;

    /**
     * @return ApiContext
     */
    public static function paypal()
    {
        $clientId = SysHelper::getEnv('paypal')['client_id'];
        $secret = SysHelper::getEnv('paypal')['secret'];
        $paypal_mode = SysHelper::getEnv('paypal')['paypal_mode'];
        $mode = $paypal_mode == 'production' ? 'live' : 'sandbox';
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $secret
            )
        );
        $apiContext->setConfig(array('mode' => $mode, 'log.LogEnabled' => false, // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS 'cache.enabled' => true,
            )
        );
        return $apiContext;
    }

    /**
     * @param $paymentId
     * @return Payment
     */
    public static function findByPaymentId($paymentId)
    {
        try {
            $payment = Payment::get($paymentId, self::paypal());
        } catch (Exception $ex) {
            ResultPrinter::printError("Get Payment", "Payment", null, null, $ex);
        }
        return $payment;
    }

    public static function close(){
        $apiContext = self::paypal();
        $payment = new \PayPal\Api\Payment();
        $transactions = $payment->getTransactions();
        $transaction = $transactions[0];

        $relatedResources = $transaction->getRelatedResources();
        $relatedResource = $relatedResources[0];
        $order = $relatedResource->getOrder();
        try {
            $result = $order->void($apiContext);
            ResultPrinter::printResult("Voided Order", "Order", $result->getId(), null, $result);
        } catch (Exception $ex) {
            ResultPrinter::printError("Voided Order", "Order", null, null, $ex);
            exit(1);
        }

        return $result;
    }

    /**
     * @param $data
     * @param $notifyUrl
     * @return null|string
     * 处理paypal支付 */
    public static function getPaypalData($data, $notifyUrl, $order_no,$lang)
    {
        $baseUrl = getBaseUrl();
        $payer = new Payer();
        $payer->setPaymentMethod("paypal"); //支付信息

        $item = new Item();
        $item->setName($data['product']['name'])->setCurrency('USD')->setQuantity($data['amount'])->setSku(' '.$data['product']['id'])->setPrice($data['product']['price']);

        $itemList = new ItemList();
        if (!empty($data['coupon_price'])) {
            $item2 = new Item();
            $item2->setName("Coupon Discount")->setCurrency('USD')->setQuantity(1)->setSku("Coupon Discount")->setPrice(-$data['coupon_price']);
            $itemList->setItems([$item, $item2]);
        } else {
            $itemList->setItems([$item]);
        }
        $details = new Details();
        $details->setShipping(0)->setTax(0)->setSubtotal($data['price']);
        $amount = new Amount();
        $amount->setCurrency('USD')->setTotal($data['price'])->setDetails($details);

        //发起交易
        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($itemList)->setDescription("Payment description")->setNotifyUrl($notifyUrl)->setInvoiceNumber($order_no);

        if(!empty($lang)) $lang = '&lang='.$lang;
        //跳转地址
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("$baseUrl/api/subscriptions/payed_callback?success=true".$lang)->setCancelUrl("$baseUrl/api/subscriptions/payed_callback?success=false");

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent("sale")->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions([$transaction]);
        $apiContext = self::paypal();
        try {
            $createdPayment = $payment->create($apiContext);
        } catch (PayPalConnectionException $e) {
            LogHelper::logSubs('paypal error' . $e->getMessage() . '---' . $e->getData());
            error('paypal error', empty($e->getData()) ? 'paypal response null' : 'paypal response ' . $e->getData(), '400');
        }
        //web端直接将 links中的 approval_uri返回给web端 web端直接进行跳转即可 app端需要将$payment的值返回---
        $result['url'] = $payment->getApprovalLink();
        $result['id'] = $payment->getId();
        return $result;
    }


}