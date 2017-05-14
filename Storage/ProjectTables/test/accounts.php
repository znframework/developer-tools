<?php return
[
    'accounts' =>
    [
        'id'   => [DB::int(7), DB::autoIncrement(), DB::primaryKey(), DB::notNull()],
        'name' => [DB::varchar(10)]
    ]
];