@@Form::id('gridForm')->open('gridForm'):
<div class="row">
    <div class="col-lg-11">
        <h1 class="page-header">
            @@LANG['grids']: <small> {{LANG['overview']}}</small>
        </h1>
    </div>

    <div class="col-lg-1">
        <h1 class="page-header">
            @@Form::class('btn btn-info')->onclick('submitPage(event)')->button('show', LANG['showButton']):
            @@Form::hidden('show', 1):
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-book fa-fw"></i> {{LANG['parameters']}}</h3>
            </div>
            <div class="panel-body">

                <div class="form-group">
                    <label>{{LANG['selectTable']}}</label>
                    @@Form::class('form-control')->onchange('getColumns(this, \'/#joinMainColumn\', \'main\')')->select('table', $tables, $selectTable):
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-lg-12">
        <div class="panel panel-default">
            <div style="cursor:pointer" data-target="/#joinsCollapse" data-toggle="collapse" class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-book fa-fw"></i> {{LANG['joins']}}</h3>
            </div>

            <div id="joinsCollapse" class="collapse panel-body">

                <div id="joinMainColumn" class="form-group col-lg-2">

                    @@Form::class('form-control')->id('joinMainColumn')->select('column', $columns):
                </div>

                @for( $i = 1; $i <= 10; $i++ ):
                <div class="form-group col-lg-1">

                    @@Form::class(($i > 1 ? 'hide ' /: '').'form-control')->onchange('getColumns(this, \'/#joinMainColumn'.$i.'\', \'sub\', \'/#joinMainTable'.($i+1).'\')')->id('joinMainTable' . $i)->select('joinMainTable[]', $tables, 'none'):
                    <div id="joinMainColumn{{$i}}">

                    </div>
                </div>
                @endfor:

            </div>

        </div>
    </div>

</div>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-th fa-fw"></i> {{LANG['grids']}}</h3>
            </div>
            <div class="panel-body">
                <div style="overflow-x: auto;" class="list-group">

                    {{$table}}

                </div>

            </div>
        </div>
    </div>

</div>

<script>

function getColumns(obj, id, type, next)
{
    $(next).removeClass('hide').attr('style', 'display/:contents');

    $.ajax
    ({
        url/:"@@siteUrl('system/gridSelectJoinTableAjax'):",
    	data/:'table=' + $(obj).val() + '&type=' + type,
    	method/:"post",
    	success/:function(data)
    	{
            if( ! data )
            {
                $(next).attr('style', 'display/:none;');
            }

            $(id).html(data);
    	}
    });
}

function submitPage(e)
{
    $.ajax
    ({
        url/:"@@siteUrl('system/grid'):",
    	data/:$('/#gridForm').serialize(),
        method/:"post",
    	success/:function(data)
    	{
            document.documentElement.innerHTML = data;
    	}
    });
}

</script>
