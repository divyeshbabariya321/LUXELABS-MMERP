<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="AutoRefreshPage"))
 */
class MagentoModuleRemark extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="magento_module_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="send_to",type="string")
     * @SWG\Property(property="remark",type="string")
     */
    protected $fillable = ['magento_module_id', 'user_id', 'send_to', 'remark', 'type', 'frontend_issues', 'backend_issues', 'security_issues', 'performance_issues', 'api_issues', 'best_practices', 'conclusion', 'other'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function magento_module(): BelongsTo
    {
        return $this->belongsTo(MagentoModule::class);
    }
}
