<?php

declare(strict_types=1);

use App\Typopo;
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Typopo.php';

Tester\Environment::setup();

const NBSP = "\u{00A0}";

/** ⎵ stands for a non-breaking space in the expectations */
function nb(string $string): string
{
	return str_replace('⎵', NBSP, $string);
}

// ellipsis
Assert::same('Ahoj…', Typopo::fixText('Ahoj.....'));
Assert::same('slovo… Další', Typopo::fixText('slovo ... Další'));
Assert::same(nb('…a⎵pak to přišlo.'), Typopo::fixText('… a pak to přišlo.'));

// spaces
Assert::same('dvě mezery', Typopo::fixText('dvě   mezery'));
Assert::same('Ahoj.', Typopo::fixText('Ahoj .'));
Assert::same('Ahoj, světe', Typopo::fixText('Ahoj ,světe'));
Assert::same('Jedna věta. Druhá věta', Typopo::fixText('Jedna věta.Druhá věta'));
Assert::same('Jedna (dva) tři', Typopo::fixText('Jedna(dva) tři'));
Assert::same('jméno(s)', Typopo::fixText('jméno(s)'));

// dashes
Assert::same(nb('slovo⎵– slovo'), Typopo::fixText('slovo - slovo'));
Assert::same(nb('slovo⎵– slovo'), Typopo::fixText('slovo—slovo'));
Assert::same('1990–1995', Typopo::fixText('1990-1995'));
Assert::same('1–2–3', Typopo::fixText('1-2-3'));
Assert::same(nb('10–20⎵%'), Typopo::fixText('10 - 20 %'));

// double quotes
Assert::same(nb('To je „citace“ a⎵tak.'), Typopo::fixText('To je "citace" a tak.'));
Assert::same(nb('To je „citace“ a⎵tak.'), Typopo::fixText('To je ,,citace" a tak.'));
Assert::same('Říká „ahoj“ všem.', Typopo::fixText('Říká " ahoj " všem.'));
Assert::same('Je to 12″ dlouhé.', Typopo::fixText('Je to 12" dlouhé.'));
Assert::same('To je „nice“ saying', Typopo::fixText('To je"nice"saying'));

// single quotes and apostrophes
Assert::same('Don’t', Typopo::fixText("Don't"));
Assert::same(nb('rock⎵’n’⎵roll'), Typopo::fixText("rock 'n' roll"));
Assert::same(nb('v⎵’70s'), Typopo::fixText("v '70s"));
Assert::same('12′ 45″', Typopo::fixText('12\' 45"'));

// accidental uppercase
Assert::same('Praha', Typopo::fixText('PRaha'));
Assert::same('Praha', Typopo::fixText('pRAHA'));
Assert::same('iOS', Typopo::fixText('iOS'));

// non-breaking spaces
Assert::same(nb('V⎵lese o⎵tyči'), Typopo::fixText('V lese o tyči'));
Assert::same(nb('Karel⎵IV.'), Typopo::fixText('Karel IV.'));
Assert::same(nb('Dne 1.⎵1.⎵1993'), Typopo::fixText('Dne 1. 1. 1993'));
Assert::same(nb('str.⎵12'), Typopo::fixText('str. 12'));
Assert::same(nb('č.⎵5'), Typopo::fixText('č. 5'));
Assert::same(nb('12⎵%'), Typopo::fixText('12 %'));
Assert::same(nb('F.⎵X. Šalda'), Typopo::fixText('F. X. Šalda'));
Assert::same(nb('X.⎵ročník'), Typopo::fixText('X. ročník'));

// protected parts: addresses, file names, entities
Assert::same(nb('Viz https://praha.skauting.cz/a-b.html, a⎵mail@example.com a⎵foto.jpg.'), Typopo::fixText('Viz https://praha.skauting.cz/a-b.html, a mail@example.com a foto.jpg.'));
Assert::same('Tom&nbsp;Jerry &amp; Bob', Typopo::fixText('Tom&nbsp;Jerry &amp; Bob'), 'entities are kept');

// HTML: tags, attributes, shortcodes, comments and code stay untouched
Assert::same(
	nb('<p class="a - b">V⎵Praze je „Zlatá“ <a href="http://a.cz/x - y">ulička</a>.</p>'),
	Typopo::fixHtml('<p class="a - b">V Praze je "Zlatá" <a href="http://a.cz/x - y">ulička</a>.</p>'),
);
Assert::same('[gallery ids="1,2" link="a - b"]', Typopo::fixHtml('[gallery ids="1,2" link="a - b"]'));
Assert::same('<!-- a "b" - c --><pre>"a" - b</pre><code>"a" - b</code>', Typopo::fixHtml('<!-- a "b" - c --><pre>"a" - b</pre><code>"a" - b</code>'));
Assert::same(nb('<p>O⎵nás</p><p>V⎵lese</p>'), Typopo::fixHtml('<p>O nás</p><p>V lese</p>'));
Assert::same(nb('<p>Slovo <strong>„citace“</strong> a⎵dál.</p>'), Typopo::fixHtml('<p>Slovo <strong>"citace"</strong> a dál.</p>'));

// edge cases
Assert::same('', Typopo::fixHtml(''));
Assert::same("\xff\xfe", Typopo::fixHtml("\xff\xfe")); // not UTF-8, left alone

// running it again changes nothing
foreach ([
	'Ahoj "světe" a jak se máš ...',
	'Byl v roce 1990-1995 - a pak odešel. Karel IV. a Praha 5 v 10 hod. 12 %',
	'To je "citace", řekl.Pak odešel.',
	"It's a \"nice\" rock 'n' roll",
	'Dne 1. 1. 1993 přišel F. X. Šalda, tj. pan Novák s 5 m a pan X (viz str. 12).',
	'<p>V <strong>lese</strong> je "stromů" - a 5 ks&nbsp;x</p>',
	'Řekl: "Ahoj, jak se máš?" a odešel... Pak "to" skončilo!!',
] as $input) {
	$once = Typopo::fixHtml($input);
	Assert::same($once, Typopo::fixHtml($once), $input);
}
