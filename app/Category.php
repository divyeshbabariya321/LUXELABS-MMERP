<?php

namespace App;

use App\Helpers\ProductHelper;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nestable\NestableTrait;
use seo2websites\MagentoHelper\MagentoHelper;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Category extends Model
{
    const UNKNOWN_CATEGORIES = 143;

    const PUSH_TYPE = [
        '0' => 'Simple',
        '1' => 'Configurable',
    ];

    use NestableTrait;

    protected $parent = 'parent_id';

    protected static $categories_with_childs = null;

    /**
     * @var string
     *
     * @SWG\Property(property="id",type="integer")
     * @SWG\Property(property="title",type="string")
     * @SWG\Property(property="parent_id",type="integer")
     * @SWG\Property(property="status_after_autocrop",type="string")
     * @SWG\Property(property="magento_id",type="integer")
     * @SWG\Property(property="show_all_id",type="integer")
     */
    public $fillable = ['id', 'title', 'parent_id', 'status_after_autocrop', 'magento_id', 'show_all_id', 'need_to_check_measurement', 'need_to_check_size', 'ignore_category', 'push_type', 'category_segment_id', 'days_refund'];

    /**
     * Get the index name for the model.
     */
    public function childs(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id');
    }

    public function childsOrderByTitle(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id')->orderBy('title');
    }

    public function childLevelSencond(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }

    public function getSupParent()
    {
        return $this->parent()->with('getSupParent');
    }

    public function getChilds()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function getSubChilds()
    {
        return $this->getChilds()->with('getSubChilds');
    }

    public function parentC(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }

    public function parentM(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }

    public static function isParent($id)
    {
        $child_count = self::where('parent_id', $id)
            ->count();

        return $child_count ? true : false;
    }

    public static function website_name($name)
    {
        $name = '"'.$name.'"';
        $products = ScrapedProducts::where('properties', 'like', '%'.$name.'%')->select('website')->distinct()->get()->pluck('website')->toArray();
        $web_name = implode(', ', $products);

        return $web_name ? $web_name : '-';
    }

    public static function hasProducts($id)
    {
        $products_count = Product::where('category', $id)
            ->count();

        return $products_count ? true : false;
    }

    public function categorySegmentId(): HasOne
    {
        return $this->hasOne(CategorySegment::class, 'id', 'category_segment_id');
    }

    public static function getCategoryIdByKeyword($keyword, $gender = null, $genderAlternative = null)
    {
        $gender = self::setGender($gender, $genderAlternative);

        $dbResult = self::fetchCategory($keyword);

        if ($dbResult->isEmpty()) {
            $dbResult = self::fetchCategoryByReference($keyword);
        }

        if ($dbResult->isEmpty()) {
            return 0;
        }

        if ($dbResult->count() == 1 && ! self::hasSubcategories($dbResult->first())) {
            return $dbResult->first()->id;
        }

        if (! $gender) {
            return 0;
        }

        return self::findCategoryByGender($dbResult, $gender);
    }

    private static function setGender($gender, $genderAlternative)
    {
        return ! $gender ? $genderAlternative : $gender;
    }

    private static function fetchCategory($keyword)
    {
        return self::select('id', 'parent_id', 'references')
            ->where('title', $keyword)
            ->get();
    }

    private static function fetchCategoryByReference($keyword)
    {
        $dbResult = self::select('id', 'parent_id', 'references')
            ->where('references', 'like', '%'.$keyword.'%')
            ->whereNotIn('id', [self::UNKNOWN_CATEGORIES, 1])
            ->get();

        $matchIds = self::findMatchingReferences($dbResult, $keyword);

        return self::select('id', 'parent_id', 'references')
            ->whereIn('id', $matchIds)
            ->get();
    }

    private static function findMatchingReferences($dbResult, $keyword)
    {
        $matchIds = [];
        foreach ($dbResult as $db) {
            if ($db->references) {
                $referenceArrays = self::cleanReferences($db->references);
                foreach ($referenceArrays as $reference) {
                    if (self::isSimilar($keyword, $reference)) {
                        $matchIds[] = $db->id;
                        break;
                    }
                }
            }
        }

        return $matchIds;
    }

    private static function cleanReferences($references)
    {
        $referenceArrays = explode(',', $references);

        return array_map(function ($ref) {
            $ref = preg_replace('/\s+/', '', $ref);

            return preg_replace('/[^a-zA-Z0-9_ -]/s', '', $ref);
        }, $referenceArrays);
    }

    private static function isSimilar($input, $reference)
    {
        $input = preg_replace('/\s+/', '', $input);
        $input = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $input);
        similar_text(strtolower($input), strtolower($reference), $percent);

        return $percent >= 80;
    }

    private static function hasSubcategories($category)
    {
        return Category::where('parent_id', $category->id)->exists();
    }

    private static function findCategoryByGender($dbResult, $gender)
    {
        foreach ($dbResult as $result) {
            $parentId = $result->parent_id;

            if (self::isTopLevelCategory($parentId) || self::isUnwantedGenderCategory($parentId, $gender)) {
                return 0;
            }

            $categoryId = $result->id;
            $parentCategory = Category::find($parentId);

            if ($parentCategory && self::isValidGenderCategory($parentCategory, $gender)) {
                return $categoryId;
            }
        }

        return 0;
    }

    private static function isTopLevelCategory($parentId)
    {
        return $parentId == 0;
    }

    private static function isUnwantedGenderCategory($parentId, $gender)
    {
        return ($parentId == 2 && strtolower($gender) == 'women') ||
            ($parentId == 3 && strtolower($gender) == 'men') ||
            ($parentId == 146 && strtolower($gender) == 'kids');
    }

    private static function isValidGenderCategory($parentCategory, $gender)
    {
        return ($parentCategory->parent_id == 2 && strtolower($gender) == 'women') ||
            ($parentCategory->parent_id == 3 && strtolower($gender) == 'men') ||
            ($parentCategory->parent_id == 146 && strtolower($gender) == 'kids');
    }

    public static function getCategoryPathById($categoryId = '')
    {
        // If we don't have an ID, return an empty string
        if (! $categoryId) {
            return '';
        }

        // Set empty category path
        $categoryPath = '';

        // Get category from database
        $category = Category::find($categoryId);

        // Do we have data?
        if ($category !== null) {
            // Set initial title
            $categoryPath = $category->title;

            // Loop while we haven't reached the top category
            while ($category && $category->parent_id > 0) {
                // Get next category from database
                $category = Category::find($category->parent_id);

                // Update category path
                if ($category !== null) {
                    $categoryPath = $category->title.' > '.$categoryPath;
                }
            }
        }

        // Return category path
        return $categoryPath;
    }

    public static function getCategoryIdsAndPathById($category)
    {
        // If we don't have an ID, return an empty string
        // if (empty($categoryId)) {
        //     return [];
        // }

        // // Set empty category path
        // $categoryPath = '';
        // $categoryTitleArray = [];
        // $categoryIdsArray = [];

        // // Get category from database
        // $category = Category::find($categoryId);

        // // Do we have data?
        // if ($category !== null) {
        // Set initial title
        $categoryPath = $category->title;
        $categoryIdsArray[] = $category->id;
        $categoryTitleArray[] = $category->title;

        // Loop while we haven't reached the top category
        while ($category && $category->parent_id > 0) {
            // Get next category from database
            $category = Category::where('id', $category->parent_id)->where('parent_id', '!=', 0)->first();

            // Update category path
            if ($category !== null) {
                $categoryPath = $category->title.' > '.$categoryPath;
                $categoryIdsArray[] = $category->id;
                $categoryTitleArray[] = $category->title;
            }
        }
        // }

        // Return category path
        return ['category_path' => $categoryPath, 'category_ids' => array_reverse($categoryIdsArray), 'category_title_array' => array_reverse($categoryTitleArray)];
    }

    public static function getCategoryTreeMagento($id)
    {
        // Load new category model
        // $category = new Category();

        // Create category instance
        $categoryInstance = Category::find($id);

        // Set empty category tree for holding categories
        $categoryTree = [];

        // Continue only if category is not null
        if ($categoryInstance !== null) {
            // Load initial category
            $categoryTree[] = $categoryInstance->magento_id;

            // Set parent ID
            $parentId = $categoryInstance->parent_id;

            // Loop until we found the top category
            while ($parentId != 0) {
                // find next category
                $categoryInstance = Category::find($parentId);

                // Add category to tree
                $categoryTree[] = $categoryInstance->magento_id;

                // Add additional category to tree
                if (! $categoryInstance->show_all_id) {
                    $categoryTree[] = $categoryInstance->show_all_id;
                }

                // Set new parent ID
                $parentId = $categoryInstance->parent_id;
            }
        }

        // Return reverse array
        return array_reverse($categoryTree);
    }

    public static function getCategoryTreeMagentoWithPosition($id, $website, $needOrigin = false)
    {
        $categoryMulti = StoreWebsiteCategory::where('category_id', $id)->where('store_website_id', $website->id)->first();
        $categoryInstance = Category::find($id);
        $categoryTree = [];
        $topParent = ProductHelper::getTopParent($id);

        if ($categoryInstance && $categoryMulti) {
            self::addCategoryToTree($categoryTree, $categoryMulti, 1, $needOrigin, $topParent);

            $parentId = $categoryInstance->parent_id;
            $position = 2;

            while ($parentId != 0) {
                $categoryInstance = Category::find($parentId);
                $categoryMultiChild = StoreWebsiteCategory::where('category_id', $parentId)
                    ->where('store_website_id', $website->id)
                    ->first();

                if ($categoryMultiChild) {
                    self::addCategoryToTree($categoryTree, $categoryMultiChild, $position, $needOrigin);
                    $position++;
                }

                $parentId = $categoryInstance->parent_id;
            }
        }

        return array_reverse($categoryTree);
    }

    private static function addCategoryToTree(&$categoryTree, $categoryMulti, $position, $needOrigin, $topParent = null)
    {
        $categoryData = [
            'position' => $position,
            'category_id' => $categoryMulti->remote_id,
        ];

        if ($needOrigin) {
            $categoryData['org_id'] = $categoryMulti->category_id;
        }

        if ($topParent) {
            $categoryData['topParent'] = $topParent;
        }

        $categoryTree[] = $categoryData;
    }

    public static function getCroppingGridImageByCategoryId($categoryId)
    {
        $imagesForGrid = [
            'Shoes' => 'shoes_grid.png',
            'Backpacks' => 'Backpack.png',
            'Bags' => 'Backpack.png',
            'Beach' => 'Backpack.png',
            'Travel' => 'Backpack.png',
            'Travel Bag' => 'Backpack.png',
            'Travel Bags' => 'Backpack.png',
            'Belt' => 'belt.png',
            'Belts' => 'belt.png',
            'Clothing' => 'Clothing.png',
            'Skirts' => 'Clothing.png',
            'Pullovers' => 'Clothing.png',
            'Shirt' => 'Clothing.png',
            'Dresses' => 'Clothing.png',
            'Kaftan' => 'Clothing.png',
            'Tops' => 'Clothing.png',
            'Jumpers & Jump Suits' => 'Clothing.png',
            'Pant' => 'Clothing.png',
            'Pants' => 'Clothing.png',
            'Dress' => 'Clothing.png',
            'Sweatshirt/s & Hoodies' => 'Clothing.png',
            'Shirts' => 'Clothing.png',
            'Denim' => 'Clothing.png',
            'Sweat Pants' => 'Clothing.png',
            'T-Shirts' => 'Clothing.png',
            'Sweater' => 'Clothing.png',
            'Sweaters' => 'Clothing.png',
            'Clothings' => 'Clothing.png',
            'Coats & Jackets' => 'Clothing.png',
            'Tie & Bow Ties' => 'Bow.png',
            'Clutches' => 'Clutch.png',
            'Clutches & Slings' => 'Clutch.png',
            'Document Holder' => 'Clutch.png',
            'Clutch Bags' => 'Clutch.png',
            'Crossbody Bag' => 'Clutch.png',
            'Wristlets' => 'Clutch.png',
            'Crossbody Bags' => 'Clutch.png',
            'Make-Up Bags' => 'Clutch.png',
            'Belt Bag' => 'Clutch.png',
            'Belt Bags' => 'Clutch.png',
            'Hair Accessories' => 'Hair_accessories.png',
            'Beanies & Caps' => 'Hair_accessories.png',
            'Handbags' => 'Handbag.png',
            'Duffle Bags' => 'Handbag.png',
            'Laptop Bag' => 'Handbag.png',
            'Bucket Bags' => 'Handbag.png',
            'Laptop Bags' => 'Handbag.png',
            'Jewelry' => 'Jewellery.png',
            'Shoulder Bags' => 'Shoulder_bag.png',
            'Sunglasses & Frames' => 'Sunglasses.png',
            'Gloves' => 'Sunglasses.png', //need to be made for gloves
            'Tote Bags' => 'Tote.png',
            'Wallet' => 'Wallet.png',
            'Wallets & Cardholder' => 'Wallet.png',
            'Wallets & Cardholders' => 'Wallet.png',
            'Key Pouches' => 'Wallet.png',
            'Key Pouch' => 'Wallet.png',
            'Coin Case / Purse' => 'Wallet.png',
            'Shawls And Scarves' => 'Shawl.png',
            'Shawls And Scarve' => 'Shawl.png',
            'Scarves & Wraps' => 'Shawl.png',
            'Key Rings & Chains' => 'Keychains.png',
            'Key Rings & Chain' => 'Keychains.png',
            'Watches' => 'Keychains.png',
            'Watch' => 'Keychains.png',
        ];

        $category = Category::find($categoryId);
        if (isset($category->title)) {
            $catName = $category->title;

            if (array_key_exists($catName, $imagesForGrid)) {
                return $imagesForGrid[$catName];
            }

            if ($category->parent_id > 1) {
                $category = Category::find($category->parent_id);

                return $imagesForGrid[trim($category->title)] ?? '';
            }
        }

        return '';
    }

    public function suppliercategorycount(): HasOne
    {
        return $this->hasOne(SupplierCategoryCount::class, 'category_id', 'id');
    }

    public static function list()
    {
        return self::pluck('title', 'id')->toArray();
    }

    public static function pushStoreWebsiteCategory($categories, $stores)
    {
        $categories = Category::whereIn('id', $categories)->orderBy('parent_id')->with('parent')->get();
        $storeWebsites = StoreWebsite::whereIn('id', $stores)->where('api_token', '!=', '')->where('website_source', 'magento')->get();

        if ($categories->isEmpty() || $storeWebsites->isEmpty()) {
            return;
        }

        foreach ($categories as $category) {
            foreach ($storeWebsites as $store) {
                try {
                    $case = self::determineCategoryCase($category);
                    self::handleCategoryCase($category, $store->id, $case);
                } catch (Exception $e) {
                    Log::error($e);
                }
            }
        }
    }

    private static function determineCategoryCase($category)
    {
        if ($category->parent_id == 0) {
            return 'single';
        } elseif (! $category->parentM && $category->parentM->parent_id == 0) {
            return 'second';
        } elseif (! $category->parentM && ! $category->parentM->parentM && $category->parentM->parentM->parent_id == 0) {
            return 'third';
        } elseif (! $category->parentM && ! $category->parentM->parentM && ! $category->parentM->parentM->parentM && $category->parentM->parentM->parentM->parent_id == 0) {
            return 'fourth';
        }
    }

    private static function handleCategoryCase($category, $storeWebsiteId, $case)
    {
        switch ($case) {
            case 'single':
                self::processSingleLevelCategory($category, $storeWebsiteId);
                break;
            case 'second':
                self::processMultiLevelCategory($category, $storeWebsiteId, 2);
                break;
            case 'third':
                self::processMultiLevelCategory($category, $storeWebsiteId, 3);
                break;
            case 'fourth':
                self::processMultiLevelCategory($category, $storeWebsiteId, 4);
                break;
        }
    }

    private static function processSingleLevelCategory($category, $storeWebsiteId)
    {
        $data = self::prepareCategoryData($category, 1, 0);
        $remoteId = MagentoHelper::createCategory(0, $data, $storeWebsiteId);
        self::storeCategory($category, $storeWebsiteId, $remoteId);
    }

    private static function processMultiLevelCategory($category, $storeWebsiteId, $level)
    {
        $parentCategory = self::getParentCategory($category, $storeWebsiteId, $level);
        if ($parentCategory === null) {
            return;
        }

        $data = self::prepareCategoryData($category, $level, $parentCategory['remoteId']);
        $remoteId = MagentoHelper::createCategory($parentCategory['remoteId'], $data, $storeWebsiteId);
        self::storeCategory($category, $storeWebsiteId, $remoteId);
    }

    private static function getParentCategory($category, $storeWebsiteId, $level)
    {
        $parentCategory = $category->parentM;
        for ($i = 1; $i < $level - 1; $i++) {
            $parentCategory = $parentCategory->parentM;
            if (! $parentCategory) {
                return null;
            }
        }

        $storeWebsiteCategory = StoreWebsiteCategory::where('store_website_id', $storeWebsiteId)
            ->where('category_id', $parentCategory->id)
            ->where('remote_id', '>', 0)
            ->first();

        if ($storeWebsiteCategory) {
            return ['category' => $parentCategory, 'remoteId' => $storeWebsiteCategory->remote_id];
        }

        return self::createParentCategory($parentCategory, $storeWebsiteId, 1);
    }

    private static function createParentCategory($parentCategory, $storeWebsiteId, $level)
    {
        $data = self::prepareCategoryData($parentCategory, $level, 0);
        $remoteId = MagentoHelper::createCategory(0, $data, $storeWebsiteId);

        if ($remoteId) {
            self::storeCategory($parentCategory, $storeWebsiteId, $remoteId);
        }

        return ['category' => $parentCategory, 'remoteId' => $remoteId];
    }

    private static function prepareCategoryData($category, $level, $parentId)
    {
        return [
            'id' => $category->id,
            'level' => $level,
            'name' => ucwords($category->title),
            'parentId' => $parentId,
        ];
    }

    private static function storeCategory($category, $storeWebsiteId, $remoteId)
    {
        $existingCategory = StoreWebsiteCategory::where('store_website_id', $storeWebsiteId)
            ->where('category_id', $category->id)
            ->where('remote_id', $remoteId)
            ->first();

        if (! $existingCategory) {
            $storeWebsiteCategory = new StoreWebsiteCategory;
            $storeWebsiteCategory->category_id = $category->id;
            $storeWebsiteCategory->store_website_id = $storeWebsiteId;
            $storeWebsiteCategory->remote_id = $remoteId;
            $storeWebsiteCategory->save();
        }
    }

    public static function ScrapedProducts($name)
    {
        $name = strtolower(str_replace('/', ',', $name));

        return ScrapedProducts::where('categories', $name)->count();
    }

    public static function updateCategoryAuto($name)
    {
        $expression = explode('/', $name);
        if (! $expression) {
            return false;
        }

        $mainCategory = self::getMainCategory($expression);
        $categoriesWithChildren = self::getCategoriesWithChildren();
        $matchedCategory = self::findMatchingCategory($expression, $categoriesWithChildren);

        if ($matchedCategory) {
            return self::getMatchedCategory($matchedCategory, $mainCategory);
        }

        return false;
    }

    private static function getMainCategory($expression)
    {
        $liForMen = ['MAN', 'MEN', 'UOMO', 'MALE'];
        $liForWomen = ['WOMAN', 'WOMEN', 'DONNA', 'FEMALE'];
        $liForKids = ['KIDS'];

        foreach ($expression as $exr) {
            if (self::inArrayIgnoreCase($exr, $liForMen)) {
                return 3;
            }
            if (self::inArrayIgnoreCase($exr, $liForWomen)) {
                return 2;
            }
            if (self::inArrayIgnoreCase($exr, $liForKids)) {
                return 146;
            }
        }

        return false;
    }

    private static function inArrayIgnoreCase($needle, $haystack)
    {
        foreach ($haystack as $item) {
            if (strtolower($item) == strtolower($needle)) {
                return true;
            }
        }

        return false;
    }

    private static function getCategoriesWithChildren()
    {
        if (self::$categories_with_childs === null) {
            self::$categories_with_childs = self::with('parentC.parentM')->get();
        }

        return self::$categories_with_childs;
    }

    private static function findMatchingCategory($expression, $categoriesWithChildren)
    {
        foreach ($expression as $exr) {
            foreach ($categoriesWithChildren as $singleCategory) {
                if (strtolower($singleCategory->title) == strtolower($exr)) {
                    return $singleCategory;
                }
            }
        }

        return null;
    }

    private static function getMatchedCategory($matchedCategory, $mainCategory)
    {
        $levelOne = $matchedCategory->parentC;
        $levelTwo = $levelOne ? $levelOne->parentM : null;

        if ($levelTwo && (self::isCategoryMatch($levelTwo, $mainCategory))) {
            return $matchedCategory;
        }

        if ($levelOne && (self::isCategoryMatch($levelOne, $mainCategory))) {
            return $matchedCategory;
        }

        if (self::isCategoryMatch($matchedCategory, $mainCategory)) {
            return $matchedCategory;
        }

        return false;
    }

    private static function isCategoryMatch($category, $mainCategory)
    {
        return $category->id == $mainCategory || $category->parent_id == $mainCategory;
    }

    public static function updateCategoryAutoSpace($name)
    {
        $categories = Category::where('id', '!=', 143)->get();
        $matchedWords = self::getMatchedWords($name, $categories);

        if (! $matchedWords) {
            return false;
        }

        $mainCategory = self::determineMainCategory($matchedWords);

        return self::findCategoryByMain($matchedWords, $mainCategory);
    }

    private static function getMatchedWords($name, $categories)
    {
        $matchedWords = [];
        foreach ($categories as $cat) {
            if (self::wordInText($name, $cat->title)) {
                $matchedWords[$cat->id] = $cat->title;
            } else {
                $referencesWords = array_filter(explode(',', $cat->references));
                foreach ($referencesWords as $word) {
                    if (self::wordInText($name, $word)) {
                        $matchedWords[$cat->id] = $cat->title;
                    }
                }
            }
        }

        return $matchedWords;
    }

    private static function wordInText($text, $word)
    {
        return strpos(strtolower($text), strtolower($word)) !== false;
    }

    private static function determineMainCategory($matchedWords)
    {
        $liForMen = ['MAN', 'MEN', 'UOMO', 'MALE'];
        $liForWomen = ['WOMAN', 'WOMEN', 'DONNA', 'FEMALE'];
        $liForKids = ['KIDS'];

        foreach ($matchedWords as $matchedWord) {
            if (self::isInCategory($matchedWord, $liForMen)) {
                return 3;  // Men's category
            } elseif (self::isInCategory($matchedWord, $liForWomen)) {
                return 2;  // Women's category
            } elseif (self::isInCategory($matchedWord, $liForKids)) {
                return 146;  // Kids' category
            }
        }

        return false;
    }

    private static function isInCategory($word, $categoryList)
    {
        foreach ($categoryList as $categoryWord) {
            if (strtolower($categoryWord) == strtolower($word)) {
                return true;
            }
        }

        return false;
    }

    private static function findCategoryByMain($matchedWords, $mainCategory)
    {
        $reversedWords = array_reverse($matchedWords, true);

        foreach ($reversedWords as $key => $value) {
            $category = Category::find($key);
            if ($category && self::isCategoryMatching($category, $mainCategory)) {
                return $category;
            }
        }

        return false;
    }

    private static function isCategoryMatching($category, $mainCategory)
    {
        if (! $mainCategory) {
            return false;
        }

        $levelOne = $category->parentM;
        if ($levelOne) {
            $levelTwo = $levelOne->parentM;
            if ($levelTwo) {
                return $levelTwo->id == $mainCategory || $levelTwo->parent_id == $mainCategory;
            }

            return $levelOne->id == $mainCategory || $levelOne->parent_id == $mainCategory;
        }

        return $category->id == $mainCategory || $category->parent_id == $mainCategory;
    }

    public function getSizeChart($websiteId = 0)
    {
        $link = 'https://erp.theluxuryunlimited.com/images/size-chart-images/';
        $charts = [
            5 => [
                5 => $link.'AC/ac-men-shoes-size-chart.jpg',
                9 => $link.'BL/bl-men-shoes-size-chart.jpg',
                17 => $link.'VL/vl-men-shoes-size-chart.jpg',
                1 => $link.'SOLO/solo-men-shoes-size-chart.jpg',
                3 => $link.'SN/sn-men-shoes-size-chart.jpg',
            ],
            41 => [
                5 => $link.'AC/ac-women-shoes-size-chart.jpg',
                9 => $link.'BL/bl-women-shoes-size-chart.jpg',
                17 => $link.'VL/vl-women-shoes-size-chart.jpg',
                1 => $link.'SOLO/solo-women-shoes-size-chart.jpg',
                3 => $link.'SN/sn-women-shoes-size-chart.jpg',
            ],
            40 => [
                5 => $link.'AC/ac-womenswear-size-chart.jpg',
                9 => $link.'BL/bl-womenswear-size-chart.jpg',
                17 => $link.'VL/vl-womenswear-size-chart.jpg',
                1 => $link.'SOLO/solo-womenswear-size-chart.jpg',
                3 => $link.'SN/sn-womenswear-size-chart.jpg',
            ],
            12 => [
                5 => $link.'AC/ac-menswear-size-chart.jpg',
                9 => $link.'BL/bl-menswear-size-chart.jpg',
                17 => $link.'VL/vl-menswear-size-chart.jpg',
                1 => $link.'SOLO/solo-menswear-size-chart.jpg',
                3 => $link.'SN/sn-menswear-size-chart.jpg',
            ],
            180 => [
                5 => $link.'AC/ac-kids-size-chart.jpg',
                9 => $link.'BL/bl-kids-size-chart.jpg',
                17 => $link.'VL/vl-kids-size-chart.jpg',
                1 => $link.'SOLO/solo-kids-size-chart.jpg',
                3 => $link.'SN/sn-kids-size-chart.jpg',
            ],
        ];

        return $charts[$this->id][$websiteId] ?? null;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category', 'id');
    }

    /**
     * Static Function for generate a keyword sting with category and its sub category
     * parent_id = 231 & 233 : 231 & 233 are ids of Root id called NEW and PREOWNED and we dont want t consider it in Sting
     * id = 1, 143, 144, 211, 241, 366, 372 <- these are some unwanted ids od category which we dont want to keep in generated string
     * ex: Select Category, Unknown Category, Ignore Category Reference, Ignore Category Reference,
     * Level in this query is taken for we wanted to go deep till 4 levels for category and sub category
     */
    public static function getCategoryHierarchyString(int $level = 4): array
    {
        $query = 'WITH RECURSIVE category_path AS(
                        SELECT id, title, title AS generated_string, 1 AS level
                        FROM categories
                        WHERE parent_id IN (231, 233) AND id NOT IN (1, 143, 144, 211, 241, 366, 372)
                        UNION ALL
                    SELECT c.id, c.title, CONCAT(cp.generated_string, " ", c.title), cp.level + 1
                    FROM categories c
                    JOIN category_path cp ON  c.parent_id = cp.id
                    WHERE cp.level < '.$level.')
                    
                    SELECT CONCAT(cp.generated_string, " ", ksv.keyword) AS combined_string
                    FROM category_path cp
                    CROSS JOIN keyword_search_variants ksv
                    WHERE NOT EXISTS (
                          SELECT 1 FROM categories c2
                          WHERE c2.parent_id = cp.id
                        )';

        return DB::select($query);
    }

    public static function updateStatusIsHashtagsGeneratedCategories($category_id_arr)
    {
        self::whereIn('id', $category_id_arr)->where('is_hashtag_generated', 0)->update(['is_hashtag_generated' => 1]);
    }
}
