<?php

namespace Mark\PlisioPayment\Controller\Public;

use XF\Mvc\Controller;
use XF\Mvc\ParameterBag;

class Webhook extends Controller
{
    public function actionIndex(ParameterBag $params)
    {
        // Handle webhook POST requests from Plisio
        $data = $this->getRequest()->getInput('json', \XF\Util\Json::TYPE_ARRAY);
        
        if (empty($data) || !isset($data['payment_address'])) {
            return $this->error('Invalid webhook data.');
        }
        
        $payment = $this->em()->findOne('Mark\PlisioPayment:Payment', ['payment_address' => $data['payment_address']]);
        
        if ($payment) {
            // Update the payment status based on webhook data
            $payment->status = $data['status'] ?? $payment->status;
            $payment->save();
        }
        
        return $this->reply('Webhook received.');
    }
}
