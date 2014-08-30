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

use Engine\Form\Element\Select;
use Blog\Model\Category;

/**
 * Categories field
 *
 * @category  PhalconEye
 * @package   Blog\Form
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Categories extends Select
{
    const
        /**
         * Will be used to indicate level of indention
         */
        INDENTION_STRING = '...';

    /**
     * {@inheritdoc}
     */
    public function __construct($name, array $options = [], array $attributes = [])
    {
        parent::__construct($name, $options, $attributes);

        if (!isset($options['elementOptions'])) {
            $filter = ['columns' => ['id', 'parent_id', 'title']];
            $this->setOption('elementOptions', Category::find($filter)->toArray());
        }

        $this->_indentCategoryNames();
    }

    /**
     * Models categories into a single-dimensional array indicating nested categories
     */
    protected function _indentCategoryNames()
    {
        $categories = $tree = $result = [];

        // This approach may be procedural, but it is to avoid highly nested loops
        foreach ($this->_options['elementOptions'] as $category) {
            $category['children'] = [];
            $category['level'] = 0;
            $categories[$category['id']] = $category;
        }

        foreach ($categories as &$category) {
            if ($category['parent_id']) {
                $category['level']++;
                $categories[$category['parent_id']]['children'][] = &$category;
            } else {
                $tree[] = &$category;
            }
        }

        foreach ($categories as $category) {
            foreach ($category['children'] as $child) {
                $categories[$child['id']]['level'] = $child['level'] + $category['level'];
            }
        }

        array_walk_recursive($tree, function(&$id, $key) use($categories, &$result) {
            if ($key === 'id') {
                if ($categories[$id]['level'] > 0) {
                    $prefix = str_repeat(static::INDENTION_STRING, $categories[$id]['level']);
                    $result[$id] = $prefix .' '. $categories[$id]['title'];
                } else {
                    $result[$id] = $categories[$id]['title'];
                }
            }
        });

        $this->_options['elementOptions'] = $result;
    }
}
