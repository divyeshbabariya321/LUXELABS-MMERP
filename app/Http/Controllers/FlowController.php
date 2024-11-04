<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\Email;
use App\Flow;
use App\FlowAction;
use App\FlowCondition;
use App\FlowMessage;
use App\FlowPath;
use App\FlowType;
use App\StoreWebsite;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Qoraiche\MailEclipse\MailEclipse;

class FlowController extends Controller
{
    public function index(): View
    {
        $flows = Flow::leftJoin('store_websites', 'flows.store_website_id', '=', 'store_websites.id')
            ->select('store_websites.title', 'flows.*')->get();
        $websites = StoreWebsite::pluck('title', 'id')->toArray();

        return view('flow.index', compact('flows', 'websites'));
    }

    public function conditionlist(): View
    {
        $flowconditions = FlowCondition::select('flow_conditions.*')->get();

        return view('flow.conditionlist', compact('flowconditions'));
    }

    public function conditionListStatus(Request $request): JsonResponse
    {
        $flowcondition = FlowCondition::find($request->get('id'));

        if ($flowcondition->status == 0) {
            $status = 1;
        }
        if ($flowcondition->status == 1) {
            $status = 0;
        }

        $updateflowcondition = FlowCondition::find($request->get('id'));
        $updateflowcondition->status = $status;
        $updateflowcondition->update();

        return response()->json(['status' => 'success', 'status' => $status]);
    }

    public function allScheduleMessages(): View
    {
        $current_date = Carbon::now()->format('Y-m-d H:i:s');
        //Chats and Whatsapp
        $messages = ChatMessage::where('scheduled_at', '>', $current_date)->where('flow_exit', 1)
            ->leftJoin('customers', 'customers.id', 'chat_messages.customer_id')
            ->leftJoin('users', 'users.id', 'chat_messages.user_id');
        $types = ['0' => 'Whatsapp', '3' => 'SMS'];
        $is_queues = ['1' => 'Yes', '0' => 'No'];
        if (request()->message) {
            $messages->where('message', 'LIKE', '%'.request()->message.'%');
        }

        if (request()->message_application_id) {
            $messages->where('message_application_id', 'LIKE', '%'.request()->message_application_id.'%');
        }

        if (request()->is_queue) {
            $messages->where('is_queue', request()->is_queue);
        }

        $messages = $messages->select('chat_messages.*', 'customers.name', 'users.name as user_name')->orderByDesc('chat_messages.id')->paginate(20);

        $message = request()->message;
        $type = request()->message_application_id;

        return view('flow.scheduled-messages', compact('messages', 'types', 'message', 'type', 'is_queues'));
    }

    public function allScheduleEmails(Request $request): View
    {
        $current_date = Carbon::now()->format('Y-m-d H:i:s');
        //Emails
        $emails = Email::where('schedule_at', '>', $current_date);

        if (request()->from) {
            $emails->where('from', 'LIKE', '%'.request()->from.'%');
        }

        if (request()->to) {
            $emails->where('to', 'LIKE', '%'.request()->to.'%');
        }

        $emails = $emails->paginate(10);

        $from = request()->from;
        $to = request()->to;

        return view('flow.scheduled-emails', compact('emails', 'from', 'to'));
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $data = $request->except('_token', 'id');

        Email::find($request->id)->update($data);

        return redirect()->back()->withSuccess('You have successfully updated a Email Data!');
    }

    public function deleteEmail(Request $request): RedirectResponse
    {
        Email::find($request->id)->delete();

        return redirect()->back()->withSuccess('You have successfully deleted a Email Data!');
    }

    public function updateMessage(Request $request): RedirectResponse
    {
        $data = $request->except('_token', 'id');

        ChatMessage::find($request->id)->update($data);

        return redirect()->back()->withSuccess('You have successfully updated a Message Data!');
    }

    public function deleteMessage(Request $request): RedirectResponse
    {
        ChatMessage::find($request->id)->delete();

        return redirect()->back()->withSuccess('You have successfully deleted a Message Data!');
    }

