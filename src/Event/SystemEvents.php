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

final class SystemEvents
{
    /**
     * Event fired when history entry is added
     *
     * @since 3.3.0
     */
    const HISTORY_ADD = 'history.add';

    /**
     * @since 3.3.0
     */
    const USER_CREATE = 'user.create';

    /**
     * @since 3.3.0
     */
    const USER_UPDATE = 'user.update';
}
