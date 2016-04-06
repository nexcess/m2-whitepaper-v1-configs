<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class WhitespacyLinesFixerTest extends AbstractFixerTestBase
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
                '<?php
$r = 5 +6;                   '.'

$t = true> 9;       '.'
',
            ),
            array(
                '<?php
    $a = 1;   ',
            ),
            array(
                "<?php
\t\$b = 1;\t\t",
            ),
            array(
                '<?php
    $b = 2;
',
                '<?php
    $b = 2;
    ',
            ),
            array(
                '<?php
    $b = 3;


',
                '<?php
    $b = 3;
    '.'
    '.'
    ',
            ),
            array(
                '<?php
    $b = 4;



    $b += 4;',
                '<?php
    $b = 4;
    '.'
    '.'
    '.'
    $b += 4;',
            ),
            array(
                "<?php\n\n\n\$b = 5;",
                "<?php\n                \n\t\n\$b = 5;",
            ),
            array(
                "<?php\necho 1;\n?>\n\n\n\n",
            ),
            array(
                "<?php\necho <<<HTML\ndata     \n  \n \t  \n     \nHTML\n;\n//a",
            ),
            array(
                "<?php\n\$sql = 'SELECT * FROM products WHERE description = \"This product\n   \nis nice\"';",
            ),
            array(
                '<?php
    /**
     * @const Foo.
     */
    const FOO = "BAR";
',
            ),
            array(
                "<?php\n\n    \$a = 1;\n\n    \$b = 2;",
                "<?php\n\n    \$a = 1;\n    \n    \$b = 2;",
            ),
        );
    }
}
