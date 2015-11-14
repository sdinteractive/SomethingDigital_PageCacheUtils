<?php

class SomethingDigital_PageCacheUtils_Model_Container_Standard extends Enterprise_PageCache_Model_Container_Abstract
{
    const CACHE_TAG_PREFIX = 'SD_FPC_';

    protected function _getCacheId()
    {
        // Multiple templates use this same block type.  We include it in the cache key to be safe.
        $template = $this->_placeholder->getAttribute('template');
        $cache_id = $this->_placeholder->getAttribute('cache_id');
        return static::CACHE_TAG_PREFIX . md5($cache_id . $template) . '_' . $this->_getIdentifier();
    }

    protected function _renderBlock()
    {
        return $this->_getPlaceHolderBlock()->toHtml();
    }

    protected function _getIdentifier()
    {
        return '';
    }

    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        if ($lifetime === null) {
            $lifetime = $this->_parseLifetime($this->_getLifetime());
        }

        if ($lifetime !== 'none') {
            return parent::_saveCache($data, $id, $tags, $lifetime);
        }
        return $this;
    }

    protected function _getLifetime()
    {
        // Magento changes the cache_lifetime to an int, which means false/null/etc. is impossible.
        // Let's search for it in the cache config.
        $config = Mage::getSingleton('enterprise_pagecache/config');
        foreach ($config->getNode('placeholders')->children() as $placeholder) {
            $blockName = $this->_placeholder->getAttribute('block');

            $sameBlock = $blockName == Mage::getConfig()->getBlockClassName($placeholder->block);
            if ($sameBlock) {
                // This should be ours - it's the correct block, which is the key.
                return $placeholder->cache_lifetime;
            }
        }

        return $this->_placeholder->getAttribute('cache_lifetime');
    }

    /**
     * Parse the lifetime value from the cache.xml.
     *
     * Note: false means "default" not "don't cache".
     *
     * @param string $lifetime The value to parse.
     * @return int|null|string|false
     * @throws SomethingDigital_PageCacheUtils_Exception_ParseException
     */
    protected function _parseLifetime($lifetime)
    {
        /** @var SomethingDigital_PageCacheUtils_Model_Lifetime_Parser $parser */
        $parser = Mage::getModel('sd_pagecacheutils/lifetime_parser', [
            'string' => $lifetime,
        ]);
        return $parser->toValue();
    }
}
