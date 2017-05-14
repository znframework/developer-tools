
@@Form::open():

<div class="row">
    <div class="col-lg-11">
        <h1 class="page-header">
            @@Strings::titleCase(CURRENT_CFUNCTION): <small> {{LANG['overview']}}</small>
        </h1>

    </div>

    <div class="col-lg-1">
        <h1 class="page-header">
            @@Form::class('btn btn-info')->submit('generate', LANG['generateButton']):
        </h1>
    </div>
</div>

@Import::view($content . '.wizard'):

@@Form::close():

@if( ! empty($files) ):

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-book fa-fw"></i> {{LANG[$content . 's']}}</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">

                    @foreach( $files as $key => $file ):
                    <a href="/#b@$key:" class="list-group-item" data-toggle="collapse">
                        <i class="fa fa-fw fa-file-text-o"></i> @$file:
                        <span><i class="fa fa-angle-down fa-fw"></i></span>

                        <span class="pull-right"><i onclick="deleteProcess('home/deleteFile/{{SELECT_PROJECT . '/' . $deletePath . $file}}');" class="fa fa-trash-o fa-fw"></i></span>
                    </a>

                    <pre id="b@$key:" class="collapse"><code onkeyup="saveProcess('{{absoluteRelativePath($fullPath . $file)}}', this, event);" contenteditable="true" class="html">@@str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', Security::phpTagEncode(Security::htmlEncode(File::read($fullPath . $file)))):</code></pre>
                    @endforeach:
                    
                </div>

            </div>
        </div>
    </div>
</div>
@endif:

<script>hljs.initHighlightingOnLoad();</script>
<script>

function saveProcess(link, e, evt)
{
    $.ajax
    ({
        'url'/:'@@siteUrl('home/saveFile'):',
        'data'/:'link=' + link + '&content=' + encodeURIComponent($(e).html()),
        'type'/:'post',
        'success'/:function()
        {

        }
    });
}

</script>
