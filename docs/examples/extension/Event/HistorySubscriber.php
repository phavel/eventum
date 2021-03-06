<?php

/*
 * This file is part of the Eventum (Issue Tracking System) package.
 *
 * @copyright (c) Eventum Team
 * @license GNU General Public License, version 2 or later (GPL-2+)
 *
 * For the full copyright and license information,
 * please see the COPYING and AUTHORS files
 * that were distributed with this source code.
 */

namespace Eventum\Event;

use Misc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HistorySubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            SystemEvents::HISTORY_ADD => 'historyAdded',
        ];
    }

    /**
     * @param UnstructuredEvent $event
     */
    public function historyAdded(UnstructuredEvent $event)
    {
        $his_summary = Misc::processTokens(ev_gettext($event->his_summary), $event->his_context);

        error_log("HISTORY: #{$event->his_id}: $his_summary");
    }
}
