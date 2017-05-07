<div class="row">
    <div class="col-lg-12">

            <div class="form-group">
                <label>{{LANG['projects']}}</label>
                {{PROJECTS_SELECT}}
            </div>

            <div class="form-group">
                <label>{{LANG['controllerName']}}</label>
                @@Form::required()->class('form-control')->placeholder('Controllers/ControllerName')->text('controller'):
            </div>

            <div class="form-group">
                <label>{{LANG['functions']}}</label>
                @@Form::class('form-control')->placeholder('Function1,Function2,Function3 ...')->text('functions'):
            </div>

    </div>
</div>
