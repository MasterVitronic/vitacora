
PRAGMA foreign_keys = on; --habilito las impresindibles claves foraneas
PRAGMA journal_mode = WAL;--probar esto para tener algo de concurrencia


/*esta es la tabla de extensions*/
drop table if exists extensions;
create table extensions(
    id_extension        integer primary key AUTOINCREMENT,
    extension           varchar(4) not null,                                          /*el nombre de la extension*/
    estatus             varchar(1) not null check (estatus in ('f','t')) default 't'  /*el estatus de la extension*/
);
create unique index extensions_id_extension on extensions (id_extension);
insert into extensions(extension)values('No extension');
insert into extensions(extension)values('.html');
insert into extensions(extension)values('.php');

/*la tabla de las publicaciens*/
drop table if exists posts;
create table posts(
    id_post             integer     primary key AUTOINCREMENT,
    id_user             smallint    not null,                                         /*relacion a users*/
    title               varchar(99) not null,                                         /*el tituo del post*/
    url                 varchar(64) not null,                                         /*La url del post*/
    description         varchar     default '' not null,                              /*la descripcion del post*/
    articleBody         varchar     default '' not null,                              /*el contenido del post*/
    datePublished       datetime    default (datetime('now','localtime')),            /*fecha de publicacion*/
    wordCount           smallint    default null,                                     /*Numero de palabras en este post*/
    dateModified        datetime    default (datetime('now','localtime')),            /*fecha de actualizacion*/
    id_extension        smallint    not null default 1,                               /*relacion a extensions*/
    status              varchar(1)  not null check (status in ('f','t')) default 't', /*el estatus del post*/
    foreign key         (id_user)   references users(id_user),                        /*la clave foranea a users*/
    foreign key         (id_extension) references extensions(id_extension)            /*la clave foranea a extensions*/
);
create unique index posts_index on posts (title,datePublished);
insert into posts(id_user,title,url,description,articleBody)
values(1,'Primera publicación','primera-publicacion.html'
,
'Introducción Este tutorial le mostrará cómo crear un tema simple.
Asumo que está familiarizado con HTML, la línea de comandos bash, y
que se siente cómodo usando Markdown para formatear el contenido.
Le explicaré cómo Hugo utiliza las plantillas y cómo puede organizarlas
para crear un tema. No cubriré el uso de CSS para darle estilo a tu tema.
con la creación de un nuevo sitio con una plantilla muy básica.
',
'
<main>
    <article itemscope itemtype="http://schema.org/BlogPosting">

        <meta itemprop="name" content="Getting Started with Hugo">
        <meta itemprop="description" content="Step 1. Install Hugo Go to Hugo releases and download the appropriate version for your OS and architecture.
Save it somewhere specific as we will be using it in the next step.
More complete instructions are available at Install Hugo
Step 2. Build the Docs Hugo has its own example site which happens to also be the documentation site you are reading right now.
Follow the following steps:
 Clone the Hugo repository Go into the repo Run hugo in server mode and build the docs Open your browser to http://localhost:1313  Corresponding pseudo commands:">


        <meta itemprop="datePublished" content="2014-04-02T00:00:00&#43;00:00" />
        <meta itemprop="dateModified" content="2014-04-02T00:00:00&#43;00:00" />
        <meta itemprop="wordCount" content="350">



        <meta itemprop="keywords" content="go,golang,hugo,development," />

        <header>
            <h1 itemprop="headline">Getting Started with Hugo</h1>
            <p class="muted">
                <svg style="margin-bottom:-3px" class="i-clock" viewBox="0 0 32 32" width="16" height="16" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
  <circle cx="16" cy="16" r="14" />
  <path d="M16 8 L16 16 20 20" />
</svg>
                <span>2 minute read</span>
                <svg style="margin-bottom: -3px" class="i-edit" viewBox="0 0 32 32" width="16" height="16" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
  <path d="M30 7 L25 2 5 22 3 29 10 27 Z M21 6 L26 11 Z M5 22 L10 27 Z" />
</svg> Published: <time datetime="2014-04-02T00:00:00&#43;00:00">2 Apr, 2014</time>


            </p>


        </header>


        <div itemprop="articleBody">


            <h2 id="step-1-install-hugo">Step 1. Install Hugo</h2>

            <p>Go to <a href="https://github.com/spf13/hugo/releases">Hugo releases</a> and download the appropriate version for your OS and architecture.</p>

            <p>Save it somewhere specific as we will be using it in the next step.</p>

            <p>More complete instructions are available at <a href="https://gohugo.io/getting-started/installing/">Install Hugo</a></p>

            <h2 id="step-2-build-the-docs">Step 2. Build the Docs</h2>

            <p>Hugo has its own example site which happens to also be the documentation site you are reading right now.</p>

            <p>Follow the following steps:</p>

            <ol>
                <li>Clone the <a href="http://github.com/spf13/hugo">Hugo repository</a></li>
                <li>Go into the repo</li>
                <li>Run hugo in server mode and build the docs</li>
                <li>Open your browser to <a href="http://localhost:1313">http://localhost:1313</a></li>
            </ol>

            <p>Corresponding pseudo commands:</p>

            <pre><code>git clone https://github.com/spf13/hugo
cd hugo
/path/to/where/you/installed/hugo server --source=./docs
&gt; 29 pages created
&gt; 0 tags index created
&gt; in 27 ms
&gt; Web Server is available at http://localhost:1313
&gt; Press ctrl+c to stop
</code></pre>

            <p>Once you&rsquo;ve gotten here, follow along the rest of this page on your local build.</p>

            <h2 id="step-3-change-the-docs-site">Step 3. Change the docs site</h2>

            <p>Stop the Hugo process by hitting Ctrl+C.</p>

            <p>Now we are going to run hugo again, but this time with hugo in watch mode.</p>

            <pre><code>/path/to/hugo/from/step/1/hugo server --source=./docs --watch
&gt; 29 pages created
&gt; 0 tags index created
&gt; in 27 ms
&gt; Web Server is available at http://localhost:1313
&gt; Watching for changes in /Users/spf13/Code/hugo/docs/content
&gt; Press ctrl+c to stop
</code></pre>

            <p>Open your <a href="http://vim.spf13.com">favorite editor</a> and change one of the source content pages. How about changing this very file to <em>fix the typo</em>. How about changing this very file to <em>fix the typo</em>.</p>

            <p>Content files are found in <code>docs/content/</code>. Unless otherwise specified, files are located at the same relative location as the url, in our case
                <code>docs/content/overview/quickstart.md</code>.</p>

            <p>Change and save this file.. Notice what happened in your terminal.</p>

            <pre><code>&gt; Change detected, rebuilding site
&gt; 29 pages created
&gt; 0 tags index created
&gt; in 26 ms
</code></pre>

            <p>Refresh the browser and observe that the typo is now fixed.</p>

            <p>Notice how quick that was. Try to refresh the site before it&rsquo;s finished building. I double dare you. Having nearly instant feedback enables you to have your creativity flow without waiting for long builds.</p>

            <h2 id="step-4-have-fun">Step 4. Have fun</h2>

            <p>The best way to learn something is to play with it.</p>

        </div>
        <footer>
            <hr>
            <p>
                Published by <span itemprop="author">Hugo</span>
                <time itemprop="datePublished" datetime="2014-04-02T00:00:00&#43;00:00">
    2 Apr, 2014
  </time> in <span itemprop="articleSection"><a href="https://themes.gohugo.io/theme/after-dark/categories/development/">Development</a> and <a href="https://themes.gohugo.io/theme/after-dark/categories/golang/">golang</a></span> and tagged <a href="https://themes.gohugo.io/theme/after-dark/tags/development/">development</a>, <a href="https://themes.gohugo.io/theme/after-dark/tags/go/">go</a>, <a href="https://themes.gohugo.io/theme/after-dark/tags/golang/">golang</a> and <a href="https://themes.gohugo.io/theme/after-dark/tags/hugo/">hugo</a> using <span itemprop="wordCount">350</span> words.
            </p>
            <aside>
                <header>Related Content</header>
                <ul>
                    <li><a href="https://themes.gohugo.io/theme/after-dark/post/goisforlovers/">(Hu)go Template Primer</a>
                        <time datetime="7M">7 minutes</time>
                </ul>
            </aside>
        </footer>
    </article>
</main>
');

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
insert into posts_tagged(id_post,id_tag)values(1,2);
insert into posts_tagged(id_post,id_tag)values(1,3);


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
/*datos de ejemplo*/
insert into posts_categories(id_post,id_category)values(1,1);
insert into posts_categories(id_post,id_category)values(1,2);


/*esta es la tabla de comentarios*/
drop table if exists comments;
create table comments(
    id_comment          integer      primary key AUTOINCREMENT,
    id_post             smallint     not null,                                        /*relacion a posts*/
    comment             varchar(512) not null,                                        /*el comentario*/
    avatar              varchar(512) default null,                                    /*el avatar*/
    timestamp           datetime     default (datetime('now','localtime')),           /*fecha de publicacion*/
    status              varchar(1)   not null check (status in ('f','t')) default 't',/*el estatus del comentario*/
    foreign key         (id_post)    references posts(id_post)                        /*la clave foranea a posts*/
);
create unique index comments_id_comment on comments (id_comment);



/*buscar por etiqueta*/
select title,description from posts
inner join posts_tagged on (posts_tagged.id_post  = posts.id_post)
inner join tags         on (posts_tagged.id_tag   = tags.id_tag)
where tags.tag='Lua';

/*buscar por categoria*/
select title,description from posts
inner join posts_categories on (posts_categories.id_post    = posts.id_post)
inner join categories       on (posts_categories.id_category= categories.id_category)
where categories.category='Tecnología';

