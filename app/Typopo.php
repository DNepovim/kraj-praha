<?php

namespace App;

/**
 * Czech microtypography fixer, a PHP port of the Czech parts of Typopo.
 *
 * Typopo v3.1.0, https://typopo.org
 * Copyright 2015–2025 Braňo Šandala (https://brano.me)
 * Licensed under MIT (https://github.com/surfinzap/typopo/blob/main/LICENSE.txt)
 *
 * Ported: ellipsis, spaces, period, dashes, single and double quotes,
 * accidental uppercase, abbreviations and non-breaking spaces (locale "cs").
 *
 * Left out on purpose:
 * - symbols (×, §, ©, №, ±, ™, exponents, #) and publication IDs
 * - moving punctuation in or out of quotes and direct speech introduction;
 *   where a comma or a period belongs next to a quote is an editorial decision
 *   a regular expression cannot make
 * - abbreviations of other locales, only the Czech ones are used
 */
class Typopo
{

	private const SPACE = ' ';
	private const NBSP = "\u{00A0}";
	private const HAIR_SPACE = "\u{200A}";
	private const NARROW_NBSP = "\u{202F}";
	private const SPACES = self::SPACE . self::NBSP . self::HAIR_SPACE . self::NARROW_NBSP;

	private const TERMINAL_PUNCTUATION = '\.!?';
	private const SENTENCE_PAUSE_PUNCTUATION = ',:;';
	private const SENTENCE_PUNCTUATION = self::SENTENCE_PAUSE_PUNCTUATION . self::TERMINAL_PUNCTUATION;
	private const OPENING_BRACKETS = '\(\[\{';
	private const CLOSING_BRACKETS = '\)\]\}';
	private const ELLIPSIS = '…';
	private const HYPHEN = '-';
	private const EN_DASH = '–';
	private const EM_DASH = '—';
	private const DEGREE = '°';
	private const AMPERSAND = '&';
	private const PERCENT = '%';
	private const PERMILLE = '‰';
	private const PERMYRIAD = '‱';
	private const PLUS = '+';
	private const MINUS = '−';
	private const COPYRIGHT = '©';
	private const REGISTERED_TRADEMARK = '®';
	private const SOUND_RECORDING_COPYRIGHT = '℗';
	private const MULTIPLICATION_SIGN = '×';
	private const ROMAN_NUMERALS = 'IVXLCDM';

	private const SINGLE_QUOTE_ADEPTS = "‚|'|‘|’|ʼ|‛|´|`|′|‹|›";
	private const APOSTROPHE = '’';
	private const SINGLE_PRIME = '′';
	private const BACKTICK = '`';
	private const DOUBLE_QUOTE_ADEPTS = "„|“|”|\"|«|»|″|,{2,}|‚{2,}|[‘’ʼ'‹›′´`]{2,}";
	private const DOUBLE_PRIME = '″';

	// Czech locale
	private const OPENING_SINGLE_QUOTE = '‚';
	private const CLOSING_SINGLE_QUOTE = '‘';
	private const OPENING_DOUBLE_QUOTE = '„';
	private const CLOSING_DOUBLE_QUOTE = '“';
	private const TERMINAL_QUOTES = self::CLOSING_SINGLE_QUOTE . self::CLOSING_DOUBLE_QUOTE;
	private const DASH_SPACE_BEFORE = self::NBSP;
	private const DASH_WORDS = self::EN_DASH;
	private const DASH_SPACE_AFTER = self::SPACE;
	private const ABBREVIATION_SPACE = self::NBSP;
	private const PERCENT_SPACE = self::NBSP;
	private const ORDINAL_INDICATOR = '\.';
	private const ORDINAL_DATE_SPACE = self::NBSP;
	private const SINGLE_WORD_ABBREVIATIONS = ['č', 'fol', 'např', 'odst', 'par', 'r', 's', 'str', 'sv', 'tj', 'tzv'];
	private const MULTIPLE_WORD_ABBREVIATIONS = ['hl m', 'n l', 'p n l', 'př n l'];

	// Private Use Area markers, Typopo's own are U+E100–U+E1FF, protected parts take U+E200–U+F8FF
	private const APOS = "\u{E100}";
	private const SINGLE_PRIME_MARK = "\u{E101}";
	private const DOUBLE_PRIME_MARK = "\u{E102}";
	private const OSQ = "\u{E103}";
	private const CSQ = "\u{E104}";
	private const OSQ_UNPAIRED = "\u{E105}";
	private const CSQ_UNPAIRED = "\u{E106}";
	private const ODQ = "\u{E107}";
	private const CDQ = "\u{E108}";
	private const ODQ_UNPAIRED = "\u{E109}";
	private const CDQ_UNPAIRED = "\u{E10A}";
	private const EN_DASH_MARK = "\u{E10B}";
	private const PROTECTED_BASE = 0xE200;
	private const PROTECTED_MAX = 0xEFFF;
	// markers of block-level tags work as a paragraph boundary
	private const BLOCK_BASE = 0xF000;
	private const BLOCK_MAX = 0xF8FF;
	private const BLOCK_MARKERS = '\x{F000}-\x{F8FF}';
	private const BLOCK_TAG_PATTERN = '<\/?(?:p|div|br|hr|li|ul|ol|dl|dt|dd|h[1-6]|blockquote|table|thead|tbody|tr|td|th|figure|figcaption|section|article|header|footer)\b[^>]*>';

