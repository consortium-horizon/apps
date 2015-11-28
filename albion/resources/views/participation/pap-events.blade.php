@extends ('layouts.main')

@section ('content')
    @parent
    <h1 class="page-header">Event Management</h1>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Register a new event</h4></div>
                <div class="panel-body">
                    {!! Form::open([ 'class' => 'form-horizontal', 'route' => 'participation.events.store']) !!}
                    <div class="form-group">
                        {!! Form::label('newEventLead', 'Event Leader',['class' => 'control-label col-sm-1']) !!}
                        <div class="col-sm-11">
                            {!!  Form::select('newEventLead', $members_list, $userID, ['class' => 'form-control ', 'id' => 'newEventLead']) !!}
                        </div>
                    </div>
                    @if($errors->any())
                        <div class="form-group has-error">
                    @else
                        <div class="form-group">
                    @endif
                            <label class="control-label col-sm-1">Event name</label>
                            <div class="col-sm-11">
                                    <input type="text" class="form-control" id="newEventName" placeholder="Event name" name="newEventName">
                            </div>
                        </div>
                    @if ($errors->any())
                        <div class="form-group">
                            <div class="col-sm-11 col-sm-offset-1">
                                <div class="alert alert-danger" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <ul>
                                            <li>{{$error}}</li>
                                        </ul>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                        <div class="form-group">
                            <label class="control-label col-sm-1">Event type</label>
                            <div class="col-sm-11">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="newEventType" id="newEventType1" value="PvP" checked>
                                        PvP
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="newEventType" id="newEventType2" value="PvE">
                                        PvE
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-1">Comments</label>
                            <div class="col-sm-11">
                                <textarea class="form-control" rows="3" id="newEventComments" name="newEventComments"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-11 col-sm-offset-1">
                                {!! Form::submit('Submit', ['class' => 'btn btn-primary col-sm-12']) !!}
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div> <!--panel-body-->
            </div><!--panel-->
        </div><!--col-sm-12-->
    </div><!--row-->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Manage events</h4></div>
                <div class="panel-body">
                   <table id="eventsTbl" class="table table-hover table-condensed table-striped" width="100%"></table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeBtn"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="eventModalLabel">New message</h4>
                </div>
                <div class="modal-body">
                    <div class="panel">
                        <div class="panel-body">
                            {!! Form::open(['route' => 'participation.events.update', 'class' => 'form-horizontal', 'id' => 'modifyForm', 'hidden'=> 'hidden', 'method'=>'put']) !!}
                                <div class="form-group">
                                    {!! Form::label('modifiedEventSelect', 'Modified event name:', ['class'=> 'control-label']) !!}
                                    {!! Form::text('modifiedEventSelect', null, ['class'=>'form-control input-sm ', 'id'=>'modifiedEventSelect', 'disabled']) !!}
                                </div>
                            @if($errors->any())
                                <div class="form-group has-error">
                            @else
                                <div class="form-group">
                            @endif
                                    {!! Form::label('modifiedEventNameInput', 'Modified event new name:', ['class'=> 'control-label']) !!}
                                    {!! Form::text('modifiedEventNameInput', null, ['class'=>'form-control input-sm', 'id'=>'modifiedEventInput', 'placeholder'=>'Modified event name']) !!}
                                </div>
                            @if ($errors->any())
                                <div class="form-group">
                                    <div class="col-sm-11 col-sm-offset-1">
                                        <div class="alert alert-danger" role="alert">
                                            @foreach ($errors->get('eventName') as $error)
                                                <ul>
                                                    <li>{{$error}}</li>
                                                </ul>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                                <div class="form-group">
                                    {!! Form::label('modifiedEventTypeRadios', 'Modified event new type:', ['class' => 'control-label']) !!}
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="modifiedEventTypeRadios" id="modifiedEventType1" value="PvP" checked>
                                            PvP
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="modifiedEventTypeRadios" id="modifiedEventType2" value="PvE">
                                            PvE
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Modified comments:</label>
                                    <textarea class="form-control" rows="3" id="modifiedEventComments" name="modifiedEventComments"></textarea>
                                </div>
                                <div class="form-group">
                                    {!! Form::submit('Modify class', ['class'=>'btn btn-primary btn-sm btn-block', 'id'=>'modifySubmitBtn']) !!}
                                </div>
                            {!! Form::close() !!}
                            {!! Form::open(['route' => 'participation.events.destroy', 'class' => 'form-horizontal', 'id' => 'deleteForm', 'hidden'=> 'hidden', 'method'=>'delete']) !!}
                                <div class="form-group">
                                    {!! Form::label('deletedEventSelect', 'Deleted event name:', ['class'=> 'control-label']) !!}
                                    {!! Form::text('deletedEventSelect', null, ['class'=>'form-control input-sm ', 'id'=>'deletedEventSelect', 'disabled']) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::submit('Delete class', ['class'=>'btn btn-danger btn-sm btn-block', 'id'=>'deleteSubmitBtn']) !!}
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section ('customScripts')
    <script src="{{URL::asset('pap-dash-ref.js')}pap-dash-ref.jsavascript"></script>
@endsection