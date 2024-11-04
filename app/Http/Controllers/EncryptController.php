<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\PublicKey;
use Illuminate\Http\Request;

class EncryptController extends Controller
{
    public function index(): View
    {
        $publicKey = PublicKey::first();

        return view('encryption.index', compact('publicKey'));
    }

    public function saveKey(Request $request): RedirectResponse
    {
        if ($request->file('public')) {
            $string = $request->file('public')->get();
            //Remove new line if exist
            $string = str_replace('\n', '', $string);
            //ReMove spamce
            $string = str_replace(' ', '', $string);

            $first = PublicKey::first();
            if ($first != null) {
                $first->key = $string;
                $first->update();
            } else {
                $public      = new PublicKey;
                $public->key = $string;
                $public->save();
            }

            return redirect()->back()->with('message', 'Public Key Stored');
        }

        if ($request->file('private')) {
            $string = $request->file('private')->get();
            //Remove new line if exist
            $string = str_replace('\n', '', $string);
            //ReMove spamce
            $string = str_replace(' ', '', $string);

            if (session()->has('encrpyt')) {
                session()->forget('encrpyt');
                session()->put('encrpyt.private', $string);
                session()->put('encrpyt.time', time());
            } else {
                session()->put('encrpyt.private', $string);
                session()->put('encrpyt.time', time());
            }

            return redirect()->back()->with('message', 'Private Key Stored');
        }

        return redirect()->back()->with('message', 'Please Select File');
    }

    public function forgetKey(Request $request): JsonResponse
    {
        if ($request->public) {
            $first = PublicKey::first();
            $first->delete();
        } elseif ($request->private) {
            session()->forget('encrpyt');
        }

        return response()->json(['success' => 'success'], 200);
    }
}
