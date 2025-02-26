<?php

namespace Mark\PlisioPayment\Controller\Public;

use XF\Mvc\ParameterBag;
use XF\Mvc\Controller;

class Payment extends Controller
{
    public function actionIndex(ParameterBag $params)
    {
        // Display the payment selection form
        $viewParams = [];
        return $this->view('Mark\PlisioPayment:Payment\Select', 'plisio_payment_select', $viewParams);
    }
    
    public function actionProcess(ParameterBag $params)
    {
        $this->assertPostOnly();
        $input = $this->filter([
            'coin'     => 'str',
            'network'  => 'str',
            'amount'   => 'float'
        ]);

        // Use the Plisio service to create a payment session
        $plisioService = $this->app()->service('Mark\PlisioPayment:Plisio');
        $result = $plisioService->createPaymentSession($input['coin'], $input['network'], $input['amount'], $this->visitor()->user_id);

        if (!$result || isset($result['error'])) {
            return $this->error('Unable to create payment session. Please try again.');
        }

        // Save payment details to the database
        $dw = $this->em()->create('Mark\PlisioPayment:Payment');
        $dw->bulkSet([
            'user_id'         => $this->visitor()->user_id,
            'coin'            => $input['coin'],
            'network'         => $input['network'],
            'amount'          => $input['amount'],
            'payment_address' => $result['payment_address'],
            'qr_code'         => $result['qr_code'],
            'status'          => 'pending',
            'expires_at'      => time() + (int) \XF::options()->plisioPaymentExpiration,
            'created_at'      => time()
        ]);
        $dw->save();

        $viewParams = [
            'payment'    => $dw->getMergedData(),
            'expiration' => \XF::options()->plisioPaymentExpiration
        ];

        return $this->view('Mark\PlisioPayment:Payment\Details', 'plisio_payment_details', $viewParams);
    }
    
    public function actionStatus(ParameterBag $params)
    {
        // Retrieve the payment status (can be used for AJAX polling)
        $paymentId = $this->filter('payment_id', 'int');
        $payment = $this->em()->find('Mark\PlisioPayment:Payment', $paymentId);
        if (!$payment) {
            return $this->error('Payment not found.');
        }
        
        // Optionally update status by calling the Plisio API
        $plisioService = $this->app()->service('Mark\PlisioPayment:Plisio');
        $status = $plisioService->checkPaymentStatus($payment->payment_address);

        if ($status) {
            $payment->status = $status;
            $payment->save();
        }
        
        $viewParams = [
            'payment' => $payment->toArray()
        ];
        
        return $this->view('Mark\PlisioPayment:Payment\Status', 'plisio_payment_status', $viewParams);
    }
}
