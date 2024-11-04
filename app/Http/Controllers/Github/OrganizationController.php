<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubOrganization;
use App\Http\Controllers\Controller;
use App\Http\Requests\Github\StoreOrganizationRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $githubOrganizations = new GithubOrganization;
        if (isset($request->query_string)) {
            $githubOrganizations = $githubOrganizations->where(DB::raw('lower(name)'), 'like', '%'.strtolower($request->query_string).'%')->orWhere(DB::raw('lower(username)'), 'like', '%'.strtolower($request->query_string).'%');
        }
        $githubOrganizations = $githubOrganizations->get();

        return view('github.organizations.index', compact('githubOrganizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $mode = 'add';

        return view('github.organizations.create', compact('mode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationRequest $request): RedirectResponse
    {

        $mode = 'created';

        if (strlen($request->organization_id) > 0) {
            $mode = 'updated';

            $githubOrganization = GithubOrganization::find($request->organization_id);
        } else {
            $githubOrganization = new GithubOrganization;
        }

        $githubOrganization->name = $request->name;
        $githubOrganization->username = $request->username;
        $githubOrganization->token = $request->token;
        $githubOrganization->created_by = auth()->user()->id;
        $githubOrganization->save();

        Session::flash('sucess', 'Success! Organization has been '.$mode.'.');

        return redirect()->to('github/organizations');
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
        GithubOrganization::destroy($id);

        Session::flash('sucess', 'Success! Organization has been deleted.');

        return redirect()->to('github/organizations');
    }

    public function deleteOrganizationToken(Request $request, $id): JsonResponse
    {
        try {
            $organization = GithubOrganization::find($id);
            if (! $organization) {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => false,
                        'message' => 'Organization not found',
                    ]
                );
            }
            if (empty($organization->token)) {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => false,
                        'message' => 'Token not found',
                    ]
                );
            }

            $organization->token = null;
            $organization->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Token deleted successfully',
                ]
            );

        } catch (Exception $e) {
            return response()->json(
                [
                    'code' => 404,
                    'status' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }

    }

    public function deleteOrganization(Request $request, $id): JsonResponse
    {
        try {
            $organization = GithubOrganization::find($id);
            if (! $organization) {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => false,
                        'message' => 'Organization not found',
                    ]
                );
            }
            GithubOrganization::destroy($id);

            return response()->json(
                [
                    'code' => 404,
                    'status' => false,
                    'message' => 'Organization has been deleted',
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'code' => 404,
                    'status' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
}
