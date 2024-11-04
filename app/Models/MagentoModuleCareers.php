<?php

declare(strict_types=1);

namespace App\Models;

use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MagentoModuleCareers extends Model
{
    use HasFactory;

    const ID = 'id';

    const LOCATION = 'location';

    const TYPE = 'type';

    const DESCRIPTION = 'description';

    const IS_ACTIVE = 'is_active';

    const STORE_WEBSITE_ID = 'store_website_id';

    const CREATED_AT = 'created_at';

    const CAREERS_STOREWEBSITES = 'careers_storewebsites';

    const TITLE = 'title';

    public function getId(): ?int
    {
        return (int) $this->getAttribute(self::ID);
    }

    public function getLocation(): ?string
    {
        return $this->getAttribute(self::LOCATION);
    }

    public function setLocation(string $location): self
    {
        $this->setAttribute(self::LOCATION, $location);

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->getAttribute(self::TITLE);
    }

    public function setTitle(string $title): self
    {
        $this->setAttribute(self::TITLE, $title);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute(self::DESCRIPTION);
    }

    public function setDescription(string $description): self
    {
        $this->setAttribute(self::DESCRIPTION, $description);

        return $this;
    }

    public function getType(): ?string
    {
        return $this->getAttribute(self::TYPE);
    }

    public function setType(string $type): self
    {
        $this->setAttribute(self::TYPE, $type);

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return (string) $this->getAttribute(self::CREATED_AT);
    }

    public function getIsActive(): ?bool
    {
        return (bool) $this->getAttribute(self::IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): self
    {
        $this->setAttribute(self::IS_ACTIVE, $isActive);

        return $this;
    }

    public function storeWebsites(): BelongsToMany
    {
        return $this->belongsToMany(
            StoreWebsite::class,
            self::CAREERS_STOREWEBSITES,
            'careers_id',
            'website_id',
            'id',
            'id'
        );
    }

    /**
     * @return StoreWebsite[]
     */
    public function getStoreWebsites(): array
    {
        return (array) $this->belongsToMany(
            StoreWebsite::class,
            self::CAREERS_STOREWEBSITES,
            'careers_id',
            'website_id',
            'id',
            'id'
        )->get()->getIterator();
    }

    /**
     * @return int[]
     */
    public function getStoreWebsitesIds(): array
    {
        return array_map(fn ($career) => (int) $career->id, $this->getStoreWebsites());
    }

    public function addStoreWebsites(mixed $id): void
    {
        if (is_array($id)) {
            $ids = $this->getStoreWebsitesIds();
            foreach ($id as $key => $value) {
                if (in_array($value, $ids)) {
                    unset($id[$key]);
                }
            }
        }
        if (! $id) {
            return;
        }
        $this->belongsToMany(StoreWebsite::class, self::CAREERS_STOREWEBSITES, 'careers_id', 'website_id')->attach($id);
    }

    public function removeOrganizations(mixed $id): void
    {
        $this->belongsToMany(StoreWebsite::class, self::CAREERS_STOREWEBSITES, 'careers_id', 'website_id')->detach($id);
    }

    public function removeAllOrgnization(): void
    {
        $this->belongsToMany(StoreWebsite::class, self::CAREERS_STOREWEBSITES, 'careers_id', 'website_id')->detach();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            self::ID               => $this->getId(),
            self::TITLE            => $this->getTitle(),
            self::TYPE             => $this->getType(),
            self::DESCRIPTION      => $this->getDescription(),
            self::LOCATION         => $this->getLocation(),
            self::IS_ACTIVE        => $this->getIsActive(),
            self::CREATED_AT       => $this->getCreatedAt(),
            self::STORE_WEBSITE_ID => $this->getStoreWebsitesIds(),
        ];
    }

    public function toArrayCareer(): array
    {
        return [
            self::TYPE        => $this->getType(),
            self::DESCRIPTION => $this->getDescription(),
            self::LOCATION    => $this->getLocation(),
            self::CREATED_AT  => $this->getCreatedAt(),
            self::TITLE       => $this->getTitle(),
        ];
    }
}
