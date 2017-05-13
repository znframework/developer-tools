@foreach( $tables as $key => $table ):

<a href="/#table-@$table:" class="list-group-item" data-toggle="collapse">
    <i class="fa fa-fw fa-table"></i> @$table:
    <span><i class="fa fa-angle-down fa-fw"></i></span>
    <span class="pull-right"><i class="fa fa-trash-o fa-fw" onclick="dropTable('@$table:')" title="Delete"></i></span>
    <span class="pull-right"><i class="fa fa-edit fa-fw" title="Edit"></i></span>
    <span class="pull-right"><i class="fa fa-plus fa-fw" title="Add"></i></span>
</a>


<div id="table-@$table:"class="collapse table-responsive">
    @Import::view('datatables-rows.wizard', ['table' => $table]):
</div>

@endforeach:
