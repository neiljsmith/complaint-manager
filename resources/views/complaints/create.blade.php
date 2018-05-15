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
            <h5 class="pb-2"><strong>Customer:</strong> {{ $customer->account_number }} - {{ $customer->first_name }} {{ $customer->last_name }} - {{ $customer->email }}</h5>
            <hr>

            <form action="/complaints/{{ $customer->id }}/store" method="post" data-validate="complaints.create">
                @csrf

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" rows="6" class="form-control" data-val-rule="required"></textarea>
                    <div class="{{ $errors->has('description') ? '' : 'd-none' }} text-danger">Description is required</div>
                </div>

                <div class="form-group form-inline">
                    <label for="reward_provider_id">Reward Type:</label> 
                    <select class="form-control" name="reward_provider_id">
                        @foreach ($rewardProviders as $rewardProvider)
                        <option value="{{ $rewardProvider->id }}">{{ $rewardProvider->name }}</option>
                        @endforeach
                    </select>

                    <label for="reward_value">Reward Value:</label> 
                    <select class="form-control" name="reward_value">
                        <option value="0">No reward</option>
                        @foreach ($rewardValues as $rewardValue)
                        <option value="{{ $rewardValue }}">&pound; {{ $rewardValue }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Save Complaint</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection