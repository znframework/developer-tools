@@Form::id($table)->open($table):
<table class="table table-bordered table-hover table-striped">

    <thead>
        {[ $columns = DB::get($table)->columns()]}

        <tr>
            @foreach( $columns as $column):
            <th>@$column:</th>
            @endforeach:
            <th>
                <span style="cursor:pointer"  class="pull-right"><i class="fa fa-trash-o fa-fw" onclick="dropTable('@$table:')" title="Delete Datatable"></i></span>
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
                                <th>@@Form::disabled()->class('form-control')->text('addColumns['.$column.']', $column):</th>
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
                                                         ? Form::class('form-control')->textarea('addColumns['.$column.'][]')
                                                         : Form::class('form-control')->text('addColumns['.$column.'][]') }}
                            </td>
                            @endforeach:

                        </tr>

                        <tr>
                            <td colspan="{{count($columns) + 1}}">
                                @@Form::onclick('addRow(\''.$table.'\', \'/#table-'.$table.'\')')->class('form-control btn btn-info')->button('update', LANG['addButton']):
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

                        {[ $alterTablePath = STORAGE_DIR . 'ProjectTables' . DS . CURRENT_DATABASE . DS . $table . '.php' ]}

                        @if( File::exists($alterTablePath) ):
                        <tr>
                            <td colspan="{{ $colspan = count($columns) + 1 }}">
                                <pre><code id="{{$table}}-modifyTableContent" contenteditable="true" class="html">@@str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', Security::phpTagEncode(Security::htmlEncode(File::read($alterTablePath)))):</code></pre>

                            </td>

                        </tr>
                        <tr>
                            <td colspan="{{ $colspan }}">
                                @@Form::onclick('alterTable(\''.$table.'\', \'/#table-'.$table.'\', \'renameTable\')')->class('btn btn-success')->button('update', LANG['renameButton']):

                                @@Form::onclick('alterTable(\''.$table.'\', \'/#table-'.$table.'\', \'dropTable\')')->class('btn btn-danger')->button('update', LANG['dropButton']):

                                @@Form::onclick('alterTable(\''.$table.'\', \'/#table-'.$table.'\', \'addColumn\')')->class('btn btn-success')->button('update', LANG['addColumnButton']):

                                @@Form::onclick('alterTable(\''.$table.'\', \'/#table-'.$table.'\', \'modifyColumn\')')->class('btn btn-info')->button('update', LANG['modifyColumnButton']):

                                @@Form::onclick('alterTable(\''.$table.'\', \'/#table-'.$table.'\', \'renameColumn\')')->class('btn btn-warning')->button('update', LANG['renameColumnButton']):

                                @@Form::onclick('alterTable(\''.$table.'\', \'/#table-'.$table.'\', \'dropColumn\')')->class('btn btn-danger')->button('update', LANG['dropColumnButton']):

                            </td>

                        </tr>
                        @endif:
                        <tr>
                            @foreach( $columns as $key => $column):
                            {[
                                if( $columnData[$column]->primaryKey == 1 )
                                {
                                    $uniqueKey = $column;
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
                                @@Form::onclick('updateRows(\''.$table.'\', \''.$uniqueKey.'\', \'/#table-'.$table.'\')')->class('form-control btn btn-info')->button('update', LANG['updateButton']):
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
                <span style="cursor: pointer;" class="pull-right"><i onclick="deleteRow('@$table:', '@@Arrays::getFirst($columns):', '@@Arrays::getFirst($row):', '/#table-@$table:')" class="fa fa-trash-o fa-fw" title="Delete"></i></span>
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
