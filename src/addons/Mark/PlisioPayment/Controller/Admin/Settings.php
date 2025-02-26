<?php

namespace Mark\PlisioPayment\Controller\Admin;

use XF\Mvc\Controller;
use XF\Mvc\ParameterBag;

class Settings extends Controller
{
    public function actionIndex(ParameterBag $params)
    {
        // Render a form to update addon settings
        $settings = [
            'plisioApiKey'             => \XF::options()->plisioApiKey,
            'plisioPaymentExpiration'  => \XF::options()->plisioPaymentExpiration
            // Additional settings (e.g. coin icons, enabled coins) can be added here
        ];
        
        $viewParams = [
            'settings' => $settings
        ];
        
        return $this->view('Mark\PlisioPayment:Settings\Edit', 'plisio_payment_settings', $viewParams);
    }
    
    public function actionSave(ParameterBag $params)
    {
        $this->assertPostOnly();
        
        $apiKey = $this->filter('plisioApiKey', 'str');
        $expiration = $this->filter('plisioPaymentExpiration', 'int');
        // Retrieve additional settings as needed
        
        // Save settings – in a full implementation these should be stored via XenForo’s options system
        \XF::options()->set('plisioApiKey', $apiKey);
        \XF::options()->set('plisioPaymentExpiration', $expiration);
        
        return $this->redirect($this->buildLink('plisio-payment/settings'), 'Settings saved.');
    }
}
