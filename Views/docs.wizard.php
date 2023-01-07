{[ $lang = strtoupper(Lang::get()) ]}
{[ $titleColumn = 'Title' . $lang ]}
{[ $keywordsColumn = 'Keywords' . $lang ]}
{[ $contentColumn = 'Content' . $lang ]}

@Form::open()

<div class="row">
    <div class="col-lg-11">
        <h1 class="page-header">
            {{LANG['documentation']}} <small> {{LANG['overview']}}</small>
        </h1>
    </div>

    <div class="col-lg-1">
        <h1 class="page-header">
            {{Form::class('btn btn-info')->submit('refresh', LANG['refreshButton'])}}
        </h1>
    </div>
</div>

<div class="row">

    <div class="col-lg-12">

        @if( ! empty($docs) ) foreach( $docs as $key => $doc )

            {[ $title = $doc->$titleColumn ?? $doc->$keywordsColumn ?? NULL ]}

            @if( ! empty($title) )
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 style="cursor:pointer" data-toggle="collapse" data-target="#id{{$key}}" class="panel-title">
                        <i class="fa fa-book fa-fw"></i>
                        {{ $title }}
                        <span><i class="fa fa-angle-down fa-fw"></i></span>
                    </h3>
                </div>
                <div id="id{{$key}}" class="collapse panel-body">
                    <div class="list-group">
                        {{ specialWord($doc->$contentColumn) }}
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>

</div>

@Form::close()

@plugin(array
(
	'Dashboard/highlight/styles/agate.css',
	'Dashboard/highlight/highlight.pack.js'
))

<script>hljs.initHighlightingOnLoad();</script>
