# SomethingDigital_PageCacheUtils

Simple utilities to make page cache block management easier.


## Basic usage

You can replace core/template with one of:

 * `sd_pagecacheutils/nocache`<br />
   A block that should never be cached.

 * `sd_pagecacheutils/volatile`<br />
   A block that can be cached, but only for a short period.
   For example, dependent on outside data.

 * `sd_pagecacheutils/customer`<br />
   Data that differs based on customer only, and is also the
   same for all non-customer (guest) users.  Great for blocks
   common to multiple pages.

 * `sd_pagecacheutils/guest`<br />
   Data that can only be cached for guest (non-customer) users.


## Advanced lifetime configuration

Place the following cache.xml in your module, replacing
`***ALLCAPS***` with the appropriate values:

```xml
<?xml version="1.0"?>
<config>
    <placeholders>
        <***UNIQUE MODULE PREFIXED ID***>
            <block>***YOUR BLOCK TYPE***</block>
            <placeholder>SD_FPC***UNIQUE MODULE PREFIXED ID***</placeholder>
            <container>SomethingDigital_PageCacheUtils_Model_Container_Standard</container>
            <cache_lifetime>5h 10m</cache_lifetime>
        </***UNIQUE MODULE PREFIXED ID***>
    </placeholders>
</config>
```

Inside `cache_lifetime`, the following are valid:

 * `false`: Default, typically 1 hour.
 * `null`: Forever, until cleared.
 * `none`: No caching, to hole-punch a block.
 * `1w 2d 3h 4m 5s`: Any combination of times, summed.
 * `3600`: Any number of seconds.

In a custom Container, subclass `SomethingDigital_PageCacheUtils_Model_Container_Standard`
to get this parsing automatically.


## Clearing customer cache

To immediately clear all cached blocks for the current customer,
you can use `cleanCacheForCustomer()`:

```php
/** @var SomethingDigital_PageCacheUtils_Model_Container_Customer $container */
$container = Mage::getModel('sd_pagecacheutils/container_customer');
$customer = Mage::getSingleton('customer/session')->getCustomer();
$container->cleanCacheForCustomer($customer);
```

This will erase all blocks for this customer only, without disrupting
other customers.  But please note that it will clear ALL blocks for this
customer, which is usually acceptable.


## Additional notes

The template path is included in the cache key, so templates
should never conflict with each other.
