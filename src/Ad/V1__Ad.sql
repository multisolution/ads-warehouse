create table ad
(
    id          uuid      not null primary key,
    name        varchar   not null,
    cost        float     not null default 0,
    impressions int       not null default 0,
    clicks      int       not null default 0,
    cpm         float     not null default 0,
    cpc         float     not null default 0,
    ctr         float     not null default 0,
    source      varchar   not null,
    date        date      not null,
    timestamp   timestamp not null default current_timestamp
);
