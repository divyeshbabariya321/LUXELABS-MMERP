<?php

declare(strict_types=1);

namespace App\Http\Controllers\Zabbix;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Zabbix\ZabbixApi;
use Illuminate\Http\Request;
use App\Models\Zabbix\Trigger;
use App\Zabbix\ZabbixException;
use Illuminate\Routing\Controller;

class TriggerController extends Controller
{
    public function __construct(
        private Trigger $trigger,
        private ZabbixApi $zabbix
    ) {
    }

    public function index(Request $request): View
    {
        $page    = $request->get('page', 1);
        $trigger = new Trigger();

        $triggers  = array_reverse($trigger->getAll());
        $count     = count($triggers);
        $triggers  = array_slice($triggers, $page * 20, 20);
        $templates = $trigger->getAllTemplates();

        array_map(function ($trigger) {
            $zbxTrigger = $this->zabbix->call('trigger.get', ['triggerids' => $trigger->getTemplateId()]);

            if (isset($zbxTrigger[0])) {
                $trigger->setTemplateName($zbxTrigger[0]['description']);
            }
        }, $triggers);
        if ($request->ajax()) {
            return view('zabbix.trigger.index', [
                'triggers'  => $triggers,
                'count'     => $count,
                'page'      => $page,
                'templates' => $templates,
            ]);
        }

        return view('zabbix.trigger.index', [
            'triggers'  => $triggers,
            'count'     => $count,
            'page'      => $page,
            'templates' => $templates,
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        $data = $request->all();

        try {
            $item   = new Trigger();
            $itemId = (int) $data['id'] ?? null;
            if (! empty($data['id'])) {
                $item = $item->getById($itemId);
            }

            $item->setEventName($data['event_name'] ?? '');
            $item->setName($data['name'] ?? '');
            $item->setExpression($data['expression'] ?? '');
            $item->setSeverity($data['severity'] ?? 1);

            $item->save();
        } catch (ZabbixException $zabbixException) {
            return response()->json([
                'message' => $zabbixException->getMessage(),
                'code'    => 500,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'code'    => 500,
            ]);
        }

        return response()->json([
            'message' => sprintf('Item with name: %s was edited. Reload page.', $item->getName()),
            'item'    => $item,
            'code'    => 200,
        ]);
    }

    public function changeStatus(Request $request): JsonResponse
    {
        $data = $request->all();

        try {
            $trigger   = new Trigger();
            $triggerId = (int) $data['id'] ?? null;
            if (! empty($data['id'])) {
                $trigger = $trigger->getById($triggerId);
            } else {
                throw new ZabbixException(sprintf('Trigger with id: %s not found.', $triggerId));
            }

            $trigger->changeStatus(! $trigger->isActive());
        } catch (ZabbixException $zabbixException) {
            return response()->json([
                'message' => $zabbixException->getMessage(),
                'code'    => 500,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'code'    => 500,
            ]);
        }

        return response()->json([
            'message' => sprintf('Item with name: %s was edited. Reload page.', $trigger->getName()),
            'trigger' => $trigger,
            'code'    => 200,
        ]);
    }
}
