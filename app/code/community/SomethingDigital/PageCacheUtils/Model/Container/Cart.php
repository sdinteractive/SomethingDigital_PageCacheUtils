<?php

class SomethingDigital_PageCacheUtils_Model_Container_Cart extends SomethingDigital_PageCacheUtils_Model_Container_Customer
{
    const CACHE_TAG_PREFIX = 'SD_FPC_CART_';

    protected function _getIdentifier()
    {
        return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CART, '')
            . $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
    }
}
