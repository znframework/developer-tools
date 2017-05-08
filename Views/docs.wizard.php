<div class="container-fluid">
    @@Form::open():
    <div class="row">
        <div class="col-lg-11">
            <h1 class="page-header">
                @@LANG['documentation']: <small> {{LANG['overview']}}</small>
            </h1>
        </div>

        <div class="col-lg-1">
            <h1 class="page-header">
                @@Form::class('btn btn-info')->submit('refresh', LANG['refreshButton']):
            </h1>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-12">

            @foreach( $docs as $doc ):
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-book fa-fw"></i> @@Separator::decode($doc->meta_keyword)->{getLang()}:</h3>
                </div>
                <div class="panel-body">
                    <div class="list-group">
                        @@specialWord(Separator::decode($doc->content)->{getLang()}):
                    </div>
                </div>
            </div>
            @endforeach:
        </div>

    </div>

    @@Form::close():

</div>
<script>hljs.initHighlightingOnLoad();</script>
