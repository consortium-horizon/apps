@extends ('layouts.main')

@section ('content')
    @parent
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Participation Registered</h1>
            <div class="col-sm-8">
                <div class="row">
                    {!! $answer !!}
            </div>
        </div>
    </div>
@endsection

