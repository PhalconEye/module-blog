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
use Engine\Db\Model\Behavior\Sortable;
use Phalcon\Mvc\Model\Validator\Uniqueness;

/**
 * Category model.
 *
 * @category  PhalconEye
 * @package   Blog\Module
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("blog_categories")
 * @BelongsTo("parent_id", '\Blog\Model\Category', "id", {
 *  "alias": "ParentCategory"
 * })
 * @HasMany("id", "\Blog\Model\Category", "parent_id", {
 *  "alias": "SubCategories"
 * })
 */
class Category extends AbstractModel
{
    use Sluggable;
    use Sortable;

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
     * @Column(type="string", nullable=false, column="description", size="255")
     */
    public $description;

    /**
     * @Column(type="integer", nullable=true, column="parent_id", size="11")
     */
    public $parent_id = null;

    /**
     * @Column(type="string", nullable=true, column="languages", size="150")
     */
    public $languages = null;

    /**
     * @Column(type="boolean", column="is_enabled")
     */
    public $is_enabled = true;

    /**
     * Get related sub categories.
     *
     * @param array $arguments Entity params.
     *
     * @return Category[]
     */
    public function getSubCategories($arguments = [])
    {
        return $this->getRelated('SubCategories', $arguments);
    }

    /**
     * Get parent Category.
     *
     * @param array $arguments Entity params.
     *
     * @return Category|null
     */
    public function getParent($arguments = [])
    {
        if ($this->parent_id) {
            return $this->getRelated('ParentCategory', $arguments);
        }
        return null;
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
     * Check if it's ok to show the category.
     *
     * @return bool
     */
    public function isAllowed()
    {
        $valid = true;
        $language = $this->getDI()->get('session')->get('language', 'en');
        $languages = $this->getLanguages();

        if (!empty($languages)) {
            $valid = in_array($language, $languages);
        }

        return $valid;
    }

    /**
     * Logic before removal.
     *
     * @return bool
     */
    protected function beforeDelete()
    {
        $flag = true;
        if ($subCategories = $this->getSubCategories()) {
            foreach ($subCategories as $category) {
                $flag = $category->delete();
                if (!$flag) {
                    break;
                }
            }
        }

        return $flag;
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
        if (empty($this->languages)) {
            $this->languages = null;
        } elseif (is_array($this->languages)) {
            $this->languages = json_encode($this->languages);
        }
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
