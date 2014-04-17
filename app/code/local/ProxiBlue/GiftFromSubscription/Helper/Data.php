<?php


/**
 * Helper methods pertaining to rule coupon generation
 * 
 *
 * @category   ProxiBlue
 * @package    ProxiBlue_GiftFromSubscription
 * @author     Lucas van Staden (support@proxiblue.com.au)
 */
class ProxiBlue_GiftFromSubscription_Helper_Data extends Mage_Core_Helper_Abstract {

    //Config paths
    const XML_PATH_RULE_ID = "giftfromsubscription/options/giftpromotionruleid";
    const XML_PATH_COUPON_EMAIL = "giftfromsubscription/options/coupon_email";
    
    /**
     * Get the rule id that is to be used for coupon generation
     * @return integer
     */
    public function getRuleId() {
        return Mage::getStoreConfig(self::XML_PATH_RULE_ID);
    }
    
    /**
     * Get the transactional email id that is the content of the email
     * @return integer
     */
    public function getCouponEmailId() {
        return Mage::getStoreConfig(self::XML_PATH_COUPON_EMAIL);
    }

    /**
     * Generate the coupon code
     * 
     * @return boolean
     * @throws Exception
     */
    public function generateCouponCode() {

        /** @var $rule Mage_SalesRule_Model_Rule */
        $rule = Mage::getModel('giftpromo/promo_rule')->load($this->getRuleId());
        $data = array(
            'rule_id' => $rule->getId(),
            'qty' => 1,
            'length' => 5,
            'format' => 'alphanum',
            'prefix' => 'NEWSLETTER-',
            'suffix' => '',
            'dash' => 0,
            'uses_per_coupon' => $rule->getUsesPerCoupon(),
            'uses_per_customer' => $rule->getUsesPerCustomer(),
            'to_date' => $rule->getToDate(),
        );
        if (!$rule->getId()) {
            throw new Exception('Shopping Cart Rule ID could not be found - did you configure the rule ID in configuration?');
        } else {
            try {
                $generator = $rule->getCouponMassGenerator();
                if (!$generator->validateData($data)) {
                    throw new Exception('Not valid data provided to use coupon mass generator!');
                } else {
                    $generator->setData($data);
                    $generator->generatePool();

                    $collection = Mage::getModel('giftpromo/promo_coupon')
                            ->getCollection()
                            ->addFieldToSelect('*');

                    $collection->getSelect()
                            ->order('coupon_id DESC')
                            ->limit(1);

                    return $collection->getFirstItem();
                }
            } catch (Exception $e) {
                throw $e;
            }
        }
        return false;
    }
}
