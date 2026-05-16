<?php

declare(strict_types=1);

namespace Infrastructure\Kernel;

use Application\UseCases\Task\CreateTaskUseCase;
use Application\UseCases\Task\DeleteTaskUseCase;
use Application\UseCases\Task\GetTaskUseCase;
use Application\UseCases\Task\ListTasksUseCase;
use Application\UseCases\Task\UpdateTaskUseCase;
use Infrastructure\Config\Config;
use Infrastructure\Config\Globals;
use Infrastructure\DataBase\MigrationRunner;
use Infrastructure\DataBase\Repositories\IdempotencyRepository;
use Infrastructure\DataBase\Repositories\TaskRepository;
use Infrastructure\Http\Controllers\EchoController;
use Infrastructure\Http\Controllers\HeadersController;
use Infrastructure\Http\Controllers\HealthController;
use Infrastructure\Http\Controllers\TaskController;
use PDO;

final class ContainerFactory
{
    public function create(): Container
    {
        $container = new Container();

        $container->set(Config::class, static fn (): Config => new Config());
        $container->set(EchoController::class, static fn (): EchoController => new EchoController());
        $container->set(HeadersController::class, static fn (): HeadersController => new HeadersController());
        $container->set(HealthController::class, static fn (): HealthController => new HealthController());

        $container->set(PDO::class, static function (Container $container): PDO {
            /** @var Config $config */
            $config = $container->get(Config::class);
            $databasePath = (string) $config->get(Globals::DATABASE_PATH_NAME);

            if (!is_dir(dirname($databasePath))) {
                mkdir(dirname($databasePath), 0777, true);
            }

            $pdo = new PDO('sqlite:' . $databasePath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        });

        $container->set(TaskRepository::class, static fn (Container $container): TaskRepository => new TaskRepository(
            $container->get(PDO::class),
        ));

        $container->set(IdempotencyRepository::class, static fn (Container $container): IdempotencyRepository => new IdempotencyRepository(
            $container->get(PDO::class),
        ));

        $container->set(MigrationRunner::class, static fn (Container $container): MigrationRunner => new MigrationRunner(
            $container->get(PDO::class),
        ));

        $container->set(CreateTaskUseCase::class, static fn (Container $container): CreateTaskUseCase => new CreateTaskUseCase(
            $container->get(TaskRepository::class),
            $container->get(IdempotencyRepository::class),
        ));

        $container->set(ListTasksUseCase::class, static fn (Container $container): ListTasksUseCase => new ListTasksUseCase(
            $container->get(TaskRepository::class),
        ));

        $container->set(GetTaskUseCase::class, static fn (Container $container): GetTaskUseCase => new GetTaskUseCase(
            $container->get(TaskRepository::class),
        ));

        $container->set(UpdateTaskUseCase::class, static fn (Container $container): UpdateTaskUseCase => new UpdateTaskUseCase(
            $container->get(TaskRepository::class),
        ));

        $container->set(DeleteTaskUseCase::class, static fn (Container $container): DeleteTaskUseCase => new DeleteTaskUseCase(
            $container->get(TaskRepository::class),
        ));

        $container->set(TaskController::class, static fn (Container $container): TaskController => new TaskController(
            $container->get(CreateTaskUseCase::class),
            $container->get(ListTasksUseCase::class),
            $container->get(GetTaskUseCase::class),
            $container->get(UpdateTaskUseCase::class),
            $container->get(DeleteTaskUseCase::class),
        ));

        return $container;
    }
}
