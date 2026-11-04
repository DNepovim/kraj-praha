<?php

use Latte\PhpWriter,
Latte\Macros\MacroSet,
Latte\MacroNode;

use Nette\Utils\Strings;

class MangoPressTemplatingMacroSet extends MacroSet {

	public static $set = [];

	public static $defaultEachVarName = 'Post';
	public static $defaultEachArrayName = '$GLOBALS["wp_query"]';

	public static function install(Latte\Compiler $compiler) {
		$me = new static($compiler);

		$me->addMacro('loop', [$me, 'macroLoop'], [$me, 'macroLoopEnd']);
		$me->addMacro('each', '', [$me, 'macroEachEnd']);
		$me->addMacro('repeat', [$me, 'macroRepeat'], [$me, 'macroRepeatEnd']);
		$me->addMacro('set', [$me, 'macroSet']);

		foreach (self::$set as $name => $macro) {
			if(is_callable($macro)) {
				$me->addMacro($name, $macro);
			} else {
				$me->addMacro($name,  ...$macro);
			}
		}
	}

	public function macroLoop(MacroNode $node, PhpWriter $writer) {
		$query = empty($node->args) ? self::$defaultEachArrayName : $node->args;
		return $writer->write('@$Posts[]=$' . self::$defaultEachVarName . '; while('.$query.'->have_posts()){ '.$query.'->the_post();$' . self::$defaultEachVarName . '='.$query.'->post;');
	}

	public function macroLoopEnd(MacroNode $node, PhpWriter $writer) {
		return $writer->write('}wp_reset_postdata(); $' . self::$defaultEachVarName . '=array_pop($Posts)');
	}

	public function macroEachEnd(MacroNode $node, PhpWriter $writer) {
		if ($node->modifiers && $node->modifiers !== '|noiterator') {
			trigger_error('Only modifier |noiterator is allowed here.', E_USER_WARNING);
		}

		$args = $writer->formatArgs();

		if(substr($args, 0, 3) === 'as ') {
			$parts = [ self::$defaultEachArrayName, substr($args, 3) ];
		} elseif(empty($args)) {
			$parts = [ self::$defaultEachArrayName ];
		} else {
			$parts = Strings::split($args, '#\s+as\s+#i');
		}

		if(count($parts) === 1) {
			$parts[] = '$' . self::$defaultEachVarName;
		}

		$array = $parts[0];
		$parts[0] = '(($_l_q instanceof WP_Query)?$_l_q->get_posts():(array)$_l_q)';
		$args = implode(' as ', $parts);

		if ($node->modifiers !== '|noiterator' && preg_match('#\W(\$iterator|include|require|get_defined_vars)\W#', $this->getCompiler()->expandTokens($node->content))) {
			$node->openingCode = '<?php $iterations = 0; $_l_q=' . $array . '; foreach ($iterator = $__its[] = new Latte\Runtime\CachingIterator('
			. preg_replace('#(.*)\s+as\s+#i', '$1) as ', $args, 1) . ') { ?>';
			$node->closingCode = '<?php $iterations++; } array_pop($__its); $iterator = end($__its) ?>';
		} else {
			$node->openingCode = '<?php $iterations = 0; $_l_q=' . $array . '; foreach (' . $args . ') { ?>';
			$node->closingCode = '<?php $iterations++; } ?>';
		}
	}

	public function macroRepeat(MacroNode $node, PhpWriter $writer) {
		$args = explode(',', empty($node->args) ? '5' : $node->args);
		$args = array_map('intval', $args);
		$min = min($args);
		$max = max($args);

		return $writer->write('@$_repeats[]=$repeated; foreach(range(1, rand('.$min.', '.$max.')) as $repeated){');
	}

	public function macroRepeatEnd(MacroNode $node, PhpWriter $writer) {
		return $writer->write('}$repeated=array_pop($_repeats)');
	}

	public function macroSet(MacroNode $node, PhpWriter $writer) {
		$parts = Strings::replace($node->args, '~(\\s*(=>|=)\\s*|\\s+)~', '~~~', 1);
		$parts = Strings::split($parts, '/~~~/');
		$variable = $parts[0];
		$rest = $parts[1];
		return $writer->write($variable . ' = %modify(' . $rest . ')');
	}

}

// alias
class MangoMacros extends MangoPressTemplatingMacroSet {}
