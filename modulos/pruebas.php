<?php

$htmlfull = '<h2>Step 1. Install Hugo</h2>
<p>Go to <a href="https://github.com/spf13/hugo/releases">Hugo releases</a> and download the appropriate version for your OS and architecture.</p>
<p>Save it somewhere specific as we will be using it in the next step.</p>
<p>More complete instructions are available at <a href="https://gohugo.io/getting-started/installing/">Install Hugo</a></p>
<h2>Step 2. Build the Docs</h2>
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
<code>
git clone https://github.com/spf13/hugo
cd hugo
/path/to/where/you/installed/hugo server --source=./docs
> 29 pages created
> 0 tags index created
> in 27 ms
> Web Server is available at http://localhost:1313
> Press ctrl+c to stop
</code>
</pre>
<p>Once you’ve gotten here, follow along the rest of this page on your local build.</p>
<h2>Step 3. Change the docs site</h2>
<p>Stop the Hugo process by hitting Ctrl+C.</p>
<p>Now we are going to run hugo again, but this time with hugo in watch mode.</p>
<pre>
<code>
/path/to/hugo/from/step/1/hugo server --source=./docs --watch
> 29 pages created
> 0 tags index created
> in 27 ms
> Web Server is available at http://localhost:1313
> Watching for changes in /Users/spf13/Code/hugo/docs/content
> Press ctrl+c to stop
</code>
</pre>
<p>Open your <a href="http://vim.spf13.com">favorite editor</a> and change one of the source content pages. How about changing this very file to <em>fix the typo</em>. How about changing this very file to <em>fix the typo</em>.</p>
<p>Content files are found in <code>docs/content/</code>. Unless otherwise specified, files are located at the same relative location as the url, in our case
<code>docs/content/overview/quickstart.md</code>.</p>
<p>Change and save this file.. Notice what happened in your terminal.</p>
<pre>
<code>
> Change detected, rebuilding site
> 29 pages created
> 0 tags index created
> in 26 ms
</code>
</pre>
<p>Refresh the browser and observe that the typo is now fixed.</p>
<p>Notice how quick that was. Try to refresh the site before it’s finished building. I double dare you. Having nearly instant feedback enables you to have your creativity flow without waiting for long builds.</p>
<h2>Step 4. Have fun</h2>
<p>The best way to learn something is to play with it.</p>" <h2 >Step 1. Install Hugo</h2>
<p>Go to <a href="https://github.com/spf13/hugo/releases">Hugo releases</a> and download the appropriate version for your OS and architecture.</p>
<p>Save it somewhere specific as we will be using it in the next step.</p>
<p>More complete instructions are available at <a href="https://gohugo.io/getting-started/installing/">Install Hugo</a></p>
<h2>Step 2. Build the Docs</h2>
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
<code>
git clone https://github.com/spf13/hugo
cd hugo
/path/to/where/you/installed/hugo server --source=./docs
> 29 pages created
> 0 tags index created
> in 27 ms
> Web Server is available at http://localhost:1313
> Press ctrl+c to stop
</code>
</pre>
<p>Once you’ve gotten here, follow along the rest of this page on your local build.</p>
<h2>Step 3. Change the docs site</h2>
<p>Stop the Hugo process by hitting Ctrl+C.</p>
<p>Now we are going to run hugo again, but this time with hugo in watch mode.</p>
<pre>
<code>
/path/to/hugo/from/step/1/hugo server --source=./docs --watch
> 29 pages created
> 0 tags index created
> in 27 ms
> Web Server is available at http://localhost:1313
> Watching for changes in /Users/spf13/Code/hugo/docs/content
> Press ctrl+c to stop
</code>
</pre>
<p>Open your <a href="http://vim.spf13.com">favorite editor</a> and change one of the source content pages. How about changing this very file to <em>fix the typo</em>. How about changing this very file to <em>fix the typo</em>.</p>
<p>Content files are found in <code>docs/content/</code>. Unless otherwise specified, files are located at the same relative location as the url, in our case
<code>docs/content/overview/quickstart.md</code>.</p>
<p>Change and save this file.. Notice what happened in your terminal.</p>
<pre>
<code>
> Change detected, rebuilding site
> 29 pages created
> 0 tags index created
> in 26 ms
</code>
</pre>
<p>Refresh the browser and observe that the typo is now fixed.</p>
<p>Notice how quick that was. Try to refresh the site before it’s finished building. I double dare you. Having nearly instant feedback enables you to have your creativity flow without waiting for long builds.</p>
<h2>Step 4. Have fun</h2>
<p>The best way to learn something is to play with it.</p>';



require_once(ROOT . 'lib'. DS . 'Markdownify' . DS . 'Converter.php');
require_once(ROOT . 'lib'. DS . 'Markdownify' . DS . 'ConverterExtra.php');
require_once(ROOT . 'lib'. DS . 'Markdownify' . DS . 'Parser.php');
$converter = new Markdownify\Converter;


require_once(ROOT . 'lib'. DS . 'Michelf' . DS . 'MarkdownExtra.inc.php');



// Get Markdown class
use Michelf\Markdown;
//use Michelf\MarkdownGeshi;




//    #!php
$codeblock = "

