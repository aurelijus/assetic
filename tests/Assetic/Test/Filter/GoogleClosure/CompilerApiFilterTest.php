<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter\GoogleClosure;

use Assetic\Asset\StringAsset;
use Assetic\Filter\GoogleClosure\CompilerApiFilter;

class CompilerApiFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group functional
     */
    public function testRoundTrip()
    {
        $input = <<<EOF
(function() {
function unused(){}
function foo(bar) {
    var foo = 'foo';
    return foo + bar;
}
alert(foo("bar"));
})();
EOF;

        $expected = <<<EOF
(function() {
  alert("foobar")
})();
EOF;

        $asset = new StringAsset($input);
        $asset->load();

        $filter = new CompilerApiFilter();
        $filter->setCompilationLevel(CompilerApiFilter::COMPILE_SIMPLE_OPTIMIZATIONS);
        $filter->setJsExterns('');
        $filter->setExternsUrl('');
        $filter->setExcludeDefaultExterns(true);
        $filter->setFormatting(CompilerApiFilter::FORMAT_PRETTY_PRINT);
        $filter->setUseClosureLibrary(false);
        $filter->setWarningLevel(CompilerApiFilter::LEVEL_VERBOSE);

        $filter->filterLoad($asset);
        $filter->filterDump($asset);

        $this->assertEquals($expected, $asset->getContent());
    }
}
