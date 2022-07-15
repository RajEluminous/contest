@if(Session::has('flash_success_message'))
    <div class="alert alert-success" role="alert">
        <i class="fa fa-check-circle"></i>
    	<span>{{ Session::get('flash_success_message') }}</span>
    </div>
@endif

@if(Session::has('flash_info_message'))
    <div class="alert alert-info" role="alert">
        <i class="fa fa-info-circle"></i>
        <span>{{ Session::get('flash_info_message') }}</span>
    </div>
@endif

@if(Session::has('flash_warning_message'))
    <div class="alert alert-warning" role="alert">
        <i class="fa fa-exclamation-circle"></i>
        <span>{{ Session::get('flash_warning_message') }}</span>
    </div>
@endif

@if(Session::has('flash_error_message'))
    <div class="alert alert-danger" role="alert">
        <i class="fa fa-exclamation-triangle"></i>
        <span>{{ Session::get('flash_error_message') }}</span>
    </div>
@endif
