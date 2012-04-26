<?php
/*
 * This file is part of the sfPropelActAsTaggableBehavior package.
 *
 * (c) 2007 Xavier Lacot <xavier@lacot.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generates a tag cloud from a tags array.
 *
 * @param   array   $tags     The tags array. This array must associate the tags
 * to their weight. The metgod TaggableToolkit::normalize() is here
 * to help generate such an array
 *
 * @param   mixed   $route    The route name to be used in the tag. If using the&
 * link_to_remote function, this parameter acts as the remote options array
 *
 * @param   array   $options  Optionnal options array for formating the link.
 * Supported keys are:
 *  * class:  html class to be applied to the tags list
 *  * link_function: custom function to be used for generating each tag link
 *  * link_options: html options to be applied to the generated link
 */
function simple_tag_cloud($tags, $options = array())
{
  $result = '';

  if(count($tags) > 0)
  {
    $emphasizers_begin = "";
    $emphasizers_end = "";

    $class = isset($options['class']) ? $options['class'] : 'tag-cloud';
    $result = '<div class="'.$class.'">';
    $overall = count($tags);
    $actual = 0;

    foreach ($tags as $name => $count)
    {
      $actual++;
      $emphasizers_begin = "";
      $emphasizers_end = "";

      for($i = 1; $i <= $count; $i++) {
        $emphasizers_begin .= "<big>";
        $emphasizers_end .= "</big>";
      }

      $result .= $emphasizers_begin.$name.$emphasizers_end;

      if ($overall > $actual) {
        $result .= ",";
      }

      $result .= " ";
    }

    $result .= '</div>';
  }

  return $result;
}

function simple_tag_list($tags, $options = array())
{
  $result = '';

  if (count($tags) > 0)
  {
    $class = isset($options['class']) ? $options['class'] : 'tags-list';

    if (isset($options['ordered']))
    {
      $result = '<ol class="'.$class.'">';
    }
    else
    {
      $result = '<ul class="'.$class.'">';
    }

    $i = 1;

    foreach ($tags as $tag)
    {
      $result .= '
                  <li>'.$tag;
      if (isset($options['separator']) && ($i != count($tags)))
      {
        $result .= $options['separator'];
      }

      $result .= '</li>';
      $i++;
    }

    if (isset($options['ordered']))
    {
      $result .= '</ol>';
    }
    else
    {
      $result .= '</ul>';
    }
  }

  return $result;
}