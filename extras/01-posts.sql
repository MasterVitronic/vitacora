
PRAGMA foreign_keys = on; --habilito las impresindibles claves foraneas
PRAGMA journal_mode = WAL;--probar esto para tener algo de concurrencia


/*la tabla de las publicaciens*/
drop table if exists posts;
create table posts(
    id_post             integer     primary key AUTOINCREMENT,
    id_user             smallint    not null,                                         /*relacion a users*/
    title               varchar(99) not null,                                         /*el tituo del post*/
    url                 varchar(64) not null,                                         /*La url del post*/
    description         varchar     default '' not null,                              /*la descripcion del post*/
    articleSrc          varchar     default '' not null,                              /*la fuente del articulo en Markdown*/
    articleBody         varchar     default '' not null,                              /*el contenido del articulo en HTML*/
    wordCount           smallint    default null,                                     /*Numero de palabras en este post*/
    datePublished       datetime    default (datetime('now','localtime')),            /*fecha de publicacion*/
    dateModified        datetime    default (datetime('now','localtime')),            /*fecha de actualizacion*/
    status              varchar(1)  not null check (status in ('f','t')) default 't', /*el estatus del post*/
    foreign key         (id_user)   references users(id_user)                         /*la clave foranea a users*/
);
create unique index posts_url on posts (url);
create unique index posts_title on posts (title);


/*esta es la tabla de etiquetas*/
drop table if exists tags;
create table tags(
    id_tag              integer primary key AUTOINCREMENT,
    tag                 varchar(64) not null,                                         /*el nombre del tag*/
    estatus             varchar(1) not null check (estatus in ('f','t')) default 't'  /*el estatus del tag*/
);
create unique index tags_id_tag on tags (id_tag);
/*datos de ejemplo*/
insert into tags(tag)values('Linux');
insert into tags(tag)values('HTML');
insert into tags(tag)values('Lua');
insert into tags(tag)values('Arduino');

/*tabla de ralacion cruzada entre posts y tags*/
drop table if exists posts_tagged;
create table posts_tagged(
    id_post_tagged      integer primary key AUTOINCREMENT,
    id_tag              smallint not null,                                            /*relacion a tags*/
    id_post             smallint not null,                                            /*relacion a posts*/
    foreign key         (id_tag)  references tags(id_tag),                            /*la clave foranea a tags*/
    foreign key         (id_post) references posts(id_post)                           /*la clave foranea a posts*/
);
create unique index posts_tagged_id_post_tagged on posts_tagged (id_post_tagged);
create unique index posts_tagged_index          on posts_tagged (id_tag,id_post);



/*esta es la tabla de categories*/
drop table if exists categories;
create table categories(
    id_category         integer primary key AUTOINCREMENT,
    category            varchar(64) not null,                                         /*el nombre de la categoria*/
    estatus             varchar(1) not null check (estatus in ('f','t')) default 't'  /*el estatus de la categoria*/
);
create unique index categories_id_category on categories (id_category);
/*datos de ejemplo*/
insert into categories(category)values('Tecnología');
insert into categories(category)values('Programación');


/*tabla de ralacion cruzada entre posts y tags*/
drop table if exists posts_categories;
create table posts_categories(
    id_post_categories  integer primary key AUTOINCREMENT,
    id_category         smallint not null,                                            /*relacion a tags*/
    id_post             smallint not null,                                            /*relacion a posts*/
    foreign key         (id_category)  references categories(id_category),            /*la clave foranea a tags*/
    foreign key         (id_post) references posts(id_post)                           /*la clave foranea a posts*/
);
create unique index posts_categories_id_post_categories on posts_categories (id_post_categories);
create unique index posts_categories_index              on posts_categories (id_category,id_post);



/*esta es la tabla de comentarios*/
drop table if exists comments;
create table comments(
    id_comment          integer      primary key AUTOINCREMENT,
    id_post             smallint     not null,                                        /*relacion a posts*/
    name                varchar(64)  not null,                                        /*el nombre*/
    comment             varchar(140) not null,                                        /*el comentario*/
    datePublished       datetime     default (datetime('now','localtime')),           /*fecha de publicacion*/
    status              varchar(1)   not null check (status in ('f','t')) default 'f',/*el estatus del comentario*/
    foreign key         (id_post)    references posts(id_post)                        /*la clave foranea a posts*/
);
create unique index comments_id_comment on comments (id_comment);


select distinct categories.id_category,category,id_post from categories
left outer join posts_categories on (posts_categories.id_category=categories.id_category)
where 1=1 or id_post=1  group by categories.id_category;




