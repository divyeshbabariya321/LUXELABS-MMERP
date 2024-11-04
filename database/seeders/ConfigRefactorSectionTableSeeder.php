<?php

namespace Database\Seeders;

use App\ConfigRefactor;
use App\ConfigRefactorSection;
use Illuminate\Database\Seeder;

class ConfigRefactorSectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleDataNDs = self::sampleDataND();

        foreach ($sampleDataNDs as $sampleDataND) {
            ConfigRefactorSection::firstOrCreate([
                'name' => $sampleDataND,
                'type' => 'ND',
            ]);
        }

        $sampleDataDEs = self::sampleDataDE();

        foreach ($sampleDataDEs as $sampleDataDE) {
            ConfigRefactorSection::firstOrCreate([
                'name' => $sampleDataDE,
                'type' => 'DE',
            ]);
        }

        $configRefactorSections = ConfigRefactorSection::all();
        foreach ($configRefactorSections as $configRefactorSection) {
            ConfigRefactor::firstOrCreate([
                'config_refactor_section_id' => $configRefactorSection->id,
            ]);
        }
    }

    public static function sampleDataND()
    {
        return [
            'amasty_base',
            'amgeoip',
            'amasty_cross_links',
            'amasty_checkout',
            'ammeta',
            'amseohtmlsitemap',
            'amseotoolkit',
            'amasty_seourl',
            'amseorichdata',
            'amsorting',
            'amxmlsitemap',
            'region',
            'multicurrency',
            'client',
            'widget',
            'bss_geoip',
            'bss_geoip_currency',
            'bss_sociallogin',
            'bss_store_flag',
            'settings',
            'connector_api_credentials',
            'sync_settings',
            'abandoned_carts',
            'connector_automation',
            'connector_dynamic_content',
            'transactional_emails',
            'connector_configuration',
            'connector_data_mapping',
            'connector_developer_settings',
            'abandonedcartapi',
            'cmspagemanager',
            'homepagemanager',
            'mytickets',
            'notifyme',
            'buynow',
            'orderreturn',
            'referfriend',
            'firas_donation_product',
            'webp',
            'admin_activity',
            'klarna',
            'msp_securitysuite_twofactorauth',
            'mage360_brands',
            'mfblog',
            'mfgeoip',
            'mageplaza',
            'layered_navigation',
            'mpcronschedule',
            'mpsearch',
            'magepow_ajaxwishlist',
            'magepow_ajaxcart',
            'magepow_core',
            'giftcard',
            'mtoptimization',
            'plumbase',
            'pramp',
            'sidebar',
            'sw_dailydeal',
            'sw_megamenu',
            'porto_settings',
            'sw_socialfeeds',
            'strategery_infinitescroll',
            'swarming_credits',
            'weltpixel_quickview',
            'yotpo',
            'porto_design',
            'porto_license',
            'sociallogin',
            'iwd_opc',
            'currencyswitcher',
            'pwa_connector',
            'progressivewebapp',
            'smartsupp',
            'elsner_geoip',
            'sendinblue',
            'geoip',
            'alsoviewed',
            'amshopby_brand',
            'amshopby_root',
            'amshopby',
            'free',
            'smtp',
            'magemojo',
            'sebwite_sidebar',
            'phpro_cookie_consent',
            'drefer',
            'sizeapi',
            'internationaltelephoneinput',
            'ordertracking_reason',
            'orderreturn_reason',
            'productdeliverydate',
            'amasty_shopby_seo',
            'amoptimizer',
            'custom',
            'amlazyload',
            'email_marketing',
            'smile_elasticsuite_ajax_settings',
            'hyva_react_checkout',
        ];
    }

    public static function sampleDataDE()
    {
        return [
            'admin',
            'design',
            'dev',
            'system',
            'web',
            'general',
            'theme',
            'currency',
            'customer',
            'cms',
            'catalog',
            'indexer',
            'export',
            'cataloginventory',
            'sales',
            'payment',
            'sales_email',
            'sales_pdf',
            'dashboard',
            'checkout',
            'captcha',
            'contact',
            'oauth',
            'carriers',
            'trans_email',
            'import',
            'three_d_secure',
            'google',
            'sales_channels',
            'analytics',
            'shipping',
            'multishipping',
            'newrelicreporting',
            'newsletter',
            'promo',
            'paypal',
            'persistent',
            'reports',
            'url_rewrite',
            'sendfriend',
            'fraud_protection',
            'sitemap',
            'crontab',
            'tax',
            'msp_securitysuite_recaptcha',
            'webapi',
            'wishlist',
        ];
    }
}
