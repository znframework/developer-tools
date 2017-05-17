@@Form::open():
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            @@LANG['languages']: <small> {{LANG['overview']}}</small>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-book fa-fw"></i> {{LANG['languages']}}</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">

                    {{$table}}

                </div>

            </div>
        </div>
    </div>

</div>

@@Form::close():
