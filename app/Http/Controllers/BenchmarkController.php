<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Benchmark;
use Illuminate\Http\Request;

class BenchmarkController extends Controller
{
    public function create(): View
    {
        $benchmark = Benchmark::orderByDesc('for_date')->first();

        $data = [];

        if (empty($benchmark)) {
            $benchmark = new Benchmark();

            foreach ($benchmark->getFillable() as $item) {
                $data[$item] = 0;
            }
        } else {
            $data = $benchmark->toArray();
        }

        $data['for_date'] = date('Y-m-d');

        return view('activity.benchmark', $data);
    }

    public function store(Request $request): RedirectResponse
    {
        $data             = $request->all();
        $data['for_date'] = date('Y-m-d');

        Benchmark::updateOrCreate(['for_date' => date('Y-m-d')], $data);

        return redirect()->back()->with('status', 'Benchmark Updated');
    }
}
