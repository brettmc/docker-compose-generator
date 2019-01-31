<?php
namespace dcgen\Test;

use dcgen\ArrayPathMatcher;
use PHPUnit\Framework\TestCase;

class ArrayPathMatcherTest extends TestCase
{
    /**
     * @dataProvider endPathProvider
     */
    public function testPathMatchingFromEnd(array $path, array $test, bool $expected)
    {
        $fromStart = false;
        $result = ArrayPathMatcher::matches($path, $test, $fromStart);
        $this->assertEquals($expected, $result);
    }

    public function endPathProvider()
    {
        $long = ['foo', 'bar', 'baz', 'bat'];
        $short = ['foo'];
        return [
            [
                $long,
                ['bat'],
                true,
            ],
            [
                $long,
                ['baz', 'bat'],
                true,
            ],
            [
                $long,
                ['bar', 'baz', 'bat'],
                true,
            ],
            [
                $long,
                ['foo', 'bar', 'baz', 'bat'],
                true,
            ],
            [
                $long,
                ['foo'],
                false,
            ],
            [
                $long,
                ['foo', 'bar'],
                false,
            ],
            [
                $long,
                ['foo', 'bar', 'baz'],
                false,
            ],
            [
                $long,
                ['elephants'],
                false,
            ],
            [
                $short,
                ['elephants'],
                false,
            ],
            [
                $short,
                ['foo'],
                true,
            ],
            [
                $short,
                ['f*'],
                true,
            ],
            [
                $long,
                ['ba[zs]', 'bat'],
                true,
            ],
            [
                $long,
                ['ba[zZ]', 'ba[tT]'],
                true,
            ],
            [
                $long,
                ['ba[zZ]', 'ba[abc]'],
                false,
            ],
        ];
    }

    /**
     * @dataProvider startPathProvider
     */
    public function testPathMatchingFromStart(array $test, bool $expected)
    {
        $path = ['foo', 'bar', 'baz', 'bat'];
        $fromStart = true;
        $result = ArrayPathMatcher::matches($path, $test, $fromStart);
        $this->assertEquals($expected, $result);
    }

    public function startPathProvider()
    {
        return [
            [
                ['bat'],
                false,
            ],
            [
                ['baz', 'bat'],
                false,
            ],
            [
                ['bar', 'baz', 'bat'],
                false,
            ],
            [
                ['foo', 'bar', 'baz', 'bat'],
                true,
            ],
            [
                ['foo', 'bar', 'baz', 'ba[rzt]'],
                true,
            ],
            [
                ['foo', 'bar', 'baz', 'ba*'],
                true,
            ],
        ];
    }

    /**
     * @dataProvider booleanProvider()
     */
    public function testDoesNotMatchWhenNeedleIsLongerThanHaystack(bool $fromStart)
    {
        $path = ['foo'];
        $test = ['foo', 'bar'];
        $result = ArrayPathMatcher::matches($path, $test, $fromStart);
        $this->assertFalse($result);
    }

    public function booleanProvider()
    {
        return [
            [true],
            [false],
        ];
    }
}
