<?php

declare(strict_types=1);

namespace App\Models;

use App\Tickets;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;

class TicketsImages extends Model
{
    use HasFactory;

    const ID = 'id';

    const FILE_PATH = 'file_path';

    const FILE_NAME = 'file_name';

    const TICKET_ID = 'ticket_id';

    const CREATED_AT = 'created_at';

    public function getId(): ?int
    {
        return (int) $this->getAttribute(self::ID);
    }

    public function getFilePath(): ?string
    {
        return $this->getAttribute(self::FILE_PATH);
    }

    public function setFilePath(string $filePath): self
    {
        $this->setAttribute(self::FILE_PATH, $filePath);

        return $this;
    }

    public function setFileName(string $fileName): self
    {
        $this->setAttribute(self::FILE_NAME, $fileName);

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return (string) $this->getAttribute(self::CREATED_AT);
    }

    public function getTicketId(): ?int
    {
        return (int) $this->getAttribute(self::TICKET_ID);
    }

    public function setTicketId(int $ticketId): self
    {
        $this->setAttribute(self::TICKET_ID, $ticketId);

        return $this;
    }

    public function getTicket(): BelongsTo
    {
        return $this->belongsTo(Tickets::class);
    }

    public function setFile(UploadedFile $file, ?string $fileName = null): self
    {
        $fileName = $fileName ?? $file->hashName();
        $file->move($this->getAbsolutePath(), $fileName);
        $this->setFilePath($fileName);

        return $this;
    }

    public function getAbsolutePath(): ?string
    {
        return public_path('images/tickets');
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            self::TICKET_ID => $this->getTicketId(),
            self::FILE_PATH => '/images/tickets'.DIRECTORY_SEPARATOR.$this->getFilePath(),
            self::FILE_NAME => $this->getFileName(),
            self::CREATED_AT => $this->getCreatedAt(),
        ];
    }
}
