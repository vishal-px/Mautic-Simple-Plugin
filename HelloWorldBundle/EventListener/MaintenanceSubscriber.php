<?php
namespace MauticPlugin\HelloWorldBundle\EventListener;

use Doctrine\DBAL\Connection;
use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\MaintenanceEvent;
use Mautic\CoreBundle\Factory\MauticFactory;

/**
 * Class MaintenanceSubscriber
 */
class MaintenanceSubscriber extends CommonSubscriber
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * MaintenanceSubscriber constructor.
     *
     * @param MauticFactory $factory
     * @param Connection    $db
     */
    public function __construct(MauticFactory $factory, Connection $db)
    {
        parent::__construct($factory);

        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::MAINTENANCE_CLEANUP_DATA => ['onDataCleanup', -50]
        ];
    }

    /**
     * @param $isDryRun
     * @param $date
     *
     * @return int
     */
    public function onDataCleanup (MaintenanceEvent $event)
    {
        $qb = $this->db->createQueryBuilder()
            ->setParameter('date', $event->getDate()->format('Y-m-d H:i:s'));

        if ($event->isDryRun()) {
            $rows = (int) $qb->select('count(*) as records')
                ->from(MAUTIC_TABLE_PREFIX.'worlds', 'w')
                ->where(
                    $qb->expr()->gte('w.date_added', ':date')
                )
                ->execute()
                ->fetchColumn();
        } else {
            $rows = (int) $qb->delete(MAUTIC_TABLE_PREFIX.'worlds')
                ->where(
                    $qb->expr()->lte('date_added', ':date')
                )
                ->execute();
        }

        $event->setStat($this->translator->trans('mautic.maintenance.hello_world'), $rows, $qb->getSQL(), $qb->getParameters());
    }
}