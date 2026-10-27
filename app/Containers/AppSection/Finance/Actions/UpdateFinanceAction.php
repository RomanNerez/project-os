<?php

namespace App\Containers\AppSection\Finance\Actions;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\Tasks\AddFinanceReceiptsTask;
use App\Containers\AppSection\Finance\Tasks\PrepareFinanceDataTask;
use App\Containers\AppSection\Finance\Tasks\UpdateFinanceTask;
use App\Containers\AppSection\Finance\UI\WEB\Requests\UpdateFinanceRequest;
use App\Containers\AppSection\Media\Tasks\MoveMediaDirectoryTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory;

final class UpdateFinanceAction extends ParentAction
{
    public function __construct(
        private readonly PrepareFinanceDataTask $prepareFinanceDataTask,
        private readonly UpdateFinanceTask $updateFinanceTask,
        private readonly AddFinanceReceiptsTask $addFinanceReceiptsTask,
        private readonly MoveMediaDirectoryTask $moveMediaDirectoryTask,
    ) {
    }

    /**
     * Removed receipts are looked up among this record's receipts only, so foreign media ids are ignored.
     * Kept receipts are moved to the new project's folder when the record changes project.
     *
     * @throws ValidationException
     */
    public function run(UpdateFinanceRequest $request, Finance $finance, int $userId): Finance
    {
        $data = $request->validated();
        [$attributes, $taskIds] = $this->prepareFinanceDataTask->run($data, $userId);
        $removedIds = array_map('intval', $data['removed_receipt_ids'] ?? []);
        $oldDirectories = $finance->getMedia(Finance::RECEIPTS_COLLECTION)
            ->mapWithKeys(static fn (Media $media): array => [$media->id => PathGeneratorFactory::create($media)->getPath($media)]);

        $finance = DB::transaction(function () use ($request, $finance, $attributes, $taskIds, $removedIds): Finance {
            // Before the update: the path generator still resolves the old project's folder.
            $finance->getMedia(Finance::RECEIPTS_COLLECTION)
                ->whereIn('id', $removedIds)
                ->each->delete();

            $finance = $this->updateFinanceTask->run($finance->id, $attributes, $taskIds);

            $this->addFinanceReceiptsTask->run($finance, $request);

            return $finance;
        });

        $finance->load('media')->getMedia(Finance::RECEIPTS_COLLECTION)
            ->each(function (Media $media) use ($oldDirectories): void {
                $oldDirectory = $oldDirectories->get($media->id);

                if (null !== $oldDirectory) {
                    $this->moveMediaDirectoryTask->run($media, $oldDirectory);
                }
            });

        return $finance;
    }
}
