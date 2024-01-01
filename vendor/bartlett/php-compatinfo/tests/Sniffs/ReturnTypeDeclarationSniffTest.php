<?php declare(strict_types=1);
/**
 * This file is part of the PHP_CompatInfo package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\CompatInfo\Tests\Sniffs;

/**
 * Unit tests for PHP_CompatInfo package, return type declaration sniff
 *
 * @author Laurent Laville
 * @since  Class available since Release 5.4.0
 *
 * @link https://wiki.php.net/rfc/return_types
 * @link https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.return-type-declarations
 * @link https://www.php.net/manual/en/migration71.new-features.php#migration71.new-features.void-functions
 * @link https://github.com/llaville/php-compat-info/issues/233
 * @link https://github.com/llaville/php-compat-info/issues/273
 */
final class ReturnTypeDeclarationSniffTest extends SniffTestCase
{
    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$fixtures .= 'functions' . DIRECTORY_SEPARATOR;
    }

    /**
     * Regression test issue #233
     *
     * @link https://github.com/llaville/php-compat-info/issues/233
     *       PHP 7 requirement not detected for return type hint
     * @group regression
     * @return void
     */
    public function testReturnTypeHint()
    {
        $dataSource = 'return_types.php';
        $metrics    = $this->executeAnalysis($dataSource);
        $versions   = $metrics[self::$analyserId]['versions'];

        $this->assertEquals(
            '7.0.0',
            $versions['php.min']
        );

        $this->assertEquals(
            '',
            $versions['php.max']
        );
    }

    /**
     * Regression test for issue #273
     *
     * @link https://github.com/llaville/php-compat-info/issues/273
     *       PHP 7.1 Nullable types not being detected
     * @group regression
     * @return void
     */
    public function testNullableReturnTypeHint()
    {
        $dataSource = 'gh273.php';
        $metrics    = $this->executeAnalysis($dataSource);
        $versions   = $metrics[self::$analyserId]['versions'];
        $functions  = $metrics[self::$analyserId]['functions'];

        $this->assertEquals(
            '7.1.0',
            $versions['php.min']
        );

        $this->assertEquals(
            '',
            $versions['php.max']
        );

        $this->assertEquals(
            '7.1.0',
            $functions['testReturn1']['php.min']
        );

        $this->assertEquals(
            '7.1.0',
            $functions['testReturn2']['php.min']
        );
    }

    /**
     * Feature test for return type void functions detection
     *
     * @link https://www.php.net/manual/en/migration71.new-features.php#migration71.new-features.void-functions
     * @group features
     * @return void
     */
    public function testVoidFunctions()
    {
        $dataSource = 'void_functions.php';
        $metrics    = $this->executeAnalysis($dataSource);
        $functions  = $metrics[self::$analyserId]['functions'];

        $this->assertEquals(
            '4.0.0',
            $functions['noReturnType']['php.min']
        );

        $this->assertEquals(
            '',
            $functions['noReturnType']['php.max']
        );

        $this->assertEquals(
            '7.1.0',
            $functions['voidReturnType']['php.min']
        );

        $this->assertEquals(
            '',
            $functions['voidReturnType']['php.max']
        );
    }
}