	private const URL_PATTERN = '((?:(http|https|Http|Https|rtsp|Rtsp):\/\/(?:(?:[a-zA-Z0-9\$\-\_\.\+\!\*\\\'\(\)'
		. '\,\;\?\&\=]|(?:\%[a-fA-F0-9]{2})){1,64}(?:\:(?:[a-zA-Z0-9\$\-\_'
		. '\.\+\!\*\\\'\(\)\,\;\?\&\=]|(?:\%[a-fA-F0-9]{2})){1,25})?\@)?)?'
		. '((?:(?:[a-zA-Z0-9][a-zA-Z0-9\-]{0,64}\.)+'
		. '(?:'
		. '(?:aero|arpa|asia|agency|a[cdefgilmnoqrstuwxz])'
		. '|(?:biz|b[abdefghijmnorstvwyz])'
		. '|(?:cat|cloud|com|company|coop|c[acdfghiklmnoruvxyz])'
		. '|(?:dev|d[ejkmoz])'
		. '|(?:edu|e[cegrstu])'
		. '|f[ijkmor]'
		. '|(?:gov|guide|g[abdefghilmnpqrstuwy])'
		. '|h[kmnrtu]'
		. '|(?:info|int|i[delmnoqrst])'
		. '|(?:jobs|j[emop])'
		. '|k[eghimnrwyz]'
		. '|l[abcikrstuvy]'
		. '|(?:mil|mobi|museum|m[acdghklmnopqrstuvwxyz])'
		. '|(?:name|net|n[acefgilopruz])'
		. '|(?:org|om|one)'
		. '|(?:pro|p[aefghklmnrstwy])'
		. '|qa'
		. '|r[eouw]'
		. '|(?:shop|store|s[abcdeghijklmnortuvyz])'
		. '|(?:tel|travel|team|t[cdfghjklmnoprtvwz])'
		. '|u[agkmsyz]'
		. '|v[aceginu]'
		. '|(?:work|w[fs])'
		. '|(?:xyz)'
		. '|y[etu]'
		. '|z[amw]))'
		. '|(?:(?:25[0-5]|2[0-4]'
		. '[0-9]|[0-1][0-9]{2}|[1-9][0-9]|[1-9])\.(?:25[0-5]|2[0-4][0-9]'
		. '|[0-1][0-9]{2}|[1-9][0-9]|[1-9]|0)\.(?:25[0-5]|2[0-4][0-9]|[0-1]'
		. '[0-9]{2}|[1-9][0-9]|[1-9]|0)\.(?:25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}'
		. '|[1-9][0-9]|[0-9])))'
		. '(?:\:\d{1,5})?)'
		. '(\/(?:(?:[a-zA-Z0-9\;\/\?\:\@\&\=\#\~'
		. '\-\.\+\!\*\\\'\(\)\,\_])|(?:\%[a-fA-F0-9]{2}))*)?'
		. '(?:\b|$)';

	private const EMAIL_PATTERN = '[a-zA-Z0-9\+\.\_\%\-]{1,256}'
		. '\@'
		. '[a-zA-Z0-9][a-zA-Z0-9\-]{0,64}'
		. '('
		. '\.'
		. '[a-zA-Z0-9][a-zA-Z0-9\-]{0,25}'
		. ')+';

	private const FILENAME_PATTERN = '\b[a-zA-Z0-9_%\-]+\.(ai|asm|bat|bmp|c|cpp|cs|css|csv|dart|doc|docx|exe|gif|go|html|ics|java|jpeg|jpg|js|json|key|kt|less|lua|log|md|mp4|odp|ods|odt|pdf|php|pl|png|ppt|pptx|psd|py|r|rar|rb|rs|scala|scss|sh|svg|sql|swift|tar.gz|tar|tex|tiff|ts|txt|vbs|xml|xls|xlsx|yaml|yml|zip)\b';

	/** HTML parts that are never touched: comments, raw text elements, tags, shortcodes and entities */
	private const HTML_PATTERNS = [
		'<!--.*?-->',
		'<(script|style|pre|code|kbd|samp|textarea)\b[^>]*>.*?<\/\1\s*>',
		'<\/?[a-zA-Z][^>]*>',
		'\[\/?[a-zA-Z_][^\]]*\]',
		'&(?:[a-zA-Z][a-zA-Z0-9]*|#[0-9]+|#[xX][0-9a-fA-F]+);',
	];


