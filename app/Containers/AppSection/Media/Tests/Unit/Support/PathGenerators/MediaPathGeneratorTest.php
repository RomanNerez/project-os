<?php

namespace App\Containers\AppSection\Media\Tests\Unit\Support\PathGenerators;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Support\PathGenerators\FinanceMediaPathGenerator;
use App\Containers\AppSection\Media\Support\PathGenerators\TaskMediaPathGenerator;
use App\Containers\AppSection\Media\Support\PathGenerators\UserMediaPathGenerator;
use App\Containers\AppSection\Media\Tests\UnitTestCase;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\PathGenerators\MediaPathGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory;

#[CoversClass(MediaPathGenerator::class)]
#[CoversClass(TaskMediaPathGenerator::class)]
#[CoversClass(FinanceMediaPathGenerator::class)]
#[CoversClass(UserMediaPathGenerator::class)]
final class MediaPathGeneratorTest extends UnitTestCase
{
    public function testTaskMediaPaths(): void
    {
        $media = $this->makeMedia(new Task(['project_id' => 12]), modelId: 45);

        $generator = PathGeneratorFactory::create($media);

        self::assertInstanceOf(TaskMediaPathGenerator::class, $generator);
        self::assertSame('projects/12/tasks/45/301/', $generator->getPath($media));
        self::assertSame('projects/12/tasks/45/301/conversions/', $generator->getPathForConversions($media));
        self::assertSame('projects/12/tasks/45/301/responsive-images/', $generator->getPathForResponsiveImages($media));
    }

    public function testFinanceMediaPaths(): void
    {
        $media = $this->makeMedia(new Finance(['project_id' => 12]), modelId: 7);

        $generator = PathGeneratorFactory::create($media);

        self::assertInstanceOf(FinanceMediaPathGenerator::class, $generator);
        self::assertSame('projects/12/finance/7/301/', $generator->getPath($media));
    }

    public function testUserAvatarPaths(): void
    {
        $media = $this->makeMedia(new User(), modelId: 45, collectionName: User::AVATAR_COLLECTION);

        $generator = PathGeneratorFactory::create($media);

        self::assertInstanceOf(UserMediaPathGenerator::class, $generator);
        self::assertSame('users/45/avatar/', $generator->getPath($media));
        self::assertSame('users/45/avatar/conversions/', $generator->getPathForConversions($media));
    }

    public function testPrependsConfiguredPrefix(): void
    {
        config(['media-library.prefix' => 'uploads']);
        $media = $this->makeMedia(new Task(['project_id' => 12]), modelId: 45);

        self::assertSame('uploads/projects/12/tasks/45/301/', (new TaskMediaPathGenerator())->getPath($media));
    }

    public function testRejectsMediaOfAnotherModel(): void
    {
        $media = $this->makeMedia(new User(), modelId: 1);

        $this->expectException(\UnexpectedValueException::class);

        (new TaskMediaPathGenerator())->getPath($media);
    }

    private function makeMedia(Task|Finance|User $model, int $modelId, string $collectionName = 'default'): Media
    {
        $model->id = $modelId;

        $media = new Media(['model_type' => $model->getMorphClass(), 'model_id' => $modelId, 'collection_name' => $collectionName]);
        $media->id = 301;
        $media->setRelation('model', $model);

        return $media;
    }
}
