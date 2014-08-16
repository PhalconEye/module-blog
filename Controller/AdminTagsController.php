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
 * @RoutePrefix("/admin/module/blog/tags")
 */
class AdminTagsController extends AbstractAdminController
{
    /**
     * Search Tags
     *
     * @param string $tag Searched tag
     *
     * @return \Phalcon\Http\ResponseInterface|null
     *
     * @Get("/search", name="admin-blog-tags-search")
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
