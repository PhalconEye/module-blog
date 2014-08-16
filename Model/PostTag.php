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

namespace Blog\Model;

use Engine\Db\AbstractModel;

/**
 * PostTag model.
 *
 * @category  PhalconEye
 * @package   Blog\Module
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("blog_posts_tags")
 * @BelongsTo("post_id", '\Blog\Model\Post', "id", {
 *  "alias": "Post"
 * })
 * @BelongsTo("tag_id", '\Blog\Model\Tag', "id", {
 *  "alias": "Tag"
 * })
 */
class PostTag extends AbstractModel
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="integer", nullable=false, column="post_id", size="11")
     */
    public $post_id;

    /**
     * @Column(type="integer", nullable=false, column="tag_id", size="11")
     */
    public $tag_id;
}
