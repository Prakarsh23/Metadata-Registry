<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004 David Heinemeier Hansson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * TagHelper defines some base helpers to construct html tags.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     David Heinemeier Hansson
 * @version    SVN: $Id: TagHelper.php 415 2008-04-04 19:30:57Z jphipps $
 */

/**
 * Constructs an html tag.
 *
 * @param  $name    string  tag name
 * @param  $options array   tag options
 * @param  $open    boolean true to leave tag open
 * @return string
 */
function tag($name, $options = array(), $open = false)
{
  if (!$name)
  {
    return '';
  }

  return '<'.$name._tag_options($options).(($open) ? '>' : ' />');
}

function content_tag($name, $content = '', $options = array())
{
  if (!$name)
  {
    return '';
  }

  return '<'.$name._tag_options($options).'>'.$content.'</'.$name.'>';
}

function cdata_section($content)
{
  return "<![CDATA[$content]]>";
}

/**
 * Escape carrier returns and single and double quotes for Javascript segments.
 *
 * @param string $javascript
 *
 * @return mixed|string
 */
function escape_javascript($javascript = '')
{
  $javascript = preg_replace('/\r\n|\n|\r/', "\\n", $javascript);
  $javascript = preg_replace('/(["\'])/', '\\\\\1', $javascript);

  return $javascript;
}

/**
 * Escapes an HTML string.
 *
 * @param  string $html HTML string to escape
 * @return string escaped string
 */
function escape_once($html)
{
  return fix_double_escape(htmlspecialchars($html, ENT_COMPAT, sfConfig::get('sf_charset')));
}

/**
 * Fixes double escaped strings.
 *
 * @param  string $escaped HTML string to fix
 * @return string escaped string
 */
function fix_double_escape($escaped)
{
  return preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', $escaped);
}

/**
 * @param array $options
 *
 * @return string
 */
function _tag_options($options = array())
{
  $options = _parse_attributes($options);

  $html = '';
  foreach ($options as $key => $value)
  {
      if ( in_array($key, [ 'id', 'name', 'type', 'value' ]) || $key != $value) {
          $html .= ' ' . $key . '="' . escape_once($value) . '"';
      } else { //html5
          $html .= ' ' . $key;
      }
  }

  return $html;
}

/**
 * @param string $string
 *
 * @return array
 */
function _parse_attributes($string)
{
  return is_array($string) ? $string : sfToolkit::stringToArray($string);
}

/**
 * @param array|string $options
 * @param string $name
 * @param string|null $default
 *
 * @return mixed|null
 */
function _get_option(&$options, $name, $default = null)
{
  if (is_array($options) && array_key_exists($name, $options))
  {
    $value = $options[$name];
    unset($options[$name]);
  }
  else
  {
    $value = $default;
  }

  return $value;
}
