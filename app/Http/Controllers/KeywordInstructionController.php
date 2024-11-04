<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateKeywordInstructionRequest;
use App\Http\Requests\StoreKeywordInstructionRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\KeywordInstruction;
use App\InstructionCategory;
use Illuminate\Http\Request;
use Exception;

class KeywordInstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $keywordInstructions = KeywordInstruction::paginate(25);

        $instructions = InstructionCategory::all();

        return view('keyword_instructions.index', compact('keywordInstructions', 'instructions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKeywordInstructionRequest $request): RedirectResponse
    {

        $keywordInstruction                          = new KeywordInstruction();
        $keywordInstruction->keywords                = $request->get('keywords');
        $keywordInstruction->instruction_category_id = $request->get('instruction_category');
        $keywordInstruction->remark                  = $request->get('remark') ?? 'N/A';
        $keywordInstruction->save();

        return redirect()->action([KeywordInstructionController::class, 'index'])->with('message', 'Keyword-instruction reference added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(KeywordInstruction $keywordInstruction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KeywordInstruction $keywordInstruction): View
    {
        if (! $keywordInstruction) {
            abort(404);
        }

        $instructions = InstructionCategory::all();

        return view('keyword_instructions.edit', compact('keywordInstruction', 'instructions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKeywordInstructionRequest $request, KeywordInstruction $keywordInstruction): RedirectResponse
    {

        $keywordInstruction->keywords                = $request->get('keywords');
        $keywordInstruction->instruction_category_id = $request->get('instruction_category');
        $keywordInstruction->remark                  = $request->get('remark') ?? 'N/A';
        $keywordInstruction->save();

        return redirect()->back()->with('message', 'Keyword-instruction reference added successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(KeywordInstruction $keywordInstruction): RedirectResponse
    {
        if ($keywordInstruction) {
            $keywordInstruction->delete();
        }

        return redirect()->back()->with('message', 'Keyword-Instruction deleted successfully!');
    }
}
