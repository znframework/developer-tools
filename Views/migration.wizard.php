
<div class="row">
    <div class="col-lg-12">

            <div class="form-group">
                <label>{{LANG['controllerName']}}</label>
                @@Form::required()->class('form-control')->placeholder('Models/Migrations/MigrationName')->text('migration'):
            </div>

            <div class="form-group">
                <label>{{LANG['version']}}</label>
                @@Form::class('form-control')->placeholder('1')->text('version'):
            </div>

    </div>
</div>
