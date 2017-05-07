<div class="container-fluid">
    @@Form::open():
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                @@Strings::titleCase(CURRENT_CFUNCTION): <small> {{LANG['overview']}}</small>
            </h1>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

                <div class="form-group">
                    <label>{{LANG['projects']}}</label>
                    {{PROJECTS_SELECT}}
                </div>

                @@Form::class('btn btn-info')->submit('show', LANG['show']):

        </div>
    </div>

    <div class="row">
        <p></p>
        <div class="col-lg-12">

            @$logs:

        </div>
    </div>
    @@Form::close():
</div>
