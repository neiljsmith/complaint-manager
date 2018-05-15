@extends('layouts.app') 
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>Voucher Stock Report</h1>

            @foreach ($stock['types'] as $typeName => $type)
            <hr>
            <h3>{{ $typeName }}</h3>

            <table class="table table-sm table-striped table-hover">
                <tr>
                    <th style="width:33%">Voucher Value</th>
                    <th style="width:33%">Number remaining</th>
                    <th style="width:33%">Total value remaining</th>
                </tr>
                @foreach ($type['number'] as $value => $number)
                <tr>
                    <td>&pound;{{ $value }}</td>
                    <td>{{ $number }}</td>
                    <td>&pound;{{ $type['value'][$value] }}</td>
                </tr>
                @endforeach

            </table>
            @endforeach

            <hr>
            <h3>Totals</h3>

            <table class="table table-sm table-striped table-hover">
                <tr>
                    <th style="width:33%"></th>
                    <th style="width:33%">Number remaining</th>
                    <th style="width:33%">Total value remaining</th>
                </tr>
                <tr>
                    <td></td>
                    <td><strong>{{ $stock['totals']['grandTotalNumber'] }}</strong></td>
                    <td><strong>&pound;{{ $stock['totals']['grandTotalValue'] }}</strong></td>
                </tr>
            </table>
            <hr>
        </div>
    </div>
</div>
@endsection