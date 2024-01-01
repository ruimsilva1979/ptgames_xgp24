<?php declare(strict_types=1);
/**
 * This file is part of the PHP_CompatInfo package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\CompatInfo\Presentation\Console\Command;

use Bartlett\CompatInfo\Application\Query\Analyser\Compatibility\GetCompatibilityQuery;
use Bartlett\CompatInfo\Presentation\Console\ApplicationInterface;
use Bartlett\CompatInfo\Presentation\Console\Style;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

/**
 * Analyse a data source to find out requirements.
 *
 * @author Laurent Laville
 * @since Release 6.0.0
 */
final class AnalyserCommand extends AbstractCommand implements CommandInterface
{
    public const NAME = 'analyser:run';

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Analyse a data source to find out requirements')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path to the data source'
            )
            ->addOption(
                'exclude',
                'e',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Provide one or more folders to exclude from data source scan'
            )
            ->addOption(
                'stop-on-failure',
                null,
                null,
                'Stop execution upon first error generated during lexing, parsing or some other operation'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ApplicationInterface $app */
        $app = $this->getApplication();
        $compatibilityQuery = new GetCompatibilityQuery(
            $input->getArgument('source'),
            $input->getOption('exclude'),
            $input->hasOption('stop-on-failure'),
            $app->getInstalledVersion()
        );

        try {
            $this->queryBus->query($compatibilityQuery);
        } catch (HandlerFailedException $e) {
            $io = new Style($input, $output);
            $io->error($e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
