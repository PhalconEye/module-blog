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
use Blog\Model\Tag;
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

        // Assets setup.
        $this->assets->addCss('assets/css/blog/admin.css');
        $this->assets->addJs('assets/js/blog/admin/tags.js');
    }

    /**
     * Module index action
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
     * @Route("/create", methods={"GET", "POST"}, name="admin-blog-posts-create")
     */
    public function createAction()
    {
        $this->view->form = $form = new PostForm();

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $post = $form->getEntity();
        $tags = [];

        // Attach related Tags
        $requestedTags = $this->request->get('tags');
        if ($requestedTags = array_filter($requestedTags)) {
            foreach ($requestedTags as $tagName) {
                if (!$tag = Tag::findFirstByLabel($tagName)) {
                    $tag = new Tag;
                    $tag->label = $tagName;
                }
                $tags[] = $tag;
            }
            $post->tags = $tags;
        }

        $this->_handleImageUpload($form);
        $post->save();
        $this->flashSession->success('Post added!');

        $this->response->redirect(['for' => 'admin-blog-posts-edit', 'id' => $post->id]);
    }

    /**
     * Edit post
     *
     * @param int $id Post identity
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
        $tags = [];

        // Attach related Tags
        $requestedTags = $this->request->get('tags');
        if ($requestedTags = array_filter($requestedTags)) {
            foreach ($requestedTags as $tagName) {
                if (!$tag = Tag::findFirstByLabel($tagName)) {
                    $tag = new Tag;
                    $tag->label = $tagName;
                }
                $tags[] = $tag;
            }
            $post->tags = $tags;
        }

        $this->_handleImageUpload($form);

        $post->update();
        $this->flash->success('Post updated!');
    }

    /**
     * Delete post.
     *
     * @param int $id Post identity
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

    /**
     * Handles upload of post image
     *
     * @param PostForm $form Instance
     *
     * todo: Abstract PhalconEye filesystem (eg. public/files folder)
     */
    private function _handleImageUpload(PostForm $form)
    {
        /** @var Post $post */
        $post = $form->getEntity();
        $hasThumbnail = $form->hasFiles('thumbnail');

        if (file_exists(Post::THUMBNAIL_PATH) == false) {
            mkdir(Post::THUMBNAIL_PATH, 0777, true);
        }

        if ($form->hasFiles('image')) {
            $file = $form->getFiles('image');
            $fileName = md5(mt_rand()) . '.' . pathinfo($file->getName(), PATHINFO_EXTENSION);
            $target = Post::IMAGE_PATH . '/' . $fileName;

            // Create thumbnail
            if (move_uploaded_file($file->getTempName(),  $target)) {
                $post->image = $target;
                $form->setValue('image', $target);
                if ($hasThumbnail == false && $post->createThumbnail()) {
                    $form->setValue('thumbnail', Post::THUMBNAIL_PATH .'/'. '/' . $fileName);
                }
            }
        }

        if ($hasThumbnail) {
            $file = $form->getFiles('thumbnail');
            $fileName = md5(mt_rand()) . '.' . pathinfo($file->getName(), PATHINFO_EXTENSION);
            $target = Post::THUMBNAIL_PATH . '/' . $fileName;
            if (move_uploaded_file($file->getTempName(), $target)) {
                $form->setValue('thumbnail', $target);
                $post->thumbnail = $target;
            }
        }
    }
}
