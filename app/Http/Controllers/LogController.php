<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Laravel\Lumen\Routing\Controller;

class LogController extends Controller
{
    private string $logPath;

    public function __construct()
    {
        $this->logPath = storage_path('logs');
    }

    /**
     * Lista arquivos de log disponíveis
     * GET /logs
     */
    public function index()
    {
        if (!File::exists($this->logPath)) {
            return response()->json([], 200);
        }

        $files = collect(File::files($this->logPath))
            ->filter(fn ($file) => str_ends_with($file->getFilename(), '.log'))
            ->map(fn ($file) => [
                'file' => $file->getFilename(),
                'date' => $this->extractDateFromFilename($file->getFilename()),
                'size_kb' => round($file->getSize() / 1024, 2),
                'last_modified' => date('Y-m-d H:i:s', $file->getMTime()),
            ])
            ->sortByDesc('date')
            ->values();

        return response()->json($files);
    }

    /**
     * Retorna logs de um arquivo específico em JSON
     * GET /logs/{date}
     * Exemplo: /logs/2026-01-15
     */
    public function show(string $date)
    {
        $file = "lumen-{$date}.log";
        $path = $this->logPath . DIRECTORY_SEPARATOR . $file;

        if (!File::exists($path)) {
            return response()->json([
                'message' => 'Arquivo de log não encontrado'
            ], 404);
        }

        $content = File::get($path);

        return response()->json([
            'file' => $file,
            'entries' => $this->parseLog($content)
        ]);
    }

    /**
     * Converte o conteúdo do log em JSON estruturado
     */
    private function parseLog(string $content): array
    {
        $entries = [];

        $blocks = preg_split(
            '/\n(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/',
            trim($content)
        );

        foreach ($blocks as $block) {

            if (!preg_match(
                '/^\[(.*?)\]\s+(\w+)\.(\w+):\s+(.*)$/s',
                $block,
                $matches
            )) {
                continue;
            }

            $timestamp   = $matches[1];
            $environment = strtolower($matches[2]);
            $level       = strtolower($matches[3]);
            $rawMessage  = trim($matches[4]);

            // Remove stacktrace completamente
            $rawMessage = preg_split('/\n\[stacktrace\]/', $rawMessage)[0];

            $message = $rawMessage;
            $body = null;

            /**
             * Se existir JSON no final da mensagem,
             * separa message e body
             */
            if (preg_match('/^(.*?)(\s+\{.*\})$/s', $rawMessage, $jsonMatch)) {
                $message = trim($jsonMatch[1]);
                $body    = trim($jsonMatch[2]);
            }

            // Normaliza espaços
            $message = trim(preg_replace('/\s+/', ' ', $message));

            $entries[] = [
                'timestamp'   => $timestamp,
                'environment' => $environment,
                'level'       => $level,
                'message'     => sprintf(
                    '%s.%s: %s',
                    $environment,
                    strtoupper($level),
                    $message
                ),
                'body'        => $body
            ];
        }

        return array_reverse($entries);
    }


    /**
     * Extrai data do nome do arquivo lumen-YYYY-MM-DD.log
     */
    private function extractDateFromFilename(string $filename): ?string
    {
        if (preg_match('/lumen-(\d{4}-\d{2}-\d{2})\.log/', $filename, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
