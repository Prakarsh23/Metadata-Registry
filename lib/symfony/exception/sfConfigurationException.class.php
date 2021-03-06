<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr <sean@code-box.org>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfConfigurationException is thrown when the framework finds an error in a
 * configuration setting.
 *
 * @package    symfony
 * @subpackage exception
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfConfigurationException.class.php 415 2008-04-04 19:30:57Z jphipps $
 */
class sfConfigurationException extends sfException
{
  /**
   * Class constructor.
   *
   * @param string $message The error message
   * @param int $code       The error code
   */
  public function __construct($message = null, $code = 0)
  {
    $this->setName('sfConfigurationException');
    parent::__construct($message, $code);
  }
}
