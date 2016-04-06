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

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\CS\ErrorsManager;
use Symfony\CS\FileCacheManager;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;

/**
 * Integration test base class.
 *
 * This test searches for '.test' fixture files in the given directory.
 * Each fixture file will be parsed and tested against the expected result.
 *
 * Fixture files have the following format:
 *
 * --TEST--
 * Example test description
 * --CONFIG--
 * level=symfony|none|psr0|psr1|psr2|symfony
 * fixers=fixer1,fixer2,...*
 * --fixers=fixer3,fixer4,...****
 * --REQUIREMENTS--
 * php=5.4**
 * hhvm=false***
 * --INPUT--
 * Code to fix
 * --EXPECT--
 * Expected code after fixing*****
 *
 *     * Additional fixers may be omitted.
 *    ** PHP minimum version. Default to current running php version (no effect).
 *   *** HHVM compliant flag. Default to true. Set to false to skip test under HHVM.
 *  **** Black listed filters may be omitted.
 * ***** When the expected block is omitted the input is expected not to
 *     be changed by the fixers.
 *
 * @author SpacePossum <possumfromspace@gmail.com>
 *
 * @internal
 */
abstract class AbstractIntegrationTest extends \PHPUnit_Framework_TestCase
{
    private static $builtInFixers;

    public static function setUpBeforeClass()
    {
        $tmpFile = static::getTempFile();
        if (!is_file($tmpFile)) {
            $dir = dirname($tmpFile);
            if (!is_dir($dir)) {
                $fs = new Filesystem();
                $fs->mkdir($dir, 0766);
            }
        }
    }

    public static function tearDownAfterClass()
    {
        @unlink(static::getTempFile());
    }

    /**
     * @dataProvider getTests
     *
     * @see doTestIntegration()
     */
    public function testIntegration($testFileName, $testTitle, $fixers, array $requirements, $input, $expected = null)
    {
        $this->doTestIntegration($testFileName, $testTitle, $fixers, $requirements, $input, $expected);
    }

    /**
     * Creates test data by parsing '.test' files.
     *
     * @return array
     */
    public function getTests()
    {
        $fixturesDir = realpath($this->getFixturesDir());
        if (!is_dir($fixturesDir)) {
            throw new \UnexpectedValueException(sprintf('Given fixture dir "%s" is not a directory.', $fixturesDir));
        }

        $tests = array();

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($fixturesDir), \RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if (!preg_match('/\.test$/', $file)) {
                continue;
            }

            $test = file_get_contents($file->getRealpath());
            $fileName = $file->getFileName();
            if (!preg_match('/--TEST--[\n](.*?)\s--CONFIG--[\n](.*?)(\s--REQUIREMENTS--[\n](.*?))?\s--INPUT--[\n](.*?[\n]*)(?:[\n]--EXPECT--\s(.*)|$)/s', $test, $match)) {
                throw new \InvalidArgumentException(sprintf('Test format invalid for "%s".', $fileName));
            }

            $tests[] = array($fileName, $match[1], $this->getFixersFromConfig($fileName, $match[2]), $this->getRequirementsFromConfig($fileName, $match[4]), $match[5], isset($match[6]) ? $match[6] : null);
        }

