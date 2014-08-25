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

namespace Blog\Controller\Grid;

use Core\Controller\Grid\CoreGrid;
use Engine\Form;
use Engine\Grid\GridItem;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\View;

/**
 * Post grid.
 *
 * @category  PhalconEye
 * @package   Blog\Controller\Grid
 * @author    Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PostsGrid extends CoreGrid
{
    /**
     * Get main select builder.
     *
     * @return Builder
     */
    public function getSource()
    {
        $builder = new Builder();
        $builder->columns(['id', 'title', 'slug', 'is_enabled', 'creation_date', 'modified_date']);
        $builder->from('Blog\Model\Post');

        return $builder;
    }

    /**
     * Get item action (Edit, Delete, etc).
     *
     * @param GridItem $item One item object.
     *
     * @return array
     */
    public function getItemActions(GridItem $item)
    {
        $actions = [
            'Edit'   => [
                'href' => ['for' => 'admin-blog-posts-edit', 'id' => $item['id']]
            ],
            'Delete' => [
                'href' => ['for' => 'admin-blog-posts-delete', 'id' => $item['id']],
                'attr' => ['class' => 'grid-action-delete']
            ],

        ];

        return $actions;
    }

    /**
     * Initialize grid columns.
     *
     * @return array
     */
    protected function _initColumns()
    {
        $url = $this->getDI()->get('url');

        $this
            ->addTextColumn('id', 'ID', [self::COLUMN_PARAM_TYPE => Column::BIND_PARAM_INT])
            ->addTextColumn(
                'title',
                'Title',
                [
                    self::COLUMN_PARAM_FILTER => false,
                    self::COLUMN_PARAM_OUTPUT_LOGIC =>
                        function (GridItem $item, $di) use ($url) {
                            return sprintf(
                                '<a href="%s" target="_blank">%s</a><br /><small>[%s]</small>',
                                $url->get(['for' => 'blog-post', 'slug' => $item['slug']]),
                                $item['title'],
                                $item['slug']
                            );
                        }
                ])
            ->addTextColumn('is_enabled', 'Enabled')
            ->addTextColumn('creation_date', 'Created')
            ->addTextColumn('modified_date', 'Updated');
    }
}