    public function createFlow(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'store_website_id' => 'required',
            'flow_name' => 'required',
            'flow_description' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0].'<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }

        $inputs = $request->input();
        if ($inputs['id'] == null) {
            $inputs['flow_code'] = $this->randomFlowCode();
        }
        $flow = Flow::updateOrCreate(['id' => $inputs['id']], $inputs);
        if ($inputs['id'] == null) {
            FlowPath::create(['flow_id' => $flow['id']]);
        }

        return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Flow Created successfully']);
    }

    public function createType(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0].'<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }
        $inputs = $request->input();
        FlowType::updateOrCreate(['id' => $inputs['id']], $inputs);

        return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Flow Type Created successfully']);
    }

    public function updateFlow(Request $request)
    {
        $inputs = $request->input();
        $flowDetail = Flow::where('id', $inputs['flow_id'])->first();
        $flowTypeId = FlowType::where('type', $inputs['action_type'])->pluck('id')->first();
        if ($flowTypeId == null) {
            $flowDetail = FlowType::create(['type' => $inputs['action_type']]);
            $flowTypeId = $flowDetail['id'];
        }
        $rank = FlowAction::where(['path_id' => $inputs['path_id']])->orderByDesc('rank')->whereNotNull('rank')->pluck('rank')->first();
        if ($rank == null) {
            $rank = 1;
        } else {
            $rank = $rank + 1;
        }

        $flowAction = FlowAction::create(['path_id' => $inputs['path_id'], 'type_id' => $flowTypeId, 'rank' => $rank]);
        if (strtolower($inputs['action_type']) == 'condition') {
            FlowPath::create(['flow_id' => $inputs['flow_id'], 'parent_action_id' => $flowAction['id'], 'path_for' => 'yes']);
            FlowPath::create(['flow_id' => $inputs['flow_id'], 'parent_action_id' => $flowAction['id'], 'path_for' => 'no']);
        }
        $flowPathId = FlowPath::where('flow_id', $inputs['flow_id'])->whereNull('parent_action_id')->orderByDesc('id')->pluck('id')->first();
        $flowActions = $this->getFlowActions($flowPathId);

        $html = view('flow.create', compact('flowDetail', 'flowActions', 'flowPathId'))->render();

        return ['message' => $html, 'statusCode' => 200];
    }

    public function getFlowActions($pathId)
    {
        return FlowAction::leftJoin('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
            ->select('flow_actions.*', 'flow_types.type')->where(['path_id' => $pathId])->orderBy('rank')
            ->get();
    }

    public function updateCondition(Request $request)
    {
        $id = $request->action_id;
        $condition = $request->condition;
        FlowAction::where('id', $id)->update(['condition' => $condition]);

        return ['message' => 'Condition Updated', 'statusCode' => 200];
    }

    public function updateFlowActions(Request $request)
    {
        $inputs = $request->input();
        if (isset($inputs['action_id'])) {
            foreach ($inputs['action_id'] as $key => $actionId) {
                if ($inputs['action_type'][$actionId] == 'Time Delay') {
                    $data = ['time_delay' => $inputs['time_delay'][$actionId], 'time_delay_type' => $inputs['time_delay_type'][$actionId], 'rank' => $key];
                    FlowAction::where('id', $actionId)->update($data);
                } elseif (isset($inputs['message_title'][$actionId]) && ($inputs['action_type'][$actionId] == 'Whatsapp' || $inputs['action_type'][$actionId] == 'SMS')) {
                    $data = ['message_title' => $inputs['message_title'][$actionId], 'rank' => $key];
                    FlowAction::where('id', $actionId)->update($data);
                } else {
                    $data = ['rank' => $key];
                    FlowAction::where('id', $actionId)->update($data);
                }
            }
        }

        return ['message' => 'Successfully updated', 'statusCode' => 200];
    }

    public function flowDetail($flowId): View
    {
        $flowDetail = Flow::where('id', $flowId)->first();
        $flowPathId = FlowPath::where('flow_id', $flowId)->whereNull('parent_action_id')->orderByDesc('id')->pluck('id')->first();
        $flowActions = $this->getFlowActions($flowPathId);

        return view('flow.create', compact('flowDetail', 'flowActions', 'flowPathId'));
    }

    public function randomFlowCode()
    {
        $random = Str::random(6);
        $codeExist = Flow::where('flow_code', $random)->first();
        if ($codeExist) {
            $this->randomFlowCode();
        } else {
            return $random;
        }
    }

    public function updateAction(Request $request)
    {
        $input = $request->input();
        FlowAction::updateOrCreate(['action_id' => $input['action_id']], $input);

        return ['message' => 'Successfully added', 'statusCode' => 200];
    }

    public function deleteActionMessage(Request $request)
    {
        $messageId = $request->messageId;
        FlowAction::where('id', $messageId)->delete();

        return ['message' => 'Message deleted successfully', 'statusCode' => 200];
    }

    public function getActionMessage($actionId): View
    {
        $flowMessage = FlowMessage::where(['action_id' => $actionId])->first();

        if ($flowMessage == null || $flowMessage['sender_name'] == null || $flowMessage['sender_email_address'] == null) {
            $flowMessage = FlowAction::leftJoin('flow_paths', 'flow_paths.id', '=', 'flow_actions.path_id')
                ->leftJoin('flows', 'flows.id', '=', 'flow_paths.flow_id')
                ->leftJoin('store_websites', 'store_websites.id', '=', 'flows.store_website_id')
                ->where('flow_actions.id', $actionId)->select('store_websites.title as sender_name', 'store_websites.username as sender_email_address')->first();
        }
        if (! isset($flowMessage['subject'])) {
            $flowMessage['subject'] = '';
        }
        if (! isset($flowMessage['mail_tpl'])) {
            $flowMessage['mail_tpl'] = '';
        }
        if (! isset($flowMessage['html_content'])) {
            $flowMessage['html_content'] = '';
        }
        if (! isset($flowMessage['action_id'])) {
            $flowMessage['action_id'] = $actionId;
        }
        if (! isset($flowMessage['id'])) {
            $flowMessage['id'] = '';
        }

        $mailEclipseTpl = MailEclipse::getTemplates();
        $rViewMail = [];
        if (! empty($mailEclipseTpl)) {
            foreach ($mailEclipseTpl as $mTpl) {
                $v = MailEclipse::$view_namespace.'::templates.'.$mTpl->template_slug;
                $rViewMail[$v] = $mTpl->template_name.' ['.$mTpl->template_description.']';
            }
        }

        return view('flow.message', compact('flowMessage', 'rViewMail'));
    }

    public function updateActionMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sender_name' => 'required',
            'sender_email_address' => 'required',
            'mail_tpl' => 'required_without:html_content',
            'html_content' => 'required_without:mail_tpl',
            'subject' => 'required',
        ], [
            'mail_tpl.required_without' => 'Please provide email content or select Email Template.',
            'html_content.required_without' => 'Please provide email content or select Email Template.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0].'<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }
        $inputs = $request->input();
        FlowMessage::updateOrCreate(['id' => $inputs['id']], $inputs);

        return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Message Saved successfully']);
    }

    public function flowDelete(Request $request)
    {
        $input = $request->input();
        $flow = Flow::find($input['id']);
        $paths = FlowPath::where('flow_id', $flow->id)->get();
        foreach ($paths as $path) {
            $flowActions = FlowAction::where('path_id', $path->id)->get();
            foreach ($flowActions as $flowAction) {
                FlowMessage::where('action_id', $flowAction->id)->delete();
                $flowAction->delete();
            }
            $path->delete();
        }
        $flow->delete();

        return ['message' => 'Flow Successfully Deleted', 'statusCode' => 200];
    }

    public function flowActionDelete(Request $request)
    {
        FlowAction::where('id', $request->input('id'))->delete();
        FlowMessage::where('action_id', $request->input('id'))->delete();

        return ['message' => 'Flow Successfully Deleted', 'statusCode' => 200];
    }
}
