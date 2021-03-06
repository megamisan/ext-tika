<?php
namespace ApacheSolrForTypo3\Tika\Tests\Unit\Service\Tika;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Ingo Renner <ingo@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ApacheSolrForTypo3\Tika\Service\Tika\ServiceFactory;
use ApacheSolrForTypo3\Tika\Tests\Unit\UnitTestCase;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;


/**
 * Class AppServiceTest
 *
 */
class ServiceFactoryTest extends UnitTestCase
{

    /**
     * @var array
     */
    private $globalsBackup;

    /**
     * @test
     */
    public function getTikaReturnsAppServiceForJarExtractor()
    {
        $extractor = ServiceFactory::getTika('jar', $this->getConfiguration());
        $this->assertInstanceOf('\ApacheSolrForTypo3\Tika\Service\Tika\AppService',
            $extractor);
    }

    /**
     * @test
     */
    public function getTikaReturnsAppServiceForTikaExtractor()
    {
        $extractor = ServiceFactory::getTika('tika', $this->getConfiguration());
        $this->assertInstanceOf('\ApacheSolrForTypo3\Tika\Service\Tika\AppService',
            $extractor);
    }

    /**
     * @test
     */
    public function getTikaReturnsServerServiceForServerExtractor()
    {
        $extractor = ServiceFactory::getTika('server',
            $this->getConfiguration());
        $this->assertInstanceOf('\ApacheSolrForTypo3\Tika\Service\Tika\ServerService',
            $extractor);
    }

    /**
     * @test
     */
    public function getTikaReturnsSolrCellServiceForSolrExtractor()
    {
        if (!ExtensionManagementUtility::isLoaded('solr')) {
            $this->markTestSkipped('EXT:solr is required for this test, but is not loaded.');
        }

        $extractor = ServiceFactory::getTika('solr', $this->getConfiguration());
        $this->assertInstanceOf('\ApacheSolrForTypo3\Tika\Service\Tika\SolrCellService',
            $extractor);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function getTikaThrowsExceptionForInvalidExtractor()
    {
        ServiceFactory::getTika('foo', $this->getConfiguration());
    }

    /**
     * @test
     */
    public function getTikaThrowsExceptionForInvalidConfiguration()
    {
        $backup = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tika'];
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tika'] = 'invalid configuration';

        try {
            ServiceFactory::getTika('foo');
        } catch (\RuntimeException $e) {
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tika'] = $backup;
            return;
        }

        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tika'] = $backup;
        $this->fail('Did not throw RuntimeException');
    }

    protected function setUp()
    {
        $this->globalsBackup = array(
            'TYPO3_CONF_VARS' => $GLOBALS['TYPO3_CONF_VARS'],
        );
        unset($GLOBALS['TYPO3_CONF_VARS']);
    }

    protected function tearDown()
    {
        foreach ($this->globalsBackup as $key => $data) {
            $GLOBALS[$key] = $data;
        }
        unset($this->globalsBackup);
    }

}
