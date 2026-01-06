<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse
    {
        // Tratamento para Credenciais Inválidas (Lançada no Controller)
        if ($e instanceof UnauthorizedHttpException) {
            return new JsonResponse([
                'error' => 'Unauthorized.',
                'message' => $e->getMessage() ?: 'Credenciais inválidas.',
            ], 401);
        }

        // Tratamento para Erros de Validação (Opcional, para padronizar o JSON)
        if ($e instanceof ValidationException) {
            return new JsonResponse([
                'error' => 'Unprocessable Entity.',
                'messages' => $e->errors()
            ], 422);
        }

        // Tratamento para Rotas não encontradas
        if ($e instanceof NotFoundHttpException) {
            return new JsonResponse([
                'error' => 'Not Found.',
                'message' => 'A rota solicitada não existe.'
            ], 404);
        }

        // Tratamento para IDs não encontrados no Banco (ModelNotFoundException)
        if ($e instanceof ModelNotFoundException) {
            $modelName = class_basename($e->getModel());
            $ids = implode(', ', $e->getIds());

            return new JsonResponse([
                'error' => 'Recurso não encontrado.',
                'message' => "Não foi possível localizar o registro [{$ids}] em {$modelName}."
            ], 404);
        }

        return parent::render($request, $e);
    }
}
