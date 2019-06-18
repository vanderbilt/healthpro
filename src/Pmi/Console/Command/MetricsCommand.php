<?php

namespace Pmi\Console\Command;

use Pmi\Application\HpoApplication;
use Pmi\Drc\RdrMetrics;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Metrics Command
 *
 * Allows debugging local with the RDR Metrics API endpoint
 */
class MetricsCommand extends Command
{
    /**
     * Allowed Stratifications
     *
     * @var array
     */
    private static $STRATIFICATIONS = [
        'TOTAL', 'ENROLLMENT_STATUS', 'GENDER_IDENTITY', 'AGE_RANGE', 'RACE',
        'EHR_CONSENT', 'EHR_RATIO', 'FULL_STATE', 'FULL_CENSUS', 'FULL_AWARDEE',
        'LIFECYCLE', 'GEO_STATE', 'GEO_CENSUS', 'GEO_AWARDEE'
    ];
    private static $STATUSES = [
        'REGISTERED', 'INTERESTED', 'PARTICIPANT', 'MEMBER', 'FULL_PARTICIPANT',
        'CORE_PARTICIPANT'
     ];

    /**
     * Configure
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('pmi:metrics')
            ->addOption(
                'start_date',
                null,
                InputOption::VALUE_REQUIRED,
                'Date range beginning.',
                date('Y-m-d', strtotime('today - 7 days'))
            )
            ->addOption(
                'end_date',
                null,
                InputOption::VALUE_REQUIRED,
                'Date range ending.',
                date('Y-m-d')
            )
            ->addOption(
                'stratification',
                null,
                InputOption::VALUE_REQUIRED,
                'How to stack the returned data.',
                'ENROLLMENT_STATUS'
            )
            ->addOption(
                'centers',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Filter to specified centers (awardees)'
            )
            ->addOption(
                'statuses',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Filter to specified statuses.'
            )
            ->addOption(
                'history',
                null,
                InputOption::VALUE_NONE,
                'Return cached history of data; required for certain stratifications.'
            )
            ->addOption(
                'api_version',
                null,
                InputOption::VALUE_REQUIRED,
                'The version of the API to use when making the metrics call.',
                '1'
            )
            ->addOption(
                'pretty',
                null,
                InputOption::VALUE_NONE,
                'Pretty formatting for JSON ouput'
            )
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                'Show request debug'
            )
            ->setDescription('Get current metrics from the RDR.')
        ;
    }

    /**
     * Execute
     *
     * @param IntputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setFormatter(new OutputFormatter(true));

        $start_date = $input->getOption('start_date');
        $end_date = $input->getOption('end_date');
        $stratification = $input->getOption('stratification');
        $centers = $input->getOption('centers');
        $statuses = $input->getOption('statuses');
        $version = $input->getOption('api_version');
        $pretty = ($input->getOption('pretty') !== false) ? JSON_PRETTY_PRINT : 0;
        $params = [
            'version' => (int) $version,
            'history' => (bool) $input->getOption('history')
        ];

        // Validate stratification
        if (!in_array($stratification, self::$STRATIFICATIONS)) {
            $output->writeln(sprintf(
                '<error>Invalid stratification: "%s"; Valid options: %s</error>',
                $stratification,
                join(', ', self::$STRATIFICATIONS)
            ));
            // Throw a non-zero exit status
            return 1;
        }

        // Validate start and end dates
        if ((bool) !strtotime($start_date) || (bool) !strtotime($end_date)) {
            $output->writeln(sprintf(
                '<error>Invalid dates: "%s", "%s"</error>',
                $start_date,
                $end_date
            ));
            // Throw a non-zero exit status
            return 1;
        }

        // Validate statuses
        if (is_string($statuses)) {
            $statuses = explode(',', $statuses);
        }
        if (!empty($statuses)) {
            foreach ($statuses as $status) {
                if (!in_array($status, self::$STATUSES)) {
                    $output->writeln(sprintf(
                        '<error>Invalid status: "%s"; Valid options: %s</error>',
                        $status,
                        join(', ', self::$STATUSES)
                    ));
                    // Throw a non-zero exit status
                    return 1;
                }
            }
        }

        putenv('PMI_ENV=' . HpoApplication::ENV_LOCAL);
        $app = new HpoApplication([
            'isUnitTest' => true,
            'debug' => true
        ]);
        $app->setup();

        if ($input->getOption('debug')) {
            $output->writeln('<info>Debugging Information</info>');
            $output->writeln('  Start Date:            ' . $start_date);
            $output->writeln('  End Date:              ' . $end_date);
            $output->writeln('  Stratification:        ' . $stratification);
            $output->writeln('  Centers:               ' . json_encode($centers));
            $output->writeln('  Statuses:              ' . json_encode($statuses));
            $output->writeln('  Additional Parameters: ' . json_encode($params));
            $output->writeln('');
        }

        $metricsApi = new RdrMetrics($app['pmi.drc.rdrhelper']);
        $data = $metricsApi->metrics($start_date, $end_date, $stratification, $centers, $statuses, $params);

        $output->writeln(json_encode($data, $pretty));
    }
}
