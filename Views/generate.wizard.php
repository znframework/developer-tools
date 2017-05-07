<div class="container-fluid">
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
</div>
