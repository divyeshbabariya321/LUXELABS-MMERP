<?php

namespace App\Console\Commands;

use App\Email;
use App\Models\EmailBox;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateMailBoxes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:create-mail-boxes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is using for create mail boxes from emails table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->updateEmailCategoryIds();
        $this->processEmailBoxAssignment();

        return 0;
    }

    protected function updateEmailCategoryIds(): void
    {
        $userEmails = $this->getGroupedEmails();

        if ($userEmails->isEmpty()) {
            return;
        }

        foreach ($userEmails as $email) {
            $this->updateEmailsWithCategory($email);
        }
    }

    protected function getGroupedEmails()
    {
        return Email::select('id', 'from', 'email_category_id')
            ->where('email_category_id', '>', 0)
            ->orderByDesc('created_at')
            ->groupBy('from')
            ->get();
    }

    protected function updateEmailsWithCategory($email): void
    {
        $emailIds = $this->getEmailIdsWithDifferentId($email);
        if ($emailIds->isNotEmpty()) {
            Email::whereIn('id', $emailIds)
                ->update(['email_category_id' => $email->email_category_id]);
        }
    }

    protected function getEmailIdsWithDifferentId($email)
    {
        return Email::where('from', $email->from)
            ->where('id', '!=', $email->id)
            ->pluck('id');
    }

    protected function processEmailBoxAssignment(): void
    {
        $emails = $this->getEmailsOlderThan72Hours();

        foreach ($emails as $email) {
            $this->assignEmailToBox($email);
        }
    }

    protected function getEmailsOlderThan72Hours()
    {
        return Email::select('from')
            ->where('created_at', '<=', Carbon::now()->subHours(72)->format('Y-m-d H:i:s'))
            ->orderBy('created_at')
            ->whereNull('email_box_id')
            ->get();
    }

    protected function assignEmailToBox($email): void
    {
        $fromEmails = $this->extractFromEmails($email->from);
        foreach ($fromEmails as $fromEmail) {
            $this->assignToBox($email, $fromEmail);
        }
    }

    protected function extractFromEmails($fromField): array
    {
        if (empty($fromField)) {
            return [];
        }

        $emailArr = str_split($fromField, 1);

        if ($emailArr[0] === '[') {
            return json_decode($fromField, true);
        }

        return [$fromField];
    }

    protected function assignToBox($email, $fromEmail): void
    {
        $emailArr = explode('@', $fromEmail);

        if (isset($emailArr[1])) {
            $emailBox = EmailBox::updateOrCreate(['box_name' => $emailArr[1]]);
            $email->email_box_id = $emailBox->id;
            $email->save();
        }
    }
}
