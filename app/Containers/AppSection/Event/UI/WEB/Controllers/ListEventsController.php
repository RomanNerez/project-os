<?php

namespace App\Containers\AppSection\Event\UI\WEB\Controllers;

use App\Containers\AppSection\Event\Actions\ListEventsAction;
use App\Containers\AppSection\Event\UI\API\Transformers\EventTransformer;
use App\Containers\AppSection\Event\UI\WEB\Requests\ListEventsRequest;
use App\Ship\Parents\Controllers\WebController;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

final class ListEventsController extends WebController
{
    public function __construct(
        private readonly ListEventsAction $action,
    ) {
    }

    /**
     * Visible events of the range as plain JSON for FullCalendar's event source.
     */
    public function __invoke(ListEventsRequest $request): JsonResponse
    {
        $events = $this->action->run(
            $request->user('web')->id,
            CarbonImmutable::parse($request->string('start')->value()),
            CarbonImmutable::parse($request->string('end')->value()),
        );

        return response()->json(
            fractal($events, new EventTransformer())->parseIncludes(['participants'])->toArray(),
        );
    }
}