	/**
	 * Fixes typography in an HTML fragment, only text between tags is touched.
	 * Returns the input unchanged when it cannot be processed.
	 */
	public static function fixHtml(string $html): string
	{
		return self::run($html, true);
	}


	/**
	 * Fixes typography in a plain text (HTML entities are left as they are).
	 */
	public static function fixText(string $text): string
	{
		return self::run($text, false);
	}


	private static function run(string $string, bool $html): string
	{
		if ($string === '' || !preg_match('//u', $string)) {
			return $string;
		}

		$original = $string;
		try {
			$protected = [];
			if ($html) {
				$string = self::protect($string, self::HTML_PATTERNS[0], $protected, 'is');
				$string = self::protect($string, self::HTML_PATTERNS[1], $protected, 'is');
				$string = self::protect($string, self::BLOCK_TAG_PATTERN, $protected, 'i', true);
				foreach (array_slice(self::HTML_PATTERNS, 2) as $pattern) {
					$string = self::protect($string, $pattern, $protected, 'is');
				}
			} else {
				$string = self::protect($string, self::HTML_PATTERNS[4], $protected);
			}
			$string = self::protect($string, self::EMAIL_PATTERN, $protected, 'i');
			$string = self::protect($string, self::URL_PATTERN, $protected, 'i');
			$string = self::protect($string, self::FILENAME_PATTERN, $protected, 'i');

			$string = self::fixEllipsis($string);
			$string = self::fixSpaces($string);
			$string = self::rep('\.{2}(?![\\\\\/])', '.', $string);
			$string = self::fixDash($string);
			$string = self::fixSingleQuotesPrimesAndApostrophes($string);
			$string = self::fixDoubleQuotesAndPrimes($string);
			$string = self::fixCase($string);
			$string = self::fixAbbreviations($string);
			$string = self::fixNbsp($string);

			return strtr($string, $protected);
		} catch (\Throwable) {
			return $original;
		}
	}


	/**
	 * Swaps every match for a single Private Use Area character and remembers the match.
	 *
	 * @param array<string, string> $protected marker => original text
	 */
	private static function protect(string $string, string $pattern, array &$protected, string $flags = '', bool $block = false): string
	{
		return self::cb($pattern, function (array $m) use (&$protected, $block): string {
			$blocks = count(array_filter(array_keys($protected), fn($marker) => $marker >= self::utf8(self::BLOCK_BASE)));
			$codePoint = $block ? self::BLOCK_BASE + $blocks : self::PROTECTED_BASE + count($protected) - $blocks;
			if ($codePoint > ($block ? self::BLOCK_MAX : self::PROTECTED_MAX)) {
				throw new \OverflowException('Too many protected parts.');
			}
			$marker = self::utf8($codePoint);
			$protected[$marker] = $m[0];
			return $marker;
		}, $string, $flags);
	}


	private static function utf8(int $codePoint): string
	{
		return chr(0xE0 | ($codePoint >> 12)) . chr(0x80 | (($codePoint >> 6) & 0x3F)) . chr(0x80 | ($codePoint & 0x3F));
	}


	// ---- regular expression helpers ----

	/**
	 * Wraps a pattern so it behaves like the JavaScript one in Typopo:
	 * ASCII-only \b, \w and \d, "." and "^" / "$" aware of \r and \n, "$" matching only at the very end.
	 */
	private static function regex(string $pattern, string $flags): string
	{
		return "\x01(*ANYCRLF)(?aW)(?aD)" . $pattern . "\x01uD" . $flags;
	}


	private static function rep(string $pattern, string $replacement, string $string, string $flags = ''): string
	{
		$result = preg_replace(self::regex($pattern, $flags), $replacement, $string);
		if ($result === null) {
			throw new \RuntimeException('Regular expression failed: ' . preg_last_error_msg());
		}
		return $result;
	}


	/**
	 * @param callable(array<int, string>): string $callback groups that did not take part are empty strings
	 */
	private static function cb(string $pattern, callable $callback, string $string, string $flags = ''): string
	{
		$result = preg_replace_callback(
			self::regex($pattern, $flags),
			fn(array $m) => $callback(array_map(fn($g) => $g ?? '', $m)),
			$string,
			-1,
			$count,
			PREG_UNMATCHED_AS_NULL,
		);
		if ($result === null) {
			throw new \RuntimeException('Regular expression failed: ' . preg_last_error_msg());
		}
		return $result;
	}


	/** Applies the replacement until nothing changes, so overlapping matches (1-2-3) get fixed too */
	private static function repOverlap(string $pattern, string $replacement, string $string, string $flags = ''): string
	{
		$previous = null;
		for ($i = 0; $i < 50 && $string !== $previous; $i++) {
			$previous = $string;
			$string = self::rep($pattern, $replacement, $string, $flags);
		}
		return $string;
	}


	private static function sp(string $extra = ''): string
	{
		return '[' . self::SPACES . $extra . ']';
	}


	// ---- ellipsis ----

