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
 * @author Graham Campbell <graham@mineuk.com>
 */
class NoEmptyLinesAfterPhpdocsFixerTest extends AbstractFixerTestBase
{
    public function testSimpleExampleIsNotChanged()
    {
        $input = <<<'EOF'
<?php

/**
 * This is the bar class.
 */
class Bar
{
    /**
     * @return void
     */
    public function foo()
    {
        //
    }
}

EOF;

        $this->makeTest($input);
    }

    public function testComplexExampleIsNotChanged()
    {
        $input = <<<'EOF'
<?php
/**
 * This is the hello function.
 * Yeh, this layout should be allowed.
 * We're fixing lines following a docblock.
 */
function hello($foo) {}
/**
 * This is the bar class.
 */
final class Bar
{
    /**
     * @return void
     */
    public static function foo()
    {
        //
    }

    /**
     * @return void
     */
    static private function bar123() {}

    /*
     * This T_COMMENT should not be moved
     *
     * Only T_DOC_COMMENT should be moved
     */
    final protected
    // mixin' it up a bit
    function baz() {
    }


    /*
     * This T_COMMENT should not be moved
     *
     * Only T_DOC_COMMENT should be moved
     */

    public function cool() {}

    /**
     * This is the first docblock
     *
     * Not removing blank line here.
     * No element is being documented
     */

    /**
     * Another docblock
     */
    public function silly() {}
}

EOF;

        $this->makeTest($input);
    }

    public function testCommentsAreNotChanged()
    {
        $input = <<<'EOF'
<?php

/*
 * This file is part of xyz.
 *
 * License etc...
 */

namespace Foo\Bar;

EOF;

        $this->makeTest($input);
    }

    public function testFixesSimpleClass()
    {
        $expected = <<<'EOF'
<?php

/**
 * This is the bar class.
 */
class Bar {}

EOF;

        $input = <<<'EOF'
<?php

/**
 * This is the bar class.
 */


class Bar {}

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixesIndentedClass()
    {
        $expected = <<<'EOF'
<?php

    /**
     *
     */
    class Foo {
        private $a;
    }

EOF;

        $input = <<<'EOF'
<?php

    /**
     *
     */

    class Foo {
        private $a;
    }

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixesOthers()
    {
        $expected = <<<'EOF'
<?php

    /**
     * Constant!
     */
    const test = 'constant';

    /**
     * Foo!
     */
    $foo = 123;

EOF;

        $input = <<<'EOF'
<?php

    /**
     * Constant!
     */


    const test = 'constant';

    /**
     * Foo!
     */

    $foo = 123;

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixesWindowsStyle()
    {
        $expected = "<?php\r\n    /**     * Constant!     */\n    \$foo = 123;";

        $input = "<?php\r\n    /**     * Constant!     */\r\n\r\n\r\n    \$foo = 123;";

        $this->makeTest($expected, $input);
    }

    /**
     * Empty line between typehinting docs and return statement should be preserved.
     *
     * @dataProvider provideInlineTypehintingDocsBeforeFlowBreakCases
     */
    public function testInlineTypehintingDocsBeforeFlowBreak($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideInlineTypehintingDocsBeforeFlowBreakCases()
    {
        $cases = array();

        $cases[] = array(<<<'EOF'
<?php
function parseTag($tag)
{
    $tagClass = get_class($tag);

    if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
        /** @var DocBlock\Tag\VarTag $tag */

        return $tag->getDescription();
    }
}
EOF
        );

        $cases[] = array(<<<'EOF'
<?php
function parseTag($tag)
{
    $tagClass = get_class($tag);

    if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
        /** @var DocBlock\Tag\VarTag $tag */

        throw new Exception($tag->getDescription());
    }
}
EOF
        );

        $cases[] = array(<<<'EOF'
<?php
function parseTag($tag)
{
    $tagClass = get_class($tag);

    if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
        /** @var DocBlock\Tag\VarTag $tag */

        goto FOO;
    }

FOO:
}
EOF
        );

        $cases[] = array(<<<'EOF'
<?php
function parseTag($tag)
{
    while (true) {
        $tagClass = get_class($tag);

        if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
            /** @var DocBlock\Tag\VarTag $tag */

            continue;
        }
    }
}
EOF
        );

        $cases[] = array(<<<'EOF'
<?php
function parseTag($tag)
{
    while (true) {
        $tagClass = get_class($tag);

        if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
            /** @var DocBlock\Tag\VarTag $tag */

            break;
        }
    }
}
EOF
        );

        return $cases;
    }
}
