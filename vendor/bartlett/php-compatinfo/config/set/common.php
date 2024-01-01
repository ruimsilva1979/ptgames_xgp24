<?php declare(strict_types=1);
/**
 * This file is part of the PHP_CompatInfo package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Bartlett\CompatInfo\Application\Event\Dispatcher\EventDispatcher;
use Bartlett\CompatInfo\Application\Extension\ExtensionLoaderInterface;
use Bartlett\CompatInfo\Application\Extension\FactoryExtensionLoader;
use Bartlett\CompatInfo\Presentation\Console\Application;
use Bartlett\CompatInfo\Presentation\Console\ApplicationInterface;
use Bartlett\CompatInfo\Presentation\Console\CommandLoaderInterface;
use Bartlett\CompatInfo\Presentation\Console\FactoryCommandLoader;
use Bartlett\CompatInfo\Presentation\Console\Input\Input;
use Bartlett\CompatInfo\Presentation\Console\Output\Output;
use function Bartlett\CompatInfo\Infrastructure\Framework\Symfony\service;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

/**
 * Build the Container with common parameters and services
 *
 * @author Laurent Laville
 *
 * @link https://symfony.com/doc/current/components/dependency_injection.html#avoiding-your-code-becoming-dependent-on-the-container
 */
return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
    ;

    $services->set(InputInterface::class, Input::class)
        // for configuration option of bin file
        ->public()
    ;
    $services->set(OutputInterface::class, Output::class)
        // for configuration option of bin file
        ->public()
    ;

    $services->set(ApplicationInterface::class, Application::class)
        ->call('setDispatcher', [service(EventDispatcherInterface::class)])
        // for bin file
        ->public()
    ;

    // @link https://symfony.com/doc/current/console/lazy_commands.html#factorycommandloader
    $services->set(CommandLoaderInterface::class, FactoryCommandLoader::class)
        ->arg('$commands', tagged_iterator('console.command'))
        // for bin file
        ->public()
    ;

    // @link https://symfony.com/doc/current/service_container/tags.html
    $services->set(ExtensionLoaderInterface::class, FactoryExtensionLoader::class)
        ->arg('$extensions', tagged_iterator('app.extension'))
    ;

    $services->set(LoggerInterface::class, NullLogger::class);

    $services->alias(EventDispatcherInterface::class . ' $compatibilityEventDispatcher', EventDispatcher::class);
};
