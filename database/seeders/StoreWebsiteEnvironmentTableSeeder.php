<?php

namespace Database\Seeders;

use App\StoreWebsite;
use Illuminate\Database\Seeder;
use App\StoreWebsiteEnvironment;

class StoreWebsiteEnvironmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleData    = self::sampleData();
        $storeWebsites = StoreWebsite::limit(2)->get(); // For testing, I am getting only 2 records.

        if (! empty($storeWebsites) && count($storeWebsites) > 0) {
            foreach ($storeWebsites as $storeWebsite) {
                StoreWebsiteEnvironment::firstOrCreate(
                    ['store_website_id' => $storeWebsite->id],
                    ['env_data' => $sampleData]
                );
            }
        }
    }

    public static function sampleData()
    {
        return [
            'backend' => [
                'frontName' => 'brands-wadm-2-1',
            ],
            'install' => [
                'date' => 'Thu, 24 Sep 2020 16:56:17 +0000',
            ],
            'crypt' => [
                'key' => 'XYLm7obbymFUpDbs6ocUTLMcgWL9wcvT',
            ],
            'session' => [
                'save'  => 'redis',
                'redis' => [
                    'host'                     => '127.0.0.1',
                    'port'                     => '6379',
                    'password'                 => '',
                    'timeout'                  => '2.5',
                    'persistent_identifier'    => '',
                    'database'                 => '2',
                    'compression_threshold'    => '2048',
                    'compression_library'      => 'gzip',
                    'log_level'                => '4',
                    'max_concurrency'          => '6',
                    'break_after_frontend'     => '5',
                    'break_after_adminhtml'    => '30',
                    'first_lifetime'           => '600',
                    'bot_first_lifetime'       => '60',
                    'bot_lifetime'             => '7200',
                    'disable_locking'          => '0',
                    'min_lifetime'             => '60',
                    'max_lifetime'             => '2592000',
                    'sentinel_master'          => '',
                    'sentinel_servers'         => '',
                    'sentinel_connect_retries' => '5',
                    'sentinel_verify_master'   => '0',
                ],
            ],
            'cache' => [
                'frontend' => [
                    'default' => [
                        'id_prefix'       => 'e1e_',
                        'backend'         => 'Cm_Cache_Backend_Redis',
                        'backend_options' => [
                            'server'          => '127.0.0.1',
                            'database'        => '0',
                            'port'            => '6379',
                            'password'        => '',
                            'compress_data'   => '1',
                            'compression_lib' => '',
                        ],
                    ],
                    'page_cache' => [
                        'id_prefix' => 'e1e_',
                    ],
                ],
            ],
            'db' => [
                'table_prefix' => '',
                'connection'   => [
                    'default' => [
                        'host'           => 'localhost',
                        'dbname'         => 'brands_livedb',
                        'username'       => 'brands_admin',
                        'password'       => 'Jvkoaw*5m9a0zzmw',
                        'model'          => 'mysql4',
                        'engine'         => 'innodb',
                        'initStatements' => 'SET NAMES utf8;',
                        'active'         => '1',
                    ],
                ],
            ],
            'resource' => [
                'default_setup' => [
                    'connection' => 'default',
                ],
            ],
            'x-frame-options' => 'SAMEORIGIN',
            'MAGE_MODE'       => 'production',
            'cache_types'     => [
                'config'                 => 1,
                'layout'                 => 1,
                'block_html'             => 1,
                'collections'            => 1,
                'reflection'             => 1,
                'db_ddl'                 => 1,
                'eav'                    => 1,
                'config_integration'     => 1,
                'config_integration_api' => 1,
                'full_page'              => 1,
                'translate'              => 1,
                'config_webservice'      => 1,
                'compiled_config'        => 1,
                'customer_notification'  => 1,
                'google_product'         => 1,
                'vertex'                 => 1,
                'amasty_shopby'          => 1,
            ],
            'system' => [
                'default' => [
                    'admin' => [
                        'url' => [
                            'custom' => null,
                        ],
                    ],
                    'dev' => [
                        'js' => [
                            'session_storage_key'         => 'collected_errors',
                            'enable_magepack_js_bundling' => '1',
                        ],
                        'restrict' => [
                            'allow_ips' => '103.79.169.130',
                        ],
                        'static' => [
                            'sign' => '1',
                        ],
                    ],
                    'system' => [
                        'smtp' => [
                            'host' => 'localhost',
                            'port' => '25',
                        ],
                        'full_page_cache' => [
                            'varnish' => [
                                'access_list'  => 'localhost',
                                'backend_host' => 'localhost',
                                'backend_port' => '8080',
                            ],
                        ],
                        'gmailsmtpapp' => [
                            'active'       => '0',
                            'ssl'          => 'ssl',
                            'auth'         => 'LOGIN',
                            'smtphost'     => 'smtp.gmail.com',
                            'smtpport'     => '465',
                            'set_reply_to' => '1',
                            'set_from'     => '0',
                            'password'     => '0:3:tcdd1hR2q0rMtnMM4d0s+b8mb7cBnPjZdr/H8pIU1ksVACYdX0Lgk6oqrONRjQCY',
                            'username'     => 'brandsandlabelsdubai@gmail.com',
                        ],
                    ],
                    'web' => [
                        'unsecure' => [
                            'base_url'        => 'https://www.brands-labels.com/',
                            'base_link_url'   => '{{unsecure_base_url}}',
                            'base_static_url' => 'https://www.brands-labels.com/static-2-1/',
                            'base_media_url'  => 'https://www.brands-labels.com/media-2-1/',
                        ],
                        'secure' => [
                            'base_url'        => 'https://www.brands-labels.com/',
                            'base_link_url'   => '{{secure_base_url}}',
                            'base_static_url' => 'https://www.brands-labels.com/static-2-1/',
                            'base_media_url'  => 'https://www.brands-labels.com/media-2-1/',
                        ],
                        'default' => [
                            'front' => 'cms',
                        ],
                        'cookie' => [
                            'cookie_path'   => null,
                            'cookie_domain' => null,
                        ],
                    ],
                    'currency' => [
                        'import' => [
                            'error_email' => null,
                        ],
                    ],
                    'customer' => [
                        'create_account' => [
                            'email_domain' => 'example.com',
                        ],
                    ],
                    'catalog' => [
                        'search' => [
                            'elasticsearch_server_hostname'  => 'localhost',
                            'elasticsearch_server_port'      => '9200',
                            'elasticsearch_index_prefix'     => 'magento2',
                            'elasticsearch_enable_auth'      => '0',
                            'elasticsearch_server_timeout'   => '15',
                            'elasticsearch5_server_hostname' => 'localhost',
                            'elasticsearch5_server_port'     => '9200',
                            'elasticsearch5_index_prefix'    => 'magento2',
                            'elasticsearch5_enable_auth'     => '0',
                            'elasticsearch5_server_timeout'  => '15',
                        ],
                        'productalert_cron' => [
                            'error_email' => null,
                        ],
                        'product_video' => [
                            'youtube_api_key' => null,
                        ],
                    ],
                    'cataloginventory' => [
                        'source_selection_distance_based_google' => [
                            'api_key' => null,
                        ],
                        'item_options' => [
                            'manage_stock' => '0',
                        ],
                    ],
                    'payment' => [
                        'checkmo' => [
                            'mailing_address' => null,
                        ],
                        'authorizenet_directpost' => [
                            'debug'          => '0',
                            'email_customer' => '0',
                            'login'          => null,
                            'merchant_email' => null,
                            'test'           => '1',
                            'trans_key'      => null,
                            'trans_md5'      => null,
                            'cgi_url'        => 'https://secure.authorize.net/gateway/transact.dll',
                            'cgi_url_td'     => 'https://api2.authorize.net/xml/v1/request.api',
                        ],
                        'paypal_express' => [
                            'debug' => '0',
                        ],
                        'paypal_express_bml' => [
                            'publisher_id' => null,
                        ],
                        'payflow_express' => [
                            'debug' => '0',
                        ],
                        'payflowpro' => [
                            'user'         => null,
                            'pwd'          => null,
                            'partner'      => null,
                            'sandbox_flag' => '0',
                            'debug'        => '0',
                        ],
                        'paypal_billing_agreement' => [
                            'debug' => '0',
                        ],
                        'payflow_link' => [
                            'pwd'          => null,
                            'url_method'   => 'GET',
                            'sandbox_flag' => '0',
                            'use_proxy'    => '0',
                            'debug'        => '0',
                        ],
                        'payflow_advanced' => [
                            'user'         => 'PayPal',
                            'pwd'          => null,
                            'url_method'   => 'GET',
                            'sandbox_flag' => '0',
                            'debug'        => '0',
                        ],
                        'authorizenet_acceptjs' => [
                            'email_customer' => '0',
                            'login'          => null,
                            'trans_key'      => null,
                            'trans_md5'      => null,
                        ],
                        'braintree' => [
                            'private_key'         => null,
                            'merchant_id'         => null,
                            'merchant_account_id' => null,
                            'descriptor_phone'    => null,
                            'descriptor_url'      => null,
                        ],
                        'braintree_paypal' => [
                            'merchant_name_override' => null,
                        ],
                        'amazon_payment' => [
                            'merchant_id'      => null,
                            'access_key'       => null,
                            'secret_key'       => null,
                            'client_id'        => null,
                            'client_secret'    => null,
                            'credentials_json' => null,
                            'sandbox'          => '0',
                        ],
                        'amazon_payments' => [
                            'simplepath' => [
                                'publickey' => '-----BEGIN PUBLIC KEY-----
        MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvd6CJRH7GFrW0XsS0ymg
        iN2//IYKo4KI/on/hiPbSkHFQ9MbbPhqDbeY5ocd+nXZl/ne4mbsgozpYDwOn3ID
        vz4s5GpnybjYmkKBq7PIEIwBqQ8EnaTjeAKY2Y33lmXZ73STvu1CaoSLmlgk32sh
        4HHgRP502BX2mjC74Vwpa0S4O7L66hTeWyqIb/3uKlE8D885Dapp9io5vERRK9qE
        Hm3E7WI2m7f7NeK8YJGaYkCLF0XNbL34N+YCal4v1slYGHvRz3DtVtvEP7w7F1yQ
        0c8IgxCikejdjGYCTR59GR32DPEkOw4QuSlAQ516/T+xqtCyEj9Dlj5ztO5yKJWM
        8wIDAQAB
        -----END PUBLIC KEY-----',
                                'privatekey' => '0:3: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',
                            ],
                        ],
                    ],
                    'checkout' => [
                        'payment_failed' => [
                            'copy_to' => null,
                        ],
                    ],
                    'contact' => [
                        'email' => [
                            'recipient_email' => 'hello@example.com',
                        ],
                    ],
                    'carriers' => [
                        'dhl' => [
                            'account'     => null,
                            'gateway_url' => 'https://xmlpi-ea.dhl.com/XMLShippingServlet',
                            'id'          => null,
                            'password'    => null,
                            'debug'       => '0',
                        ],
                        'fedex' => [
                            'account'                    => null,
                            'meter_number'               => null,
                            'key'                        => null,
                            'password'                   => null,
                            'sandbox_mode'               => '0',
                            'production_webservices_url' => 'https://ws.fedex.com:443/web-services/',
                            'sandbox_webservices_url'    => 'https://wsbeta.fedex.com:443/web-services/',
                            'smartpost_hubid'            => null,
                        ],
                        'ups' => [
                            'access_license_number' => null,
                            'gateway_url'           => 'https://www.ups.com/using/services/rave/qcostcgi.cgi',
                            'gateway_xml_url'       => 'https://onlinetools.ups.com/ups.app/xml/Rate',
                            'tracking_xml_url'      => 'https://onlinetools.ups.com/ups.app/xml/Track',
                            'username'              => null,
                            'password'              => null,
                            'is_account_live'       => '0',
                            'shipper_number'        => null,
                            'debug'                 => '0',
                        ],
                        'usps' => [
                            'gateway_url'        => 'http://production.shippingapis.com/ShippingAPI.dll',
                            'gateway_secure_url' => 'https://secure.shippingapis.com/ShippingAPI.dll',
                            'userid'             => null,
                            'password'           => null,
                        ],
                    ],
                    'trans_email' => [
                        'ident_custom1' => [
                            'email' => 'care@brands-labels.com',
                            'name'  => 'Info',
                        ],
                        'ident_custom2' => [
                            'email' => 'care@brands-labels.com',
                            'name'  => 'No-Reply',
                        ],
                        'ident_general' => [
                            'email' => 'care@brands-labels.com',
                            'name'  => 'Admin',
                        ],
                        'ident_sales' => [
                            'email' => 'care@brands-labels.com',
                            'name'  => 'Sales',
                        ],
                        'ident_support' => [
                            'email' => 'care@brands-labels.com',
                            'name'  => 'Care',
                        ],
                    ],
                    'analytics' => [
                        'url' => [
                            'signup'              => 'https://advancedreporting.rjmetrics.com/signup',
                            'update'              => 'https://advancedreporting.rjmetrics.com/update',
                            'bi_essentials'       => 'https://dashboard.rjmetrics.com/v2/magento/signup',
                            'otp'                 => 'https://advancedreporting.rjmetrics.com/otp',
                            'report'              => 'https://advancedreporting.rjmetrics.com/report',
                            'notify_data_changed' => 'https://advancedreporting.rjmetrics.com/report',
                        ],
                        'general' => [
                            'token' => null,
                        ],
                    ],
                    'newrelicreporting' => [
                        'general' => [
                            'api_url'             => 'https://api.newrelic.com/deployments.xml',
                            'insights_api_url'    => 'https://insights-collector.newrelic.com/v1/accounts/%s/events',
                            'account_id'          => '3537655',
                            'api'                 => '0:3:XY5X8cTFGdvdP86nC3+Nqec1xwI+dwwfhpg/oJtd5nFubo3BYH7pCszo4tQ9AHdf9KtOx+tyHgBmC1NA',
                            'app_id'              => '1489738014',
                            'insights_insert_key' => '0:3:I811ksE6+DDebvc/E4NAMXwSUZLxdg6gzOUA5D9QXsp2hjzqq+nV/6ba6ETkanf7tRr4RJ5WA7VRNv3ou+zf7Yc=',
                        ],
                    ],
                    'paypal' => [
                        'wpp' => [
                            'api_password'  => null,
                            'api_signature' => null,
                            'api_username'  => null,
                            'sandbox_flag'  => '0',
                        ],
                        'fetch_reports' => [
                            'ftp_login'    => null,
                            'ftp_password' => null,
                            'ftp_sandbox'  => '0',
                            'ftp_ip'       => null,
                            'ftp_path'     => null,
                        ],
                        'general' => [
                            'merchant_country' => null,
                            'business_account' => null,
                        ],
                    ],
                    'fraud_protection' => [
                        'signifyd' => [
                            'api_url' => 'https://api.signifyd.com/v2/',
                            'api_key' => null,
                        ],
                    ],
                    'sitemap' => [
                        'generate' => [
                            'error_email' => null,
                        ],
                    ],
                    'crontab' => [
                        'default' => [
                            'jobs' => [
                                'analytics_collect_data' => [
                                    'schedule' => [
                                        'cron_expr' => '00 02 * * *',
                                    ],
                                ],
                                'analytics_subscribe' => [
                                    'schedule' => [
                                        'cron_expr' => '0 * * * *',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'shellpea_erp' => [
                        'general' => [
                            'email'    => 'admin@sololuxury.com',
                            'password' => 'yRTHjnK9UaSI',
                            'enabled'  => '1',
                        ],
                    ],
                ],
            ],
            'lock' => [
                'provider' => 'db',
                'config'   => [
                    'prefix' => '',
                ],
            ],
        ];
    }
}
