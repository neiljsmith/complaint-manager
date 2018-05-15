<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'customer_id', 'description'
    ];

    /**
     * Gets a complaint including all associated resources
     *
     * @param int $complaintId
     * @return Complaint
     */
    public static function findWithDetails($complaintId) {
        $complaint = static::with('customer')
            ->with('user')
            ->with(['complaintNotes' => function($query) {
                $query->with('user')->orderBy('created_at', 'desc');
            }])
            ->with(['reward' => function($query) {
                $query->with('rewardProvider');
            }])
            ->find($complaintId);

        foreach ($complaint->complaintNotes as $complaintNote) {
            $complaintNote->formattedDate = $complaintNote->created_at->toRfc7231String();
        }

        $complaint->formattedDate = $complaint->created_at->toRfc7231String();

        return $complaint;
    }

    /**
     * Returns paginated complaints result with customer
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function paginated()
    {
        $itemsPerPage = 10;

        return static::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate($itemsPerPage);
    }

    /**
     * Finds a customer and their complaints with reward data from search criteria
     *
     * @param string $searchField
     * @param string $searchData
     * @return Customer
     */
    public static function findByCustomerSearch($searchField, $searchData)
    {
        $data = Customer::with(['complaints' => function($query) {
            $query->with('reward')->orderBy('created_at', 'desc');
        }])->where($searchField, $searchData)->first();

        if ($data) {
            foreach ($data->complaints as $complaint) {
                $complaint->created_at_diff = $complaint->created_at->diffForHumans();
                $complaint->description = substr($complaint->description, 0, 50) . '...';
            }

            return $data;
        }    
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complaintNotes()
    {
        return $this->hasMany(ComplaintNote::class);
    }

    public function reward()
    {
        return $this->hasOne(Reward::class);
    }
}
