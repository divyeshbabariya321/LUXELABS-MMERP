<?php

namespace Database\Seeders;

use App\GitMigrationErrorLog;
use Illuminate\Database\Seeder;
use App\Github\GithubRepository;
use App\Github\GithubBranchState;
use App\Github\GithubOrganization;
use App\Github\GithubRepositoryUser;
use App\Github\GithubRepositoryGroup;

class GithubOrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizationObj = [
            'name'     => 'MMMagento',
            'username' => 'MioModaMagento',
            'token'    => env('GITHUB_TOKEN'), // Use environment variable
        ];

        $organization = GithubOrganization::updateOrCreate(
            [
                'name' => 'MMMagento',
            ],
            $organizationObj
        );

        $organizationCount = GithubOrganization::count();

        if ($organizationCount == 1) {
            $isUpdated = GithubRepository::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);

            $isStateUpdated = GithubBranchState::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);

            $isLogUpdated = GitMigrationErrorLog::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);

            $isGroupUpdated = GithubRepositoryGroup::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);

            $isUserUpdated = GithubRepositoryUser::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);
        }
    }
}
