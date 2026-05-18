<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Application\DTO\IdempotencyData;
use Application\UseCases\Task\CreateTaskUseCase;
use Application\UseCases\Task\DeleteTaskUseCase;
use Application\UseCases\Task\GetTaskUseCase;
use Application\UseCases\Task\ListTasksUseCase;
use Application\UseCases\Task\UpdateTaskUseCase;
use Domain\Task\Task;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController
{
    public function __construct(
        private readonly CreateTaskUseCase $createTask,
        private readonly ListTasksUseCase $listTasks,
        private readonly GetTaskUseCase $getTask,
        private readonly UpdateTaskUseCase $updateTask,
        private readonly DeleteTaskUseCase $deleteTask,
    ) {
    }

    public function create(Request $request): Response
    {
        $data = $request->toArray();
        $idempotency = new IdempotencyData(
            key: $request->headers->get('Idempotency-Key'),
            operation: $request->getMethod() . ' ' . $request->getPathInfo(),
        );

        $task = $this->createTask->execute(
            title: $data['title'] ?? '',
            idempotencyData: $idempotency,
            description: $data['description'] ?? null,
            status: $data['status'] ?? null,
            requestBody: $data,
        );

        return new JsonResponse(
            $task->toArray(),
            Response::HTTP_CREATED,
            ['Location' => '/tasks/' . $task->id->value],
        );
    }

    public function index(Request $request): Response
    {
        $page = $this->listTasks->execute(
            status: $request->query->getString('status') ?: null,
            limit: $request->query->getInt('limit', 100),
            cursor: $request->query->getString('cursor') ?: null,
        );

        return new JsonResponse(
            [
                'items' => array_map(
                    fn (Task $task): array => $task->toArray(),
                    $page['items'],
                ),
                'nextCursor' => $page['nextCursor'],
            ],
            Response::HTTP_OK,
        );
    }

    public function show(Request $request): Response
    {
        $task = $this->getTask->execute((string) $request->attributes->get('id'));

        return new JsonResponse($task->toArray(), Response::HTTP_OK);
    }

    public function update(Request $request): Response
    {
        $data = $request->toArray();
        $task = $this->updateTask->execute(
            id: (string) ($request->attributes->get('id') ?? $data['id'] ?? ''),
            data: $data,
        );

        return new JsonResponse($task->toArray(), Response::HTTP_OK);
    }

    public function delete(Request $request): Response
    {
        $this->deleteTask->execute((string) $request->attributes->get('id'));

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
