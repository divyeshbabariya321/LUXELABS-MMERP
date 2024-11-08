<?php

namespace App\Helpers;

class QueryHelper
{
    public static function approvedListingOrder($query)
    {
        // Only show products which have a stock bigger than zero
        $query = $query->where('stock', '>=', 1);

        $query = $query->orderByDesc('scrap_priority');

        // Prioritize suppliers
        $prioritizeSuppliers = "CASE WHEN brand IN (4,13,15,18,20,21,24,25,27,30,32,144,145) AND category IN (11,39,5,41,14,42,60,17,31,63) AND supplier IN ('G & B Negozionline', 'Tory Burch', 'Wise Boutique', 'Biffi Boutique (S.P.A.)', 'MARIA STORE', 'Lino Ricci Lei', 'Al Duca d\'Aosta', 'Tiziana Fausti', 'Leam') THEN 0 ELSE 1 END";
        $query               = $query->orderByRaw($prioritizeSuppliers);

        // Show on sale products first
        $query = $query->orderByDesc('is_on_sale');

        // Show latest approvals first
        $query = $query->orderByDesc('listing_approved_at');

        // Return query
        return $query;
    }

    public static function approvedListingOrderFinalApproval($query, $forStoreWebsite = false)
    {
        // Only show products which have a stock bigger than zero
        $query = $query->leftJoin('brands', function ($join) {
            $join->on('products.brand', '=', 'brands.id');
        });

        $query = $query->leftJoin('suppliers', function ($join) {
            $join->on('products.supplier_id', '=', 'suppliers.id');
        });

        // Only show products which have a stock bigger than zero
        if ($forStoreWebsite) {
            // check if the product is last instock before 30 day
            $query           = $query->join('scraped_products as sp1', 'sp1.sku', 'products.sku');
            $dateBefore30day = date('Y-m-d H:i:s', strtotime('-30 days'));
            $query           = $query->where(function ($q) use ($dateBefore30day) {
                $q->orWhere('stock', '>=', 1)->orWhere('sp1.last_inventory_at', $dateBefore30day);
            });
        } else {
            $query = $query->where('stock', '>=', 1);
        }

        $query = $query->orderByDesc('brands.priority')->orderByDesc('suppliers.priority')->latest('products.created_at');

        // Return query
        return $query;
    }
}
