<?php


 $miFecha= gmmktime(12,0,0,1,15,2089);

echo 'Antes de setlocale strftime devuelve: '.strftime("%A, %d de %B de %Y", $miFecha).'<br/>';

echo 'Antes de setlocale date devuelve: '.date("l, d-m-Y (H:i:s)", $miFecha).'<br/>';

setlocale(LC_TIME,"es_ES");

echo 'Después de setlocale es_ES date devuelve: '.date("l, d-m-Y (H:i:s)", $miFecha).'<br/>';

echo 'Después de setlocale es_ES strftime devuelve: '.strftime("%A, %d de %B de %Y", $miFecha).'<br/>';

setlocale(LC_TIME, 'es_ES.UTF-8');

echo 'Después de setlocale es_ES.UTF-8 date devuelve: '.date("l, d-m-Y (H:i:s)", $miFecha).'<br/>';

echo 'Después de setlocale es_ES.UTF-8 strftime devuelve: '.strftime("%A, %d de %B de %Y", $miFecha).'<br/>';

setlocale(LC_TIME, 'de_DE.UTF-8');

echo 'Después de setlocale de_DE.UTF-8 date devuelve: '.date("l, d-m-Y (H:i:s)", $miFecha).'<br/>';

echo 'Después de setlocale de_DE.UTF-8 strftime devuelve: '.strftime("%A, %d de %B de %Y", $miFecha).'<br/>';




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




