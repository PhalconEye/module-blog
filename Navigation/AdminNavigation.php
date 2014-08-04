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

namespace Blog\Navigation;

use Core\Navigation\CoreNavigation;

/**
 * Admin Navigation.
 *
 * @category  PhalconEye
 * @package   Blog\Navigation
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class AdminNavigation extends CoreNavigation
{
    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $this->setItems([
            ['Posts', 'admin/module/blog/posts/browse', [
                'prepend' => '<i class="glyphicon glyphicon-file"></i>'
            ]],
            ['Create new', 'admin/module/blog/posts/create', [
                'prepend' => '<i class="glyphicon glyphicon-add"></i>'
            ]],
            null,
            ['Categories', 'admin/module/blog/categories/browse', [
                'prepend' => '<i class="glyphicon glyphicon-list"></i>'
            ]],
            ['Options', 'admin/module/blog', [
                'prepend' => '<i class="glyphicon glyphicon-cog"></i>'
            ]],
        ]);
    }
}
