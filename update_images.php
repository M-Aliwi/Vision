<?php

require __DIR__.'/vendor/autoload.php';

$kernel = new App\Kernel('dev', true);
$kernel->boot();
$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

$posts = $em->getRepository('App\Entity\Posts')->findAll();
$images = [
    'img/bentleygrey.png',
    'img/benzinteriourwhite.png',
    'img/bigrimsrangebenz.png',
    'img/blackrolls.png',
    'img/blackscat.png',
    'img/brownleatherinterour.png',
    'img/corvette.png',
    'img/custominteriour.png'
];

foreach($posts as $i => $post) {
    if($i < count($images)) {
        $post->setUrl($images[$i]);
    }
}

$em->flush();
echo "Updated " . count($posts) . " posts\n";
