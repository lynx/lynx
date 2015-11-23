Lynx
====

> An awesome Mapper on top of Doctrine 2 components

## Testing

#### PostgresSQL

```
sudo docker run --name lynx-test -p 5432:5432 -e POSTGRES_PASSWORD= -d postgres
psql -p 5432 -h 127.0.0.1 -U postgres 'create database lynx_test;'
psql -p 5432 -h 127.0.0.1 -U postgres -d lynx_test -f tests/schemas/pqsql/lynx_test.sql
```

## LICENSE

MIT
