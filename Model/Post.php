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
use Engine\Db\Model\Behavior\Sluggable;
use Engine\Db\Model\Behavior\Timestampable;
use Phalcon\Mvc\Model\Validator\Uniqueness;

/**
 * Post model.
 *
 * @category  PhalconEye
 * @package   Blog\Module
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      i://phalconeye.com/
 *
 * @Source("blog_posts")
 * @BelongsTo("category_id", '\Blog\Model\Category', "id", {
 *  "alias": "Category"
 * })
 * @HasMany("id", "\Blog\Model\PostTag", "post_id", {
 *  "alias": "PostTags"
 * })
 */
class Post extends AbstractModel
{
    use Sluggable, Timestampable {
        Sluggable::beforeCreate as protected _beforeCreateSluggable;
        Sluggable::beforeUpdate as protected _beforeUpdateSluggable;
        Timestampable::beforeCreate as protected _beforeCreateTimestampable;
        Timestampable::beforeUpdate as protected _beforeUpdateTimestampable;
    }

    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="string", nullable=false, column="title", size="255")
     */
    public $title;

    /**
     * @Column(type="text", nullable=false, column="description")
     */
    public $description;

    /**
     * @Column(type="text", nullable=false, column="text")
     */
    public $text;

    /**
     * @Column(type="integer", nullable=true, column="category_id", size="11")
     */
    public $category_id = null;

    /**
     * @Column(type="string", nullable=true, column="languages", size="150")
     */
    public $languages = null;

    /**
     * @Column(type="boolean", column="is_enabled")
     */
    public $is_enabled = true;

    public function initialize()
    {
        $this->hasManyToMany("id", '\Blog\Model\PostTag', "post_id", "tag_id", '\Blog\Model\Tag', "id", [
            "alias" => "Tags"
        ]);
    }

    /**
     * Get languages.
     *
     * @return string
     */
    public function getLanguages()
    {
        if (!is_array($this->languages)) {
            $this->prepareLanguages();
        }

        return $this->languages;
    }

    /**
     * Prepare json encoded languages.
     */
    public function prepareLanguages()
    {
        if (!is_array($this->languages)) {
            $this->languages = json_decode($this->languages);
        }
    }

    /**
     * Check if it's ok to show the post.
     *
     * @return bool
     */
    public function isAllowed()
    {
        $valid = true;
        $language = $this->getDI()->get('session')->get('language');
        $languages = $this->getLanguages();

        if (!empty($languages)) {
            $valid = in_array($language, $languages);
        }

        return $valid;
    }

    /**
     * Spell some logic after fetching.
     */
    protected function afterFetch()
    {
        if (!empty($this->languages)) {
            $this->languages = json_decode($this->languages);
        }
    }

    /**
     * Logic before save.
     */
    protected function beforeSave()
    {
        // Encode languages
        if (empty($this->languages)) {
            $this->languages = null;
        } elseif (is_array($this->languages)) {
            $this->languages = json_encode($this->languages);
        }
    }

    /**
     * Before entity creation.
     *
     * @return void
     */
    public function beforeCreate()
    {
        $this->_beforeCreateSluggable();
        $this->_beforeCreateTimestampable();
    }

    /**
     * Before entity update.
     *
     * @return void
     */
    public function beforeUpdate()
    {
        // Remove PostTags relations
        if ($this->id) {
            $this->getRelated('PostTags')->delete();
        }

        $this->_beforeUpdateSluggable();
        $this->_beforeUpdateTimestampable();
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

        $this->validate(new Uniqueness(["field" => "slug"]));

        return $this->validationHasFailed() !== true;
    }
}
