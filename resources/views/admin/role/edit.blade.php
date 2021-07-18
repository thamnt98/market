@extends('layouts.base')

@section('css')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/treeview.css') }}">
@endsection
@section('content')

    <div class="container-fluid">
        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <form method="post" action="{{ route('role.update', $roleData['id']) }}">
            @csrf
            <div class="">
                <div class="form-group col-md-6">
                    <label><b>Role name</b></label>
                    <input type="text" name="display_name" value="{{ old('display_name', $roleData['display_name']) }}" class="form-control">
                    @if($errors->has('display_name'))
                        <span class="text-danger text-md-left">{{ $errors->first('display_name') }}</span>
                    @endif
                </div>
            </div>
            <div class="">
                <div class="form-group col-md-6">
                    <label><b>Permission</b></label>
                    <div class="treeview-content">
                        {!! $permissions !!}
                    </div>
                    @if($errors->has('permissions'))
                        <span class="text-danger">{{ $errors->first('permissions') }}</span>
                    @endif
                </div>
            </div>
            @if(old('permissions'))
                @foreach (old('permissions') as $key => $oldPermission)
                    <input class="old-permisisons hidden" value="{{ $oldPermission }}">
                @endforeach
            @endif
            <button type="submit" class="btn btn-primary">Update Role</button>
        </form>
    </div>

@endsection
@section('javascript')
    <script>
        $(document).ready(function () {
            let permissionIds = [];
            $(".old-permisisons").each(function(){
                permissionIds.push($(this).val());
            })
            if(permissionIds.length == 0){
                permissionIds = JSON.parse('{{json_encode($roleData['permissionIds'])}}');
            }
            $.each(permissionIds, function (i,k) {
                let id = k;
                if (!$('.treeview #'+id).is(':checked')) {
                    $('input[type="checkbox"]#' + id).attr("checked",true);
                }
            });
        });

        $('input[type="checkbox"]').change(function(e) {

            var checked = $(this).prop("checked"),
                container = $(this).parent(),
                siblings = container.siblings();

            container.find('input[type="checkbox"]').prop({
                indeterminate: false,
                checked: checked
            });

            function checkSiblings(el) {
                var parent = el.parent().parent(),
                    all = true;

                el.siblings().each(function() {
                    let returnValue = all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
                    return returnValue;
                });

                if (all && checked) {

                    parent.children('input[type="checkbox"]').prop({
                        indeterminate: false,
                        checked: checked
                    });

                    checkSiblings(parent);

                } else if (all && !checked) {

                    parent.children('input[type="checkbox"]').prop("checked", checked);
                    parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));
                    checkSiblings(parent);

                } else {

                    el.parents("li").children('input[type="checkbox"]').prop({
                        indeterminate: true,
                        checked: false
                    });

                }

            }

            checkSiblings(container);
        });

        var toggler = document.getElementsByClassName("fa-caret-right");
        var i;
        for (i = 0; i < toggler.length; i++) { toggler[i].addEventListener("click", function() {
            this.parentElement.querySelector(".nested").classList.toggle("active");
            this.classList.toggle("fa-caret-right-down"); }); }

        $('input[type="checkbox"].level-1').each(function(){
            this.parentElement.querySelector(".nested").classList.toggle("active");
            this.classList.toggle("fa-caret-right-down");
        })

    </script>
@endsection
