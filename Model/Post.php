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

use Blog\Form\ConfigForm;
use Core\Model\Settings;
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
 * @method    static \Blog\Model\Post|false findFirstBySlug($slug) Find Post by slug
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

    const
        /**
         * Path, URL to images folder
         */
        IMAGE_PATH = 'files/blog/image',

        /**
         * Path, URL to thumbnails folder
         */
        THUMBNAIL_PATH = 'files/blog/image/thumbnail';

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

    /**
     * @Column(type="integer", nullable=false, column="hits", size="11")
     */
    public $hits = 0;

    /**
     * @Column(type="string", nullable=true, column="image", size="37")
     */
    public $image = null;

    /**
     * @Column(type="string", nullable=true, column="thumbnail", size="37")
     */
    public $thumbnail = null;

    /**
     * Initialize model
     */
    public function initialize()
    {
        $this->hasManyToMany("id", '\Blog\Model\PostTag', "post_id", "tag_id", '\Blog\Model\Tag', "id", [
            "alias" => "Tags"
        ]);
    }

    /**
     * Increase hits counter
     */
    public function hit()
    {
        if (Settings::getValue('blog', 'post_hits', 0)) {
            $this->hits++;
            $this->update();
        }
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

        // Strip images
        if ($this->image) {
            $this->image = self::IMAGE_PATH .'/'. $this->image;
        }
        if ($this->thumbnail) {
            $this->thumbnail = self::THUMBNAIL_PATH .'/'. $this->thumbnail;
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

        // Strip images
        if ($this->image) {
            $this->image = pathinfo($this->image, PATHINFO_BASENAME);
        }
        if ($this->thumbnail) {
            $this->thumbnail = pathinfo($this->thumbnail, PATHINFO_BASENAME);
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


    /**
     * Creates thumbnail from post image
     *
     * @return bool
     */
    public function createThumbnail()
    {
        $hasGd = extension_loaded('gd');
        $hasImagick = extension_loaded('imagick');

        if ($hasGd || $hasImagick) {

            /** @var \Phalcon\Image\AdapterInterface $adapter **/
            $adapterClass = 'Phalcon\Image\Adapter\\'. ($hasGd ? 'GD' : 'Imagick');
            $adapter = new $adapterClass($this->image);
            $adapter->resize(
                Settings::getValue('blog', 'thumbnail_width', ConfigForm::DEFAULT_THUMBNAIL_WIDTH),
                Settings::getValue('blog', 'thumbnail_height', ConfigForm::DEFAULT_THUMBNAIL_HEIGHT)
            );

            if ($adapter->save(self::THUMBNAIL_PATH .'/'. pathinfo($this->image, PATHINFO_BASENAME))) {
                $this->thumbnail = $this->image;
                return true;
            }
        }
        return false;
    }

}
