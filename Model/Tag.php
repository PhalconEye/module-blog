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
use Phalcon\Mvc\Model\Validator\Uniqueness;

/**
 * Tag model.
 *
 * @category  PhalconEye
 * @package   Blog\Module
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @method    static \Blog\Model\Tag|false findFirstByLabel($label) Find Tag by label
 *
 * @Source("blog_tags")
 * @HasMany("id", "\Blog\Model\PostTag", "tag_id", {
 *  "alias": "PostTags"
 * })
 */
class Tag extends AbstractModel
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="string", nullable=false, column="label", size="255")
     */
    public $label;

    public function initialize()
    {
        $this->hasManyToMany("id", '\Blog\Model\PostTag', "tag_id", "post_id", '\Blog\Model\Post', "id", [
            "alias" => "Posts"
        ]);
    }

    /**
     * Validations and business logic.
     *
     * @return bool
     */
    public function validation()
    {
        if ($this->_errorMessages === null) {
            $this->_errorMessages = [];
        }

        $this->validate(new Uniqueness(["field" => "label"]));

        return $this->validationHasFailed() !== true;
    }
}
