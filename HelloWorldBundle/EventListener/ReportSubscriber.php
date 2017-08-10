<?php
namespace MauticPlugin\HelloWorldBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\GraphHelper;
use Mautic\ReportBundle\Event\ReportBuilderEvent;
use Mautic\ReportBundle\Event\ReportGeneratorEvent;
use Mautic\ReportBundle\Event\ReportGraphEvent;
use Mautic\ReportBundle\ReportEvents;
use Mautic\CoreBundle\Helper\Chart\ChartQuery;
use Mautic\CoreBundle\Helper\Chart\LineChart;

/**
 * Class ReportSubscriber
 */
class ReportSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents ()
    {
        return array(
            ReportEvents::REPORT_ON_BUILD          => array('onReportBuilder', 0),
            ReportEvents::REPORT_ON_GENERATE       => array('onReportGenerate', 0),
            ReportEvents::REPORT_ON_GRAPH_GENERATE => array('onReportGraphGenerate', 0)
        );
    }

    /**
     * Add available tables, columns, and graphs to the report builder lookup
     *
     * @param ReportBuilderEvent $event
     *
     * @return void
     */
    public function onReportBuilder (ReportBuilderEvent $event)
    {
        // Use checkContext() to determine if the report being requested is this report
        if ($event->checkContext(array('worlds'))) {
            // Define the columns that are available to the report.
            $prefix  = 'w.';
            $columns = array(
                $prefix . 'visit_count' => array(
                    'label' => 'mautic.hellobundle.report.visit_count',
                    'type'  => 'int'
                ),
                $prefix . 'world' => array(
                    'label' => 'mautic.hellobundle.report.world',
                    'type'  => 'text'
                ),
            );

             // Several helper functions are available to append common columns such as categories, publish state fields, lead, etc.  Refer to the ReportBuilderEvent class for more details.
            $columns = $filters = array_merge($columns, $event->getStandardColumns($prefix), $event->getCategoryColumns());

            // Optional to override and update filters, i.e. change it to a select list for the UI
            $filters[$prefix.'world']['type'] = 'select';
            $filters[$prefix.'world']['list'] = array(
                'earth' => 'Earth',
                'mars'  => 'Mars'
            );

            // Add the table to the list
            $event->addTable('worlds',
                array(
                    'display_name' => 'mautic.helloworld.worlds',
                    'columns'      => $columns,
                    'filters'      => $filters // Defaults to columns if not set
                )
            );

            // Register available graphs; can use line, pie, or table
            $event->addGraph('worlds', 'line', 'mautic.hellobundle.graph.line.visits');
        }
    }

    /**
     * Initialize the QueryBuilder object used to generate the report's data.
     * This should use Doctrine's DBAL layer, not the ORM so be sure to use
     * the real schema column names (not the ORM property names) and the
     * MAUTIC_TABLE_PREFIX constant.
     *
     * @param ReportGeneratorEvent $event
     *
     * @return void
     */
    public function onReportGenerate (ReportGeneratorEvent $event)
    {
        $context = $event->getContext();
        if ($context == 'worlds') {
            $qb = $event->getQueryBuilder();

            $qb->from(MAUTIC_TABLE_PREFIX . 'worlds', 'w');
            $event->addCategoryLeftJoin($qb, 'w');

            $event->setQueryBuilder($qb);
        }
    }

    /**
     * Generate the graphs
     *
     * @param ReportGraphEvent $event
     *
     * @return void
     */
    public function onReportGraphGenerate (ReportGraphEvent $event)
    {
        if (!$event->checkContext('worlds')) {
            return;
        }

        $graphs   = $event->getRequestedGraphs();
        $qb       = $event->getQueryBuilder();

        foreach ($graphs as $graph) {
            $queryBuilder = clone $qb;
            $options      = $event->getOptions($graph);
            /** @var ChartQuery $chartQuery */
            $chartQuery    = clone $options['chartQuery'];
            $chartQuery->applyDateFilters($queryBuilder, 'date_added', 'v');

            switch ($graph) {
                case 'mautic.hellobundle.graph.line.visits':
                    $chart = new LineChart(null, $options['dateFrom'], $options['dateTo']);
                    $chartQuery->modifyTimeDataQuery($queryBuilder, 'date_added', 'v');
                    $visits = $chartQuery->loadAndBuildTimeData($queryBuilder);
                    $chart->setDataset($options['translator']->trans('mautic.hellobundle.graph.line.visits'), $visits);
                    $data         = $chart->render();
                    $data['name'] = $graph;
                    $data['iconClass'] = 'fa-tachometer';
                    $event->setGraph($graph, $data);

                    break;
            }
        }
    }
}

