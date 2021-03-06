<?php

/**
 * Nette Framework
 *
 * Copyright (c) 2004, 2009 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license" that is bundled
 * with this package in the file license.txt.
 *
 * For more information please see http://nettephp.com
 *
 * @copyright  Copyright (c) 2004, 2009 David Grudl
 * @license    http://nettephp.com/license  Nette license
 * @link       http://nettephp.com
 * @category   Nette
 * @package    Nette\Forms
 * @version    $Id: Button.php 182 2008-12-31 00:28:33Z david@grudl.com $
 */



require_once dirname(__FILE__) . '/../../Forms/Controls/FormControl.php';



/**
 * Push button control with no default behavior.
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2004, 2009 David Grudl
 * @package    Nette\Forms
 */
class Button extends FormControl
{

	/**
	 * @param  string  caption
	 */
	public function __construct($caption)
	{
		parent::__construct($caption);
		$this->control->type = 'button';
		$this->value = FALSE;
	}



	/**
	 * Bypasses label generation.
	 * @return void
	 */
	public function getLabel()
	{
		return NULL;
	}



	/**
	 * Sets 'pressed' indicator.
	 * @param  bool
	 * @return void
	 */
	public function setValue($value)
	{
		$this->value = is_scalar($value) ? (bool) $value : FALSE;
	}



	/**
	 * Generates control's HTML element.
	 * @return Html
	 */
	public function getControl()
	{
		$control = parent::getControl();
		$control->value = $this->translate($this->caption);
		return $control;
	}

}