	private static function fixEllipsis(string $string): string
	{
		$e = self::ELLIPSIS;
		$s = self::sp();

		// 3 and more periods or ellipses
		$string = self::rep('[' . $e . '\.]{3,}', $e, $string);

		// , … ,
		$string = self::rep("(,)({$s}?)({$e})({$s}?)(,)", '${1} ' . $e . '${5}', $string);

		// ellipsis as the last item in a list
		$string = self::rep(
			"(,)({$s}?)({$e})({$s}?)(\\B|[" . self::CLOSING_BRACKETS . "])([^,]|$)",
			'${1}${3}${5}${6}',
			$string,
		);

		// aposiopesis starting a paragraph
		$string = self::rep("(^{$e})({$s})(\\p{L})", '${1}${3}', $string, 'm');

		// aposiopesis starting a sentence
		$string = self::rep(
			'([^' . self::TERMINAL_QUOTES . '])([' . self::SENTENCE_PUNCTUATION . "])({$s}?)([{$e}])({$s}?)(\\p{Ll})",
			'${1}${2} ${4}${6}',
			$string,
		);

		// aposiopesis between sentences
		$string = self::rep("(\\p{Ll})({$s})([{$e}])({$s}?)(\\p{Lu})", '${1}${3} ${5}', $string);

		// aposiopesis between words
		$string = self::rep("(\\p{L})([{$e}])(\\p{L})", '${1}${2} ${3}', $string);

		// ellipsis between sentences
		$string = self::rep(
			'([' . self::SENTENCE_PUNCTUATION . self::TERMINAL_QUOTES . "])({$s}?)({$e})({$s}?)(\\p{Lu})",
			'${1} ${3} ${5}',
			$string,
		);

		// aposiopesis ending a paragraph
		$string = self::rep(
			"(\\p{Ll})({$s})+([{$e}][" . self::CLOSING_DOUBLE_QUOTE . self::CLOSING_SINGLE_QUOTE . "]?$)",
			'${1}${3}',
			$string,
			'm',
		);

		// .… …. …… → …
		$string = self::rep("\\.{$e}|{$e}{2,}|{$e}\\.", $e, $string);

		// word .. word
		$string = self::rep("{$s}\\.{2}{$s}", ' ' . $e . ' ', $string);

		return $string;
	}


	// ---- spaces ----

	private static function fixSpaces(string $string): string
	{
		$s = self::sp();

		$string = self::rep("(\\S)({$s}{2,})(\\S)", '${1} ${3}', $string);

		// extra spaces at the beginning and at the end of each paragraph
		$lines = preg_split('/\r?\n/', $string);
		$string = implode("\n", array_map(fn($line) => preg_replace('/^\s+|\s+$/u', '', $line) ?? $line, $lines));

		$string = self::rep("({$s})([" . self::SENTENCE_PAUSE_PUNCTUATION . "])([^\\-\\)]|$)", '${2}${3}', $string);

		// space before terminal punctuation, closing brackets and degree
		$string = self::rep(
			'([^' . self::OPENING_BRACKETS . "])({$s})([" . self::TERMINAL_PUNCTUATION . self::CLOSING_BRACKETS . self::DEGREE . '])',
			'${1}${3}',
			$string,
		);

		// space before ordinal indicator
		$string = self::rep('(\d)(' . $s . '?)(' . self::ORDINAL_INDICATOR . ")({$s}|\\b)", '${1}${3}${4}', $string);

		// space after opening brackets
		$string = self::rep('([' . self::OPENING_BRACKETS . "])({$s})([^" . self::CLOSING_BRACKETS . '])', '${1}${3}', $string);

		// space before opening brackets, except for name(s) and mass(es)
		$string = self::cb(
			'(\p{L})([' . self::OPENING_BRACKETS . '])([\p{L}' . self::ELLIPSIS . '])([\p{L}' . self::ELLIPSIS . self::CLOSING_BRACKETS . '])',
			function (array $m): string {
				[, $a, $b, $c, $d] = $m;
				if ($c === 's' || $c === 'S' || $c . $d === 'es' || $c . $d === 'ES') {
					return $a . $b . $c . $d;
				}
				return $a . ' ' . $b . $c . $d;
			},
			$string,
		);

		// space after terminal punctuation
		$string = self::rep('(\p{L}{2,}|[' . self::ELLIPSIS . '])([' . self::TERMINAL_PUNCTUATION . '])(\p{Lu})', '${1}${2} ${3}', $string);

		// space after closing brackets
		$string = self::rep('([' . self::CLOSING_BRACKETS . '])(\p{L})', '${1} ${2}', $string);

		// space after sentence pause punctuation
		$string = self::rep('(\p{L}{2,}|[' . self::ELLIPSIS . '])([' . self::SENTENCE_PAUSE_PUNCTUATION . '])(\p{L})', '${1}${2} ${3}', $string);

		return $string;
	}


	// ---- dashes ----

