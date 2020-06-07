<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Nette\Loaders;

use Nette;


/**
 * Nette auto loader is responsible for loading Nette classes and interfaces.
 *
 * @author     David Grudl
 */
class NetteLoader extends AutoLoader
{
	/** @var NetteLoader */
	private static $instance;

	/** @var array */
	public $renamed = [];

	/** @var array */
	public $list = [
		'NetteModule\MicroPresenter' => '/Application/MicroPresenter',
		'Nette\Application\AbortException' => '/Application/exceptions',
		'Nette\Application\ApplicationException' => '/Application/exceptions',
		'Nette\Application\BadRequestException' => '/Application/exceptions',
		'Nette\Application\ForbiddenRequestException' => '/Application/exceptions',
		'Nette\Application\InvalidPresenterException' => '/Application/exceptions',
		'Nette\Callback' => '/common/Callback',
		'Nette\Environment' => '/common/Environment',
		'Nette\FatalErrorException' => '/common/exceptions',
    ];


	/**
	 * Returns singleton instance with lazy instantiation.
	 * @return NetteLoader
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			self::$instance = new static;
		}
		return self::$instance;
	}


	/**
	 * Handles autoloading of classes or interfaces.
	 * @param  string
	 * @return void
	 */
	public function tryLoad($type)
	{
		$type = ltrim($type, '\\');
		if (isset($this->renamed[$type])) {
			class_alias($this->renamed[$type], $type);
			trigger_error("Class $type has been renamed to {$this->renamed[$type]}.", E_USER_WARNING);

		} elseif (isset($this->list[$type])) {
			Nette\Utils\LimitedScope::load(NETTE_DIR . $this->list[$type] . '.php', TRUE);
			self::$count++;

		} elseif (substr($type, 0, 6) === 'Nette\\' && is_file($file = NETTE_DIR . strtr(substr($type, 5), '\\', '/') . '.php')) {
			Nette\Utils\LimitedScope::load($file, TRUE);
			self::$count++;
		}
	}
}
