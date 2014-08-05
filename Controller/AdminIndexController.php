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

namespace Blog\Controller;

use Core\Controller\AbstractAdminController;
use Blog\Navigation\AdminNavigation;

/**
 * Admin Index Controller.
 *
 * @category  PhalconEye
 * @package   Blog\Controller
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/module/blog")
 */
class AdminIndexController extends AbstractAdminController
{
    /**
     * @{inheritdoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->view->navigation = new AdminNavigation;
    }

    /**
     * Module index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="admin-blog")
     */
    public function indexAction()
    {
    }
}
