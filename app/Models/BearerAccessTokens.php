<?php

declare(strict_types=1);

namespace App\Models;
use App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BearerAccessTokens extends Model
{
    use HasFactory;

    const ID = 'id';

    const CREATED_AT = 'created_at';

    const TOKEN = 'token';

    const USER_ID = 'user_id';

    public function getId(): ?int
    {
        return (int) $this->getAttribute(self::ID);
    }

    public function getToken(): ?string
    {
        return (string) $this->getAttribute(self::TOKEN);
    }

    public function setToken(string $token): self
    {
        $this->setAttribute(self::TOKEN, $token);

        return $this;
    }

    public function setUser(User $user): self
    {
        $this->setAttribute(self::USER_ID, $user->id);

        return $this;
    }

    public function getUser(): ?User
    {
        return User::find($this->getUserId()) ?? null;
    }

    public function getUserId(): ?int
    {
        return (int) $this->getAttribute(self::USER_ID);
    }

    public function getByToken(string $token): ?self
    {
        return $this
            ->where(self::TOKEN, $token)
            ->whereDate('created_at', '>=', now()->subDay()->toDateString())
            ->first() ?? null;
    }
}
