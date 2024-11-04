<?php

namespace App\Exports;
use App\Exports;

use App\Category;
use App\SystemSizeManager;
use App\SystemSizeRelation;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class SystemSizeExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Main Category',
            'Category',
            'Erp Size',
            'Sizes',
        ];
    }

    public function collection()
    {
        $systemSizesManagers = SystemSizeManager::select(
            'categories.parent_id as category_parent_id',
            'categories.title as category',
            'system_size_managers.erp_size',
            'system_size_managers.id'
        )
            ->leftjoin('categories', 'categories.id', 'system_size_managers.category_id')
            ->where('system_size_managers.status', 1)->get();

        $systemSizesManagers->map(function ($manager) {
            $related = SystemSizeRelation::select('system_size_relations.size', 'system_sizes.name')
                ->leftjoin('system_sizes', 'system_sizes.id', 'system_size_relations.system_size')
                ->where('system_size_manager_id', $manager->id)->get();
            $sizes = '';

            foreach ($related as $v) {
                $string = $v->name . ' => ' . $v->size;
                $sizes .= $sizes == '' ? $string : ', ' . $string;
            }
            $manager->category_parent_id = Category::where('id', $manager->category_parent_id)->value('title');
            $manager->sizes              = $sizes;
            unset($manager->id);

            return $manager;
        });

        return $systemSizesManagers;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
}
