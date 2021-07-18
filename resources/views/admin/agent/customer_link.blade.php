@extends('layouts.base')
@section('content')
    <div class="container-fluid">
        <header><h2>Customer Link</h2></header>
        <form>
            <div class="form-row">
                <div class="col-md-3"></div>
                <div class="form-group col-md-6">
                    <label>IB Account</label>
                    <input type="text" class="form-control"
                           value="{{ $ibId }}" disabled>
                </div>
                <div class="col-md-3"></div>
            </div>
            <div class="form-row">
                <div class="col-md-3"></div>
                <div class="form-group col-md-6">
                    <label>Link Standard Account</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" readonly id="register-link"
                               value="https://go.marketfinexia.com/register?ib_id={{ $ibId }}">
                        <button type="button" onclick="myFunction()" class="btn btn-primary copy-link">Copy link</button>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </form>
    </div>
@endsection
@section('javascript')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        function myFunction() {
            var copyText = document.getElementById("register-link");
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
        }
    </script>
@endsection

