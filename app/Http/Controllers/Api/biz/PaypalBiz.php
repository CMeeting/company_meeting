<?php


namespace App\Http\Controllers\Api\biz;


use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\LicenseModel;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\LicenseService;
use App\Services\UserService;
use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;

class PaypalBiz extends Controller
{
    protected $client_id;
    protected $client_secret;
    protected $accept_url;
    protected $paypal_model;
    protected $paypal;
    protected $currency = 'USD';

    public function __construct()
    {
        $this->client_id = env('PAYPAL_CLIENT_ID');
        $this->client_secret = env('PAYPAL_CLIENT_SECRET');
        $this->paypal_model = env('PAYPAL_MODEL');
        $this->paypal = new ApiContext(
            new OAuthTokenCredential(
                $this->client_id,
                $this->client_secret
            )
        );

        if($this->paypal_model != 'sandbox'){
            $this->paypal->setConfig(
                [
                    'mode' => 'live'
                ]
            );
        }
    }

    /**
     * paypal创建支付链接
     * @param $product
     * @param $price
     * @param $order_no
     * @return mixed
     */
    public function pay($product, $price, $order_no){
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName($product)->setCurrency($this->currency)->setQuantity(1)->setPrice($price);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $details = new Details();
        $details->setShipping(0)->setSubtotal($price);

        $amount = new Amount();
        $amount->setCurrency($this->currency)->setTotal($price)->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($itemList)->setDescription('Payment Description')->setInvoiceNumber($order_no);

        $redirectUrls = new RedirectUrls();
        $serverName = 'https://' . $_SERVER['SERVER_NAME'] . '/api/paypal-callback';
        \Log::info('paypal支付重定向地址' . $serverName);
        $redirectUrls->setReturnUrl($serverName . '?success=true')->setCancelUrl($serverName . '?success=false');

        $payment = new Payment();
        $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions([$transaction]);
        try{
            $payment->create($this->paypal);
        }catch (\Exception $e){
            \Log::info('paypal创建订单失败#order_no' . $order_no, [$e->getMessage()]);
            die;
        }

        $result['url'] = $payment->getApprovalLink();
        $result['id'] = $payment->getId();

        \Log::info('paypal创建订单成功', ['order_no'=>$order_no, 'id'=>$result['id'], 'url'=>$result['url']]);
        return $result;
    }

    /**
     * paypal重定向方法
     * @param $paymentId
     * @param $payerId
     */
    public function callBack($paymentId, $payerId){
        $apiContext = $this->paypal;

        $payment = Payment::get($paymentId, $apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);
        try {
            $result = $payment->execute($execution, $apiContext);
            \Log::info('paypal支付成功', [$result->toArray()]);
            Payment::get($paymentId, $apiContext);
        } catch (\Exception $ex) {
            \Log::info('paypal支付失败', ['payment_id'=>$paymentId, 'result'=>$result->toArray(), 'message'=>$ex->getMessage()]);
        }
    }
}