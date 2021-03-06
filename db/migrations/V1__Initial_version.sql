create table account
(
    id               uuid      not null primary key,
    name             varchar   not null,
    ga_view_id       varchar,
    fb_ad_account_id varchar,
    timestamp        timestamp not null default current_timestamp
);

create table ad
(
    id          uuid      not null primary key,
    account_id  uuid      not null references account (id),
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

create table metric
(
    id         uuid      not null primary key,
    account_id uuid      not null references account (id),
    key        varchar   not null,
    value      int       not null default 0,
    source     varchar   not null,
    timestamp  timestamp not null default current_timestamp
);
