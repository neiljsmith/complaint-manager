@extends('layouts.app') 
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>Complaint</h1>
            <hr>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h5 class="pb-2"><strong>Customer:</strong> {{ $complaint->customer->account_number}} - 
                {{ $complaint->customer->first_name }} {{ $complaint->customer->last_name }} - {{ $complaint->customer->email }}</h5>
            <p><strong>Logged by:</strong> {{ $complaint->user->first_name }} {{ $complaint->user->last_name }} on {{ $complaint->formattedDate }}</p>
            <hr>
            <h5><strong>Description:</strong></h5>
            <p>{{ $complaint->description }}</p>
            <h5><strong>Reward:</strong></h5>

            @if ($complaint->reward)
            <p>&pound;{{ $complaint->reward->value }} {{ $complaint->reward->rewardProvider->name }} voucher</p>
            @else
            <p>N/A</p>
            @endif

            <h5><strong>Notes</strong></h5>

            @if (count($complaint->complaintNotes))
            @foreach ($complaint->complaintNotes as $complaintNote)
            <hr>
            <p><strong>Logged by:</strong> {{ $complaintNote->user->first_name }} {{ $complaintNote->user->last_name }} on {{ $complaintNote->formattedDate }}</p>
            <p>{{ $complaintNote->content }}</p>
            @endforeach
            <hr>
            @endif

            <form action="/complaints/{{ $complaint->id }}/add-note" method="post" data-validate="complaintNotes.add">
                @csrf
                <div class="form-group">
                    <textarea name="content" id="content" rows="6" class="form-control" data-val-rule="required"></textarea>
                    <div class="{{ $errors->has('content') ? '' : 'd-none' }} text-danger">Note content is required</div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Add Note</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection