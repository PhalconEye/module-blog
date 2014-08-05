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
*/

namespace Blog\Controller;

use Blog\Model\Post;
use Core\Controller\AbstractController;

/**
 * Index controller.
 *
 * @category PhalconEye\Module
 * @package  Controller
 *
 * @RoutePrefix("/blog", name="blogs")
 */
class IndexController extends AbstractController
{
    /**
     * Module index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="blog")
     */
    public function indexAction()
    {
        $this->renderParts();

        $this->view->posts = Post::find([
            'is_enabled = 1',
            'order' => 'creation_date DESC'
        ]);
    }
}
