<?php

namespace App\Http\Controllers;

use App\Routes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class RoutesController extends Controller
{
    /**
     * List out all the register routes
     * $param String $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Routes::query();
        if ($request->id) {
            $query = $query->where('id', $request->id);
        }

        if ($request->search) {
            $searchRegex = '';

            if (is_array($request->search)) {
                $searchListLength = count($request->search);

                for ($i = 0; $i < $searchListLength; $i++) {
                    $searchRegex .= preg_replace(
                        [
                            '/[\s]+/',
                            '/\{.*\}/',
                        ],
                        [
                            '\/',
                            '',
                        ],
                        $request->search[$i]
                    );

                    if ($i != $searchListLength - 1) {
                        $searchRegex .= '|';
                    }
                }
            } else {
                $searchRegex = $request->search;
            }

            $searchRegex = '.*('.$searchRegex.').*'; // ex:- (url1|url2|url3)

            $query = $query
                ->where('url', 'regexp', $searchRegex)
                ->orWhere('page_title', 'regexp', $searchRegex)
                ->orWhere('page_description', 'regexp', $searchRegex);
        }

        if ($request->ajax()) {
            if ($request->get('route-suggestions', null) != null) {
                if ($request->search) {
                    return $query->orderBy('id')->select('url')->get();
                } else {
                    return [];
                }
            }

            $routesData = $query->orderBy('id')->paginate(25)->appends(request()->except(['page']));

            return view('routes.ajax.index_ajax', compact('routesData'))->render();
        }

        $routesData = $query->orderBy('id')->paginate(25)->appends(request()->except(['page']));

        return view('routes.index', compact('routesData'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Sync the registered routes in DB.
     * It skip if any route entry is already exist
     * $param String $request
     */
    public function sync(Request $request): RedirectResponse
    {
        Artisan::call('routes:sync');
        Session::flash('message', 'Data Sync Completed!');

        return redirect()->back();
    }

    /**
     * Sync the registered routes in DB.
     * It skip if any route entry is already exist
     * $param String $request
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $routes = Routes::find($id);
        if ($request->post('page_title') && $request->post('page_description')) {
            $updateData = ['page_title' => $request->post('page_title'), 'page_description' => $request->post('page_description')];
            Routes::whereId($id)->update($updateData);
            Session::flash('message', 'Data Updated Successfully');

            return redirect()->route('routes.update', [$id]);
        }

        if ($request->post('status')) {
            $updateData = ['status' => $request->post('status')];
            Routes::whereId($id)->update($updateData);

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Status updated successfully']);
        }

        return view('routes.update', compact('routes'));
    }

    public function updateEmailAlert(Request $request): JsonResponse
    {
        $result = null;
        $email_alert = $request->post('email_alert') == 'true' ? 1 : 0;

        if ($request->post('type') == 'single') {
            $routes = Routes::find((int) $request->post('id'));
        } elseif ($request->post('type') == 'all') {
            $routes = Routes::query();
        }
        if ($routes) {
            $result = $routes->update(['email_alert' => $email_alert]);
        }

        return response()->json(['code' => 200, 'data' => $result, 'message' => 'Email alert updated successfully']);
    }
}
