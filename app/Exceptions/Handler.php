<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
     * @param Throwable $e
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $e): void
    {
        parent::report($e);
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

        // Tratamento para acesso não permitido
        if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
            return new JsonResponse([
                'error' => 'Forbidden.',
                'message' => 'Você não tem permissão para realizar esta ação.'
            ], 403);
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

        // Tratamento para Metodo Não Permitido (405)
        if ($e instanceof MethodNotAllowedHttpException) {
            return new JsonResponse([
                'error' => 'Method Not Allowed.',
                'message' => 'O método HTTP utilizado (' . $request->method() . ') não é permitido para esta rota.'
            ], 405);
        }

        // Tratamento para erro de integridade dos dados
        if ($e instanceof QueryException && $e->getCode() === "23000") {
            return new JsonResponse([
                'error' => 'Conflito de Integridade',
                'message' => 'Não é possível realizar esta operação: este registro possui dependências vinculadas e não pode ser removido ou alterado.'
            ], 409);
        }

        // Tratamento para Erros de Validação (Opcional, para padronizar o JSON)
        if ($e instanceof ValidationException) {
            return new JsonResponse([
                'error' => 'Unprocessable Entity.',
                'messages' => $e->errors()
            ], 422);
        }

        return parent::render($request, $e);
    }
}
