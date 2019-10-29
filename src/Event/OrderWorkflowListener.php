<?php
/**
 * Created by PhpStorm.
 * User: timur
 * Date: 10.06.19
 * Time: 17:24
 */

namespace App\Event;


use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class OrderWorkflowListener implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            // Guard events
            'workflow.order.guard.' . Order::TRANSITION_CHECKOUT => 'guardCheckout',
            // todo:

            // Enter events
            'workflow.order.enter.' . Order::STATE_CONFIRMED => 'enterConfirmed',

        ] ;
    }

    public static function guardCheckout()
    {
        // todo:
    }

    public function enterConfirmed(Event $event): void
    {
        $order = $event->getSubject();

        // todo:
        // send the email to a user
    }
}