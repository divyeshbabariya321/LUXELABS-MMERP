<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\User;
use Exception;
use App\Models\IndexerState;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Elasticsearch\Elasticsearch;
use Illuminate\Support\Facades\Artisan;
use App\Elasticsearch\Reindex\Interfaces\Reindex;

class IndexerStateController extends Controller
{
    public function index(Request $request)
    {
        $this->createIndexerStateIfNotExist();
        $indexerStates = IndexerState::all();

        if ($request->ajax()) {
            $view = (string) view('indexer_state.list', [
                'indexerStates' => $indexerStates,
            ]);

            return response()->json(['code' => 200, 'tpl' => $view]);
        }

        return view('indexer_state.index', [
            'indexerStates' => $indexerStates,
        ]);
    }

    public function elasticConnect(): JsonResponse
    {
        try {
            $elastic = new Elasticsearch();
            $elastic->connect();
            $elastic->getConn()->ping();

            return response()->json([
                'code'    => 200,
                'message' => 'Connection successful to elasticsearch.',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function reindex(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            $id = $data['id'] ?? null;

            if ($id === null) {
                throw new Exception('Id is required param.');
            }

            /** @var IndexerState $indexerState */
            $indexerState = IndexerState::find($id);

            if ($indexerState === null) {
                throw new Exception(sprintf('Indexer with %s id not found.', $id));
            }

            if (! empty($data['stop_reindex'])) {
                $message = '';
                if ($indexerState->getStatus() === Reindex::RUNNING) {
                    if ($pId = $indexerState->getProcessId()) {
                        if (posix_kill($pId, SIGKILL)) {
                            $message = sprintf('Reindex terminated with pId %s. Index is set to invalidate status.', $pId);
                            $indexerState->setProcessId(null);
                        } else {
                            $message = sprintf('Error with terminating process with pId %s.', $pId);
                        }
                    }
                    $indexerState->setStatus(Reindex::INVALIDATE);
                } else {
                    throw new Exception('Cannot terminate process, because indexer state not in status \'running\'.');
                }
                $indexerState->addLog($message);
                $indexerState->save();

                return response()->json(['message' => $message ?: 'Invalidate reindex.', 'code' => 200]);
            }

            if ($indexerState->isSkip()) {
                throw new Exception(sprintf('Cannot start again reindex for index: %s', $indexerState->getIndex()));
            }

            Artisan::call('reindex:messages');
        } catch (\Throwable $throwable) {
            return response()->json(['message' => $throwable->getMessage(), 'code' => 500], 500);
        }

        return response()->json(['message' => 'Reindex started.', 'code' => 200]);
    }

    public function save(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            $id = $data['id'] ?? null;

            if ($id === null) {
                throw new Exception('Id is required param.');
            }

            /** @var IndexerState $indexerState */
            $indexerState = IndexerState::find($id);

            if ($indexerState === null) {
                throw new Exception(sprintf('Indexer with %s id not found.', $id));
            }

            if ($data['cycles']) {
                $indexerState->setSettings([
                    'cycles' => (int) $data['cycles'],
                ]);
                $indexerState->save();
            }

            return response()->json(['message' => 'Indexer saved.', 'code' => 200]);
        } catch (\Throwable $throwable) {
            return response()->json(['message' => $throwable->getMessage(), 'code' => 500], 500);
        }
    }

    public function masterSlave(Request $request): JsonResponse
    {
        DB::setDefaultConnection('mysql_read');
        $select     = User::query()->limit(5)->orderByDesc('id');
        $selectHost = $select->getConnection()->getConfig('host');
        $rand       = rand(1, 55);
        DB::setDefaultConnection('mysql');
        $create     = User::create(['name' => 'test' . $rand, 'email' => 'test' . $rand . '@example.com', 'password' => '$2y$10$Sr8Gzf8en1WuxAl0XRB1se3loslJH/kIOt3Dyz6zZ4eqYEae9J5Uq']);
        $createHost = $create->getConnection()->getConfig('host');

        return response()->json(
            [
                'data' => [
                    'select' => [
                        'host' => $selectHost,
                    ],
                    'insert' => [
                        'host' => $createHost,
                    ],
                ],
            ]
        );
    }

    public function logs(Request $request, ?int $id = null): JsonResponse
    {
        try {
            if ($id === null) {
                throw new Exception('Id is required param.');
            }

            /** @var IndexerState $indexerState */
            $indexerState = IndexerState::find($id);

            if ($indexerState === null) {
                throw new Exception(sprintf('Indexer with %s id not found.', $id));
            }

            return response()->json(['data' => $indexerState->getLogs() ?? []]);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage()], 500);
        }
    }

    private function createIndexerStateIfNotExist(): void
    {
        foreach (IndexerState::INDEXER_MAPPING as $index => $className) {
            $exists = IndexerState::where(IndexerState::INDEX, $index)->exists();

            if (! $exists) {
                $indexerState = new IndexerState();
                $indexerState->setIndex($index);
                $indexerState->setStatus(Reindex::INVALIDATE);
                $indexerState->save();
            }
        }
    }
}
