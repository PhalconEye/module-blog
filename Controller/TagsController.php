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

use Blog\Model\Tag;
use Core\Controller\AbstractAdminController;
use Phalcon\Mvc\Dispatcher\Exception;
use Phalcon\Mvc\Model\Query\Builder;
use User\Model\User;

/**
 * Admin Posts Controller.
 *
 * @category  PhalconEye
 * @package   Blog\Controller
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class TagsController extends AbstractAdminController
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
     * Tag view
     *
     * @Route("/blog/tag/{tag:[a-zA-Z0-9_|+ -]+}", methods={"GET"}, name="blog-tag")
     */
    public function indexAction($tag)
    {
        if (!$tag = Tag::findFirstByLabel($tag)) {
            throw new Exception;
        }

        $session = $this->getDI()->get('session');

        $builder = new Builder();
        $builder
            ->from('Blog\Model\Post')
            ->leftJoin('Blog\Model\PostTag', 'Blog\Model\Post.id = Blog\Model\PostTag.post_id')
            ->andWhere('Blog\Model\PostTag.tag_id = '. (int) $tag->id)
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

        $this->renderParts();
        $this->view->blogTag = $tag;
        $this->view->posts = $builder->getQuery()->execute();
    }

    /**
     * Search Tags
     *
     * @param string $tag Searched tag
     *
     * @return \Phalcon\Http\ResponseInterface|null
     *
     * @Get("/blog/tags/search", name="blog-tags-search")
     */
    public function searchAction()
    {
        $this->view->disable();
        $query = (string) $this->request->get('query');
        if (empty($query)) {
            $this->response->setContent('[]')->send();
            return;
        }

        $results = $this->modelsManager->createBuilder()
            ->columns(['label as id', 'label'])
            ->from('Blog\Model\Tag')
            ->where(
                "label LIKE :query:",
                ['query' => '%' . $query . '%'],
                ['query' => \PDO::PARAM_STR]
            )
            ->getQuery()
            ->execute();

        $this->response->setContent(json_encode($results->toArray()))->send();
    }
}
