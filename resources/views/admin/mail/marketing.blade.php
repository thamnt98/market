@extends('layouts.base')
@section('css')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <link href="{{ asset('css/bootstrap-multiselect.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <link href="{{ asset('css/email.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class='loading'><i class="fa fa-spinner fa-spin fa-3x"></i></div>
    <div class="container-fluid email-marketing">
        <div class="alert alert-danger alert-block hidden">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong class="message"></strong>
        </div>
        <div class="alert alert-success alert-block hidden">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong class="message"></strong>
        </div>
        <form>
            @csrf
            @can('email.create')
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <a target="_blank" href="{{ url('/maileclipse/templates') }}"> <i
                                class="fa fa-angle-double-right"></i>Edit Email Template</a>
                    </div>
                </div>
            @endcan
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Select Email Template</label>
                    <select class="form-control" name="template_email" required>
                        @foreach($templates as $template)
                            <option value="{{ $template->template_slug }}"> {{ $template->template_name }}</option>
                        @endforeach
                    </select>
                    <div class="errors errors-template_email"></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Subject</label>
                    <input class="form-control" type="text" name="title" value="{{ old('title') }}" required>
                    <div class="errors errors-title"></div>
                </div>
                <div class="form-group col-md-6">
                    <label>Users</label>
                    <div class="example">
                        <select
                            id="example-enableCollapsibleOptGroups-enableClickableOptGroups-enableFiltering-includeSelectAllOption"
                            class="form-control" multiple="multiple" name="users[]">
                            <optgroup label="Agent Account">
                                @foreach($users['agents'] as $c1)
                                    <option value="{{ $c1->email }}">{{ $c1->email }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Opened MT4 Account">
                                @foreach($users['yes'] as $c2)
                                    <option value="{{ $c2->email }}">{{ $c2->email }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="No MT4 Account">
                                @foreach($users['no'] as $c3)
                                    <option value="{{ $c3->email }}">{{ $c3->email }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                        <div class="errors errors-users"></div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary send-email">Send Email</button>
        </form>
    </div>
@endsection
@section('javascript')
    <script type="text/javascript">
        $('.loading').hide();

        $(document).ready(function () {
            $('#example-enableCollapsibleOptGroups-enableClickableOptGroups-enableFiltering-includeSelectAllOption').multiselect({
                enableClickableOptGroups: true,
                enableCollapsibleOptGroups: true,
                enableFiltering: true,
                includeSelectAllOption: true,
                buttonWidth: '100%'
            })
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".send-email").click(function (e) {
            $('.loading').show();
            e.preventDefault();
            let template_email = $("select[name=template_email]").val();
            let title = $("input[name=title]").val();
            let users = $("select[name='users[]']")
                .map(function () {
                    return $(this).val();
                }).get();
            $.ajax({
                url: "/admin/email/marketing",
                type: "POST",
                data: {
                    template_email: template_email,
                    title: title,
                    users: users,
                },
                success: function (response) {
                    $(document).find('.email-marketing .errors .text-danger').remove();
                    $(document).find('.email-marketing .errors').removeClass('has-error');
                    $(document).find('.alert-success').addClass('hidden');
                    response = JSON.parse(response);
                    if (response.status == '400') {
                        $.each(response.message, function (index, value) {
                            let errorsDiv = $(document).find('.errors-' + index);
                            errorsDiv.html('');
                            errorsDiv.addClass('has-error');
                            $.each(value, function (key, error) {
                                errorsDiv.append('<span class="text-danger">' + error + '</span>');
                            });
                        });
                    } else if (response.status == '417') {
                            $(document).find('.alert-success').addClass('hidden');
                            $(document).find('.alert-danger .message').html(response.message);
                            $(document).find('.alert-danger').removeClass('hidden');
                    } else {
                        $(document).find('.alert-success .message').html(response.message);
                        $(document).find('.alert-success').removeClass('hidden');
                        $(document).find('.alert-danger').addClass('hidden');
                    }
                },
                error: function (result) {
                },
                complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('.loading').hide();
                },
            });
        });
    </script>
@endsection
