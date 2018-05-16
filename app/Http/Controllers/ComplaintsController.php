<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Complaint;
use App\ComplaintNote;
use Illuminate\Http\Request;
use App\RewardProvider;
use App\Reward;

class ComplaintsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $complaints = Complaint::paginated();

        return view('complaints.index', compact('complaints'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Customer $customer)
    {
        // Required for creating a reward
        $rewardValues = config('app.reward_values');
        $rewardProviders = RewardProvider::all();

        return view('complaints.create', compact('customer', 'rewardValues', 'rewardProviders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'description' => 'required|min:2',
            'reward_provider_id' => 'required',
            'reward_value' => 'required',
        ]);

        $complaint = Complaint::create([
            'user_id' => \Auth::id(),
            'customer_id' => $customer->id,
            'description' => $request->description,
        ]);

        $reward = false;
        if ((int) $request->reward_value > 0) {
            $reward = Reward::where('reward_provider_id', (int) $request->reward_provider_id)
                ->where('value', (int) $request->reward_value)
                ->first();
            $reward->complaint_id = $complaint->id;
            $reward->save();
        }

        return redirect('/')->with('status', 'Complaint added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $complaintId
     * @return \Illuminate\Http\Response
     */
    public function show($complaintId)
    {
        $complaint = Complaint::findWithDetails($complaintId);

        return view('complaints.show', compact('complaint'));
    }

    /**
     * Returns data to AJAX call from user search form,
     * looking for either user's 'account_number' or 'email'
     *
     * @param string $searchData
     * @return void
     */
    public function findByCustomerAccOrEmail($searchData)
    {
        if (is_numeric($searchData)) {
            $searchField = 'account_number';
        } elseif (filter_var($search, FILTER_VALIDATE_EMAIL)) {
            $searchField = 'email';
        }

        if (isset($searchField)) {
            return Complaint::findByCustomerSearch($searchField, $searchData);
        }
    }   
    
    /**
     * Responds to AJAX call from complaint detail page
     * to add a note to the complaint.
     *
     * @param Request $request
     * @param Complaint $complaint
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addNote(Request $request, Complaint $complaint)
    {
        $request->validate(['content' => 'required|min:2']);
        $complaintNote = new ComplaintNote([
            'content' => $request->content,
            'user_id' => \Auth::id(),
        ]);
        $complaint->complaintNotes()->save($complaintNote);

        return back()->with('status', 'Comment added!');
    }
}
