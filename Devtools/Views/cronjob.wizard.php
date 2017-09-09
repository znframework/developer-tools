@@Form::open():
<div class="row">
    <div class="col-lg-11">
        <h1 class="page-header">
            {{LANG['cronjobs']}} <small> {{LANG['overview']}}</small>
        </h1>

    </div>

    <div class="col-lg-1">
        <h1 class="page-header">
            @@Form::class('btn btn-info')->submit('create', LANG['createButton']):
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-th fa-fw"></i> {{LANG['cronjobs']}} </h3>
            </div>
            <div class="panel-body">

                <div class="form-group col-lg-2">
                    <label>{{LANG['certainTime']}}</label>
                    @@Form::id('certain')->class('form-control')->select('certain',
                    [
                        'none'      => LANG['none'],
                        'hourly'    => LANG['hourly'],
                        'daily'     => LANG['daily'],
                        'weekly'    => LANG['weekly'],
                        'monthly'   => LANG['monthly'],
                        'yearly'    => LANG['yearly']
                    ]):
                </div>

                <div class="form-group col-lg-2">
                    <label>{{LANG['perTime']}}</label>
                    @@Form::id('per')->class('form-control')->select('per',
                    [
                        'none'      => LANG['none'],
                        'perminute' => LANG['perminute'],
                        'perhour'   => LANG['perhour'],
                        'perDay'    => LANG['perday'],
                        'permonth'  => LANG['permonth']
                    ]):
                </div>

                <div class="form-group col-lg-2">
                    <label>{{LANG['minute']}}</label>
                    @@Form::class('form-control')->onchange('inputControlActive(this, \'minuteInput\')')->select('minute',
                    [
                        'none'      => LANG['none'],
                        'minute'    => LANG['minute']
                    ]):
                </div>

                <div class="form-group col-lg-2">
                    <label>{{LANG['hour']}}</label>
                    @@Form::class('form-control')->onchange('inputControlActive(this, \'hourInput\')')->select('hour',
                    [
                        'none'      => LANG['none'],
                        'hour'      => LANG['hour']
                    ]):
                </div>

                <div class="form-group col-lg-2">
                    <label>{{LANG['day']}}</label>
                    @@Form::class('form-control')->onchange('inputControlActive(this, \'dayInput\')')->select('day',
                    [
                        'none'      => LANG['none'],
                        'day'       => LANG['day']
                    ]):
                </div>


                <div class="form-group col-lg-2">
                    <label>{{LANG['month']}}</label>
                    @@Form::class('form-control')->onchange('inputControlActive(this, \'monthInput\')')->select('month',
                    [
                        'none'      => LANG['none'],
                        'month'     => LANG['month']
                    ]):
                </div>


                <div class="form-group col-lg-2">
                    @@Form::id('certainInput')->disabled()->class('form-control')->text('certainval'):
                </div>

                <div class="form-group col-lg-2">
                    @@Form::id('perInput')->placeholder('Number Value/: Example/: 10')->disabled()->class('form-control')->text('perval'):
                </div>

                {[
                    $minutes = [];

                    for($i = 0; $i < 59; $i++)
                    {
                        $minutes[$i] = $i;
                    }
                ]}

                <div class="form-group col-lg-2">
                    @@Form::id('minuteInput')->sub(true)->disabled()->class('form-control')->select('minuteval', $minutes):
                </div>

                {[
                    $hours = [];

                    for($i = 0; $i < 24; $i++)
                    {
                        $hours[$i] = $i;
                    }
                ]}

                <div class="form-group col-lg-2">
                    @@Form::id('hourInput')->sub(true)->disabled()->class('form-control')->select('hourval', $hours):
                </div>

                <div class="form-group col-lg-2">
                    @@Form::id('dayInput')->sub(true)->disabled()->class('form-control')->select('dayval',
                    [
                        'monday'    => LANG['monday'],
                        'tuesday'   => LANG['tuesday'],
                        'wednesday' => LANG['wednesday'],
                        'thursday'  => LANG['thursday'],
                        'friday'    => LANG['friday'],
                        'saturday'  => LANG['saturday'],
                        'sunday'    => LANG['sunday']
                    ]):
                </div>


                <div class="form-group col-lg-2">
                    @@Form::id('monthInput')->sub(true)->disabled()->class('form-control')->select('monthval',
                    [
                        'january'   => LANG['january'],
                        'february'  => LANG['february'],
                        'march'     => LANG['march'],
                        'april'     => LANG['april'],
                        'may'       => LANG['may'],
                        'july'      => LANG['july'],
                        'august'    => LANG['august'],
                        'september' => LANG['september'],
                        'october'   => LANG['october'],
                        'november'  => LANG['november'],
                        'december'  => LANG['december']
                    ]):
                </div>

                <div class="form-group col-lg-6">
                    @@Form::id('monthInput')->class('form-control')->select('type',
                    [
                        'controller' => LANG['controllerMethod'],
                        'command'    => LANG['commandMethod'],
                        'wget'       => 'WGET'
                    ]):
                </div>

                <div class="form-group col-lg-6">
                    @@Form::required()->placeholder('Controller/Method, Command/:Method or WGET URL')->class('form-control')->text('typeval'):
                </div>

            </div>

        </div>
    </div>
</div>

@if( ! empty($list) ):

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-th fa-fw"></i> {{LANG['myPackages']}} </h3>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>{{'ID'}}</th>
                                <th>{{LANG['job']}}</th>
                                <th>{{LANG['process']}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach( $list as $key => $row ):
                            <tr>
                                <td>{{$key}}</td>
                                <td>{{$row}}</td>
                                <td>
                                    {{Html::class('form-control btn btn-danger')->anchor('cronjobs/delete/' . $key, LANG['deleteButton'])}}
                                </td>
                            </tr>
                            @endforeach:
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endif:

@@Form::close():

<script>

$('/#certain').change(function()
{
    $('/#certainInput').attr('disabled', 'disabled');
    $('/#perInput').attr('disabled', 'disabled');
    $('select[sub="sub"]').attr('disabled', 'disabled');

    if( $(this).val() === 'none' )
    {
        $('/#certainInput').attr('disabled', 'disabled');
    }
});

$('/#per').change(function()
{
    $('/#certainInput').attr('disabled', 'disabled');
    $('select[sub="sub"]').attr('disabled', 'disabled');

    if( $(this).val() === 'none' )
    {
        $('/#certainInput').attr('disabled', 'disabled');
    }
    else
    {
        $('/#perInput').removeAttr('disabled');
    }
});

function inputControlActive(th, obj)
{
    var obj = '#' + obj;

    $(obj).removeAttr('disabled');

    if( $(th).val() === 'none' )
    {
        $(obj).attr('disabled', 'disabled');
    }

    $('/#certainInput').attr('disabled', 'disabled');
    $('/#perInput').attr('disabled', 'disabled');
}

</script>
