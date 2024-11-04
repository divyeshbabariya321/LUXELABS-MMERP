<?php

namespace App\Http\Controllers\Pinterest;

use Illuminate\Support\Facades\Validator;
use App\Setting;
use App\PinterestBoards;
use Illuminate\Http\Request;
use App\PinterestAdsAccounts;
use App\PinterestBoardSections;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use App\PinterestBusinessAccountMails;
use Illuminate\Support\Facades\Redirect;
use Exception;

class PinterestAdsAccountsController extends Controller
{
    public function __construct()
    {
        View::share([
            'countries' => (new PinterestService())->getSupportedCountries(),
        ]);
    }

    /**
     * Show list of ads account for the connected account.
     *
     * @param mixed $id
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function dashboard(Request $request, $id)
    {
        try {
            $pinterestBusinessAccountMail = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestBusinessAccountMail) {
                return Redirect::route('pinterest.accounts')
                    ->with('error', 'No account found');
            }
            $pinterestAdsAccounts = PinterestAdsAccounts::where(function ($query) use ($pinterestBusinessAccountMail, $request) {
                $query->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
                if ($request->has('name') && $request->name) {
                    $query->where('ads_account_name', 'like', '%' . $request->name . '%');
                }
                if ($request->has('country') && $request->country) {
                    $query->where('ads_account_country', 'like', '%' . $request->country . '%');
                }
                if ($request->has('currency') && $request->currency) {
                    $query->where('ads_account_currency', 'like', '%' . $request->currency . '%');
                }
            })->paginate(Setting::get('pagination'), ['*'], 'ads_accounts');

            return view('pinterest.account-dashboard', compact('pinterestBusinessAccountMail', 'pinterestAdsAccounts'));
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new Ads account.
     *
     * @param mixed $id
     */
    public function createAdsAccount(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'name'    => 'required|max:255',
                'country' => 'required',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $response  = $pinterest->createAdsAccount([
                'name'    => $request->get('name'),
                'country' => $request->get('country'),
            ]);
            if ($response['status']) {
                PinterestAdsAccounts::create([
                    'pinterest_mail_id'    => $pinterestAccount->id,
                    'ads_account_id'       => $response['data']['id'],
                    'ads_account_name'     => $request->get('name'),
                    'ads_account_country'  => $request->get('country'),
                    'ads_account_currency' => $response['data']['currency'],
                ]);

                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('success', 'Ads account created.');
            } else {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.dashboard', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get all boards for a account.
     *
     * @param mixed $id
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function boardsIndex(Request $request, $id)
    {
        try {
            $pinterestBusinessAccountMail = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestBusinessAccountMail) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', 'No account found');
            }
            $pinterestBoards = PinterestBoards::with('account')
                ->where(function ($query) use ($pinterestBusinessAccountMail, $request) {
                    $query->whereHas('account', function ($query2) use ($pinterestBusinessAccountMail) {
                        $query2->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
                    });
                    if ($request->has('name') && $request->name) {
                        $query->where('name', 'like', '%' . $request->name . '%');
                    }
                    if ($request->has('account_id') && $request->account_id) {
                        $query->where('pinterest_ads_account_id', $request->account_id);
                    }
                })->paginate(Setting::get('pagination'), ['*'], 'boards');
            $pinterestAdsAccount = PinterestAdsAccounts::where('pinterest_mail_id', $pinterestBusinessAccountMail->id)->pluck('ads_account_name', 'id')->toArray();

            return view('pinterest.boards-index', compact('pinterestBusinessAccountMail', 'pinterestBoards', 'pinterestAdsAccount'));
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.dashboard', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new Board.
     *
     * @param mixed $id
     */
    public function createBoard(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'pinterest_ads_account_id' => 'required',
                'name'                     => 'required',
                'description'              => 'sometimes',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestAdAccount = PinterestAdsAccounts::where('id', $request->get('pinterest_ads_account_id'))->first();
            $pinterest          = $this->getPinterestClient($pinterestAccount);
            $response           = $pinterest->createBoards([
                'name'        => $request->get('name'),
                'description' => $request->get('description'),
            ], ['ad_account_id' => $pinterestAdAccount->ads_account_id]);
            if ($response['status']) {
                PinterestBoards::create([
                    'pinterest_ads_account_id' => $request->get('pinterest_ads_account_id'),
                    'board_id'                 => $response['data']['id'],
                    'name'                     => $request->get('name'),
                    'description'              => $request->get('description'),
                ]);

                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('success', 'Board created successfully.');
            } else {
                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.board.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get Board Details
     *
     * @param $accountId
     * @param mixed $id
     * @param mixed $boardId
     */
    public function getBoard($id, $boardId): JsonResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccountMails::findOrFail($id);
            if (! $pinterestBusinessAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }
            $pinterestBoard = PinterestBoards::findOrFail($boardId);
            if (! $pinterestBoard) {
                return response()->json(['status' => false, 'message' => 'Board not found']);
            }

            return response()->json(['status' => true, 'message' => 'Account found', 'data' => $pinterestBoard->toArray()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a new Board.
     *
     * @param mixed $id
     */
    public function updateBoard(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'edit_board_id'                 => 'required',
                'edit_pinterest_ads_account_id' => 'required',
                'edit_name'                     => 'required',
                'edit_description'              => 'sometimes',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestAdAccount = PinterestAdsAccounts::where('id', $request->get('edit_pinterest_ads_account_id'))->first();
            $pinterestBoard     = PinterestBoards::where('id', $request->get('edit_board_id'))->first();
            $pinterest          = $this->getPinterestClient($pinterestAccount);
            $response           = $pinterest->updateBoards($pinterestBoard->board_id, [
                'name'        => $request->get('edit_name'),
                'description' => $request->get('edit_description'),
            ], ['ad_account_id' => $pinterestAdAccount->ads_account_id]);
            if ($response['status']) {
                $pinterestBoard->pinterest_ads_account_id = $request->get('edit_pinterest_ads_account_id');
                $pinterestBoard->name                     = $request->get('edit_name');
                $pinterestBoard->description              = $request->get('edit_description');
                $pinterestBoard->save();

                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('success', 'Board Updated successfully.');
            } else {
                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.board.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete Board
     *
     * @param mixed $id
     * @param mixed $boardId
     */
    public function deleteBoard($id, $boardId): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('error', 'No account found');
            }
            $pinterestBoard = PinterestBoards::with('account')->findOrFail($boardId);
            if (! $pinterestBoard) {
                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('error', 'Board not found');
            }
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $response  = $pinterest->deleteBoards($pinterestBoard->board_id, ['ad_account_id' => $pinterestBoard->account->ads_account_id]);
            if ($response['status']) {
                $pinterestBoard->delete();

                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('success', 'Board deleted successfully');
            } else {
                return Redirect::route('pinterest.accounts.board.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.board.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get all board sections for a account.
     *
     * @param mixed $id
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function boardSectionsIndex(Request $request, $id)
    {
        try {
            $pinterestBusinessAccountMail = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestBusinessAccountMail) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', 'No account found');
            }
            $pinterestBoardSections = PinterestBoardSections::with(['account', 'board'])
                ->where(function ($query) use ($pinterestBusinessAccountMail, $request) {
                    $query->whereHas('account', function ($query2) use ($pinterestBusinessAccountMail) {
                        $query2->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
                    });
                    if ($request->has('name') && $request->name) {
                        $query->where('name', 'like', '%' . $request->name . '%');
                    }
                    if ($request->has('pinterest_board_id') && $request->pinterest_board_id) {
                        $query->where('pinterest_board_id', $request->pinterest_board_id);
                    }
                })->paginate(Setting::get('pagination'), ['*'], 'boards-sections');
            $pinterestBoards = PinterestBoards::whereHas('account', function ($query) use ($pinterestBusinessAccountMail) {
                $query->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
            })->pluck('name', 'id')->toArray();

            return view('pinterest.board-section-index', compact('pinterestBusinessAccountMail', 'pinterestBoardSections', 'pinterestBoards'));
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.dashboard', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new Board Section.
     *
     * @param mixed $id
     */
    public function createBoardSections(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'pinterest_board_id' => 'required',
                'name'               => 'required|max:179',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestBoard = PinterestBoards::with('account')->where('id', $request->get('pinterest_board_id'))->first();
            $pinterest      = $this->getPinterestClient($pinterestAccount);
            $response       = $pinterest->createBoardSection($pinterestBoard->board_id, [
                'name' => $request->get('name'),
            ], ['ad_account_id' => $pinterestBoard->account->ads_account_id]);
            if ($response['status']) {
                PinterestBoardSections::create([
                    'pinterest_ads_account_id' => $pinterestBoard->account->id,
                    'pinterest_board_id'       => $pinterestBoard->id,
                    'board_section_id'         => $response['data']['id'],
                    'name'                     => $request->get('name'),
                ]);

                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('success', 'Board Section created successfully.');
            } else {
                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get Board Section Details
     *
     * @param mixed $id
     * @param mixed $boardSectionId
     */
    public function getBoardSection($id, $boardSectionId): JsonResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccountMails::findOrFail($id);
            if (! $pinterestBusinessAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }
            $pinterestBoardSection = PinterestBoardSections::findOrFail($boardSectionId);
            if (! $pinterestBoardSection) {
                return response()->json(['status' => false, 'message' => 'Board Section not found']);
            }

            return response()->json(['status' => true, 'message' => 'Account found', 'data' => $pinterestBoardSection->toArray()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a Board section.
     *
     * @param mixed $id
     */
    public function updateBoardSection(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'edit_board_section_id' => 'required',
                'edit_board_id'         => 'required',
                'edit_name'             => 'required|max:179',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestBoard         = PinterestBoards::with('account')->where('id', $request->get('edit_board_id'))->first();
            $pinterestBoardSections = PinterestBoardSections::where('id', $request->get('edit_board_section_id'))->first();
            $pinterest              = $this->getPinterestClient($pinterestAccount);
            $response               = $pinterest->updateBoardSection($pinterestBoard->board_id, $pinterestBoardSections->board_section_id, [
                'name' => $request->get('edit_name'),
            ], ['ad_account_id' => $pinterestBoard->account->ads_account_id]);
            if ($response['status']) {
                $pinterestBoardSections->pinterest_ads_account_id = $pinterestBoard->account->id;
                $pinterestBoardSections->pinterest_board_id       = $request->get('edit_board_id');
                $pinterestBoardSections->name                     = $request->get('edit_name');
                $pinterestBoardSections->save();

                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('success', 'Board Section Updated successfully.');
            } else {
                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete Board Section
     *
     * @param mixed $id
     * @param mixed $boardSectionId
     */
    public function deleteBoardSection($id, $boardSectionId): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('error', 'No account found');
            }
            $pinterestBoardSections = PinterestBoardSections::with(['account', 'board'])->findOrFail($boardSectionId);
            if (! $pinterestBoardSections) {
                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('error', 'Board not found');
            }
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $response  = $pinterest->deleteBoardSection($pinterestBoardSections->board->board_id, $pinterestBoardSections->board_section_id,
                ['ad_account_id' => $pinterestBoardSections->account->ads_account_id]);
            if ($response['status']) {
                $pinterestBoardSections->delete();

                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('success', 'Board Section deleted successfully');
            } else {
                return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.boardSections.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get Pinterest Client
     *
     * @param mixed $pinterestAccount
     *
     * @throws Exception
     */
    public function getPinterestClient($pinterestAccount): PinterestService
    {
        $pinterest = new PinterestService($pinterestAccount->account->pinterest_client_id, $pinterestAccount->account->pinterest_client_secret, $pinterestAccount->account->id);
        $pinterest->updateAccessToken($pinterestAccount->pinterest_access_token);

        return $pinterest;
    }
}
