@if(isset ($errors) && count($errors) > 0)

<div class="alert alert-danger overflow-hidden p-0" role="alert">
    <div class="p-3 bg-danger text-fixed-white d-flex justify-content-between">
        <h6 class="aletr-heading mb-0 text-fixed-white">A problem has been occurred while performing an action.</h6>
        <button type="button" class="btn-close p-0 text-fixed-white" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button>
    </div>
    <hr class="my-0">
    <div class="p-3">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>

@endif
