<?php

namespace App\Helpers;
use App\SupplierPriority;
use App\Supplier;
use App\Helpers;

trait SupplierPriorityTrait
{
    private function updatePriority($id, $priority)
    {
        $getPriority            = SupplierPriority::where('id', $priority)->first();
        $getHigherPriority      = Supplier::where('priority', '>=', $getPriority->priority)->get();
        $selected_supplier      = Supplier::where('id', $id)->first();
        $updateSupplierPriority = 0;
        if ($selected_supplier) {
            $updateSupplierPriority = Supplier::where('id', $id)->update(['priority' => $priority]);
        }

        $getTotalPriorities = SupplierPriority::count();
        foreach ($getHigherPriority as $supplier) {
            if ($getTotalPriorities > $supplier->priority) {
                $supplier->priority += 1;
                $supplier->save();
            } else {
                $supplier->priority = null;
                $supplier->save();
            }
        }

        return $updateSupplierPriority;
    }
}
