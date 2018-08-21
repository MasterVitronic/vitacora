
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
    articleBody         varchar     default '' not null,                              /*el contenido del post*/
    wordCount           smallint    default null,                                     /*Numero de palabras en este post*/
    datePublished       datetime    default (datetime('now','localtime')),            /*fecha de publicacion*/
    dateModified        datetime    default (datetime('now','localtime')),            /*fecha de actualizacion*/
    status              varchar(1)  not null check (status in ('f','t')) default 't', /*el estatus del post*/
    foreign key         (id_user)   references users(id_user)                        /*la clave foranea a users*/
);
create unique index posts_index on posts (title,datePublished);
insert into posts(id_user,title,url,description,articleBody)
values(1,'Getting Started with Hugo','primera-publicacion.html'
,'Introducción Este tutorial le mostrará cómo crear un tema simple. Asumo que está familiarizado con HTML, la línea de comandos bash, y que se siente cómodo usando Markdown para formatear el contenido. Le explicaré cómo Hugo utiliza las plantillas y cómo puede organizarlas para crear un tema. No cubriré el uso de CSS para darle estilo a tu tema. con la creación de un nuevo sitio con una plantilla muy básica.',
'<h2 id="step-1-install-hugo">Step 1. Install Hugo</h2>
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
<pre>
<code class="lang-bash">
git clone https://github.com/spf13/hugo
cd hugo
/path/to/where/you/installed/hugo server --source=./docs
&gt; 29 pages created
&gt; 0 tags index created
&gt; in 27 ms
&gt; Web Server is available at http://localhost:1313
&gt; Press ctrl+c to stop
</code>
</pre>
<p>Once you&rsquo;ve gotten here, follow along the rest of this page on your local build.</p>
<h2 id="step-3-change-the-docs-site">Step 3. Change the docs site</h2>
<p>Stop the Hugo process by hitting Ctrl+C.</p>
<p>Now we are going to run hugo again, but this time with hugo in watch mode.</p>
<pre>
<code class="lang-bash">
/path/to/hugo/from/step/1/hugo server --source=./docs --watch
&gt; 29 pages created
&gt; 0 tags index created
&gt; in 27 ms
&gt; Web Server is available at http://localhost:1313
&gt; Watching for changes in /Users/spf13/Code/hugo/docs/content
&gt; Press ctrl+c to stop
</code>
</pre>
<p>Open your <a href="http://vim.spf13.com">favorite editor</a> and change one of the source content pages. How about changing this very file to <em>fix the typo</em>. How about changing this very file to <em>fix the typo</em>.</p>
<p>Content files are found in <code>docs/content/</code>. Unless otherwise specified, files are located at the same relative location as the url, in our case
<code>docs/content/overview/quickstart.md</code>.</p>
<p>Change and save this file.. Notice what happened in your terminal.</p>
<pre>
<code class="lang-bash">
&gt; Change detected, rebuilding site
&gt; 29 pages created
&gt; 0 tags index created
&gt; in 26 ms
</code>
</pre>
<p>Refresh the browser and observe that the typo is now fixed.</p>
<p>Notice how quick that was. Try to refresh the site before it&rsquo;s finished building. I double dare you. Having nearly instant feedback enables you to have your creativity flow without waiting for long builds.</p>
<h2 id="step-4-have-fun">Step 4. Have fun</h2>
<p>The best way to learn something is to play with it.</p>'),
(1,'(Hu)go Template Primer','segunda-publicacion.html',
'Hugo uses the excellent Go html/template library for its template engine. It is an extremely lightweight engine that provides a very small amount of logic. In our experience that it is just the right amount of logic to be able to create a good static website. If you have used other template systems from different languages or frameworks you will find a lot of similarities in Go templates. This document is a brief primer on using Go templates.',
'<p>Hugo uses the excellent <a href="https://golang.org/">Go</a> <a href="https://golang.org/pkg/html/template/">html/template</a> library for
its template engine. It is an extremely lightweight engine that provides a very
small amount of logic. In our experience that it is just the right amount of
logic to be able to create a good static website. If you have used other
template systems from different languages or frameworks you will find a lot of
similarities in Go templates.</p>

<p>This document is a brief primer on using Go templates. The <a href="https://golang.org/pkg/html/template/">Go docs</a>
provide more details.</p>

<h2 id="introduction-to-go-templates">Introduction to Go Templates</h2>

<p>Go templates provide an extremely simple template language. It adheres to the
belief that only the most basic of logic belongs in the template or view layer.
One consequence of this simplicity is that Go templates parse very quickly.</p>

<p>A unique characteristic of Go templates is they are content aware. Variables and
content will be sanitized depending on the context of where they are used. More
details can be found in the <a href="https://golang.org/pkg/html/template/">Go docs</a>.</p>

<h2 id="basic-syntax">Basic Syntax</h2>

<p>Golang templates are HTML files with the addition of variables and
functions.</p>

<p><strong>Go variables and functions are accessible within {{ }}</strong></p>

<p>Accessing a predefined variable &ldquo;foo&rdquo;:</p>

<pre><code>{{ foo }}
</code></pre>

<p><strong>Parameters are separated using spaces</strong></p>

<p>Calling the add function with input of 1, 2:</p>

<pre><code>{{ add 1 2 }}
</code></pre>

<p><strong>Methods and fields are accessed via dot notation</strong></p>

<p>Accessing the Page Parameter &ldquo;bar&rdquo;</p>

<pre><code>{{ .Params.bar }}
</code></pre>

<p><strong>Parentheses can be used to group items together</strong></p>

<pre><code>{{ if or (isset .Params &quot;alt&quot;) (isset .Params &quot;caption&quot;) }} Caption {{ end }}
</code></pre>

<h2 id="variables">Variables</h2>

<p>Each Go template has a struct (object) made available to it. In hugo each
template is passed either a page or a node struct depending on which type of
page you are rendering. More details are available on the
<a href="https://themes.gohugo.io/theme/after-dark/layout/variables">variables</a> page.</p>

<p>A variable is accessed by referencing the variable name.</p>

<pre><code>&lt;title&gt;{{ .Title }}&lt;/title&gt;
</code></pre>

<p>Variables can also be defined and referenced.</p>

<pre><code>{{ $address := &quot;123 Main St.&quot;}}
{{ $address }}
</code></pre>

<h2 id="functions">Functions</h2>

<p>Go template ship with a few functions which provide basic functionality. The Go
template system also provides a mechanism for applications to extend the
available functions with their own. <a href="https://themes.gohugo.io/theme/after-dark/layout/functions">Hugo template
functions</a> provide some additional functionality we believe
are useful for building websites. Functions are called by using their name
followed by the required parameters separated by spaces. Template
functions cannot be added without recompiling hugo.</p>

<p><strong>Example:</strong></p>

<pre><code>{{ add 1 2 }}
</code></pre>

<h2 id="includes">Includes</h2>

<p>When including another template you will pass to it the data it will be
able to access. To pass along the current context please remember to
include a trailing dot. The templates location will always be starting at
the /layout/ directory within Hugo.</p>

<p><strong>Example:</strong></p>

<pre><code>{{ template &quot;chrome/header.html&quot; . }}
</code></pre>

<h2 id="logic">Logic</h2>

<p>Go templates provide the most basic iteration and conditional logic.</p>

<h3 id="iteration">Iteration</h3>

<p>Just like in Go, the Go templates make heavy use of range to iterate over
a map, array or slice. The following are different examples of how to use
range.</p>

<p><strong>Example 1: Using Context</strong></p>

<pre><code>{{ range array }}
    {{ . }}
{{ end }}
</code></pre>

<p><strong>Example 2: Declaring value variable name</strong></p>

<pre><code>{{range $element := array}}
    {{ $element }}
{{ end }}
</code></pre>

<p><strong>Example 2: Declaring key and value variable name</strong></p>

<pre><code>{{range $index, $element := array}}
    {{ $index }}
    {{ $element }}
{{ end }}
</code></pre>

<h3 id="conditionals">Conditionals</h3>

<p>If, else, with, or, &amp; and provide the framework for handling conditional
logic in Go Templates. Like range, each statement is closed with <code>end</code>.</p>

<p>Go Templates treat the following values as false:</p>

<ul>
<li>false</li>
<li>0</li>
<li>any array, slice, map, or string of length zero</li>
</ul>

<p><strong>Example 1: If</strong></p>

<pre><code>{{ if isset .Params &quot;title&quot; }}&lt;h4&gt;{{ index .Params &quot;title&quot; }}&lt;/h4&gt;{{ end }}
</code></pre>

<p><strong>Example 2: If -&gt; Else</strong></p>

<pre><code>{{ if isset .Params &quot;alt&quot; }}
    {{ index .Params &quot;alt&quot; }}
{{else}}
    {{ index .Params &quot;caption&quot; }}
{{ end }}
</code></pre>

<p><strong>Example 3: And &amp; Or</strong></p>

<pre><code>{{ if and (or (isset .Params &quot;title&quot;) (isset .Params &quot;caption&quot;)) (isset .Params &quot;attr&quot;)}}
</code></pre>

<p><strong>Example 4: With</strong></p>

<p>An alternative way of writing &ldquo;if&rdquo; and then referencing the same value
is to use &ldquo;with&rdquo; instead. With rebinds the context <code>.</code> within its scope,
and skips the block if the variable is absent.</p>

<p>The first example above could be simplified as:</p>

<pre><code>{{ with .Params.title }}&lt;h4&gt;{{ . }}&lt;/h4&gt;{{ end }}
</code></pre>

<p><strong>Example 5: If -&gt; Else If</strong></p>

<pre><code>{{ if isset .Params &quot;alt&quot; }}
    {{ index .Params &quot;alt&quot; }}
{{ else if isset .Params &quot;caption&quot; }}
    {{ index .Params &quot;caption&quot; }}
{{ end }}
</code></pre>

<h2 id="pipes">Pipes</h2>

<p>One of the most powerful components of Go templates is the ability to
stack actions one after another. This is done by using pipes. Borrowed
from unix pipes, the concept is simple, each pipeline&rsquo;s output becomes the
input of the following pipe.</p>

<p>Because of the very simple syntax of Go templates, the pipe is essential
to being able to chain together function calls. One limitation of the
pipes is that they only can work with a single value and that value
becomes the last parameter of the next pipeline.</p>

<p>A few simple examples should help convey how to use the pipe.</p>

<p><strong>Example 1 :</strong></p>

<pre><code>{{ if eq 1 1 }} Same {{ end }}
</code></pre>

<p>is the same as</p>

<pre><code>{{ eq 1 1 | if }} Same {{ end }}
</code></pre>

<p>It does look odd to place the if at the end, but it does provide a good
illustration of how to use the pipes.</p>

<p><strong>Example 2 :</strong></p>

<pre><code>{{ index .Params &quot;disqus_url&quot; | html }}
</code></pre>

<p>Access the page parameter called &ldquo;disqus_url&rdquo; and escape the HTML.</p>

<p><strong>Example 3 :</strong></p>

<pre><code>{{ if or (or (isset .Params &quot;title&quot;) (isset .Params &quot;caption&quot;)) (isset .Params &quot;attr&quot;)}}
Stuff Here
{{ end }}
</code></pre>

<p>Could be rewritten as</p>

<pre><code>{{  isset .Params &quot;caption&quot; | or isset .Params &quot;title&quot; | or isset .Params &quot;attr&quot; | if }}
Stuff Here
{{ end }}
</code></pre>

<h2 id="context-aka-the-dot">Context (aka. the dot)</h2>

<p>The most easily overlooked concept to understand about Go templates is that {{ . }}
always refers to the current context. In the top level of your template this
will be the data set made available to it. Inside of a iteration it will have
the value of the current item. When inside of a loop the context has changed. .
will no longer refer to the data available to the entire page. If you need to
access this from within the loop you will likely want to set it to a variable
instead of depending on the context.</p>

<p><strong>Example:</strong></p>

<pre><code>  {{ $title := .Site.Title }}
  {{ range .Params.tags }}
    &lt;li&gt; &lt;a href=&quot;{{ $baseurl }}/tags/{{ . | urlize }}&quot;&gt;{{ . }}&lt;/a&gt; - {{ $title }} &lt;/li&gt;
  {{ end }}
</code></pre>

<p>Notice how once we have entered the loop the value of {{ . }} has changed. We
have defined a variable outside of the loop so we have access to it from within
the loop.</p>

<h1 id="hugo-parameters">Hugo Parameters</h1>

<p>Hugo provides the option of passing values to the template language
through the site configuration (for sitewide values), or through the meta
data of each specific piece of content. You can define any values of any
type (supported by your front matter/config format) and use them however
you want to inside of your templates.</p>

<h2 id="using-content-page-parameters">Using Content (page) Parameters</h2>

<p>In each piece of content you can provide variables to be used by the
templates. This happens in the <a href="https://themes.gohugo.io/theme/after-dark/content/front-matter">front matter</a>.</p>

<p>An example of this is used in this documentation site. Most of the pages
benefit from having the table of contents provided. Sometimes the TOC just
doesn&rsquo;t make a lot of sense. We&rsquo;ve defined a variable in our front matter
of some pages to turn off the TOC from being displayed.</p>

<p>Here is the example front matter:</p>

<pre><code>---
title: &quot;Permalinks&quot;
date: &quot;2013-11-18&quot;
aliases:
  - &quot;/doc/permalinks/&quot;
groups: [&quot;extras&quot;]
groups_weight: 30
notoc: true
---
</code></pre>

<p>Here is the corresponding code inside of the template:</p>

<pre><code>  {{ if not .Params.notoc }}
    &lt;div id=&quot;toc&quot; class=&quot;well col-md-4 col-sm-6&quot;&gt;
    {{ .TableOfContents }}
    &lt;/div&gt;
  {{ end }}
</code></pre>

<h2 id="using-site-config-parameters">Using Site (config) Parameters</h2>

<p>In your top-level configuration file (eg, <code>config.yaml</code>) you can define site
parameters, which are values which will be available to you in chrome.</p>

<p>For instance, you might declare:</p>
<div class="highlight"><pre style="background-color:#fff;-moz-tab-size:4;-o-tab-size:4;tab-size:4"><code class="language-yaml" data-lang="yaml">params:<span style="color:#bbb">
</span><span style="color:#bbb">  </span>CopyrightHTML:<span style="color:#bbb"> </span><span style="color:#b84">&#34;Copyright &amp;#xA9; 2013 John Doe. All Rights Reserved.&#34;</span><span style="color:#bbb">
</span><span style="color:#bbb">  </span>TwitterUser:<span style="color:#bbb"> </span><span style="color:#b84">&#34;spf13&#34;</span><span style="color:#bbb">
</span><span style="color:#bbb">  </span>SidebarRecentLimit:<span style="color:#bbb"> </span><span style="color:#099">5</span></code></pre></div>
<p>Within a footer layout, you might then declare a <code>&lt;footer&gt;</code> which is only
provided if the <code>CopyrightHTML</code> parameter is provided, and if it is given,
you would declare it to be HTML-safe, so that the HTML entity is not escaped
again.  This would let you easily update just your top-level config file each
January 1st, instead of hunting through your templates.</p>

<pre><code>{{if .Site.Params.CopyrightHTML}}&lt;footer&gt;
&lt;div class=&quot;text-center&quot;&gt;{{.Site.Params.CopyrightHTML | safeHtml}}&lt;/div&gt;
&lt;/footer&gt;{{end}}
</code></pre>

<p>An alternative way of writing the &ldquo;if&rdquo; and then referencing the same value
is to use &ldquo;with&rdquo; instead. With rebinds the context <code>.</code> within its scope,
and skips the block if the variable is absent:</p>

<pre><code>{{with .Site.Params.TwitterUser}}&lt;span class=&quot;twitter&quot;&gt;
&lt;a href=&quot;https://twitter.com/{{.}}&quot; rel=&quot;author&quot;&gt;
&lt;img src=&quot;/images/twitter.png&quot; width=&quot;48&quot; height=&quot;48&quot; title=&quot;Twitter: {{.}}&quot;
 alt=&quot;Twitter&quot;&gt;&lt;/a&gt;
&lt;/span&gt;{{end}}
</code></pre>

<p>Finally, if you want to pull &ldquo;magic constants&rdquo; out of your layouts, you can do
so, such as in this example:</p>

<pre><code>&lt;nav class=&quot;recent&quot;&gt;
  &lt;h1&gt;Recent Posts&lt;/h1&gt;
  &lt;ul&gt;{{range first .Site.Params.SidebarRecentLimit .Site.Recent}}
    &lt;li&gt;&lt;a href=&quot;{{.RelPermalink}}&quot;&gt;{{.Title}}&lt;/a&gt;&lt;/li&gt;
  {{end}}&lt;/ul&gt;
&lt;/nav&gt;
</code></pre>'),
(1,'Migrate to Hugo from Jekyll','tercera-publicacion.html'
,'Move static content to static Jekyll has a rule that any directory not starting with _ will be copied as-is to the _site output. Hugo keeps all static content under static. You should therefore move it all there. With Jekyll, something that looked like ▾ <root>/ ▾ images/ logo.png should become ▾ <root>/ ▾ static/ ▾ images/ logo.png Additionally, you’ll want any files that should reside at the root (such as CNAME) to be moved to static. '
,
'
<h2 id="move-static-content-to-static">Move static content to <code>static</code></h2>

<p>Jekyll has a rule that any directory not starting with <code>_</code> will be copied as-is to the <code>_site</code> output. Hugo keeps all static content under <code>static</code>. You should therefore move it all there.
With Jekyll, something that looked like</p>

<pre><code>▾ &lt;root&gt;/
    ▾ images/
        logo.png
</code></pre>

<p>should become</p>

<pre><code>▾ &lt;root&gt;/
    ▾ static/
        ▾ images/
            logo.png
</code></pre>

<p>Additionally, you&rsquo;ll want any files that should reside at the root (such as <code>CNAME</code>) to be moved to <code>static</code>.</p>

<h2 id="create-your-hugo-configuration-file">Create your Hugo configuration file</h2>

<p>Hugo can read your configuration as JSON, YAML or TOML. Hugo supports parameters custom configuration too. Refer to the <a href="https://themes.gohugo.io/theme/after-dark/overview/configuration/">Hugo configuration documentation</a> for details.</p>

<h2 id="set-your-configuration-publish-folder-to-site">Set your configuration publish folder to <code>_site</code></h2>

<p>The default is for Jekyll to publish to <code>_site</code> and for Hugo to publish to <code>public</code>. If, like me, you have <a href="http://blog.blindgaenger.net/generate_github_pages_in_a_submodule.html"><code>_site</code> mapped to a git submodule on the <code>gh-pages</code> branch</a>, you&rsquo;ll want to do one of two alternatives:</p>

<ol>
<li><p>Change your submodule to point to map <code>gh-pages</code> to public instead of <code>_site</code> (recommended).</p>

<pre><code>git submodule deinit _site
git rm _site
git submodule add -b gh-pages git@github.com:your-username/your-repo.git public
</code></pre></li>

<li><p>Or, change the Hugo configuration to use <code>_site</code> instead of <code>public</code>.</p>

<pre><code>{
    ..
    &quot;publishdir&quot;: &quot;_site&quot;,
    ..
}
</code></pre></li>
</ol>

<h2 id="convert-jekyll-templates-to-hugo-templates">Convert Jekyll templates to Hugo templates</h2>

<p>That&rsquo;s the bulk of the work right here. The documentation is your friend. You should refer to <a href="http://jekyllrb.com/docs/templates/">Jekyll&rsquo;s template documentation</a> if you need to refresh your memory on how you built your blog and <a href="https://themes.gohugo.io/theme/after-dark/layout/templates/">Hugo&rsquo;s template</a> to learn Hugo&rsquo;s way.</p>

<p>As a single reference data point, converting my templates for <a href="http://heyitsalex.net/">heyitsalex.net</a> took me no more than a few hours.</p>

<h2 id="convert-jekyll-plugins-to-hugo-shortcodes">Convert Jekyll plugins to Hugo shortcodes</h2>

<p>Jekyll has <a href="http://jekyllrb.com/docs/plugins/">plugins</a>; Hugo has <a href="https://themes.gohugo.io/theme/after-dark/doc/shortcodes/">shortcodes</a>. It&rsquo;s fairly trivial to do a port.</p>

<h3 id="implementation">Implementation</h3>

<p>As an example, I was using a custom <a href="https://github.com/alexandre-normand/alexandre-normand/blob/74bb12036a71334fdb7dba84e073382fc06908ec/_plugins/image_tag.rb"><code>image_tag</code></a> plugin to generate figures with caption when running Jekyll. As I read about shortcodes, I found Hugo had a nice built-in shortcode that does exactly the same thing.</p>

<p>Jekyll&rsquo;s plugin:</p>

<pre><code>module Jekyll
  class ImageTag &lt; Liquid::Tag
    @url = nil
    @caption = nil
    @class = nil
    @link = nil
    // Patterns
    IMAGE_URL_WITH_CLASS_AND_CAPTION =
    IMAGE_URL_WITH_CLASS_AND_CAPTION_AND_LINK = /(\w+)(\s+)((https?:\/\/|\/)(\S+))(\s+)&quot;(.*?)&quot;(\s+)-&gt;((https?:\/\/|\/)(\S+))(\s*)/i
    IMAGE_URL_WITH_CAPTION = /((https?:\/\/|\/)(\S+))(\s+)&quot;(.*?)&quot;/i
    IMAGE_URL_WITH_CLASS = /(\w+)(\s+)((https?:\/\/|\/)(\S+))/i
    IMAGE_URL = /((https?:\/\/|\/)(\S+))/i
    def initialize(tag_name, markup, tokens)
      super
      if markup =~ IMAGE_URL_WITH_CLASS_AND_CAPTION_AND_LINK
        @class   = $1
        @url     = $3
        @caption = $7
        @link = $9
      elsif markup =~ IMAGE_URL_WITH_CLASS_AND_CAPTION
        @class   = $1
        @url     = $3
        @caption = $7
      elsif markup =~ IMAGE_URL_WITH_CAPTION
        @url     = $1
        @caption = $5
      elsif markup =~ IMAGE_URL_WITH_CLASS
        @class = $1
        @url   = $3
      elsif markup =~ IMAGE_URL
        @url = $1
      end
    end
    def render(context)
      if @class
        source = &quot;&lt;figure class=''#{@class}''&gt;&quot;
      else
        source = &quot;&lt;figure&gt;&quot;
      end
      if @link
        source += &quot;&lt;a href=\&quot;#{@link}\&quot;&gt;&quot;
      end
      source += &quot;&lt;img src=\&quot;#{@url}\&quot;&gt;&quot;
      if @link
        source += &quot;&lt;/a&gt;&quot;
      end
      source += &quot;&lt;figcaption&gt;#{@caption}&lt;/figcaption&gt;&quot; if @caption
      source += &quot;&lt;/figure&gt;&quot;
      source
    end
  end
end
Liquid::Template.register_tag(''image'', Jekyll::ImageTag)
</code></pre>

<p>is written as this Hugo shortcode:</p>

<pre><code>&lt;!-- image --&gt;
&lt;figure {{ with .Get &quot;class&quot; }}class=&quot;{{.}}&quot;{{ end }}&gt;
    {{ with .Get &quot;link&quot;}}&lt;a href=&quot;{{.}}&quot;&gt;{{ end }}
        &lt;img src=&quot;{{ .Get &quot;src&quot; }}&quot; {{ if or (.Get &quot;alt&quot;) (.Get &quot;caption&quot;) }}alt=&quot;{{ with .Get &quot;alt&quot;}}{{.}}{{else}}{{ .Get &quot;caption&quot; }}{{ end }}&quot;{{ end }} /&gt;
    {{ if .Get &quot;link&quot;}}&lt;/a&gt;{{ end }}
    {{ if or (or (.Get &quot;title&quot;) (.Get &quot;caption&quot;)) (.Get &quot;attr&quot;)}}
    &lt;figcaption&gt;{{ if isset .Params &quot;title&quot; }}
        {{ .Get &quot;title&quot; }}{{ end }}
        {{ if or (.Get &quot;caption&quot;) (.Get &quot;attr&quot;)}}&lt;p&gt;
        {{ .Get &quot;caption&quot; }}
        {{ with .Get &quot;attrlink&quot;}}&lt;a href=&quot;{{.}}&quot;&gt; {{ end }}
            {{ .Get &quot;attr&quot; }}
        {{ if .Get &quot;attrlink&quot;}}&lt;/a&gt; {{ end }}
        &lt;/p&gt; {{ end }}
    &lt;/figcaption&gt;
    {{ end }}
&lt;/figure&gt;
&lt;!-- image --&gt;
</code></pre>

<h3 id="usage">Usage</h3>

<p>I simply changed:</p>

<pre><code>{% image full http://farm5.staticflickr.com/4136/4829260124_57712e570a_o_d.jpg &quot;One of my favorite touristy-type photos. I secretly waited for the good light while we were &quot;having fun&quot; and took this. Only regret: a stupid pole in the top-left corner of the frame I had to clumsily get rid of at post-processing.&quot; -&gt;http://www.flickr.com/photos/alexnormand/4829260124/in/set-72157624547713078/ %}
</code></pre>

<p>to this (this example uses a slightly extended version named <code>fig</code>, different than the built-in <code>figure</code>):</p>

<pre><code>{{% fig class=&quot;full&quot; src=&quot;http://farm5.staticflickr.com/4136/4829260124_57712e570a_o_d.jpg&quot; title=&quot;One of my favorite touristy-type photos. I secretly waited for the good light while we were having fun and took this. Only regret: a stupid pole in the top-left corner of the frame I had to clumsily get rid of at post-processing.&quot; link=&quot;http://www.flickr.com/photos/alexnormand/4829260124/in/set-72157624547713078/&quot; %}}
</code></pre>

<p>As a bonus, the shortcode named parameters are, arguably, more readable.</p>

<h2 id="finishing-touches">Finishing touches</h2>

<h3 id="fix-content">Fix content</h3>

<p>Depending on the amount of customization that was done with each post with Jekyll, this step will require more or less effort. There are no hard and fast rules here except that <code>hugo server --watch</code> is your friend. Test your changes and fix errors as needed.</p>

<h3 id="clean-up">Clean up</h3>

<p>You&rsquo;ll want to remove the Jekyll configuration at this point. If you have anything else that isn&rsquo;t used, delete it.</p>

<h2 id="a-practical-example-in-a-diff">A practical example in a diff</h2>

<p><a href="http://heyitsalex.net/">Hey, it&rsquo;s Alex</a> was migrated in less than a <em>father-with-kids day</em> from Jekyll to Hugo. You can see all the changes (and screw-ups) by looking at this <a href="https://github.com/alexandre-normand/alexandre-normand/compare/869d69435bd2665c3fbf5b5c78d4c22759d7613a...b7f6605b1265e83b4b81495423294208cc74d610">diff</a>.</p>')
;

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

