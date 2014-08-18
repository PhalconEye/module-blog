<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Blog\Form\Element;

use Engine\Form\AbstractElement;

/**
 * Tags field
 *
 * @category  PhalconEye
 * @package   Blog\Form
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Tags extends AbstractElement
{
    const
        /**
         * Default UL class
         */
        UL_CLASS = 'blog-tags';

    /**
     * Element attributes.
     *
     * @var array
     */
    protected $_attributes = [
        "id" => "search-field",
        "class" => "ui-autocomplete-input",
        "autocomplete" => "off",
        "role" => "textbox",
        "aria-autocomplete" => "list",
        "aria-haspopup" => "true"
    ];

    /**
     * Get element html template.
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        $tmpl = <<<TAGS
        <ul class="%s">
            %s
            <li class="tagAdd">
                <input type="text" %s/>
            </li>
            <li class="clr"></li>
        </ul>
TAGS;

        return $this->getOption('htmlTemplate', $tmpl);
    }

    /**
     * Get element html template values
     *
     * @return array
     */
    public function getHtmlTemplateValues()
    {
        $content = '';

        if ($values = $this->getValue()) {
            foreach ($values as $value) {
                if ($value = htmlspecialchars($value)) {
                    $content .= sprintf(
                        '<li class="addedTag">%s<span class="tagRemove">x</span><input type="hidden" name="%s" value="%s"></li>',
                        $value,
                        $this->getName(),
                        $value
                    );
                }
            }
        }

        return [static::UL_CLASS, $content, $this->_renderAttributes()];
    }
}