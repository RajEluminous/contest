@if($errors->any())
    <div class="alert alert-danger">
        <p>
            <b><i class="fa fa-warning"></i> Whoops!</b> There were some problems with your input.
        </p>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif