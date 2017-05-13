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

        {[
            $get = DB::limit($start ?? NULL, 10)->get($table);
            $result = $get->resultArray();
        ]}

        @foreach( $result as $key => $row ):
        <tr>
            @foreach( $columns as $column):
            <td>@@Limiter::word((string) $row[$column], 10):</td>
            @endforeach:
            <td>
                <span style="cursor: pointer;" class="pull-right"><i onclick="deleteRow('@$table:', '@@Arrays::getFirst($columns):', '@@Arrays::getFirst($row):', '/#table-@$table:')" class="fa fa-trash-o fa-fw" title="Delete"></i></span>
                <span style="cursor: pointer;" class="pull-right"><i onclick="" class="fa fa-edit fa-fw" title="Edit"></i></span>
            </td>
        </tr>
        @endforeach:
        <tr>
            <td colspan="@@count($columns):">
                <ul class="pagination">
                  {[ $rows = ceil($get->totalRows(true) / 10) ]}
                  @for( $i = 1; $i <= $rows; $i++ ):
                  {[ $s = ($i - 1) * 10 ]}
                  <li {{ $s == ($start ?? 0) ? 'class="active"' : ''}}><a href="javascript:;" onclick="paginationRow('@$table:', '@$s:', '/#table-@$table:')">{{$i}}</a></li>
                  @endfor:
                </ul>
            </td>
        </tr>

    </tbody>
</table>
