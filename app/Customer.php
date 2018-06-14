<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * Finds a customer and their complaints with reward data from search criteria
     *
     * @param string $searchString
     * @return Customer[]
     */
    public static function findMatchingEmailOrAccountNo($searchString)
    {
        $resultLimit = 10;
        
        $customers = static::with(['complaints' => function($query) {
            $query->with('reward')
            ->orderBy('created_at', 'desc');
        }])->where('email', 'like', $searchString . '%')
            ->orWhere('account_number', 'like', $searchString . '%')
            ->limit($resultLimit)
            ->get();

        // Additional/modified complaint data for display
        foreach ($customers as $customer) {
            foreach ($customer->complaints as $complaint) {
                $complaint->created_at_diff = $complaint->created_at->diffForHumans();
                $complaint->description = substr($complaint->description, 0, 50) . '...';
            }
        }

        return $customers;
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
