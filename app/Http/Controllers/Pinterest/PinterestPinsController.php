<?php

namespace App\Http\Controllers\Pinterest;

use Illuminate\Support\Facades\Validator;
use App\Setting;
use App\PinterestPins;
use App\PinterestBoards;
use Illuminate\Http\Request;
use App\PinterestAdsAccounts;
use App\PinterestBoardSections;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\PinterestBusinessAccountMails;
use Illuminate\Support\Facades\Redirect;
use Exception;

class PinterestPinsController extends Controller
{
    /**
     * Get all pins for a account.
     *
     * @param mixed $id
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function pinsIndex(Request $request, $id)
    {
        try {
            $pinterestBusinessAccountMail = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestBusinessAccountMail) {
                return Redirect::route('pinterest.accounts.dashboard', [$id])
                    ->with('error', 'No account found');
            }
            $pinterestPins = PinterestPins::with(['account', 'board'])
                ->where(function ($query) use ($pinterestBusinessAccountMail, $request) {
                    $query->whereHas('account', function ($query2) use ($pinterestBusinessAccountMail) {
                        $query2->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
                    });
                    if ($request->has('name') && $request->name) {
                        $query->where('title', 'like', '%' . $request->name . '%');
                    }
                    if ($request->has('pinterest_board_id') && $request->pinterest_board_id) {
                        $query->where('pinterest_board_id', $request->pinterest_board_id);
                    }
                })->paginate(Setting::get('pagination'), ['*'], 'pins');
            $pinterestBoards = PinterestBoards::whereHas('account', function ($query) use ($pinterestBusinessAccountMail) {
                $query->where('pinterest_mail_id', $pinterestBusinessAccountMail->id);
            })->pluck('name', 'id')->toArray();
            $pinterestAdsAccount = PinterestAdsAccounts::where('pinterest_mail_id', $pinterestBusinessAccountMail->id)->pluck('ads_account_name', 'id')->toArray();

            return view('pinterest.pins-index', compact('pinterestBusinessAccountMail', 'pinterestPins', 'pinterestBoards', 'pinterestAdsAccount'));
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.dashboard', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new Pin.
     *
     * @param mixed $id
     */
    public function createPin(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'link'                       => 'sometimes|max:2048|url|nullable',
                'title'                      => 'sometimes|max:100',
                'description'                => 'sometimes|max:500',
                'alt_text'                   => 'sometimes|max:500',
                'pinterest_board_id'         => 'required',
                'pinterest_board_section_id' => 'sometimes',
                'media_source_type'          => 'required',
                'media_content_type'         => 'required|in:image/jpeg,image/png,image/jpg',
                'media_data'                 => 'required',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('create_popup', true)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestBoard        = PinterestBoards::with('account')->where('id', $request->get('pinterest_board_id'))->first();
            $pinterestBoardSection = PinterestBoardSections::where('id', $request->get('pinterest_board_section_id'))->first();
            $pinterest             = $this->getPinterestClient($pinterestAccount);
            $response              = $pinterest->createPin([
                'link'             => $request->has('link') ? $request->get('link') : null,
                'title'            => $request->has('title') ? $request->get('title') : null,
                'description'      => $request->has('description') ? $request->get('description') : null,
                'alt_text'         => $request->has('alt_text') ? $request->get('alt_text') : null,
                'board_id'         => $pinterestBoard->board_id,
                'board_section_id' => $pinterestBoardSection ? $pinterestBoardSection->board_section_id : null,
                'media_source'     => $this->buildMedia($request->all()),
            ], ['ad_account_id' => $pinterestBoard->account->ads_account_id]);
            if ($response['status']) {
                PinterestPins::create([
                    'link'                       => $request->has('link') ? $request->get('link') : null,
                    'title'                      => $request->has('title') ? $request->get('title') : null,
                    'description'                => $request->has('description') ? $request->get('description') : null,
                    'alt_text'                   => $request->has('alt_text') ? $request->get('alt_text') : null,
                    'pinterest_board_id'         => $pinterestBoard->id,
                    'pinterest_board_section_id' => $pinterestBoardSection ? $pinterestBoardSection->id : null,
                    'media_source'               => json_encode($this->buildMedia($request->all())),
                    'pinterest_ads_account_id'   => $pinterestBoard->account->id,
                    'pin_id'                     => $response['data']['id'],
                ]);

                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('success', 'Pin created successfully.');
            } else {
                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.pin.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get media parameters
     *
     * @param mixed $params
     */
    public function buildMedia($params): array
    {
        $mediaParams = [];
        switch ($params['media_source_type']) {
            case 'image_url':
                $mediaParams = ['source_type' => $params['media_source_type'], 'url' => $params['media_url']];
                break;
            case 'multiple_image_urls':
                $items = [];
                foreach ($params['media_items'] as $item) {
                    $item[] = [
                        'title'       => $item['title'],
                        'description' => $item['description'],
                        'link'        => $item['link'],
                        'url'         => $item['url'],
                    ];
                }
                $mediaParams = [
                    'source_type' => $params['media_source_type'],
                    'items'       => $items,
                    'index'       => 0,
                ];
                break;
            case 'image_base64':
                $mediaParams = [
                    'source_type'  => $params['media_source_type'],
                    'content_type' => $params['media_content_type'],
                    'data'         => $params['media_data'],
                ];
                break;
            case 'multiple_image_base64':
                $items = [];
                foreach ($params['media_items'] as $item) {
                    $item[] = [
                        'title'        => $item['title'],
                        'description'  => $item['description'],
                        'content_type' => $item['content_type'],
                        'data'         => $item['data'],
                    ];
                }
                $mediaParams = [
                    'source_type' => $params['media_source_type'],
                    'items'       => $items,
                    'index'       => 0,
                ];
                break;
        }

        return $mediaParams;
    }

    /**
     * Get Pin Details
     *
     * @param mixed $id
     * @param mixed $pinId
     */
    public function getPin($id, $pinId): JsonResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccountMails::findOrFail($id);
            if (! $pinterestBusinessAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }
            $pinterestPin = PinterestPins::findOrFail($pinId);
            if (! $pinterestPin) {
                return response()->json(['status' => false, 'message' => 'Pin not found']);
            }
            $pinterestBoardSection = PinterestBoardSections::where('pinterest_board_id', $pinterestPin->pinterest_board_id)->get();

            return response()->json(['status' => true, 'message' => 'Account found', 'data' => ['pin' => $pinterestPin->toArray(), 'sections' => $pinterestBoardSection->toArray()]]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a Pin.
     *
     * @param mixed $id
     */
    public function updatePin(Request $request, $id): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('error', 'No account found');
            }
            $validator = Validator::make($request->all(), [
                'edit_pin_id'                     => 'required',
                'edit_link'                       => 'sometimes|max:2048|url|nullable',
                'edit_title'                      => 'sometimes|max:100',
                'edit_description'                => 'sometimes|max:500',
                'edit_alt_text'                   => 'sometimes|max:500',
                'edit_pinterest_board_id'         => 'sometimes',
                'edit_pinterest_board_section_id' => 'sometimes',
            ]);
            if ($validator->fails()) {
                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->withErrors($validator)
                    ->withInput();
            }
            $pinterestBoard        = PinterestBoards::with('account')->where('id', $request->get('edit_pinterest_board_id'))->first();
            $pinterestBoardSection = PinterestBoardSections::where('id', $request->get('edit_pinterest_board_section_id'))->first();
            $pin                   = PinterestPins::where('id', $request->get('edit_pin_id'))->first();
            $pinterest             = $this->getPinterestClient($pinterestAccount);
            $response              = $pinterest->updatePin($pin->pin_id, [
                'link'                       => $request->has('edit_link') ? $request->get('edit_link') : null,
                'title'                      => $request->has('edit_title') ? $request->get('edit_title') : null,
                'description'                => $request->has('edit_description') ? $request->get('edit_description') : null,
                'alt_text'                   => $request->has('edit_alt_text') ? $request->get('edit_alt_text') : null,
                'pinterest_board_id'         => $pinterestBoard->board_id,
                'pinterest_board_section_id' => $pinterestBoardSection ? $pinterestBoardSection->board_section_id : null,
            ], ['ad_account_id' => $pinterestBoard->account->ads_account_id]);
            if ($response['status']) {
                $pin->link                       = $request->has('edit_link') ? $request->get('edit_link') : null;
                $pin->title                      = $request->has('edit_title') ? $request->get('edit_title') : null;
                $pin->description                = $request->has('edit_description') ? $request->get('edit_description') : null;
                $pin->alt_text                   = $request->has('edit_alt_text') ? $request->get('edit_alt_text') : null;
                $pin->pinterest_board_id         = $pinterestBoard->id;
                $pin->pinterest_board_section_id = $pinterestBoardSection ? $pinterestBoardSection->id : null;
                $pin->pinterest_ads_account_id   = $pinterestBoard->account->id;
                $pin->save();

                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('success', 'Pin updated successfully.');
            } else {
                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.pin.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete Pin
     *
     * @param mixed $id
     * @param mixed $pinId
     */
    public function deletePin($id, $pinId): RedirectResponse
    {
        try {
            $pinterestAccount = PinterestBusinessAccountMails::with('account')->findOrFail($id);
            if (! $pinterestAccount) {
                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('error', 'No account found');
            }
            $pin = PinterestPins::with(['account'])->findOrFail($pinId);
            if (! $pin) {
                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('error', 'Pin not found');
            }
            $pinterest = $this->getPinterestClient($pinterestAccount);
            $response  = $pinterest->deletePin($pin->pin_id, ['ad_account_id' => $pin->account->ads_account_id]);
            if ($response['status']) {
                $pin->delete();

                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('success', 'Pin deleted successfully');
            } else {
                return Redirect::route('pinterest.accounts.pin.index', [$id])
                    ->with('error', $response['message']);
            }
        } catch (Exception $e) {
            return Redirect::route('pinterest.accounts.pin.index', [$id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get Board Sections by boardId.
     *
     * @param mixed $id
     * @param mixed $boardId
     */
    public function getBoardSections($id, $boardId): JsonResponse
    {
        try {
            $pinterestBusinessAccount = PinterestBusinessAccountMails::findOrFail($id);
            if (! $pinterestBusinessAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }
            $pinterestBoardSection = PinterestBoardSections::where('pinterest_board_id', $boardId)->get();

            return response()->json(['status' => true, 'message' => 'Account found', 'data' => $pinterestBoardSection->toArray()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
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
