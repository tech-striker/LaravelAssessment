@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Update Profile</div>
                @if ( Session::has('flash_message') )
                    <div class="alert {{ Session::get('flash_type') }}" id="flash_message_alert">
                        <h3>{{ Session::get('flash_message') }}</h3>
                    </div>
                @endif
                <div class="card-body">
                    <form method="POST" action="{{ url('update-profile') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user['user_name'] }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="avtar" class="col-md-4 col-form-label text-md-right">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control @error('avtar') is-invalid @enderror" name="email" readonly value="{{ $user['email'] }}" required autocomplete="file">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="avtar" class="col-md-4 col-form-label text-md-right">Avtar</label>

                            <div class="col-md-6">
                                <input id="avtar" type="file" class="form-control @error('avtar') is-invalid @enderror" name="avtar" value="" required autocomplete="file">

                                @error('avtar')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    UPDATE
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
