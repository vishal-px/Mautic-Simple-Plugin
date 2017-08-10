<?php
namespace MauticPlugin\MauticHelloWorldBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\LeadTimelineEvent;
use Mautic\LeadBundle\LeadEvents;

/**
 * Class LeadSubscriber
 */
class LeadSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return [
            LeadEvents::TIMELINE_ON_GENERATE => ['onTimelineGenerate', 0]
        ];
    }

    /**
     * Compile events for the lead timeline
     *
     * @param LeadTimelineEvent $event
     */
    public function onTimelineGenerate(LeadTimelineEvent $event)
    {
        // Add this event to the list of available events which generates the event type filters
        $eventTypeKey  = 'visited.worlds';
        $eventTypeName = $this->translator->trans('mautic.hello.world.visited_worlds');
        $event->addEventType($eventTypeKey, $eventTypeName);

        // Determine if this event has been filtered out
        if (!$event->isApplicable($eventTypeKey)) {

            return;
        }

        /** @var \MauticPlugin\HelloWorldRepository\Entity\WorldRepository $repository */
        $repository = $this->em->getRepository('MauticHelloWorldBundle:World');

        // $event->getQueryOptions() provide timeline filters, etc.
        // This method should use DBAL to obtain the events to be injected into the timeline based on pagination
        // but also should query for a total number of events and return an array of ['total' => $x, 'results' => []].
        // There is a TimelineTrait to assist with this. See repository example.
        $stats = $repository->getVisitedWorldStats($event->getLead()->getId(), $event->getQueryOptions());

        // If isEngagementCount(), this event should only inject $stats into addToCounter() to append to data to generate
        // the engagements graph. Not all events are engagements if they are just informational so it could be that this
        // line should only be used when `!$event->isEngagementCount()`. Using TimelineTrait will determine the appropriate
        // return value based on the data included in getQueryOptions() if used in the stats method above.
        $event->addToCounter($eventTypeKey, $stats);

        if (!$event->isEngagementCount()) {
            // Add the events to the event array
            foreach ($stats['results'] as $stat) {
                if ($stat['dateSent']) {
                    $event->addEvent(
                        [
                            // Event key type
                            'event'           => $eventTypeKey,
                            // Event name/label - can be a string or an array as below to convert to a link
                            'eventLabel'      => [
                                'label' => $stat['name'],
                                'href'  => $this->router->generate(
                                    'mautic_dynamicContent_action',
                                    ['objectId' => $stat['dynamic_content_id'], 'objectAction' => 'view']
                                )
                            ],
                            // Translated string displayed in the Event Type column
                            'eventType'       => $eventTypeName,
                            // \DateTime object for the timestamp column
                            'timestamp'       => $stat['dateSent'],
                            // Optional details passed through to the contentTemplate
                            'extra'           => [
                                'stat' => $stat,
                                'type' => 'sent'
                            ],
                            // Optional template to customize the details of the event in the timeline
                            'contentTemplate' => 'MauticDynamicContentBundle:SubscribedEvents\Timeline:index.html.php',
                            // Font Awesome class to display as the icon
                            'icon'            => 'fa-envelope'
                        ]
                    );
                }
            }
        }
    }