<?php

/**
 * Observer to deal with subscriptions
 *
 * @category   ProxiBlue
 * @package    ProxiBlue_GiftFromSubscription
 * @author     Lucas van Staden (support@proxiblue.com.au)
 */
class ProxiBlue_GiftFromSubscription_Model_Observer {

    /**
     * Handle subscibe and unsubscribe
     * 
     * @param type $observer
     */
    public function newsletter_subscriber_save_before($observer) {
        $subscriber = $observer->getEvent()->getSubscriber();
        if ($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
            $coupon = mage::helper('proxiblue_giftfromsubscription')->generateCouponCode();
            if ($coupon) {
                //send email
                Mage::helper('proxiblue_giftfromsubscription/couponmailer')->sendCoupon($subscriber, $coupon->getCode());
            } else {
                Mage::throwException("couldnt generate coupon");
            }
        } elseif ($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
            //TODO - code to cancel coupon if person unsubscribe
        }
    }

}
