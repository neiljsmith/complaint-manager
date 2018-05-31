<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * Finds a customer and their complaints with reward data from search criteria
     *
     * @param string $searchField
     * @param string $searchData
     * @return Customer
     */
    public static function findBySearchTerm($searchField, $searchData)
    {
        $customer = static::with(['complaints' => function($query) {
            $query->with('reward')->orderBy('created_at', 'desc');
        }])->where($searchField, $searchData)->first();

        if ($customer) {
            foreach ($customer->complaints as $complaint) {
                $complaint->created_at_diff = $complaint->created_at->diffForHumans();
                $complaint->description = substr($complaint->description, 0, 50) . '...';
            }

            return $customer;
        }    
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
