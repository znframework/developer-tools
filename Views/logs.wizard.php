@@Form::open():
<div class="row">
    <div class="col-lg-11">
        <h1 class="page-header">
            @@Strings::titleCase(CURRENT_CFUNCTION): <small> {{LANG['overview']}}</small>
        </h1>

    </div>

    <div class="col-lg-1">
        <h1 class="page-header">
            @@Form::class('btn btn-info')->submit('show', LANG['show']):
        </h1>
    </div>
</div>


<div class="row">
    @if( ! empty($files) ) foreach( $files as $file ):
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-file-text-o fa-fw"></i> @$file:</h3>
            </div>
            <div class="panel-body">
                @@ltrim(str_replace('IP', '<br>IP', File::read($path . $file)), '<br>'):
            </div>
        </div>
    </div>
    @endforeach:
</div>

@@Form::close():
