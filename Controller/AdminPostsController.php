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

use Blog\Controller\Grid\PostsGrid;
use Blog\Form\PostForm;
use Blog\Model\Post;
use Blog\Navigation\AdminNavigation;
use Core\Controller\AbstractAdminController;

/**
 * Admin Posts Controller.
 *
 * @category  PhalconEye
 * @package   Blog\Controller
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/module/blog/posts")
 */
class AdminPostsController extends AbstractAdminController
{
    /**
     * Initialize
     */
    public function initialize()
    {
        parent::initialize();
        $this->view->navigation = new AdminNavigation;
    }

    /**
     * Module index action
     *
     * @return \Phalcon\Http\ResponseInterface|null
     *
     * @Route("/browse{params:([\0-9]+)*}", methods={"GET"}, name="admin-blog-posts")
     */
    public function indexAction()
    {
        $grid = new PostsGrid($this->view);
        if ($response = $grid->getResponse()) {
            return $response;
        }
    }

    /**
     * Create post
     *
     * @return \Phalcon\Http\ResponseInterface|null
     *
     * @Route("/create", methods={"GET", "POST"}, name="admin-blog-posts-create")
     */
    public function createAction()
    {
        $this->view->form = $form = new PostForm();

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $post = $form->getEntity();
        $post->create();
        $this->flashSession->success('Post added!');

        $this->response->redirect(['for' => 'admin-blog-posts-edit', 'id' => $post->id]);
    }

    /**
     * Edit post
     *
     * @param int $id Post identity
     *
     * @return \Phalcon\Http\ResponseInterface|null
     *
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-blog-posts-edit")
     */
    public function editAction($id)
    {
        if (!$post = Post::findFirst($id)) {
            return $this->response->redirect(['for' => 'admin-blog-posts']);
        }

        $this->view->post = $post;
        $this->view->form = $form = new PostForm($post);

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $post = $form->getEntity();
        $post->update();
        $this->flash->success('Post updated!');
    }

    /**
     * Delete post.
     *
     * @param int $id Post identity
     *
     * @return \Phalcon\Http\ResponseInterface|null
     *
     * @Get("/delete/{id:[0-9]+}", name="admin-blog-posts-delete")
     */
    public function deleteAction($id)
    {
        $this->view->disable();
        if ($post = Post::findFirst($id)) {
            $post->delete();
            $this->flashSession->success('Category deleted!');
        }

        $this->response->redirect(['for' => 'admin-blog-posts']);
    }
}
