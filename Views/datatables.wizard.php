<div class="row">
    <div class="col-lg-11">
        <h1 class="page-header">
            {{LANG['datatables']}} <small> {{LANG['overview']}}</small>
        </h1>

    </div>

    <div class="col-lg-1">
        <h1 class="page-header">
            @@Form::class('btn btn-info')->onclick('createTable()')->button('create', LANG['createButton']):
        </h1>
    </div>

</div>
<!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-table fa-fw"></i> {{LANG['newDatatable']}}</h3>
            </div>
            <div class="panel-body">
            <pre><code id="createTableContent" contenteditable="true" class="html">@@str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', Security::phpTagEncode(Security::htmlEncode(File::read(VIEWS_DIR . 'create-table.php')))):</code></pre>
            </div>
        </div>
    </div>

</div>

@Import::view('alert-bar.wizard'):

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
<script>hljs.initHighlightingOnLoad();</script>
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
            dataType:"json",
        	success/:function(data)
        	{
                $('/#tables').html(data.result);

                if( data.status )
                {
                    $('/#success-process').removeClass('hide');
                }
                else
                {
                    $('/#error-process').removeClass('hide');
                    $('/#error-process-content').text(data.error);
                }
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

function createTable()
{
    $.ajax
    ({
        url/:"@@siteUrl('datatables/createTable'):",
    	data/:{"content":$('/#createTableContent').text()},
    	method/:"post",
        dataType:"json",
    	success/:function(data)
    	{
            $('/#tables').html(data.result);

            if( data.status )
            {
                $('/#success-process').removeClass('hide');
            }
            else
            {
                $('/#error-process').removeClass('hide');
                $('/#error-process-content').text(data.error);
            }
    	}
    });
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
