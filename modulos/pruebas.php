<?php


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

echo $converter->parseString($Parsedown->text('Hello _Parsedown_!'));



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




