<?php

class SomethingDigital_PageCacheUtils_Model_Container_Guest extends SomethingDigital_PageCacheUtils_Model_Container_Standard
{
    const CACHE_TAG_PREFIX = 'SD_FPC_GUEST_';

    protected function _getIdentifier()
    {
        return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_LOGGED_IN, '');
    }

    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        // Let's add a tag for quick and easy clean convenience.
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer->getId()) {
            // Do not save anything.
            return $this;
        }

        return parent::_saveCache($data, $id, $tags, $lifetime);
    }
}