	private static function fixDash(string $string): string
	{
		$s = self::sp();
		$dashes = self::HYPHEN . self::EN_DASH . self::EM_DASH;
		$dash = self::DASH_SPACE_BEFORE . self::DASH_WORDS . self::DASH_SPACE_AFTER;
		$ob = self::OPENING_BRACKETS;
		$cb = self::CLOSING_BRACKETS;

		// between words
		$string = self::rep(
			'([\p{L}\d])(' . $s . '*[' . self::EN_DASH . self::EM_DASH . ']{1,3}' . $s . '*|' . $s . '+[' . self::HYPHEN . ']{1,3}' . $s . '+)([\p{L}\d])',
			'${1}' . $dash . '${3}',
			$string,
		);

		// between a word and punctuation or the end of a paragraph
		$string = self::rep(
			'(\p{L})(' . $s . '?)([' . $dashes . ']{1,3})(' . $s . '?)([' . self::SENTENCE_PUNCTUATION . '\n\r])',
			'${1}' . self::DASH_SPACE_BEFORE . self::DASH_WORDS . '${5}',
			$string,
		);

		// between words and brackets; a dash alone in brackets only loses its spaces
		$string = self::rep("([{$ob}]){$s}*([{$dashes}]+){$s}*([{$cb}])", '${1}${2}${3}', $string);
		$string = self::rep("(\\p{L}){$s}*[{$dashes}]{1,3}{$s}*([{$ob}])", '${1}' . $dash . '${2}', $string);
		$string = self::rep("([{$cb}]){$s}*[{$dashes}]{1,3}{$s}*(\\p{L})", '${1}' . $dash . '${2}', $string);
		$string = self::rep("(\\p{L}){$s}*[{$dashes}]{1,3}{$s}*([{$cb}])", '${1}' . $dash . '${2}', $string);
		$string = self::rep("([{$ob}]){$s}*[{$dashes}]{1,3}{$s}*(\\p{L})", '${1}' . $dash . '${2}', $string);
		$string = self::rep("([{$cb}]){$s}*[{$dashes}]{$s}*([{$ob}])", '${1}' . $dash . '${2}', $string);

		// between cardinal numbers
		$string = self::repOverlap("(\\d)({$s}?[{$dashes}]{1,3}{$s}?)(\\d)", '${1}' . self::EN_DASH_MARK . '${3}', $string);
		$string = str_replace(self::EN_DASH_MARK, self::EN_DASH, $string);

		// percentage range
		$string = self::rep(
			'([' . self::PERCENT . self::PERMILLE . self::PERMYRIAD . "])({$s}?[{$dashes}]{1,3}{$s}?)(\\d)",
			'${1}' . self::EN_DASH . '${3}',
			$string,
		);

		// ordinal numbers
		$string = self::rep(
			'(\d)(' . self::ORDINAL_INDICATOR . ")({$s}?[{$dashes}]{1,3}{$s}?)(\\d)(" . self::ORDINAL_INDICATOR . ')',
			'${1}${2}' . self::EN_DASH . '${4}${5}',
			$string,
			'i',
		);

		return $string;
	}


	// ---- single quotes, primes and apostrophes ----

