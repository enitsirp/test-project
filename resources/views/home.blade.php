@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10  col-lg-10 px-4">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if(Auth::user()->is_admin)
                        <a href="/users/list" class="btn btn-outline-secondary">Users</a>
                    @endif
                    <a href="/notes/list"  class="btn btn-outline-primary">Notes</a>
                    
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
