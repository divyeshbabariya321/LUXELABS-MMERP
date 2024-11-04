<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\ConversionRate;
use Illuminate\Http\Request;

class ConversionRateController extends Controller
{
    public function index(): View
    {
        $conversionRates = ConversionRate::orderBy('id')->paginate(30);

        return view('conversion_rate.index', compact('conversionRates'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->input();
        unset($data['_token']);
        if ($request->post('id')) {
            ConversionRate::whereId($request->post('id'))->update($data);
            Session::flash('message', 'Conversion Rate Updated Successfully');
        } else {
            unset($data['id']);
            ConversionRate::updateOrCreate(['currency' => $data['currency'], 'to_currency' => $data['to_currency']], $data);
            Session::flash('message', 'Conversion Rate  Created Successfully');
        }

        return redirect()->to('/conversion/rates');
    }
}
