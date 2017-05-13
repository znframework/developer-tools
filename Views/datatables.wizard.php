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
                <div id="tables" class="list-group">

                    @Import::view('datatables-tables.wizard', ['tables' => $tables]):

                </div>

            </div>
        </div>
    </div>

</div>
<script>
function dropTable(table)
{
    if( confirm("@@LANG['areYouSure']:") )
    {
        $.ajax
        ({
            url/:"@@siteUrl('datatables/dropTable'):",
        	data/:"table=" + table,
        	method/:"post",

        	success/:function(data)
        	{
                $('/#tables').html(data);
        	}
        });
    }
}

function deleteRow(table, column, value, id)
{
    if( confirm("@@LANG['areYouSure']:") )
    {
        $.ajax
        ({
            url/:"@@siteUrl('datatables/deleteRow'):",
        	data/:{"table":table, "column":column, "value":value},
        	method/:"post",

        	success/:function(data)
        	{
                $(id).html(data);
        	}
        });
    }
}

function paginationRow(table, start, id)
{
    $.ajax
    ({
        url/:"@@siteUrl('datatables/paginationRow'):",
    	data/:{"table":table, "start":start},
    	method/:"post",

    	success/:function(data)
    	{
            $(id).html(data);
    	}
    });
}
</script>
