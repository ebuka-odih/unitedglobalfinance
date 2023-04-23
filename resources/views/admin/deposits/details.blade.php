@extends('admin.layouts.app')
@section('content')
    <main id="main-container">
        <!-- Hero -->
        <div class="bg-body-light">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Deposits</h1>

                </div>
            </div>
        </div>
        <!-- END Hero -->
        <!-- Page Content -->
        <div class="content">

            <!-- Dynamic Table with Export Buttons -->
            <div class="block block-rounded">

                <div class="block-header block-header-default">
                    <h3 class="block-title">All Deposits</h3> </div>
                <div class="block-content block-content-full">
                    <hr>
                    <form action="{{ route('admin.deposit.backDate') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        @if(session()->has('success'))
                            <div class="alert alert-success">
                                {{ session()->get('success') }}
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="deposit_id" value="{{ $deposit->id }}">
                                <input type="date" class="form-control" name="created_at">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-sm btn-primary">Change Date</button>
                            </div>
                        </div>
                    </form>
                    <br>
                    <table class="table table-striped">
                        <tr>
                            <th>Date:</th>
                            <td>{{ date('Y-M-d', strtotime($deposit->created_at)) }}</td>
                        </tr>
                        <tr>
                            <th>Receiver:</th>
                            <td>{{ $deposit->user->first_name." ".$deposit->user->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Amount:</th>
                            <td>$@convert($deposit->amount)</td>
                        </tr>
                        <tr>
                            <th>Sender:</th>
                            <td>{{ $deposit->from }}</td>
                        </tr>
                    </table>


                </div>
            </div>
            <!-- END Dynamic Table with Export Buttons -->

        </div>
        <!-- END Page Content -->
    </main>
@endsection
