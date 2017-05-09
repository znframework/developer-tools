
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

@if( $success ?? NULL ):
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="fa fa-info-circle"></i> @$success:
        </div>
    </div>
</div>
@endif:

@if( $error ?? NULL ):
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="fa fa-info-circle"></i> @$error:
        </div>
    </div>
</div>
@endif:
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
