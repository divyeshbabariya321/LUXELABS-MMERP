<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Migration;

class MigrationSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultMigrations       = ['2016_06_01_000001_create_oauth_auth_codes_table', '2016_06_01_000002_create_oauth_access_tokens_table', '2016_06_01_000003_create_oauth_refresh_tokens_table', '2016_06_01_000004_create_oauth_clients_table', '2016_06_01_000005_create_oauth_personal_access_clients_table', '2016_06_27_000000_create_mediable_tables', '2017_08_05_194349_create_tasks_table', '2017_08_26_083622_alter_tasks_table_add_notifications_fields', '2018_01_02_121533_alter_tasks_table_add_auto_cleanup_num_and_type_fields', '2018_07_03_120000_alter_tasks_table_add_run_on_one_server_support', '2018_07_06_165603_add_indexes_for_tasks', '2018_08_08_100000_create_telescope_entries_table', '2019_08_02_045350_create_log_google_vision', '2019_08_03_225655_create_log_google_vision_reference', '2019_08_09_100041_update_log_google_vision_reference', '2019_09_25_103421_update_task_results_duration_type', '2019_10_03_154557_create_excel_importers_table', '2019_10_03_154609_create_excel_importer_details_table', '2019_10_05_024053_update_excel_importer_table', '2019_10_08_013641_update_excel_importer_tables', '2019_11_21_114331_create_customer_next_action_table', '2019_11_21_124341_alter_customers_table_add_customer_next_action_id', '2019_12_06_112838_update_log_excel_imports_add_column_status', '2019_12_07_123100_update_log_excel_imports_table_add_column_website', '2020_09_15_172222_add_deleted_at_column_in_return_exchanges_table', '2020_09_15_172542_add_deleted_at_column_in_erp_leads_table', '2020_10_12_000000_add_variants_to_media', '2020_12_10_120000_alter_tasks_table_add_run_in_background_support', '2021_02_01_128100_update_log_excel_imports_table_add_column_md5', '2021_02_09_180955_update_log_excel_imports_table_add_columns_message'];
        $alreadyExistsMigrations = Migration::whereIn(column: 'migration', $defaultMigrations)
                    ->pluck('migration')
                    ->toArray();

        $remainingMigrations = array_diff($defaultMigrations, $alreadyExistsMigrations);

        foreach ($remainingMigrations as $migration) {
            Migration::insert([
                'migration' => $migration,
                'batch'     => 1,
            ]);
        }
    }
}
