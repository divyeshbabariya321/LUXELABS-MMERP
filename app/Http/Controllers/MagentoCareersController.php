<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Exception;
use App\StoreWebsite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\MagentoModuleCareers as Career;
use Illuminate\Validation\ValidationException;

class MagentoCareersController extends Controller
{
    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request): View
    {
        $careers       = Career::orderByDesc('created_at')->get();
        $storeWebsites = StoreWebsite::all();

        return view('magento_careers.index', [
            'careers'       => $careers,
            'storeWebsites' => $storeWebsites,
        ]);
    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getCareerByFilter(Request $request)
    {
        $html    = '';
        $careers = Career::orderByDesc('created_at');
        if (isset($request->location) && ! empty($request->location)) {
            $careers->where('magento_module_careers.location', 'Like', '%' . $request->location . '%');
        }
        if (isset($request->type) && ! empty($request->type)) {
            $careers->where('magento_module_careers.type', 'Like', '%' . $request->type . '%');
        }
        if (isset($request->descriptions) && ! empty($request->descriptions)) {
            $careers->where('magento_module_careers.description', 'Like', '%' . $request->descriptions . '%');
        }

        if (isset($request->store_ids) && ! empty($request->store_ids)) {
            $storeWebsitesIds = $request->store_ids;
            if ($storeWebsitesIds) {
                $careers->whereHas('storeWebsites', function ($subquery) use ($storeWebsitesIds) {
                    $subquery->whereIn('store_websites.id', $storeWebsitesIds);
                });
            }
        }
        $careers = $careers->get();
        if (! empty($careers)) {
            foreach ($careers as $career) {
                $title = '';
                $title = implode(', ', array_map(function ($item) {
                    return $item->title;
                }, (array) $career->getStoreWebsites()));
                $isActive = ($career->getIsActive() == 1) ? 'checked' : '';
                $html .= '<tr>
        <td class="td-id-' . $career->getId() . '">
            ' . $career->getId() . '
        </td>
        <td class="td-title-' . $career->getId() . '">
                                            ' . $career->getTitle() . '
                                        </td>
        <td class="td-store-websites-' . $career->getId() . '">' . $title . '</td>
        <td class="td-location-' . $career->getId() . '">
            ' . $career->getLocation() . '
        </td>
        <td class="td-type-' . $career->getId() . '">
            ' . $career->getType() . '
        </td>
        <td class="td-description-' . $career->getId() . '">
            ' . Str::limit($career->getDescription(), 255) . '
        </td>
        <td class="text-center">
        <span class="td-mini-container">
            <input type="checkbox" class="isActive td-is-active-' . $career->getId() . '" name="is_active" ' . $isActive . '/>
        </span>
        </td>
        <td class="td-created-at-' . $career->getId() . '">
                                        ' . $career->getCreatedAt() . '
                                    </td>

        <td>
            <a href="#" class="btn btn-xs btn-secondary" onclick="updateCareerData(' . $career->getId() . ')">Edit</a>
        </td>
    </tr>';
            }
        }
        $responseData = ['success' => true, 'html' => $html];

        return response()->json($responseData);
    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getCareerRecord(Request $request): JsonResponse
    {
        $career = Career::where('id', $request->careerId)->orderByDesc('created_at')->first();
        if (! empty($career)) {
            $jsonEncode   = json_encode($career);
            $responseData = ['success' => true, 'career-id' => $career->id, 'data-json' => $jsonEncode];

            return response()->json($responseData);
        }
    }

    public function createOrEdit(Request $request): JsonResponse
    {
        $data     = $request->all();
        $isCreate = true;

        try {
            $validator = Validator::make($data, [
                'description' => 'required|max:10000',
                'title'       => 'max:255',
                'type'        => 'max:255',
                'location'    => 'max:255',
                'is_active'   => 'max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator, 'Validation failed.');
            }

            if (isset($data['id']) && Career::find($data['id'])) {
                $career   = Career::find($data['id']);
                $isCreate = false;
            } else {
                $career = new Career();
            }
            $career->setDescription($data[Career::DESCRIPTION] ?? '');
            $career->setTitle($data[Career::TITLE] ?? '');
            $career->setType($data[Career::TYPE] ?? '');
            $career->setLocation($data[Career::LOCATION] ?? '');
            $career->setIsActive(isset($data[Career::IS_ACTIVE]) ? (bool) $data[Career::IS_ACTIVE] : false);
            $career->save();

            $career->removeAllOrgnization();
            $career->addStoreWebsites($data['store_websites'] ?? []);

            return response()->json([
                'code'    => 200,
                'message' => sprintf('Career with id: %s was %s.', $career->getId(), $isCreate ? 'created' : 'edited'),
                'career'  => [
                    Career::ID               => $career->getId(),
                    Career::TYPE             => $career->getType(),
                    Career::DESCRIPTION      => $career->getDescription(),
                    Career::LOCATION         => $career->getLocation(),
                    Career::IS_ACTIVE        => $career->getIsActive(),
                    Career::CREATED_AT       => $career->getCreatedAt(),
                    Career::TITLE            => $career->getTitle(),
                    Career::STORE_WEBSITE_ID => array_map(fn ($item) => $item->title, $career->getStoreWebsites()),
                ],
                'career_json' => (string) json_encode($career),
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'code'    => 400,
                'message' => $validationException->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => 'error',
            ], 500);
        }
    }

    public function listingApi(Request $request): JsonResponse
    {
        $data = $request->all();

        /** @var Career $career */
        $career = Career::where(Career::IS_ACTIVE, true);

        if (isset($data['website_id']) && $data['website_id']) {
            $career->whereHas('storeWebsites', fn ($query) => $query->where('website_id', (int) $data['website_id']));
        }

        if (isset($data['title'])) {
            $career->where('title', 'like', "%{$data['title']}%");
        }

        if (isset($data['order_by']) && $data['order_by'] == 1) {
            $career->orderBy('created_at');
        } else {
            $career->orderByDesc('created_at');
        }

        /** @var Career $career */
        $career = $career->get();

        return response()->json([
            'code'    => 200,
            'careers' => array_map(fn ($career) => $career->toArrayCareer(), (array) $career->getIterator()),
        ]);
    }
}
