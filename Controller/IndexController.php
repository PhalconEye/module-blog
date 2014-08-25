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
use Phalcon\Mvc\Dispatcher\Exception;
use Phalcon\Mvc\Model\Query\Builder;
use User\Model\User;

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
     * @{inheritdoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->assets->addCss('assets/css/blog/site.css');
    }

    /**
     * Module index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="blog")
     */
    public function indexAction()
    {
        $session = $this->getDI()->get('session');

        $builder = new Builder();
        $builder
            ->from('Blog\Model\Post')
            ->orderBy('creation_date DESC');

        // Apply language restrictions
        if ($session->has('language')) {
            $builder
                ->andWhere('languages IS NULL')
                ->orWhere(
                    'languages LIKE :language:', [
                        'language' => '%"'. $session->get('language') .'"%'
                    ]
                );
            ;
        }

        // Show all articles to Admins otherwise only enabled
        if (false == User::getViewer()->isAdmin()) {
            $builder->andWhere('is_enabled = 1');
        }
        // echo $builder->getPhql(); exit;

        $this->renderParts();
        $this->view->posts = $posts = $builder->getQuery()->execute();
    }

    /**
     * Post view.
     *
     * @return void
     *
     * @Route("/{slug:[a-zA-Z0-9\/_|+ -]+}", methods={"GET"}, name="blog-post")
     */
    public function postAction($slug)
    {
        // Article not found
        if (!$post = Post::findFirstBySlug($slug)) {
            throw new Exception;
        }

        // Article not enabled
        if (!$post->is_enabled && !User::getViewer()->isAdmin()) {
            throw new Exception;
        }

        $this->renderParts();
        $this->view->post = $post;
    }
}
