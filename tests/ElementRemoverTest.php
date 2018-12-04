<?php
namespace dcgen\Test;

use dcgen\ElementRemover;
use PHPUnit\Framework\TestCase;

class ElementRemoverTest extends TestCase
{
    private $remover;

    public function setup()
    {
        $this->remover = new ElementRemover();
    }

    public function testSimpleRemoveOnlyElementGivesEmptyArray()
    {
        $array = ['foo' => 'bar'];
        $this->remover->remove($array, ['foo']);
        $this->assertEquals([], $array);
    }

    public function testSimpleRemoveSecondLevelElement()
    {
        $array = [
            'foo' => [
                'bar' => 'baz',
            ],
        ];
        $this->remover->remove($array, ['bar']);
        $this->assertEquals(['foo' => []], $array);
    }

    /**
     * @dataProvider pathProvider
     */
    public function testPathRemove(array $paths, array $expected)
    {
        $array = [
            'foo' => [
                'bar' => [
                    'baz' => 'baz',
                    'bat' => 'bat',
                ],
            ],
            'do' => [
                're' => [
                    'mi' => 'mi',
                    'fa' => 'fa',
                ],
                'so' => [
                    'la' => 'la',
                    'ti' => 'ti',
                ],
            ],
        ];
        $this->remover->remove($array, $paths);
        $this->assertEquals($expected, $array);
    }

    public function pathProvider()
    {
        return [
            [
                ['foo.bar.bat', 'do'],
                [
                    'foo' => [
                        'bar' => [
                            'baz' => 'baz',
                        ],

                    ],
                ]
            ],
            [
                ['foo', 'do.re.mi', 'do.so'],
                [
                    'do' => [
                        're' => [
                            'fa' => 'fa',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider regexPathProvider
     */
    public function testRemoveOnlyTopLevelElementsByPath($paths, $expected)
    {
        $array = [
            'foo' => [],
            'bar' => [
                'foo' => 'bar',
            ],
        ];
        $this->remover->remove($array, $paths);
        $this->assertEquals($expected, $array);
    }

    public function regexPathProvider()
    {
        return [
            /*'top-level foo only' => [
                ['^foo'],
                [
                    'bar' => [
                        'foo' => 'bar',
                    ],
                ],
            ],*/
            'top-level bar.foo' => [
                ['^bar.foo'],
                [
                    'foo' => [],
                    'bar' => [],
                ],
            ],
            'top-level bar' => [
                ['^bar'],
                [
                    'foo' => [],
                ],
            ],
        ];
    }

    public function testDoesNotRemovePartialMatches()
    {
        $array = [
            'foo' => 1,
            'bar' => 2,
            'foobar' => 3,
            'barfoo' => 4,
        ];
        $expected = [
            'bar' => 2,
            'foobar' => 3,
            'barfoo' => 4,
        ];
        $paths = ['foo'];
        $this->remover->remove($array, $paths);
        $this->assertEquals($expected, $array);
    }

    public function testKeysContainingDot()
    {
        $array = [
            'foo.bar' => 1,
            'foo' => [
                'bar' => 2,
            ],
        ];
        $expected = [
            'foo.bar' => 1,
            'foo' => [],
        ];
        $paths = ['foo.bar'];
        $this->remover->remove($array, $paths);
        $this->assertEquals($expected, $array);
    }
}
