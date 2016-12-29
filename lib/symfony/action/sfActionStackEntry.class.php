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
 * sfActionStackEntry represents information relating to a single sfAction request during a single HTTP request.
 *
 * @package    symfony
 * @subpackage action
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfActionStackEntry.class.php 415 2008-04-04 19:30:57Z jphipps $
 */
class sfActionStackEntry
{
  protected
    $actionInstance = null,
    $actionName     = null,
    $moduleName     = null,
    $presentation   = null,
    $viewInstance   = null;

  /**
   * Class constructor.
   *
   * @param string A module name
   * @param string An action name
   * @param sfAction An sfAction implementation instance
   */
  public function __construct($moduleName, $actionName, $actionInstance)
  {
    $this->actionName     = $actionName;
    $this->actionInstance = $actionInstance;
    $this->moduleName     = $moduleName;
  }

  /**
   * Retrieves this entry's action name.
   *
   * @return string An action name
   */
  public function getActionName()
  {
    return $this->actionName;
  }

  /**
   * Retrieves this entry's action instance.
   *
   * @return sfAction An sfAction implementation instance
   */
  public function getActionInstance()
  {
    return $this->actionInstance;
  }

  /**
   * Retrieves this entry's view instance.
   *
   * @return sfView A sfView implementation instance.
   */
  public function getViewInstance()
  {
    return $this->viewInstance;
  }

  /**
   * Sets this entry's view instance.
   *
   * @param sfView A sfView implementation instance.
   */
  public function setViewInstance($viewInstance)
  {
    $this->viewInstance = $viewInstance;
  }

  /**
   * Retrieves this entry's module name.
   *
   * @return string A module name
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }

  /**
   * Retrieves this entry's rendered view presentation.
   *
   * This will only exist if the view has processed and the render mode is set to sfView::RENDER_VAR.
   *
   * @return string Rendered view presentation
   */
  public function & getPresentation()
  {
    return $this->presentation;
  }

  /**
   * Sets the rendered presentation for this action.
   *
   * @param string A rendered presentation.
   */
  public function setPresentation(&$presentation)
  {
    $this->presentation =& $presentation;
  }
}