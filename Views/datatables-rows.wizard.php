@@Form::id($table)->open($table):
<table class="table table-bordered table-hover table-striped">

    <thead>
        {[
            $columns = DB::get($table)->columns();
            echo Form::id('tableName')->hidden('tableName', $table);
            echo Form::id('tableNameID')->hidden('tableNameID', '/#table-'.$table);
            $uniqueKey = 'id';
        ]}

        <tr>
            @foreach( $columns as $column):
            <th>@$column:</th>
            @endforeach:
            <th>
                <span style="cursor:pointer"  class="pull-right"><i class="fa fa-trash-o fa-fw" onclick="dropTable()" title="Delete Datatable"></i></span>
                <span style="cursor:pointer" class="pull-right"><i data-toggle="collapse" data-target="/#edit-@$table:"  class="fa fa-edit fa-fw" title="Edit Datatable"></i></span>
                <span style="cursor:pointer" class="pull-right"><i data-toggle="collapse" data-target="/#add-@$table:"  class="fa fa-plus fa-fw" title="Add Data"></i></span>

            </th>
        </tr>

    </thead>
    <tbody>

        {[
            $get = DB::limit($start ?? NULL, 10)->get($table);
            $result = $get->resultArray();
        ]}

        <tr class="collapse" id="add-@$table:">
            <td colspan="{{count($columns) + 1}}">
                <table class="table table-bordered table-hover table-striped">

                    <thead>
                        {[
                            $columns = DB::get($table)->columns();
                            $columnData = DB::get($table)->columnData();
                        ]}


                        <tr>
                            @foreach( $columns as $key => $column):
                                <th>@@Form::disabled()->class('form-control')->text('addColumn', $column):</th>
                            @endforeach:

                        </tr>

                    </thead>
                    <tbody>


                        <tr>
                            @foreach( $columns as $key => $column):
                            {[
                                if( $columnData[$column]->primaryKey == 1 )
                                {
                                    Form::disabled();
                                }
                            ]}
                            <td>{{ $columnData[$column]->maxLength > 255
                                                         ? Form::class('form-control')->textarea('addColumns['.$column.']')
                                                         : Form::class('form-control')->text('addColumns['.$column.']') }}
                            </td>
                            @endforeach:

                        </tr>

                        <tr>
                            <td colspan="{{count($columns) + 1}}">
                                @@Form::onclick('addRow()')->class('form-control btn btn-info')->button('update', LANG['addButton']):
                            </td>

                        <tr>

                    </tbody>
                </table>
                @Import::view('alert-bar.wizard', ['id' => '-' . $table]):

            </td>
        </tr>

        <tr class="collapse" id="edit-@$table:">
            <td colspan="{{count($columns) + 1}}">
                <table class="table table-bordered table-hover table-striped">

                    <thead>

                        <tr>
                            @foreach( $columns as $key => $column):

                            {[
                                if( $columnData[$column]->primaryKey == 1 )
                                {
                                    $uniqueKey = $column;
                                    echo Form::id('uniqueKey')->hidden('uniqueKey', $uniqueKey);
                                }

                                echo Form::hidden('columns['.$column.']', $column);
                            ]}

                            <th>@@Form::disabled()->class('form-control')->text('columns['.$column.']', $column):</th>
                            @endforeach:

                        </tr>

                    </thead>
                    <tbody>

                        {[
                            $get = DB::limit($start ?? NULL, 10)->get($table);
                            $result = $get->resultArray();
                        ]}

                        @foreach( $result as $key => $row ):
                        <tr>
                            @foreach( $columns as $key => $column):
                            {[
                                if( $columnData[$column]->primaryKey == 1 )
                                {
                                    echo Form::hidden('columns['.$column.'][]', $row[$column]);
                                    Form::disabled();
                                }
                            ]}
                            <td>{{ strlen($row[$column]) > 255
                                                         ? Form::class('form-control')->textarea('columns['.$column.'][]', $row[$column])
                                                         : Form::class('form-control')->text('columns['.$column.'][]', $row[$column]) }}
                            </td>
                            @endforeach:

                        </tr>

                        @endforeach:
                        <tr>
                            <td colspan="{{count($columns) + 1}}">

                                @@Form::onclick('updateRows()')->class('form-control btn btn-info')->button('update', LANG['updateButton']):

                            </td>

                        <tr>


                    </tbody>
                </table>
                @Import::view('alert-bar.wizard', ['id' => '-' . $table]):

            </td>
        </tr>

        @foreach( $result as $key => $row ):
        <tr>
            @foreach( $columns as $column):
            <td>@@Limiter::word((string) $row[$column], 10):</td>
            @endforeach:
            <td>
                <span style="cursor: pointer;" class="pull-right"><i onclick="deleteRow('@@Arrays::getFirst($columns):', '@@Arrays::getFirst($row):')" class="fa fa-trash-o fa-fw" title="Delete"></i></span>
                <span data-target="/#updateColumn{{$row[$uniqueKey]}}" data-toggle="collapse" style="cursor: pointer;" class="pull-right"><i class="fa fa-edit fa-fw" title="Edit Column"></i></span>

            </td>
        </tr>
        <tr id="updateColumn{{$row[$uniqueKey]}}" class="collapse">
            @foreach( $columns as $column):
            {[
                if( $columnData[$column]->primaryKey == 1 )
                {
                    echo Form::hidden('updateColumns['.$column.']', $row[$column]);
                    Form::disabled();
                }
            ]}
            <td>{{ strlen($row[$column]) > 255
                                         ? Form::class('form-control')->textarea('updateColumns['.$row[$uniqueKey].']['.$column.']', $row[$column])
                                         : Form::class('form-control')->text('updateColumns['.$row[$uniqueKey].']['.$column.']', $row[$column]) }}
            </td>

            @endforeach:

            <td>
                @@Form::onclick('updateRow(\''.$row[$uniqueKey].'\')')->class('form-control btn btn-info')->button('update', LANG['updateButton']):
            </td>
        </tr>
        @endforeach:

        <tr>
            <td colspan="{{count($columns) + 1}}">
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

@@Form::close():