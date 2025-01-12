@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Verify Your Account</div>
                <div class="card-body">
                    @if (session('message'))
                        <div class="alert alert-info">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('otp.verify', ['user_id' => $userId]) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" id="otp" name="otp" class="form-control " required>

                            @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        </div>
                        <button type="submit" class="btn btn-success">Verify</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
