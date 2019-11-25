create table ad
(
    id        uuid      not null primary key,
    name      varchar   not null,
    timestamp timestamp not null default current_timestamp
);