@extends ('layouts.main')

@section ('content')
    @parent
    <div class="row">
        <div class="col-sm-12">
            <h1 class="page-header">New Event</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-9">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Enregister un nouvel Event</h4></div>
                <div class="panel-body">
                    {!! Form::open([ 'class' => 'form-horizontal', 'action' => 'ParticipationController@postNewEvent']) !!}
                        <div class="form-group">
                            {!! Form::label('eventLead', 'Event Leader',['class' => 'control-label col-sm-4']) !!}
                            <div class="col-sm-8">
                                {!!  Form::select('eventLead', $members_list, $userID, ['class' => 'form-control ', 'id' => 'eventLead', 'name' => 'eventLead']) !!}
                            </div>
                        </div>
                    @if($errors->any())
                        <div class="form-group has-error">
                    @else
                        <div class="form-group">
                    @endif
                            <label class="control-label col-sm-4">Event name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="eventName" placeholder="Event name" name="eventName">
                            </div>
                        </div>

                    @if ($errors->any())
                        <div class="row">
                            <div class="col-sm-8 col-sm-offset-4">
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
                            <label class="control-label col-sm-4">Event type</label>
                            <div class="col-sm-8">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="eventType" id="eventType1" value="PvP" checked>
                                        PvP
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="eventType" id="eventType2" value="PvE">
                                        PvE
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Comments</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" rows="3" id="eventComments" name="eventComments"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-4">
                                {!! Form::submit('Submit', ['class' => 'btn btn-primary col-sm-12']) !!}
                            </div>
                        </div>
                    {!! Form::close() !!}
                    <!--</form>-->
                </div>
                <!--panel-body-->
            </div>
            <!--panel panel-default-->
        <!--/div-->
        <!--col-sm-8-->
    </div>
    <!--row-->

@endsection