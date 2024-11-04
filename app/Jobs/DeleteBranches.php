<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\GithubTrait;
use Illuminate\Bus\Queueable;
use App\Github\GithubBranchState;
use App\Models\DeletedGithubBranchLog;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteBranches implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, GithubTrait;

    private $error;

    private $response;

    /**
     * Create a new job instance.
     *
     * @param private $branches
     * @param private $repositoryId
     *
     * @return void
     */
    public function __construct(private $branches, private $repositoryId)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        foreach ($this->branches as $branch) {
            $this->response = $this->deleteBranch($this->repositoryId, $branch);

            if ($this->response['status']) {
                Log::info('Branch ' . $branch . ' has been deleted successfully.');
                DeletedGithubBranchLog::create([
                    'branch_name'   => $branch,
                    'repository_id' => $this->repositoryId,
                    'deleted_by'    => Auth::id(),
                    'status'        => 'success',
                ]);
            }

            $githubBranchState = GithubBranchState::where('repository_id', $this->repositoryId)->where('branch_name', $branch)->first();
            if (! empty($githubBranchState) && $this->response['status']) {
                $githubBranchState->delete();
            } else {
                Log::info('ERROR : while delete the branch - ' . $branch);
                DeletedGithubBranchLog::create([
                    'branch_name'   => $branch,
                    'repository_id' => $this->repositoryId,
                    'deleted_by'    => Auth::id(),
                    'status'        => 'failed',
                    'error_message' => $this->response['error'],
                ]);
                $this->error .= $this->response['error'] . ',';
            }
        }

        dd($this->response);
    }
}
