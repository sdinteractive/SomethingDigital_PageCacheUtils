<?php

class SomethingDigital_PageCacheUtils_Model_Container_Customer extends SomethingDigital_PageCacheUtils_Model_Container_Standard
{
    const CACHE_TAG_PREFIX = 'SD_FPC_CUST_';

    public function cleanCacheForCustomer(Mage_Customer_Model_Customer $customer)
    {
        $tags = array($this->getCacheCustomerTag($customer));
        Enterprise_PageCache_Model_Cache::getCacheInstance()->clean($tags);
    }

    protected function _getIdentifier()
    {
        return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
    }

    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        // Let's add a tag for quick and easy clean convenience.
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer->getId()) {
            $tags[] = $this->getCacheCustomerTag($customer);
        }
        return parent::_saveCache($data, $id, $tags, $lifetime);
    }

    protected function getCacheCustomerTag(Mage_Customer_Model_Customer $customer)
    {
        return self::CACHE_TAG_PREFIX . $customer->getId();
    }
}
