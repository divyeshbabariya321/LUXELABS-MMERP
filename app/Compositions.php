<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Compositions extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="replace_with",type="string")
     */
    //
    protected $fillable = [
        'name',
        'replace_with',
    ];

    public static function getErpName($name)
    {
        $parts = preg_split('/\s+/', trim($name));

        $mc = self::query();
        if (! empty($parts)) {
            $parts = array_filter(array_map(function ($p) {
                return trim(str_replace('%', '', $p));
            }, $parts));

            if (! empty($parts)) {
                // Use whereRaw to build the query dynamically with LIKE conditions
                $likeClauses = implode(' OR ', array_fill(0, count($parts), 'name LIKE ?'));
                $bindings = array_map(function ($p) {
                    return "%{$p}%";
                }, $parts);
                $mc->whereRaw("($likeClauses)", $bindings);
            }
        }
        $mc = $mc->groupBy('name')->get(['name', 'replace_with']);

        $isReplacementFound = false;
        if (! $mc->isEmpty() && ! empty($name)) {
            // Create a replacement map for efficient lookup
            $replacementMap = [];
            foreach ($mc as $c) {
                if (! empty($c->replace_with)) {
                    $replacementMap[strtolower($c->name)] = $c->replace_with;
                }
            }

            // Direct match for full name
            $lowerName = strtolower($name);
            if (isset($replacementMap[$lowerName])) {
                return $replacementMap[$lowerName];
            }

            // Replace parts of the name based on the map
            foreach ($parts as $p) {
                $lowerPart = strtolower($p);
                if (isset($replacementMap[$lowerPart])) {
                    $name = str_replace($p, $replacementMap[$lowerPart], $name);
                    $isReplacementFound = true;
                }
            }
        }

        // check if replacement found then assing that to the composition otherwise add new one and start next process
        if ($isReplacementFound) {
            $checkExist = self::where('name', $name)->value('replace_with');
            if (! empty($checkExist)) {
                return $checkExist;
            }
        }

        // in this case color refenrece we don't found so we need to add that one
        if (! empty($name)) {
            $compositionModel = self::select('id')->where('name', $name)->first();
            if (! $compositionModel) {
                self::create([
                    'name' => $name,
                    'replace_with' => '',
                ]);
            }
        }

        // Return an empty string by default
        return '';
    }

    public static function products($name)
    {
        return ScrapedProducts::where('composition', 'LIKE', $name)->count();
    }

    public function productCounts(): HasMany
    {
        return $this->hasMany(ScrapedProducts::class, 'composition', 'name');
    }
}
