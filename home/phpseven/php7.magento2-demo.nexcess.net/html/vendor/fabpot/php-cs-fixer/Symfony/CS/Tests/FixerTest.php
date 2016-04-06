<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\Config\Config;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;

class FixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Symfony\CS\Fixer::sortFixers
     */
    public function testThatFixersAreSorted()
    {
        $fixer = new Fixer();

        $fxPrototypes = array(
            array('getPriority' => 0),
            array('getPriority' => -10),
            array('getPriority' => 10),
            array('getPriority' => -10),
        );

        $fxs = array();

        foreach ($fxPrototypes as $fxPrototype) {
            $fx = $this->getMock('Symfony\CS\FixerInterface');
            $fx->expects($this->any())->method('getPriority')->willReturn($fxPrototype['getPriority']);

            $fixer->addFixer($fx);
            $fxs[] = $fx;
        }

        // There are no rules that forces $fxs[1] to be prioritized before $fxs[3]. We should not test against that
        $this->assertSame(array($fxs[2], $fxs[0]), array_slice($fixer->getFixers(), 0, 2));
    }

    /**
     * @covers Symfony\CS\Fixer::registerBuiltInFixers
     */
    public function testThatRegisterBuiltInFixers()
    {
        $fixer = new Fixer();

        $this->assertCount(0, $fixer->getFixers());
        $fixer->registerBuiltInFixers();
        $this->assertGreaterThan(0, count($fixer->getFixers()));
    }

    /**
     * @covers Symfony\CS\Fixer::registerBuiltInConfigs
     */
    public function testThatRegisterBuiltInConfigs()
    {
        $fixer = new Fixer();

        $this->assertCount(0, $fixer->getConfigs());
        $fixer->registerBuiltInConfigs();
        $this->assertGreaterThan(0, count($fixer->getConfigs()));
    }

    /**
     * @covers Symfony\CS\Fixer::addFixer
     * @covers Symfony\CS\Fixer::getFixers
     */
    public function testThatCanAddAndGetFixers()
    {
        $fixer = new Fixer();

        $f1 = $this->getMock('Symfony\CS\FixerInterface');
        $f2 = $this->getMock('Symfony\CS\FixerInterface');
        $fixer->addFixer($f1);
        $fixer->addFixer($f2);

        $this->assertTrue(in_array($f1, $fixer->getFixers(), true));
        $this->assertTrue(in_array($f2, $fixer->getFixers(), true));
    }

    /**
     * @covers Symfony\CS\Fixer::addConfig
     * @covers Symfony\CS\Fixer::getConfigs
     */
    public function testThatCanAddAndGetConfigs()
    {
        $fixer = new Fixer();

        $c1 = $this->getMock('Symfony\CS\ConfigInterface');
        $c2 = $this->getMock('Symfony\CS\ConfigInterface');
        $fixer->addConfig($c1);
        $fixer->addConfig($c2);

        $this->assertSame(array($c1, $c2), $fixer->getConfigs());
    }

    /**
     * @covers Symfony\CS\Fixer::fix
     * @covers Symfony\CS\Fixer::fixFile
     * @covers Symfony\CS\Fixer::prepareFixers
     */
    public function testThatFixSuccessfully()
    {
        $fixer = new Fixer();
        $fixer->addFixer(new \Symfony\CS\Fixer\PSR2\VisibilityFixer());
        $fixer->addFixer(new \Symfony\CS\Fixer\PSR0\Psr0Fixer()); //will be ignored cause of test keyword in namespace

        $config = Config::create()->finder(new \DirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'));
        $config->fixers($fixer->getFixers());

        $changed = $fixer->fix($config, true, true);
        $pathToInvalidFile = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'somefile.php';

        $this->assertCount(1, $changed);
        $this->assertCount(2, $changed[$pathToInvalidFile]);
        $this->assertSame(array('appliedFixers', 'diff'), array_keys($changed[$pathToInvalidFile]));
        $this->assertSame('visibility', $changed[$pathToInvalidFile]['appliedFixers'][0]);
    }

    /**
     * @covers Symfony\CS\Fixer::getLevelAsString
     * @dataProvider getFixerLevels
     */
    public function testThatCanGetFixerLevelString($level, $expectedLevelString)
    {
        $fixer = $this->getMock('Symfony\CS\FixerInterface');
        $fixer->expects($this->any())->method('getLevel')->will($this->returnValue($level));

        $this->assertSame($expectedLevelString, Fixer::getLevelAsString($fixer));
    }

    public function testFixersPriorityEdgeFixers()
    {
        $fixer = new Fixer();
        $fixer->registerBuiltInFixers();
        $fixers = $fixer->getFixers();

        $this->assertSame('encoding', $fixers[0]->getName());
        $this->assertSame('short_tag', $fixers[1]->getName());
        $this->assertSame('eof_ending', $fixers[count($fixers) - 1]->getName());
    }

    /**
     * @dataProvider getFixersPriorityCases
     */
    public function testFixersPriority(FixerInterface $first, FixerInterface $second)
    {
        $this->assertLessThan($first->getPriority(), $second->getPriority());
    }

    public function getFixersPriorityCases()
    {
        $fixer = new Fixer();
        $fixer->registerBuiltInFixers();

        $fixers = array();

        foreach ($fixer->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        $cases = array(
            array($fixers['unused_use'], $fixers['extra_empty_lines']),
            array($fixers['multiple_use'], $fixers['unused_use']),
            array($fixers['multiple_use'], $fixers['ordered_use']),
            array($fixers['remove_leading_slash_use'], $fixers['ordered_use']),
            array($fixers['remove_lines_between_uses'], $fixers['ordered_use']),
            array($fixers['unused_use'], $fixers['remove_leading_slash_use']),
            array($fixers['multiple_use'], $fixers['remove_leading_slash_use']),
            array($fixers['concat_without_spaces'], $fixers['concat_with_spaces']),
            array($fixers['elseif'], $fixers['braces']),
            array($fixers['duplicate_semicolon'], $fixers['braces']),
            array($fixers['duplicate_semicolon'], $fixers['spaces_before_semicolon']),
            array($fixers['duplicate_semicolon'], $fixers['multiline_spaces_before_semicolon']),
            array($fixers['standardize_not_equal'], $fixers['strict']),
            array($fixers['double_arrow_multiline_whitespaces'], $fixers['multiline_array_trailing_comma']),
            array($fixers['double_arrow_multiline_whitespaces'], $fixers['align_double_arrow']),
            array($fixers['operators_spaces'], $fixers['align_double_arrow']), // tested also in: align_double_arrow,operators_spaces.test
            array($fixers['operators_spaces'], $fixers['align_equals']), // tested also in: align_double_arrow,align_equals.test
            array($fixers['indentation'], $fixers['phpdoc_indent']),
            array($fixers['phpdoc_order'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_separation'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_short_description'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_var_without_name'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_order'], $fixers['phpdoc_trim']),
            array($fixers['unused_use'], $fixers['line_after_namespace']),
            array($fixers['linefeed'], $fixers['eof_ending']),
            array($fixers['php_unit_strict'], $fixers['php_unit_construct']),
            array($fixers['unary_operators_spaces'], $fixers['logical_not_operators_with_spaces']),
            array($fixers['unary_operators_spaces'], $fixers['logical_not_operators_with_successor_space']),
            array($fixers['short_echo_tag'], $fixers['echo_to_print']), // tested also in: echo_to_print,short_echo_tag.test
            array($fixers['short_bool_cast'], $fixers['spaces_cast']),
            array($fixers['unneeded_control_parentheses'], $fixers['trailing_spaces']), // tested also in: trailing_spaces,unneeded_control_parentheses.test
        );

        $docFixerNames = array_filter(
            array_keys($fixers),
            function ($name) {
                return false !== strpos($name, 'phpdoc');
            }
        );

        // prepare bulk tests for phpdoc fixers to test that:
        // * `phpdoc_to_comment` is first
        // * `phpdoc_indent` is second
        // * `phpdoc_types` is third
        // * `phpdoc_scalar` is fourth
        // * `phpdoc_params` is last
        $cases[] = array($fixers['phpdoc_to_comment'], $fixers['phpdoc_indent']);
        $cases[] = array($fixers['phpdoc_indent'], $fixers['phpdoc_types']);
        $cases[] = array($fixers['phpdoc_types'], $fixers['phpdoc_scalar']);

        foreach ($docFixerNames as $docFixerName) {
            if (!in_array($docFixerName, array('phpdoc_to_comment', 'phpdoc_indent', 'phpdoc_types', 'phpdoc_scalar'), true)) {
                $cases[] = array($fixers['phpdoc_to_comment'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_indent'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_types'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_scalar'], $fixers[$docFixerName]);
            }

            if ('phpdoc_params' !== $docFixerName) {
                $cases[] = array($fixers[$docFixerName], $fixers['phpdoc_params']);
            }
        }

        return $cases;
    }

    public static function getFixerLevels()
    {
        return array(
            array(FixerInterface::NONE_LEVEL, 'none'),
            array(FixerInterface::PSR0_LEVEL, 'PSR-0'),
            array(FixerInterface::PSR1_LEVEL, 'PSR-1'),
            array(FixerInterface::PSR2_LEVEL, 'PSR-2'),
            array(FixerInterface::SYMFONY_LEVEL, 'symfony'),
            array(FixerInterface::CONTRIB_LEVEL, 'contrib'),
        );
    }

    /**
     * @dataProvider provideFixersDescriptionConsistencyCases
     */
    public function testFixersDescriptionConsistency(FixerInterface $fixer)
    {
        $this->assertRegExp('/^[A-Z@].*\.$/', $fixer->getDescription(), 'Description must start with capital letter or an @ and end with dot.');
    }

    public function provideFixersDescriptionConsistencyCases()
    {
        $fixer = new Fixer();
        $fixer->registerBuiltInFixers();
        $fixers = $fixer->getFixers();
        $cases = array();

        foreach ($fixers as $fixer) {
            $cases[] = array($fixer);
        }

        return $cases;
    }
}
