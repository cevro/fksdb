<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Nette\Config\Adapters;

use Nette;


/**
 * Reading and generating PHP files.
 *
 * @author     David Grudl
 */
class PhpAdapter implements Nette\Config\IAdapter
{
    use Nette\SmartObject;

	/**
	 * Reads configuration from PHP file.
	 * @param  string  file name
	 * @return array
	 */
	public function load($file)
	{
		return Nette\Utils\LimitedScope::load($file);
	}


	/**
	 * Generates configuration in PHP format.
	 * @return string
	 */
	public function dump(array $data)
	{
		return "<?php // generated by Nette \nreturn " . Nette\Utils\PhpGenerator\Helpers::dump($data) . ';';
	}

}
