<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

namespace ZFTest\Configuration;

use PHPUnit_Framework_TestCase as TestCase;
use ZF\Configuration\ConfigResource;

class ConfigResourceTest extends TestCase
{
    public function setUp()
    {
        $this->file   = 'php://memory';
        $this->writer = new TestAsset\ConfigWriter();
        $this->configResource = new ConfigResource(array(), $this->file, $this->writer);
    }

    public function testCreateNestedKeyValuePairExtractsDotSeparatedKeysAndCreatesNestedStructure()
    {
        $patchValues = array();
        $this->configResource->createNestedKeyValuePair($patchValues, 'foo.bar.baz', 'value');
        $this->assertArrayHasKey('foo', $patchValues);
        $this->assertInternalType('array', $patchValues['foo']);
        $this->assertArrayHasKey('bar', $patchValues['foo']);
        $this->assertInternalType('array', $patchValues['foo']['bar']);
        $this->assertArrayHasKey('baz', $patchValues['foo']['bar']);
        $this->assertEquals('value', $patchValues['foo']['bar']['baz']);
    }

    public function testPatchListUpdatesFileWithMergedConfig()
    {
        $config = array(
            'foo' => 'bar',
            'bar' => array(
                'baz' => 'bat',
                'bat' => 'bogus',
            ),
            'baz' => 'not what you think',
        );
        $configResource = new ConfigResource($config, $this->file, $this->writer);

        $patch = array(
            'bar.baz' => 'UPDATED',
            'baz'     => 'what you think',
        );
        $response = $configResource->patch($patch);

        $this->assertEquals($patch, $response);

        $expected = array(
            'foo' => 'bar',
            'bar' => array(
                'baz' => 'UPDATED',
                'bat' => 'bogus',
            ),
            'baz' => 'what you think',
        );
        $written = $this->writer->writtenConfig;
        $this->assertEquals($expected, $written);
    }

    public function testTraverseArrayFlattensToDotSeparatedKeyValuePairs()
    {
        $config = array(
            'foo' => 'bar',
            'bar' => array(
                'baz' => 'bat',
                'bat' => 'bogus',
            ),
            'baz' => 'not what you think',
        );
        $expected = array(
            'foo'     => 'bar',
            'bar.baz' => 'bat',
            'bar.bat' => 'bogus',
            'baz'     => 'not what you think',
        );

        $this->assertEquals($expected, $this->configResource->traverseArray($config));
    }

    public function testFetchFlattensComposedConfiguration()
    {
        $config = array(
            'foo' => 'bar',
            'bar' => array(
                'baz' => 'bat',
                'bat' => 'bogus',
            ),
            'baz' => 'not what you think',
        );
        $expected = array(
            'foo'     => 'bar',
            'bar.baz' => 'bat',
            'bar.bat' => 'bogus',
            'baz'     => 'not what you think',
        );
        $configResource = new ConfigResource($config, $this->file, $this->writer);

        $this->assertEquals($expected, $configResource->fetch());
    }

    public function testFetchWithTreeFlagSetToTrueReturnsConfigurationUnmodified()
    {
        $config = array(
            'foo' => 'bar',
            'bar' => array(
                'baz' => 'bat',
                'bat' => 'bogus',
            ),
            'baz' => 'not what you think',
        );
        $configResource = new ConfigResource($config, $this->file, $this->writer);
        $this->assertEquals($config, $configResource->fetch(true));
    }

    public function testPatchWithTreeFlagSetToTruePerformsArrayMergeAndReturnsConfig()
    {
        $config = array(
            'foo' => 'bar',
            'bar' => array(
                'baz' => 'bat',
                'bat' => 'bogus',
            ),
            'baz' => 'not what you think',
        );
        $configResource = new ConfigResource($config, $this->file, $this->writer);

        $patch = array(
            'bar' => array(
                'baz' => 'UPDATED',
            ),
            'baz' => 'what you think',
        );
        $response = $configResource->patch($patch, true);

        $this->assertEquals($patch, $response);

        $expected = array(
            'foo' => 'bar',
            'bar' => array(
                'baz' => 'UPDATED',
                'bat' => 'bogus',
            ),
            'baz' => 'what you think',
        );
        $written = $this->writer->writtenConfig;
        $this->assertEquals($expected, $written);
    }
}
