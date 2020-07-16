@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h1>Complaints</h1>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <input type="text" class="form-control dropdown" data-customer-search data-toggle="dropdown" placeholder="Search customer account no. or email">
                <div class="dropdown-menu d-none" data-customer-search-suggest></div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">

        <div class="col-md-10 d-none" data-customer-found>

            <div data-customer-detail></div>

            <table class="table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Reward Issued</th>
                        <th scope="col">Description</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>


        <div class="col-md-10" data-complaints-table>
            <table class="table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Acct Number</th>
                        <th scope="col">Email</th>
                        <th scope="col">Name</th>
                        <th scope="col">Date Created</th>
                        <th scope="col">Reward Issued</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($complaints as $complaint)
                    <tr>
                        <td>{{ $complaint->customer->account_number }}</td>
                        <td>{{ $complaint->customer->email }}</td>
                        <td>{{ $complaint->customer->first_name . ' ' . $complaint->customer->last_name }}</td>
                        <td>{{ $complaint->created_at }}</td>
                        <td class="text-center"><i class="fas fa-{{ $complaint->reward ? 'check' : 'times' }}"></i></td>
                        <td>
                            <a href="{{ route('complaint-show', ['complaint' => $complaint->id]) }}" class="btn btn-primary btn-sm">Show</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="ml-auto">
                {{ $complaints->links() }}
            </div>
        </div>
        <div class="col-md-10 d-none" data-customer-searching>
            <hr>
            <p>Search result will appear when found...</p>
        </div>
    </div>
</div>
@endsection