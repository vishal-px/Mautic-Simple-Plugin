<?php
namespace MauticPlugin\HelloWorldBundle\EventListener;

use MauticPlugin\HelloWorldBundle\HelloWorldEvents;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\FormBundle\Event as Events;
use Mautic\FormBundle\FormEvents;

/**
 * Class FormSubscriber
 */
class FormSubscriber extends CommonSubscriber
{

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            FormEvents::FORM_ON_BUILD => array('onFormBuilder', 0)
        );
    }

    /**
     * Add a simple email form
     *
     * @param FormBuilderEvent $event
     */
    public function onFormBuilder(Events\FormBuilderEvent $event)
    {
        // Register a form submit actions
        $event->addSubmitAction(
            'helloworld.sendemail',
            [
                // Label to group by in the dropdown
                'group'       => 'plugin.helloworld.header',

                // Label to list by in the dropdown
                'label'       => 'plugin.helloworld.formaction.send_email',
                'description' => 'plugin.helloworld.formaction.send_email_descr',

                // Form service for custom config options
                'formType'    => 'helloworld_worlds',
                'formTheme'   => 'HelloWorldBundle:FormTheme\SubmitAction',

                // Callback method to be executed after the submission 
                'eventName'    => HelloWorldEvents::FORM_SUBMIT_ACTION
            ]
        );

        // Register a custom validation service
        $event->addValidator(
            'helloworld.customfield',
            [
                'eventName' => HelloWorldEvents::FORM_VALIDATION,
                'fieldType' => 'helloworld.customfield' // Optional - otherwise all fields will be sent through this listener for validation
            ]
        );

        // Register a custom form field
        $event->addFormField(
            'helloworld.customfield',
            [
                // Field label
                'label'    => 'plugin.helloworld.formfield.customfield',

                // Form service for the field's configuration
                'formType' => 'helloworld_worlds',

                // Template to use to render the formType
                'template' => 'HelloWorldBundle:SubscribedEvents\FormField:customfield.html.php'
            ]
        );
    }
}