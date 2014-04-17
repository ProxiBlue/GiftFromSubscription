<?php

/**
 * Helper method to send email for subscription with coupon code
 *
 * @category   ProxiBlue
 * @package    ProxiBlue_GiftFromSubscription
 * @author     Lucas van Staden (support@proxiblue.com.au)
 */
class ProxiBlue_GiftFromSubscription_Helper_Couponmailer extends Mage_Core_Helper_Abstract {

    /**
     * Send coupon code to subscripber
     * 
     * @param object $subscriber
     * @param object $couponcode
     */
    public function sendCoupon($subscriber, $couponcode) {

        // Set sender information
        $senderName = Mage::getStoreConfig('trans_email/ident_general/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');

        $sender = array(
            'name' => $senderName,
            'email' => $senderEmail
        );
        $storeId = Mage::app()->getStore()->getId();
        $transactional = Mage::getModel('core/email_template');
        $customer = Mage::getModel("customer/customer");
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $name = $customer->loadByEmail($subscriber->getSubscriberEmail())->getName();
        if (!$customer->getId() || $name = '') {
            $name = 'Customer';
        }
        $vars = array(
            'coupon' => $couponcode,
            'name' => $name,
        );
        $transactional->sendTransactional(
                Mage::helper('proxiblue_giftfromsubscription')->getCouponEmailId(), $sender, $subscriber->getSubscriberEmail(), $name, $vars, $storeId
        );
    }

}