	private static function fixSingleQuotesPrimesAndApostrophes(string $string): string
	{
		$s = self::sp();
		$adepts = self::SINGLE_QUOTE_ADEPTS;
		$apos = self::APOS;

		// contracted "and": rock 'n' roll
		foreach ([
			['dead', 'buried'], ['drill', 'bass'], ['drum', 'bass'], ['rock', 'roll'], ['pick', 'mix'], ['fish', 'chips'],
			['salt', 'shake'], ['mac', 'cheese'], ['pork', 'beans'], ['drag', 'drop'], ['rake', 'scrape'], ['hook', 'kill'],
		] as [$first, $second]) {
			$string = self::rep(
				"({$first})({$s}?)({$adepts})(n)({$adepts})({$s}?)({$second})",
				'${1}' . self::NBSP . $apos . '${4}' . $apos . self::NBSP . '${7}',
				$string,
				'i',
			);
		}

		// contracted beginnings: 'em, 'cause
		$string = self::rep(
			"({$adepts})(cause|em|mid|midst|mongst|prentice|round|sblood|ssdeath|sfoot|sheart|shun|slid|slife|slight|snails|strewth|til|tis|twas|tween|twere|twill|twixt|twould)",
			$apos . '${2}',
			$string,
			'i',
		);

		// in-word contractions: Don't, O'Doole
		$string = self::rep("([\\d\\p{L}])({$adepts})+(\\p{L})", '${1}' . $apos . '${3}', $string);

		// contracted years: ’70s
		$string = self::rep("([^0-9]|[A-Z][0-9])({$s})({$adepts})(\\d{2})", '${1}${2}' . $apos . '${4}', $string);

		// contracted ends: nottin'
		$string = self::rep("(\\Bin)({$adepts})", '${1}' . $apos, $string, 'i');

		// feet and arcminutes
		$string = self::rep("(\\d)( ?)('|‘|’|‛|′)", '${1}${2}' . self::SINGLE_PRIME_MARK, $string);

		// single quote pair around a single word
		$string = self::rep(
			"(\\B)({$adepts})(\\p{L}+)({$adepts})(\\B)",
			'${1}' . self::OSQ . '${3}' . self::CSQ . '${5}',
			$string,
		);

		// single quotes within double quotes
		$string = self::cb(
			'(' . self::DOUBLE_QUOTE_ADEPTS . ')(.*?)(' . self::DOUBLE_QUOTE_ADEPTS . ')',
			function (array $m) use ($s, $adepts): string {
				$inner = $m[2];
				$inner = self::rep(
					"(^|[{$s}" . self::EM_DASH . self::EN_DASH . "])({$adepts}|,)([\\p{L}" . self::ELLIPSIS . self::OPENING_BRACKETS . '\{])',
					'${1}' . self::OSQ_UNPAIRED . '${3}',
					$inner,
				);
				$inner = self::rep(
					'([\p{L}\d' . self::CLOSING_BRACKETS . '])([' . self::SENTENCE_PUNCTUATION . self::ELLIPSIS . "])?({$adepts})([ " . self::SENTENCE_PUNCTUATION . '])?',
					'${1}${2}' . self::CSQ_UNPAIRED . '${4}',
					$inner,
				);
				$inner = self::rep('(' . self::OSQ_UNPAIRED . ')(.*)(' . self::CSQ_UNPAIRED . ')', self::OSQ . '${2}' . self::CSQ, $inner);
				return $m[1] . $inner . $m[3];
			},
			$string,
		);

		// a single quote and a single prime make a single quote pair
		$string = self::rep('(' . self::OSQ_UNPAIRED . ')(.*?)(' . self::SINGLE_PRIME_MARK . ')', self::OSQ . '${2}' . self::CSQ, $string);
		$string = self::rep('(' . self::SINGLE_PRIME_MARK . ')(.*?)(' . self::CSQ_UNPAIRED . ')', self::OSQ . '${2}' . self::CSQ, $string);

		// residual apostrophes
		$string = self::rep("({$adepts})", $apos, $string);

		// place the Czech quotes
		$string = str_replace(self::SINGLE_PRIME_MARK, self::SINGLE_PRIME, $string);
		$string = str_replace([$apos, self::OSQ_UNPAIRED, self::CSQ_UNPAIRED], self::APOSTROPHE, $string);
		$string = str_replace(self::OSQ, self::OPENING_SINGLE_QUOTE, $string);
		$string = str_replace(self::CSQ, self::CLOSING_SINGLE_QUOTE, $string);

		// space around a single prime
		return self::rep("({$s})(" . self::SINGLE_PRIME . ')', '${2}', $string);
	}


	// ---- double quotes and primes ----

	private static function fixDoubleQuotesAndPrimes(string $string): string
	{
		$s = self::sp();
		$adepts = self::DOUBLE_QUOTE_ADEPTS;
		$romans = self::ROMAN_NUMERALS;

		// extra punctuation before and after quotes
		$string = self::rep("([^{$romans}])([" . self::SENTENCE_PUNCTUATION . "])([" . self::SENTENCE_PAUSE_PUNCTUATION . "])({$adepts})", '${1}${2}${4}', $string);
		$string = self::rep("([^{$romans}])([" . self::SENTENCE_PUNCTUATION . "])({$adepts})([" . self::SENTENCE_PUNCTUATION . '])', '${1}${2}${3}', $string);

		// inches, arcseconds, seconds
		$string = self::rep(
			"([^0-9]|^)({$adepts})(.+?)(\\d+)({$adepts})([" . self::TERMINAL_PUNCTUATION . self::ELLIPSIS . '])',
			'${1}${2}${3}${4}${6}${5}',
			$string,
		);
		$string = self::rep("(\\b\\d{1,3})({$s}?)({$adepts})([^\\p{L}]|\\B)", '${1}${2}' . self::DOUBLE_PRIME_MARK . '${4}', $string);

		// double quote pairs
		$string = self::rep("({$adepts})(\\d+)(" . self::DOUBLE_PRIME_MARK . ')', self::ODQ . '${2}' . self::CDQ, $string);
		$string = self::rep("({$adepts})(.*?)({$adepts})", self::ODQ . '${2}' . self::CDQ, $string);

		// unpaired double quotes
		$string = self::rep("({$adepts})([0-9\\p{L}])", self::ODQ_UNPAIRED . '${2}', $string);
		$string = self::rep('([\p{L}' . self::SENTENCE_PUNCTUATION . self::ELLIPSIS . "])({$adepts})", '${1}' . self::CDQ_UNPAIRED, $string);
		$string = self::rep("({$s})({$adepts})({$s})", '${1}', $string);

		// a double quote and a double prime make a double quote pair
		$string = self::rep('(' . self::ODQ_UNPAIRED . ')(.*?)(' . self::DOUBLE_PRIME_MARK . ')', self::ODQ . '${2}' . self::CDQ, $string);
		$string = self::rep('(' . self::DOUBLE_PRIME_MARK . ')(.*?)(' . self::CDQ_UNPAIRED . ')', self::ODQ . '${2}' . self::CDQ, $string);

		// place the Czech quotes
		$string = str_replace(self::DOUBLE_PRIME_MARK, self::DOUBLE_PRIME, $string);
		$string = str_replace([self::ODQ, self::ODQ_UNPAIRED], self::OPENING_DOUBLE_QUOTE, $string);
		$string = str_replace([self::CDQ, self::CDQ_UNPAIRED], self::CLOSING_DOUBLE_QUOTE, $string);

		// spaces around quotes and primes
		$string = self::rep('(' . self::OPENING_DOUBLE_QUOTE . ")({$s})", '${1}', $string);
		$string = self::rep("({$s})(" . self::CLOSING_DOUBLE_QUOTE . ')', '${2}', $string);
		$string = self::rep("({$s})(" . self::DOUBLE_PRIME . ')', '${2}', $string);
		$string = self::rep('([' . self::SENTENCE_PUNCTUATION . '\p{L}])([' . self::OPENING_DOUBLE_QUOTE . '])', '${1} ${2}', $string);
		$string = self::addNbspAfterPreposition($string);
		$string = self::rep('([' . self::CLOSING_DOUBLE_QUOTE . '])(\p{L})', '${1} ${2}', $string);

		return $string;
	}


