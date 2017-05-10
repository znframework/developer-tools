<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            {{LANG['terminal']}} <small> {{LANG['overview']}}</small>
        </h1>

    </div>
</div>
<!-- /.row -->


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-book fa-fw"></i> {{LANG['supportCommands']}}</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">

                    @foreach( $supportCommands as $command ):
                    <a href="/#" class="list-group-item">
                        <i class="fa fa-fw fa-folder"></i> @$command:
                    </a>
                    @endforeach:

                </div>

            </div>
        </div>
    </div>

</div>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-book fa-fw"></i> PHP {{LANG['terminal']}}</h3>
            </div>
            <div class="panel-body">
                <div class="form-group pull-left">
                @Terminal::run('cmd'):
                </div>
            </div>
        </div>
    </div>
</div>


<!-- /.container-fluid -->