&lt;?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';
isset();
\$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    \$mail->SMTPDebug = 2;                                 // Enable verbose debug output
    \$mail-&gt;isSMTP();                                      // Set mailer to use SMTP
    \$mail-&gt;Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
    \$mail-&gt;SMTPAuth = true;                               // Enable SMTP authentication
    \$mail-&gt;Username = 'user@example.com';                 // SMTP username
    \$mail-&gt;Password = 'secret';                           // SMTP password
    \$mail-&gt;SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    \$mail-&gt;Port = 587;                                    // TCP port to connect to

    //Recipients
    \$mail-&gt;setFrom('from@example.com', 'Mailer');
    \$mail-&gt;addAddress('joe@example.net', 'Joe User');     // Add a recipient
    \$mail-&gt;addAddress('ellen@example.com');               // Name is optional
    \$mail-&gt;addReplyTo('info@example.com', 'Information');
    \$mail-&gt;addCC('cc@example.com');
    \$mail-&gt;addBCC('bcc@example.com');

    //Attachments
    \$mail-&gt;addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    \$mail-&gt;addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    //Content
    \$mail-&gt;isHTML(true);                                  // Set email format to HTML
    \$mail-&gt;Subject = 'Here is the subject';
    \$mail-&gt;Body    = 'This is the HTML message body &lt;b&gt;in bold!&lt;/b&gt;';
    \$mail-&gt;AltBody = 'This is the body in plain text for non-HTML mail clients';

    \$mail-&gt;send();
    echo 'Message has been sent';
} catch (Exception \$e) {
    echo 'Message could not be sent. Mailer Error: ', \$mail-&gt;ErrorInfo;
}


";

//echo Markdown::defaultTransform();

//$geshi = new MarkdownGeshi;

$highlighter = new GeSHi( $codeblock, 'php');
$highlighted = $highlighter->parse_code();

//echo  $highlighted;

echo PHP_EOL;

echo Markdown::defaultTransform($converter->parseString( $htmlfull ));

//echo htmlentities($codeblock);

//echo $highlighted;

//echo MarkdownGeshi::_doGeshi($md);


return ;

session_start();

// form submitted... (?)
if(isset($_POST['security']))
{
    // If true, the send from <keygen/> is valid and you can
    // test the challenge too
    if(openssl_spki_verify($_POST['security'])){
        // Gets challenge string
        $challenge = openssl_spki_export_challenge($_POST['security']);

        // If true... you are not trying to trick it.
        // If user open 2 windows to prevent data lost from a "mistake" or him just press "back" button
        //  and re-send last data... you can handle it using something like it.
        if($challenge == $_SESSION['lastForm']){
            echo 'Ok, this one is valid.', '<br><br>';
        }else{
            echo 'Nice try... nice try...', '<br><br>';
        }
    }
}

// If you open two window, the challenge won't match!
$_SESSION['lastForm'] = hash('md5', microtime(true));

?>

<!DOCTYPE html>
<html>
<body>

<form action="" method="post">
  Encryption: <keygen name="security" keytype="rsa" challenge="<?php echo $_SESSION['lastForm']; ?>"/>
  <input type="submit">
</form>

</body>
</html>




<?php








return;




error_reporting(-1);   

require_once(ROOT . 'lib'. DS . 'Markdownify' . DS . 'Converter.php');
require_once(ROOT . 'lib'. DS . 'Markdownify' . DS . 'ConverterExtra.php');
require_once(ROOT . 'lib'. DS . 'Markdownify' . DS . 'Parser.php');
$converter = new Markdownify\Converter;


require_once(ROOT . 'lib' . DS . 'class.parsedown.php');
$Parsedown = new Parsedown();


echo $Parsedown->text('Hello _Parsedown_!'); # prints: <p>Hello <em>Parsedown</em>!</p>
echo PHP_EOL;
// you can also parse inline markdown only
echo $Parsedown->line('Hello _Parsedown_!'); # prints: Hello <em>Parsedown</em>!

echo PHP_EOL;




return;

$person=[
                "@context"  => "http://schema.org/",
                "@type"     => "Person",
                "name"      => "Díaz Devera Víctor Diex Gamar",
                "gender"    => "Male",
                "url"       => "https://vitronic.me/",
                "image"     => "https://vitronic.me/img/Master-Vitronic.png",
                "telephone" => "+58-288-442-0387",
                "email"     => "mailto:vitronic2@gmail.com",
                "jobTitle"  => "Software Engineer",
                "address"   => [
                        "@type"           => "PostalAddress",
                        "streetAddress"   => "Avenida Valmore Rodríguez, Upata, Bolívar",
                        "addressLocality" => "Upata",
                        "addressRegion"   => "VE-F",
                        "addressCountry"  => "VE",
                        "postalCode"      => "8052"                        
                    ],
                "sameAs"=> [
                        "https://www.facebook.com/MasterVitronic",
                        "https://www.youtube.com/channel/UClT_C_Bp9gJlK-0-eGCS9Hg",
                        "https://www.linkedin.com/in/Master-Vitronic",
                        "https://plus.google.com/+VíctorDiexDíazDevera",
                        "https://twitter.com/MasterVitronic"
                ]
];
echo json_encode($person, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";


$organization =[
              "@context"        => "http://schema.org",
              "@type"           => "Organization",
              "name"            => "Máster Vitronic",
              "url"             => "https://vitronic.me/",
              "logo"            => "https://vitronic.me/img/Master-Vitronic.png",
              "contactPoint"    => [
                  [
                    "@type"         => "ContactPoint",
                    "telephone"     => "+58-288-442-0387",
                    "contactType"   => "Customer Service",
                    "availableLanguage"=>"Spanish"
                  ]
              ]
];

echo json_encode($organization, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";





$site = [
        "@context"  => "http://schema.org",
        "@type"     => "WebSite",
        "url"       => "https://vitronic.me/"
];

echo json_encode($site, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);




