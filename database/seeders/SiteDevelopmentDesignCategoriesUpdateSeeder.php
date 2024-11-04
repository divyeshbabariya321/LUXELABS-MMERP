<?php

namespace Database\Seeders;

use App\StoreWebsite;
use App\SiteDevelopment;
use Illuminate\Database\Seeder;
use App\SiteDevelopmentCategory;
use App\SiteDevelopmentMasterCategory;

class SiteDevelopmentDesignCategoriesUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $siteDevelopmentMasterCategory = SiteDevelopmentMasterCategory::select('id')->where('title', 'Design')->first();

        $lists = [
            'Language pop up',
            'Brand designer page',
            'Brand designer page - not found',
            'Brand detail page',
            'Brand Product Query-Modal',
            'Menu open - men , women , kids',
            'Buy back',
            'Sale',
            'Wishlist',
            'Product detail filter open page',
            'Product detail filter selected page',
            'Product detail page',
            'Product description page',
            'Product Sold Out',
            'Special Size Request',
            'Notify me when available',
            'thank you for your request',
            'thank you size request',
            'Add to cart',
            'best prise promise',
            'price match ticket',
            'Special request',
            'Size chart- women',
            'Size chart- men',
            'shopping cart',
            'Checkout login - summery open',
            'Checkout Edit',
            'Track order ID',
            'Thank you for purchase',
            'Login',
            'Login - error',
            'signup - error',
            'Account created - success',
            'Forgot password - email',
            'set password',
            'Search filed open',
            'open filter - search',
            'open filter -  no search',
            'Search found',
            'Influencer Registration',
            'Influencer Registration - error',
            'Influencer Registration - success',
            'App email',
            'App qr code',
            'Refer a Friend - Error',
            'Refer a Friend - Success',
            'track your ticket by email',
            'track your order',
            'Advance search',
            'FAQ',
            'sHIPPING',
            'return & refunds',
            'Donation',
            'Affiliate program',
            'Career',
            'Veralusso',
            'Change the world',
            'Shop safely',
            'Product information',
            'My order',
            'My Orders Order not found',
            'Wishlist',
            'My Wishlist share',
            'Address book - no found',
            'Address book',
            'Address book',
            'Edit Address book',
            'Change password',
            'Return List - Empty state',
            'My ticket - Empty state',
            'My Ticket',
            'Ticket chat- popup',
            'Coupon list',
            'Notification - Empty state',
            'Notification',
            'store credit - Empty state',
            'store credit',
            'My social account',
            'order confirmation mail',
            'ReFund Request Mail',
            'Password Reset Request Mail',
            'Store Credit Mail',
            'Ticket Email Mail',
            'Account Verification Link Mail',
            'Account Successfully Created Mail',
            'Order Cancel Mail',
            'Order on the way mail',
            'Order Return Confirm Mail',
            'Track your Ticket by Email',
            'Donation Email',
        ];

        $all_website = StoreWebsite::get();

        foreach ($lists as $list) {
            $develop = SiteDevelopmentCategory::firstOrCreate([
                'master_category_id' => $siteDevelopmentMasterCategory->id,
                'title'              => $list,
            ]);

            if ($develop) {
                foreach ($all_website as $key => $value) {
                    SiteDevelopment::firstOrCreate([
                        'site_development_category_id'        => $develop->id,
                        'site_development_master_category_id' => $develop->master_category_id,
                        'website_id'                          => $value->id,
                    ]);
                }
            }
        }
    }
}
