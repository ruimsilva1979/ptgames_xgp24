<?php declare(strict_types=1);
/**
 * This file is part of the PHP_CompatInfo package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\CompatInfo\Application\Extension;

use Bartlett\CompatInfo\Application\Event\AfterAnalysisEvent;
use Bartlett\CompatInfo\Application\Event\AfterAnalysisInterface;
use Bartlett\CompatInfo\Application\Event\AfterFileAnalysisEvent;
use Bartlett\CompatInfo\Application\Event\AfterFileAnalysisInterface;
use Bartlett\CompatInfo\Application\Event\BeforeAnalysisEvent;
use Bartlett\CompatInfo\Application\Event\BeforeAnalysisInterface;
use Bartlett\CompatInfo\Application\Event\BeforeFileAnalysisEvent;
use Bartlett\CompatInfo\Application\Event\BeforeFileAnalysisInterface;
use Bartlett\CompatInfo\Presentation\Console\Style;

use Symfony\Component\Console\Helper\ProgressBar as SymfonyProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Extension that inject progress display at execution.
 *
 * @author Laurent Laville
 * @since Release 6.0.0
 */
final class ProgressBar implements
    ExtensionInterface,
    BeforeAnalysisInterface,
    AfterAnalysisInterface,
    BeforeFileAnalysisInterface,
    AfterFileAnalysisInterface
{
    private ?SymfonyProgressBar $progressBar;

    /**
     * ProgressBar extension constructor.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $io = new Style($input, $output);

        if ($input->hasOption('progress') && $input->getOption('progress')) {
            $this->progressBar = $io->createProgressBar();
        } else {
            $this->progressBar = null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'progress_bar';
    }

    /**
     * {@inheritDoc}
     */
    public function beforeAnalysis(BeforeAnalysisEvent $event): void
    {
        if ($this->progressBar) {
            $this->progressBar->start(count($event->getArgument('queue')));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function afterAnalysis(AfterAnalysisEvent $event): void
    {
        if ($this->progressBar) {
            $this->progressBar->finish();
            $this->progressBar->clear();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeAnalyzeFile(BeforeFileAnalysisEvent $event): void
    {
        if ($this->progressBar) {
            $fileInfo = $event->getArgument('file');
            $this->progressBar->setMessage(sprintf('File %s in progress...', $fileInfo->getRelativePathname()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function afterAnalyzeFile(AfterFileAnalysisEvent $event): void
    {
        if ($this->progressBar) {
            $this->progressBar->advance();
        }
    }
}
