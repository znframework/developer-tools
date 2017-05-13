<div class="row">
    <div class="col-lg-11">
        <h1 class="page-header">
            {{LANG['datatables']}} <small> {{LANG['overview']}}</small>
        </h1>

    </div>

</div>
<!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-table fa-fw"></i> {{LANG['datatables']}}</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">

                    @foreach( $tables as $key => $table ):
                    <a href="/#b@$key:" class="list-group-item" data-toggle="collapse">
                        <i class="fa fa-fw fa-table"></i> @$table:
                        <span><i class="fa fa-angle-down fa-fw"></i></span>
                        <span class="pull-right"><i onclick="" class="fa fa-trash-o fa-fw" title="Delete"></i></span>
                        <span class="pull-right"><i onclick="" class="fa fa-edit fa-fw" title="Edit"></i></span>
                        <span class="pull-right"><i onclick="" class="fa fa-plus fa-fw" title="Add"></i></span>
                    </a>

                        <div id="b@$key:"class="collapse table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    {[ $columns = DB::get($table)->columns()]}

                                    <tr>
                                        @foreach( $columns as $column):
                                        <th>@$column:</th>
                                        @endforeach:
                                        <th></th>
                                    </tr>

                                </thead>
                                <tbody>

                                    {[ $result = DB::get($table)->resultArray()]}
                                    @foreach( $result as $key => $row ):
                                    <tr>
                                        @foreach( $columns as $column):
                                        <td>@$row[$column]:</td>
                                        @endforeach:
                                        <td>
                                            <span class="pull-right"><i onclick="" class="fa fa-trash-o fa-fw" title="Delete"></i></span>
                                            <span class="pull-right"><i onclick="" class="fa fa-edit fa-fw" title="Edit"></i></span>
                                        </td>
                                    </tr>
                                    @endforeach:

                                </tbody>
                            </table>
                        </div>

                    @endforeach:


                </div>

            </div>
        </div>
    </div>

</div>