	// ---- words ----

	/** Accidental uppercase: UPpercase and uPPERCASE */
	private static function fixCase(string $string): string
	{
		$string = self::cb(
			'([^\p{L}]|^)(\p{Lu}{2})(\p{Ll}{2,})',
			fn(array $m) => $m[1] . mb_substr($m[2], 0, 1) . mb_strtolower(mb_substr($m[2], 1)) . $m[3],
			$string,
		);

		return self::cb(
			'(\b)(?!iOS)(\p{Ll})(\p{Lu}{2,})',
			fn(array $m) => $m[1] . mb_strtoupper($m[2]) . mb_strtolower($m[3]),
			$string,
		);
	}


	private static function fixAbbreviations(string $string): string
	{
		$s = self::sp();
		$nbsp = self::NBSP;

		// initials: F. X. Šalda
		$initial = "(\\p{Lu}\\p{L}?\\.)({$s}?)";
		$fullName = '(\p{L}{2,}[^\.])';
		$string = self::rep($initial . $fullName, '${1}' . $nbsp . '${3}', $string);
		$string = self::rep($initial . $initial . $fullName, '${1}' . self::ABBREVIATION_SPACE . '${3} ${5}', $string);
		$string = self::rep(
			$initial . $initial . $initial . $fullName,
			'${1}' . self::ABBREVIATION_SPACE . '${3}' . self::ABBREVIATION_SPACE . '${5} ${7}',
			$string,
		);

		// multiple-word abbreviations: p. n. l.
		$before = '([^\p{L}' . self::EN_DASH . self::EM_DASH . ']|^)';
		foreach (self::MULTIPLE_WORD_ABBREVIATIONS as $abbreviation) {
			$words = explode(' ', $abbreviation);
			$pattern = '';
			foreach ($words as $word) {
				$pattern .= "({$word})(\\.)({$s}?)";
			}
			$count = count($words);

			$replacement = '${1}';
			for ($j = 0; $j < $count - 1; $j++) {
				$replacement .= '${' . ($j * 3 + 2) . '}.' . self::ABBREVIATION_SPACE;
			}
			$last = '${' . (($count - 1) * 3 + 2) . '}.';
			$following = '${' . ($count * 3 + 2) . '}';

			$string = self::rep($before . $pattern . '(\p{L}|\D)', $replacement . $last . ' ' . $following, $string, 'i');
			$string = self::rep(
				$before . $pattern . '([^\p{L}' . self::OPENING_DOUBLE_QUOTE . self::OPENING_SINGLE_QUOTE . self::BACKTICK . '\p{Emoji}]|$)',
				$replacement . $last . $following,
				$string,
				'i',
			);
		}

		// single-word abbreviations: č. 5, str. 12
		foreach (self::SINGLE_WORD_ABBREVIATIONS as $abbreviation) {
			$pattern = "({$abbreviation})(\\.)({$s}?)";
			$string = self::rep(
				'([^\p{L}' . self::EN_DASH . self::EM_DASH . self::NBSP . '\.]|^)' . $pattern . '([\p{L}\d]+)([^\.]|$)',
				'${1}${2}${3}' . $nbsp . '${5}${6}',
				$string,
				'i',
			);
		}
		foreach (self::SINGLE_WORD_ABBREVIATIONS as $abbreviation) {
			$pattern = "({$abbreviation})(\\.)({$s}?)";
			$string = self::rep(
				"([\\p{L}\\d])({$s})" . $pattern . "([^{$s}\\p{L}\\d]|$)",
				'${1}' . $nbsp . '${3}${4}${5}${6}',
				$string,
				'i',
			);
		}

		return $string;
	}


