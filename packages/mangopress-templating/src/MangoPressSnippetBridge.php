<?php

class MangoPressSnippetBridge implements Latte\Runtime\ISnippetBridge
{
	public $snippetMode = false;
	public $payload = [];
	private $redrawn = [];

	public function isSnippetMode()
	{
		return $this->snippetMode;
	}

	public function setSnippetMode($snippetMode)
	{
		$this->snippetMode = $snippetMode;
	}

	public function needsRedraw($name)
	{
		return empty($this->redrawn[$name]);
	}

	public function markRedrawn($name)
	{
		$this->redrawn[$name] = true;
	}

	public function getHtmlId($name)
	{
		return $name;
	}

	public function addSnippet($name, $content)
	{
		$this->payload[$name] = $content;
	}

	public function renderChildren()
	{
	}

}