        return $tests;
    }

    /**
     * Returns the full path to directory which contains the tests.
     *
     * @return string
     */
    protected static function getFixturesDir()
    {
        throw new \BadMethodCallException('Method "getFixturesDir" must be overridden by the extending class.');
    }

    /**
     * Returns the full path to the temporary file where the test will write to.
     *
     * @return string
     */
    protected static function getTempFile()
    {
        throw new \BadMethodCallException('Method "getTempFile" must be overridden by the extending class.');
    }

    /**
     * Applies the given fixers on the input and checks the result.
     *
     * It will write the input to a temp file. The file will be fixed by a Fixer instance
     * configured with the given fixers. The result is compared with the expected output.
     * It checks if no errors were reported during the fixing.
     *
     * @param string           $testFileName Filename
     * @param string           $testTitle    Test title
     * @param FixerInterface[] $fixers       Fixers to use
     * @param array            $requirements Env requirements (PHP, HHVM)
     * @param string           $input        Code to fix
     * @param string|null      $expected     Expected result or null if the input is expected not to change
     */
    protected function doTestIntegration($testFileName, $testTitle, $fixers, array $requirements, $input, $expected = null)
    {
        if (defined('HHVM_VERSION') && false === $requirements['hhvm']) {
            $this->markTestSkipped('HHVM is not supported.');
        }
        if (isset($requirements['php']) && version_compare(PHP_VERSION, $requirements['php']) < 0) {
            $this->markTestSkipped(sprintf('PHP %s (or later) is required.', $requirements['php']));
        }

        $errorsManager = new ErrorsManager();
        $fixer = new Fixer();
        $fixer->setErrorsManager($errorsManager);

        $tmpFile = static::getTempFile();
        if (false === @file_put_contents($tmpFile, $input)) {
            throw new IOException(sprintf('Failed to write to tmp. file "%s".', $tmpFile));
        }

        $changed = $fixer->fixFile(new \SplFileInfo($tmpFile), $fixers, false, true, new FileCacheManager(false, null, $fixers));
        $this->assertTrue($errorsManager->isEmpty(), 'Errors reported during fixing.');

        if (null === $expected) {
            $this->assertEmpty($changed, sprintf("Expected no changes made to test \"%s\" in \"%s\".\nFixers applied:\n\"%s\".\nDiff.:\n\"%s\".", $testTitle, $testFileName, $changed === null ? '[None]' : implode(',', $changed['appliedFixers']), $changed === null ? '[None]' : $changed['diff']));

            return;
        }

        $this->assertNotEmpty($changed, sprintf('Expected changes made to test "%s" in "%s".', $testTitle, $testFileName));
        $this->assertSame($expected, file_get_contents($tmpFile), sprintf('Expected changes do not match result for "%s" in "%s".', $testTitle, $testFileName));

        // run the test again with the `expected` part, this should always stay the same
        $this->testIntegration($testFileName, $testTitle.' "--EXPECT-- part run"', $fixers, $requirements, $expected);
    }

    /**
     * Parses the '--CONFIG--' block of a '.test' file and determines what fixers should be used.
     *
     * @param string $fileName
     * @param string $config
     *
     * @return FixerInterface[]
     */
    protected function getFixersFromConfig($fileName, $config)
    {
        static $levelMap = array(
            'none' => FixerInterface::NONE_LEVEL,
            'psr1' => FixerInterface::PSR1_LEVEL,
            'psr2' => FixerInterface::PSR2_LEVEL,
            'symfony' => FixerInterface::SYMFONY_LEVEL,
        );

        $lines = explode("\n", $config);
        if (empty($lines)) {
            throw new \InvalidArgumentException(sprintf('No configuration options found in "%s".', $fileName));
        }

        $config = array('level' => null, 'fixers' => array(), '--fixers' => array());

        foreach ($lines as $line) {
            $labelValuePair = explode('=', $line);
            if (2 !== count($labelValuePair)) {
                throw new \InvalidArgumentException(sprintf('Invalid configuration line "%s" in "%s".', $line, $fileName));
            }

            $label = strtolower(trim($labelValuePair[0]));
            $value = trim($labelValuePair[1]);

            switch ($label) {
                case 'level':
                    if (!array_key_exists($value, $levelMap)) {
                        throw new \InvalidArgumentException(sprintf('Unknown level "%s" set in configuration in "%s", expected any of "%s".', $value, $fileName, implode(', ', array_keys($levelMap))));
                    }

                    if (null !== $config['level']) {
                        throw new \InvalidArgumentException(sprintf('Cannot use multiple levels in configuration in "%s".', $fileName));
                    }

                    $config['level'] = $value;
                    break;
                case 'fixers':
                case '--fixers':
                    foreach (explode(',', $value) as $fixer) {
                        $config[$label][] = strtolower(trim($fixer));
                    }

                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unknown configuration item "%s" in "%s".', $label, $fileName));
            }
        }

        if (null === $config['level']) {
            throw new \InvalidArgumentException(sprintf('Level not set in configuration "%s".', $fileName));
        }

        if (null === self::$builtInFixers) {
            $fixer = new Fixer();
            $fixer->registerBuiltInFixers();
            self::$builtInFixers = $fixer->getFixers();
        }

        $fixers = array();
        for ($i = 0, $limit = count(self::$builtInFixers); $i < $limit; ++$i) {
            $fixer = self::$builtInFixers[$i];
            $fixerName = $fixer->getName();
            if ('psr0' === $fixer->getName()) {
                // File based fixer won't work
                continue;
            }

            if ($fixer->getLevel() !== ($fixer->getLevel() & $levelMap[$config['level']])) {
                if (false !== $key = array_search($fixerName, $config['fixers'], true)) {
                    $fixers[] = $fixer;
                    unset($config['fixers'][$key]);
                }
                continue;
            }

            if (false !== $key = array_search($fixerName, $config['--fixers'], true)) {
                unset($config['--fixers'][$key]);
                continue;
            }

            if (in_array($fixerName, $config['fixers'], true)) {
                throw new \InvalidArgumentException(sprintf('Additional fixer "%s" configured, but is already part of the level.', $fixerName));
            }

            $fixers[] = $fixer;
        }

        if (!empty($config['fixers']) || !empty($config['--fixers'])) {
            throw new \InvalidArgumentException(sprintf('Unknown fixers in configuration "%s".', implode(',', empty($config['fixers']) ? $config['--fixers'] : $config['fixers'])));
        }

        return $fixers;
    }

    /**
     * Parses the '--REQUIREMENTS--' block of a '.test' file and determines requirements.
     *
     * @param string $fileName
     * @param string $config
     *
     * @return array
     */
    protected function getRequirementsFromConfig($fileName, $config)
    {
        $requirements = array('hhvm' => true, 'php' => PHP_VERSION);

        if ('' === $config) {
            return $requirements;
        }

        $lines = explode("\n", $config);
        if (empty($lines)) {
            return $requirements;
        }

        foreach ($lines as $line) {
            $labelValuePair = explode('=', $line);
            if (2 !== count($labelValuePair)) {
                throw new \InvalidArgumentException(sprintf('Invalid requirements line "%s" in "%s".', $line, $fileName));
            }

            $label = strtolower(trim($labelValuePair[0]));
            $value = trim($labelValuePair[1]);

            switch ($label) {
                case 'hhvm':
                    $requirements['hhvm'] = 'false' === $value ? false : true;
                    break;
                case 'php':
                    $requirements['php'] = $value;
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unknown configuration item "%s" in "%s".', $label, $fileName));
            }
        }

        return $requirements;
    }
}
