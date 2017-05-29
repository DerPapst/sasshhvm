<?hh

use Sass\Sass;

function testHeaderHandling(): void
{
    $sass = new Sass();

    var_dump($sass->listHeaders());

    $sass->addHeader('header-1', '$header-1: "header-1";');
    $sass->addHeader('header-2', '$header-2: "header-2";', 10);
    $sass->addHeader('header-3', '$header-3: "header-3";', -3);

    var_dump($sass->listHeaders());

    $sass->removeHeader('idontexist');
    $sass->removeHeader('header-1');

    var_dump($sass->listHeaders());

    $sass->setHeaderPriority('header-3', 3);

    var_dump($sass->listHeaders());
}

testHeaderHandling();
