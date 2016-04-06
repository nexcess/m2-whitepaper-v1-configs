<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Denis Sokolov <denis@sokolov.cc>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class FunctionDeclarationFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                // non-PHP test
                'function foo () {}',
            ),
            array(
                '<?php function foo() {}',
                '<?php function	foo() {}',
            ),
            array(
                '<?php function foo() {}',
                '<?php function foo () {}',
            ),
            array(
                '<?php function foo() {}',
                '<?php function foo	() {}',
            ),
            array(
                '<?php function foo() {}',
                '<?php function
foo () {}',
            ),
            array(
                '<?php function ($i) {};',
                '<?php function($i) {};',
            ),
            array(
                '<?php function _function() {}',
                '<?php function _function () {}',
            ),
            array(
                '<?php function foo($a, $b = true) {}',
                '<?php function foo($a, $b = true){}',
            ),
            array(
                '<?php function foo($a, $b = true) {}',
                '<?php function foo($a, $b = true)    {}',
            ),
            array(
                '<?php function foo($a)
{}',
            ),
            array(
                '<?php function ($a) use ($b) {};',
                '<?php function ($a) use ($b)     {};',
            ),
            array(
                '<?php function &foo($a) {}',
                '<?php function &foo( $a ) {}',
            ),
            array(
                '<?php function foo($a)
	{}',
                '<?php function foo( $a)
	{}',
            ),
            array(
                '<?php
    function foo(
        $a,
        $b,
        $c
    ) {}',
            ),
            array(
                '<?php $function = function () {};',
                '<?php $function = function(){};',
            ),
            array(
                '<?php $function("");',
            ),
            array(
                '<?php function ($a) use ($b) {};',
                '<?php function($a)use($b) {};',
            ),
            array(
                '<?php function ($a) use ($b) {};',
                '<?php function($a)         use      ($b) {};',
            ),
            array(
                '<?php function ($a) use ($b) {};',
                '<?php function ($a) use ( $b ) {};',
            ),
            array(
                '<?php function &($a) use ($b) {};',
                '<?php function &(  $a   ) use (   $b      ) {};',
            ),
            array(
                '<?php
    interface Foo
    {
        public function setConfig(ConfigInterface $config);
    }',
            ),
            // do not remove multiline space before { when end of previous line is a comment
            array(
                '<?php
function foo() // bar
{              // baz
}',
            ),
            array(
                '<?php
function foo() /* bar */
{              /* baz */
}',
            ),
        );
    }
}
