<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Configuration;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\Config\Writer\PhpArray;
use ZF\Configuration\ConfigResource;

class ConfigResourceTest extends TestCase
{
    public $file;
    /** @var ConfigResource */
    protected $configResource;
    protected $writer;

    public function setUp()
    {
        $this->removeScaffold();
        $this->file = tempnam(sys_get_temp_dir(), 'zfconfig');
        file_put_contents($this->file, '<' . "?php\nreturn array();");

        $this->writer = new TestAsset\ConfigWriter();
        $this->configResource = new ConfigResource([], $this->file, $this->writer);
    }

    public function tearDown()
    {
        $this->removeScaffold();
    }

    public function removeScaffold()
    {
        if ($this->file && file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function arrayIntersectAssocRecursive($array1, $array2)
    {
        if (!is_array($array1) || !is_array($array2)) {
            if ($array1 === $array2) {
                return $array1;
            }
            return false;
        }

        $commonKeys = array_intersect(array_keys($array1), array_keys($array2));
        $return = [];
        foreach ($commonKeys as $key) {
            $value = $this->arrayIntersectAssocRecursive($array1[$key], $array2[$key]);
            if ($value) {
                $return[$key] = $value;
            }
        }
        return $return;
    }

    public function testCreateNestedKeyValuePairExtractsDotSeparatedKeysAndCreatesNestedStructure()
    {
        $patchValues = [];
        $this->configResource->createNestedKeyValuePair($patchValues, 'foo.bar.baz', 'value');
        $this->assertArrayHasKey('foo', $patchValues);
        $this->assertInternalType('array', $patchValues['foo']);
        $this->assertArrayHasKey('bar', $patchValues['foo']);
        $this->assertInternalType('array', $patchValues['foo']['bar']);
        $this->assertArrayHasKey('baz', $patchValues['foo']['bar']);
        $this->assertEquals('value', $patchValues['foo']['bar']['baz']);

        // ensure second call to createNestedKeyValuePair does not destroy original values
        $this->configResource->createNestedKeyValuePair($patchValues, 'foo.bar.boom', 'value2');
        $this->assertCount(2, $patchValues['foo']['bar']);
    }

    public function testPatchListUpdatesFileWithMergedConfig()
    {
        $config = [
            'foo' => 'bar',
            'bar' => [
                'baz' => 'bat',
                'bat' => 'bogus',
            ],
            'baz' => 'not what you think',
        ];
        $configResource = new ConfigResource($config, $this->file, $this->writer);

        $patch = [
            'bar.baz' => 'UPDATED',
            'baz'     => 'what you think',
        ];
        $response = $configResource->patch($patch);

        $this->assertEquals($patch, $response);

        $expected = [
            'bar' => [
                'baz' => 'UPDATED',
            ],
            'baz' => 'what you think',
        ];
        $written = $this->writer->writtenConfig;
        $this->assertEquals($expected, $written);
    }

    public function testTraverseArrayFlattensToDotSeparatedKeyValuePairs()
    {
        $config = [
            'foo' => 'bar',
            'bar' => [
                'baz' => 'bat',
                'bat' => 'bogus',
            ],
            'baz' => 'not what you think',
        ];
        $expected = [
            'foo'     => 'bar',
            'bar.baz' => 'bat',
            'bar.bat' => 'bogus',
            'baz'     => 'not what you think',
        ];

        $this->assertEquals($expected, $this->configResource->traverseArray($config));
    }

    public function testFetchFlattensComposedConfiguration()
    {
        $config = [
            'foo' => 'bar',
            'bar' => [
                'baz' => 'bat',
                'bat' => 'bogus',
            ],
            'baz' => 'not what you think',
        ];
        $expected = [
            'foo'     => 'bar',
            'bar.baz' => 'bat',
            'bar.bat' => 'bogus',
            'baz'     => 'not what you think',
        ];
        $configResource = new ConfigResource($config, $this->file, $this->writer);

        $this->assertEquals($expected, $configResource->fetch());
    }

    public function testFetchWithTreeFlagSetToTrueReturnsConfigurationUnmodified()
    {
        $config = [
            'foo' => 'bar',
            'bar' => [
                'baz' => 'bat',
                'bat' => 'bogus',
            ],
            'baz' => 'not what you think',
        ];
        $configResource = new ConfigResource($config, $this->file, $this->writer);
        $this->assertEquals($config, $configResource->fetch(true));
    }

    public function testPatchWithTreeFlagSetToTruePerformsArrayMergeAndReturnsConfig()
    {
        $config = [
            'foo' => 'bar',
            'bar' => [
                'baz' => 'bat',
                'bat' => 'bogus',
            ],
            'baz' => 'not what you think',
        ];
        $configResource = new ConfigResource($config, $this->file, $this->writer);

        $patch = [
            'bar' => [
                'baz' => 'UPDATED',
            ],
            'baz' => 'what you think',
        ];
        $response = $configResource->patch($patch, true);

        $this->assertEquals($patch, $response);

        $expected = [
            'bar' => [
                'baz' => 'UPDATED',
            ],
            'baz' => 'what you think',
        ];
        $written = $this->writer->writtenConfig;
        $this->assertEquals($expected, $written);
    }

    public function replaceKeyPairs()
    {
        return [
            'scalar-top-level'        => ['top', 'updated', ['top' => 'updated']],
            'overwrite-hash'          => ['sub', 'updated', ['sub' => 'updated']],
            'nested-scalar'           => ['sub.level', 'updated', [
                'sub' => [
                    'level' => 'updated'
                ]
            ]],
            'nested-list'             => ['sub.list', ['three', 'four'], [
                'sub' => [
                    'list' => ['three', 'four']
                ]
            ]],
            'nested-hash'             => ['sub.hash.two', 'updated', [
                'sub' => [
                    'hash' => [
                        'two' => 'updated'
                    ]
                ]
            ]],
            'overwrite-nested-null'   => ['sub.null', 'updated', [
                'sub' => [
                    'null' => 'updated'
                ]
            ]],
            'overwrite-nested-object' => ['sub.object', 'updated', [
                'sub' => [
                    'object' => 'updated'
                ]
            ]],
        ];
    }

    /**
     * @dataProvider replaceKeyPairs
     */
    public function testReplaceKey($key, $value, $expected)
    {
        $config = [
            'top' => 'level',
            'sub' => [
                'level' => 2,
                'list'  => [
                    'one',
                    'two',
                ],
                'hash' => [
                    'one' => 1,
                    'two' => 2,
                ],
                'null' => null,
                'object' => new stdClass(),
            ],
        ];

        $updated = $this->configResource->replaceKey($key, $value, $config);
        $intersection = $this->arrayIntersectAssocRecursive($expected, $updated);
        $this->assertEquals($expected, $intersection);
        $this->assertEquals(2, count($updated));
    }

    public function deleteKeyPairs()
    {
        return [
            'scalar-top-level' => ['top', ['sub' => [
                'level' => 2,
                'list'  => [
                    'one',
                    'two',
                ],
                'hash' => [
                    'one' => 1,
                    'two' => 2,
                ],
            ]]],
            'delete-hash' => ['sub', ['top' => 'level']],
            'delete-nested-via-arrays' => [['sub', 'level'], [
                'top' => 'level',
                'sub' => [
                    'list'  => [
                        'one',
                        'two',
                    ],
                    'hash' => [
                        'one' => 1,
                        'two' => 2,
                    ],
                ],
            ]],
            'delete-nested-via-dot-separated-values' => ['sub.level', [
                'top' => 'level',
                'sub' => [
                    'list'  => [
                        'one',
                        'two',
                    ],
                    'hash' => [
                        'one' => 1,
                        'two' => 2,
                    ],
                ],
            ]],
            'delete-nested-array' => ['sub.list', [
                'top' => 'level',
                'sub' => [
                    'level' => 2,
                    'hash' => [
                        'one' => 1,
                        'two' => 2,
                    ],
                ],
            ]],
        ];
    }

    /**
     * @dataProvider deleteKeyPairs
     */
    public function testDeleteKey($key, array $expected)
    {
        $config = [
            'top' => 'level',
            'sub' => [
                'level' => 2,
                'list'  => [
                    'one',
                    'two',
                ],
                'hash' => [
                    'one' => 1,
                    'two' => 2,
                ],
            ],
        ];
        $writer = new PhpArray();
        $writer->toFile($this->file, $config);
        // Ensure the writer has written to the file!
        $this->assertEquals($config, include $this->file);

        // Create config resource, and delete a key
        $configResource = new ConfigResource($config, $this->file, $writer);
        $test = $configResource->deleteKey($key);

        // Verify what was returned was what we expected
        $this->assertEquals($expected, $test);

        // Verify the file contains what we expect
        $this->assertEquals($expected, include $this->file);
    }

    public function testDeleteNestedKeyShouldAssignArrayToParent()
    {
        $config = [
            'top' => 'level',
            'sub' => [
                'sub2'  => [
                    'sub3' => [
                        'two',
                    ],
                ],
            ],
        ];
        $writer = new PhpArray();
        $writer->toFile($this->file, $config);
        // Ensure the writer has written to the file!
        $this->assertEquals($config, include $this->file);

        // Create config resource, and delete a key
        $configResource = new ConfigResource($config, $this->file, $writer);
        $test = $configResource->deleteKey('sub.sub2.sub3');

        // Verify what was returned was what we expected
        $expected = [
            'top' => 'level',
            'sub' => [
                'sub2' => [],
            ],
        ];
        $this->assertEquals($expected, $test);
        $this->assertSame($expected['sub']['sub2'], $test['sub']['sub2']);

        // Verify the file contains what we expect
        $test = include $this->file;
        $this->assertEquals($expected, $test);
        $this->assertSame($expected['sub']['sub2'], $test['sub']['sub2']);
    }

    public function testDeleteNonexistentKeyShouldDoNothing()
    {
        $config = [];
        $writer = new PhpArray();
        $writer->toFile($this->file, $config);
        // Ensure the writer has written to the file!
        $this->assertEquals($config, include $this->file);

        // Create config resource, and delete a key
        $configResource = new ConfigResource($config, $this->file, $writer);
        $test = $configResource->deleteKey('sub.sub2.sub3');

        // Verify what was returned was what we expected
        $expected = [];
        $this->assertEquals($expected, $test);

        // Verify the file contains what we expect
        $test = include $this->file;
        $this->assertEquals($expected, $test);
    }
}
