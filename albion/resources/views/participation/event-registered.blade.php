@extends ('layouts.main')

@section ('content')
    @parent
    <div class="row">
        <div class="col-sm-12">
            <h1 class="page-header">Event Registered</h1>
            <div class="col-sm-12">
                <div class="row">
                    {!! $answer !!}
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group" >
                            <label for="papURL" class="control-label">Link this to your teammates:</label>
                            <div class="input-group">
                              <input type="text" class="form-control" value={{$papURL}} onclick="this.select()" id="papURL">
                                  <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button" id="papBtn">Copy</button>
                                  </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section ('customScripts')
    <script type="text/javascript" src="{{URL::asset('assets/js/jquery.zclip.js')}}"></script>
    <script>
        $(document).ready(function(){

            $("button#papBtn").zclip({
                path:'/assets/js/ZeroClipboard.swf',
                copy:$('input#papURL').val(),
                beforeCopy:function(){

                },
                afterCopy:function(){
                    $('button#papBtn').replaceWith('<button class="btn btn-success type="button" id="papBtn">Copied!</button>')
                    $(this).next('.check').show();
                }
            });

        });
    </script>
@endsection