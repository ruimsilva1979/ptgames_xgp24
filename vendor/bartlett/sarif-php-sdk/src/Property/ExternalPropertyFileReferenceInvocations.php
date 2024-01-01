<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-SDK package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Property;

use Bartlett\Sarif\Definition\ExternalPropertyFileReference;

/**
 * @author Laurent Laville
 */
trait ExternalPropertyFileReferenceInvocations
{
    /**
     * @var ExternalPropertyFileReference[]
     */
    protected $invocations;

    /**
     * @param ExternalPropertyFileReference[] $invocations
     */
    public function addInvocations(array $invocations): void
    {
        foreach ($invocations as $invocation) {
            if ($invocation instanceof ExternalPropertyFileReference) {
                $this->invocations[] = $invocation;
            }
        }
    }
}
