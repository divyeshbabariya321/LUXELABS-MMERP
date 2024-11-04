<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCaseReceivableRequest;
use App\Http\Requests\StoreCaseReceivableRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Helpers;
use App\LegalCase;
use App\CaseReceivable;
use Illuminate\Http\Request;
use App\Events\CaseReceivableCreated;
use Exception;

class CaseReceivableController extends Controller
{
    public function index(LegalCase $case): View
    {
        $receivables = $case->receivables()->orderBy('receivable_date')->paginate(50);

        return view('case.receivables', [
            'receivables' => $receivables,
            'case'        => $case,
            'currencies'  => Helpers::currencies(),
        ]);
    }

    public function store(LegalCase $case, StoreCaseReceivableRequest $request): RedirectResponse
    {
        try {
            $status = 0;
            if ($request->get('received_date') && $request->get('received_amount')) {
                $status = 1;
            }
            $case_receivable = $case->receivables()->create([
                'receivable_date'   => $request->get('receivable_date'),
                'receivable_amount' => $request->get('receivable_amount'),
                'received_date'     => $request->get('received_date'),
                'received_amount'   => $request->get('received_amount'),
                'description'       => $request->get('description'),
                'currency'          => $request->get('currency'),
                'status'            => $status,
            ]);
            event(new CaseReceivableCreated($case, $case_receivable, $status));
        } catch (Exception $exception) {
            return redirect()->back()->withErrors('Couldn\'t store Case Receivable');
        }

        return redirect()->back()->withSuccess('You have successfully added a Case Receivable!');
    }

    public function update(LegalCase $case, CaseReceivable $case_receivable, UpdateCaseReceivableRequest $request): RedirectResponse
    {
        try {
            $receivable = $case->receivables()->where('id', $case_receivable->id)->first();
            $status     = 0;
            if ($request->get('received_date') && $request->get('received_amount')) {
                $status = 1;
            }
            $receivable->fill([
                'receivable_date'   => $request->get('receivable_date'),
                'receivable_amount' => $request->get('receivable_amount'),
                'received_date'     => $request->get('received_date'),
                'received_amount'   => $request->get('received_amount'),
                'description'       => $request->get('description'),
                'currency'          => $request->get('currency'),
                'status'            => $status,
            ])->save();
            event(new CaseReceivableCreated($case, $case_receivable, $status));
        } catch (Exception $exception) {
            return redirect()->back()->withErrors('Couldn\'t update Case Receivable');
        }

        return redirect()->back()->withSuccess('You have successfully updated Case Receivable!');
    }

    public function destroy(LegalCase $case, CaseReceivable $case_receivable): RedirectResponse
    {
        $receivable = $case->receivables()->where('id', $case_receivable->id)->firstOrFail();
        try {
            $receivable->delete();
        } catch (Exception $exception) {
            return redirect()->back()->withErrors('Couldn\'t delete Case Receivable');
        }

        return redirect()->back()->withSuccess('You have successfully deleted Case Receivable!');
    }
}
