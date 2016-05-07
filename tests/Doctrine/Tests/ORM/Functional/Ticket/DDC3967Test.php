<?php

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\Tests\Models\Cache\Country;
use Doctrine\Tests\ORM\Functional\SecondLevelCacheAbstractTest;

class DDC3967Test extends SecondLevelCacheAbstractTest
{
    protected function setUp()
    {
        parent::setUp();

        $this->loadFixturesCountries();
        $this->_em->getCache()->evictEntityRegion(Country::class);
        $this->_em->clear();
    }

    public function testIdentifierCachedWithProperType()
    {
        $country = array_pop($this->countries);
        $id = $country->getId();

        // First time, loaded from database
        $this->_em->find(Country::class, "$id");
        $this->_em->clear();

        // Second time, loaded from cache
        /** @var Country $country */
        $country = $this->_em->find(Country::class, "$id");

        // Identifier type should be integer
        self::assertSame($country->getId(), $id);
    }
}
