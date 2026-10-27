<?php

namespace App\Containers\AppSection\Event\UI\WEB\Controllers;

use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ShowCalendarController extends WebController
{
    /**
     * Events themselves are loaded by FullCalendar per visible range, see ListEventsController.
     */
    public function __invoke(): Response
    {
        return Inertia::render('calendar');
    }
}