	// ---- non-breaking spaces ----

	private static function addNbspAfterPreposition(string $string): string
	{
		$s = self::sp();

		// one-letter prepositions, small letters anywhere in a sentence
		$string = self::repOverlap(
			'(^|[' . self::SPACE . ']|[^\p{L}\d' . self::APOSTROPHE . self::PLUS . self::MINUS . self::HYPHEN . '])(\p{Ll})([' . self::SPACE . '])',
			'${1}${2}' . self::NBSP,
			$string,
		);

		// capital letters at the beginning of a sentence
		return self::rep(
			'(^|[' . self::BLOCK_MARKERS . self::SENTENCE_PUNCTUATION . self::ELLIPSIS . self::COPYRIGHT . self::REGISTERED_TRADEMARK . self::SOUND_RECORDING_COPYRIGHT . "])({$s}?)(\\p{Lu})({$s})",
			'${1}${2}${3}' . self::NBSP,
			$string,
		);
	}


	private static function fixNbsp(string $string): string
	{
		$s = self::sp();
		$nbsp = self::NBSP;
		$romans = self::ROMAN_NUMERALS;
		$ordinal = self::ORDINAL_INDICATOR;

		// no non-breaking spaces between longer words
		$string = self::repOverlap('(\p{L}{2,})([' . self::NBSP . self::NARROW_NBSP . '])(\p{L}{2,})', '${1} ${3}', $string);

		$string = self::addNbspAfterPreposition($string);

		// ampersand
		$string = self::rep("({$s})(" . self::AMPERSAND . ")({$s})", ' ${2}' . $nbsp, $string);

		// cardinal numbers up to 99
		$string = self::rep("([^{$nbsp}\\d]|^)(\\d{1,2})({$s})(\\p{L})", '${1}${2}' . $nbsp . '${4}', $string);

		// ordinal numbers up to 99
		$string = self::rep("([^{$nbsp}\\d_%\\-]|^)(\\d{1,2})({$ordinal})({$s}?)(\\p{L})", '${1}${2}${3}' . $nbsp . '${5}', $string);

		// ordinal dates: 1. 1. 1993
		$string = self::rep(
			"(\\d{1,2})(\\.)({$s}?)(\\d{1,2})(\\.)({$s}?)(\\d{4})",
			'${1}${2}' . self::ORDINAL_DATE_SPACE . '${4}${5}' . self::ORDINAL_DATE_SPACE . '${7}',
			$string,
		);

		// roman numerals: X. ročník
		$string = self::cb(
			"(\\b\\p{Lu}\\p{L}?{$ordinal}{$s}?)?(\\b)([{$romans}]+)({$ordinal})({$s}?)([\\p{L}\\d])",
			fn(array $m) => $m[1] === '' ? $m[2] . $m[3] . $m[4] . $nbsp . $m[6] : $m[0],
			$string,
		);

		// a single capital letter: pán X
		$string = self::cb(
			'([^' . self::SENTENCE_PUNCTUATION . self::ELLIPSIS . self::CLOSING_BRACKETS . self::CLOSING_DOUBLE_QUOTE . self::CLOSING_SINGLE_QUOTE . self::APOSTROPHE . self::MULTIPLICATION_SIGN . self::EM_DASH . self::EN_DASH . "])({$s})(\\p{Lu})({$s}|\\.$|$)",
			function (array $m) use ($nbsp): string {
				[, $before, , $letter, $end] = $m;
				if ($letter === 'I' && $end !== '' && str_contains(self::SPACES, $end)) {
					return $before . $nbsp . $letter . self::SPACE;
				}
				return $before . $nbsp . $letter . $end;
			},
			$string,
		);

		// names with a regnal number: Karel IV.
		$string = self::cb(
			"(\\b\\p{Lu}\\p{Ll}+?)({$s})([{$romans}]+\\b)({$ordinal})([{$nbsp}]?)",
			function (array $m) use ($nbsp): string {
				[, $name, , $numeral, $indicator, $trailing] = $m;
				$hasTrailing = $trailing === $nbsp;
				if (str_starts_with($numeral, 'M') || str_starts_with($numeral, 'D') || str_starts_with($numeral, 'C')) {
					return $name . self::SPACE . $numeral . $indicator . $trailing;
				}
				if ($numeral === 'I') {
					return $name . self::SPACE . $numeral . $indicator . ($hasTrailing ? $trailing : '');
				}
				return $name . $nbsp . $numeral . $indicator . ($hasTrailing ? self::SPACE : '');
			},
			$string,
		);

		// percent: 12 %
		return self::rep(
			'(\d)(' . $s . ')([' . self::PERCENT . self::PERMILLE . self::PERMYRIAD . '])',
			'${1}' . self::PERCENT_SPACE . '${3}',
			$string,
		);
	}

}
