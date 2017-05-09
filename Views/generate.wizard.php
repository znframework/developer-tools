
@@Form::open():

<div class="row">
    <div class="col-lg-11">
        <h1 class="page-header">
            @@Strings::titleCase(CURRENT_CFUNCTION): <small> {{LANG['overview']}}</small>
        </h1>

    </div>

    <div class="col-lg-1">
        <h1 class="page-header">
            @@Form::class('btn btn-info')->submit('generate', LANG['generateButton']):
        </h1>
    </div>
</div>

@Import::view($content . '.wizard'):

@@Form::close():

@if( ! empty($files) ):

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-book fa-fw"></i> {{LANG[$content . 's']}}</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">

                    @foreach( $files as $file ):
                    <a href="/#" class="list-group-item">
                        <i class="fa fa-fw fa-file-text-o"></i> @$file:
                    </a>
                    @endforeach:

                </div>

            </div>
        </div>
    </div>
</div>
@endif:
