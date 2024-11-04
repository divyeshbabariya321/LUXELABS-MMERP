<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCategoryStoreBudgetRequest;
use App\Http\Requests\CategoryStoreBudgetRequest;
use App\Http\Requests\StoreBudgetRequest;
use Illuminate\Http\RedirectResponse;
use App\Budget;
use App\Setting;
use Carbon\Carbon;
use App\BudgetCategory;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|array|Factory|View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $date = $request->date ?? Carbon::now()->format('Y-m-d');

        if ($request->date) {
            $fixed_budgets    = Budget::where('type', 'fixed')->where('date', 'LIKE', "%$date%")->latest()->paginate(Setting::get('pagination'));
            $variable_budgets = Budget::where('type', 'variable')->where('date', 'LIKE', "%$date%")->latest()->paginate(Setting::get('pagination'), ['*'], 'variable-page');
        } else {
            $fixed_budgets    = Budget::where('type', 'fixed')->latest()->paginate(Setting::get('pagination'));
            $variable_budgets = Budget::where('type', 'variable')->latest()->paginate(Setting::get('pagination'), ['*'], 'variable-page');
        }

        $categories    = BudgetCategory::where('parent_id', 0)->get();
        $subcategories = BudgetCategory::where('parent_id', '!=', 0)->get();

        return view('budgets.index', [
            'fixed_budgets'    => $fixed_budgets,
            'variable_budgets' => $variable_budgets,
            'categories'       => $categories,
            'subcategories'    => $subcategories,
            'date'             => $date,
        ]);
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
    public function store(StoreBudgetRequest $request): RedirectResponse
    {

        $data = $request->except('_token');

        Budget::create($data);

        return redirect()->route('budget.index')->withSuccess('You have successfully created a budget!');
    }

    public function categoryStore(CategoryStoreBudgetRequest $request): RedirectResponse
    {

        $category       = new BudgetCategory;
        $category->name = $request->category;
        $category->save();

        return redirect()->route('budget.index')->withSuccess('You have successfully added a budget category!');
    }

    public function subCategoryStore(SubCategoryStoreBudgetRequest $request): RedirectResponse
    {

        $category            = new BudgetCategory;
        $category->parent_id = $request->parent_id;
        $category->name      = $request->subcategory;
        $category->save();

        return redirect()->route('budget.index')->withSuccess('You have successfully added a budget sub category!');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $budget = Budget::find($id);

        $budget->delete();

        return redirect()->route('budget.index')->withSuccess('You have successfully deleted a budget!');
    }
}
