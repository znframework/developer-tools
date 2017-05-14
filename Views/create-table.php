DBForge::createTable('table_name',
[
    'id'   => [DB::int(11), DB::autoIncrement(), DB::primaryKey(), DB::notNull()],
    'name' => [DB::varchar(255), DB::notNull()]
])
