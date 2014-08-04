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

use Blog\Navigation\AdminNavigation;
use Core\Controller\AbstractAdminController;
use Blog\Form\CategoryForm;
use Blog\Model\Category;

/**
 * Admin Categories Controller.
 *
 * @category  PhalconEye
 * @package   Blog\Controller
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/module/blog/categories")
 */
class AdminCategoriesController extends AbstractAdminController
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
     * @Route("/browse{params:([\0-9]+)*}", methods={"GET"}, name="admin-blog")
     */
    public function indexAction()
    {
        // Set proper order.
        $orderData = [
            'order' => 'item_order ASC'
        ];

        $parent_id = null;
        $ancestors = [];
        if (func_num_args()) {
            // Get ancestors ids
            $ancestors = func_get_args();
            $parent_id = end($ancestors);
            $orderData[] = "parent_id = {$parent_id}";

            // Get ancestors data
            $parentCategory = Category::findFirst($parent_id);
            $ancestors = [$parentCategory];
            while ($parentCategory = $parentCategory->getParent()) {
                $ancestors[] = $parentCategory;
            }

        } else {
            $orderData[] = "parent_id IS NULL";
        }

        $this->view->parent_id = $parent_id;
        $this->view->ancestors = array_reverse($ancestors);
        $this->view->categories = Category::find($orderData);
    }

    /**
     * Create Category
     *
     * @return \Phalcon\Http\ResponseInterface|null
     *
     * @Route("/create/{parent_id:[0-9]*}", methods={"GET", "POST"}, name="admin-blog-categories-create")
     */
    public function createAction($parent_id = null)
    {
        $form = new CategoryForm;
        $this->view->form = $form;

        $form->setValues(['parent_id' => $parent_id]);
        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $item = $form->getEntity();

        // Set proper order.
        $orderData = [
            'order' => 'item_order DESC'
        ];

        if (!empty($parent_id)) {
            $orderData[] = "parent_id = {$parent_id}";
        }

        $orderItem = Category::findFirst($orderData);

        if ($orderItem->id != $item->id) {
            $item->item_order = $orderItem->item_order + 1;
        }

        $item->save();
        $this->resolveModal(['reload' => true]);
    }

    /**
     * Edit Category
     *
     * @param int $id Category identity
     *
     * @return \Phalcon\Http\ResponseInterface|null
     *
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-blog-categories-edit")
     */
    public function editAction($id)
    {
        $item = Category::findFirst($id);
        if (!$item) {
            return $this->response->redirect(['for' => "admin-blog"]);
        }

        $form = new CategoryForm($item);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $item = $form->getEntity();
        $item->save();
        $this->resolveModal(['reload' => true]);
    }

    /**
     * Delete category.
     *
     * @param int $id Category identity
     *
     * @return \Phalcon\Http\ResponseInterface|null
     *
     * @Get("/delete/{id:[0-9]+}", name="admin-blog-categories-delete")
     */
    public function deleteAction($id)
    {
        $this->view->disable();
        if ($category = Category::findFirst($id)) {
            $category->delete();
            $this->flashSession->success('Post deleted!');
        }

        return $this->response->redirect(['for' => "admin-blog"]);
    }

    /**
     * Order category.
     *
     * @param int $id Category identity
     *
     * @return \Phalcon\Http\ResponseInterface|null
     *
     * @Post("/order", name="admin-blog-categories-order")
     */
    public function orderAction()
    {
        $order = $this->request->get('order', null, []);
        foreach ($order as $index => $id) {
            $this->db->update(Category::getTableName(), ['item_order'], [$index], "id = {$id}");
        }
        $this->view->disable();
    }
}
