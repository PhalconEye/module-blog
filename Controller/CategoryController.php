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

use Blog\Model\Category;
use Core\Controller\AbstractController;
use Phalcon\Mvc\Dispatcher\Exception;
use Phalcon\Mvc\Model\Query\Builder;
use User\Model\User;

/**
 * Category controller.
 *
 * @category  PhalconEye
 * @package   Blog\Controller
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/blog/category")
 */
class CategoryController extends AbstractController
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
     * Category view
     *
     * @Route("/{slug:[a-zA-Z0-9_|+ -]+}", methods={"GET"}, name="blog-category")
     */
    public function indexAction($slug)
    {
        if (!$category = Category::findFirstBySlug($slug)) {
            throw new Exception;
        }

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

        // Categories scope
        $categories = $category->getNestedCategoriesIds();
        $categories[] = $category->id;
        $builder->andWhere('category_id IN ('. implode(',', $categories) .')');

        // Parent Categories
        $parentCategories = [];
        $parentCategory = $category->getParent();
        while ($parentCategory) {
            $parentCategories[$parentCategory->slug] = $parentCategory->title;
            $parentCategory = $parentCategory->getParent();
        }

        $this->renderParts();
        $this->view->parentCategories = array_reverse($parentCategories);
        $this->view->category = $category;
        $this->view->posts = $builder->getQuery()->execute();
    }
}
