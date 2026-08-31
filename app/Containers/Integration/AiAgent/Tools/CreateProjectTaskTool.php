<?php

namespace App\Containers\Integration\AiAgent\Tools;

use App\Containers\AppSection\Task\Tasks\CreateTaskTask;
use LLPhant\Chat\FunctionInfo\FunctionInfo;
use LLPhant\Chat\FunctionInfo\Parameter;

final class CreateProjectTaskTool extends BaseTool
{   
    public function __construct(
        private readonly CreateTaskTask $createTaskTask,
    ) {}

    /**
     * @return FunctionInfo
     */
    public function toFunctionInfo(): FunctionInfo
    {
        $title = new Parameter('title', 'string', 'Назва або короткий суть задачі');
        $description = new Parameter('description', 'string', 'Детальний опис задачі, якщо він є');

        return new FunctionInfo(
            name: 'createProjectTask',
            instance: $this,
            description: 'Створює нову задачу в проєкті, коли користувач просить щось запланувати, зробити або додати в список задач.',
            parameters: [$title, $description]
        );
    }

    /**
     * Створює нову задачу в проєкті.
     *
     * @param string $title Назва або короткий зміст задачі
     * @param string|null $description Детальний опис задачі (необов'язково)
     * @return string Повідомлення про результат створення
     */
    public function createProjectTask(string $title, ?string $description = null): string
    {
        $this->createTaskTask->run([
            'user_id' => $this->userId,
            'title' => $title,
            'description' => $description
        ]);

        $info = "Задача '{$title}' успішно створена!";

        return $info;
    }
}