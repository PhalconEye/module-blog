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

namespace Blog\Form;

use Blog\Model\Category;
use Blog\Model\Post;
use Blog\Form\Element\Tags as TagsField;
use Core\Form\CoreForm;
use Core\Model\Language;
use Engine\Db\AbstractModel;
use Engine\Exception;

/**
 * Create Post
 *
 * @category  PhalconEye
 * @package   Blog\Form
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PostForm extends CoreForm
{
    /**
     * Constructor
     *
     * @param AbstractModel $entity Entity object
     */
    public function __construct(AbstractModel $entity = null)
    {
        parent::__construct();

        if (!$entity) {
            $entity = new Post();
        }

        // We need to call these in given order since initialize() has already been run
        $this->addEntity($entity);
        $this->setupTags($entity);
        $this->setupFooter($entity);
    }

    /**
     * Initialize form
     */
    public function initialize()
    {
        $content = $this->addContentFieldSet()
            ->addText('title')
            ->addText('slug')
            // todo: make multiselect
            // todo: use indentions
            ->addSelect(
                'category_id',
                'Category',
                '',
                Category::find(),
                null,
                ['using' => ['id', 'title']]
            )
            ->addCkEditor(
                'description',
                'Short Description',
                '',
                ['allowedContent' => true],
                null
            )
            ->addCkEditor(
                'text',
                'Text',
                '',
                ['allowedContent' => true],
                null
            )
            ->addMultiSelect(
                'languages',
                'Languages',
                'Choose the language the category should belong to',
                Language::find(),
                null,
                ['using' => ['language', 'name']]
            )
            ->addCheckbox('is_enabled', 'Is enabled', null, 1, true, false);

        $content->setRequired('title');
    }

    /**
     * Setup Post Tags
     *
     * @param AbstractModel $entity Entity object
     */
    protected function setupTags()
    {
        $url = $this->getDI()->get('url');
        $entity = $this->getEntity();

        // Setup tag field
        $tags = new TagsField('tags[]');
        $tags->setOption('label', 'Tags');
        $tags->setAttribute('multiple', 'multiple');
        $tags->setAttribute('data-link', $url->get(['for' => 'admin-blog-tags-search']));
        if ($entity->id) {
            $relatedTags = [];
            foreach ($entity->getRelated('Tags') as $tag) {
                $relatedTags[$tag->label] = $tag->label;
            }

            $tags->setValue($relatedTags);
        }
        $this->add($tags);
    }

    /**
     * Setup form Footer
     *
     * @param AbstractModel $entity Entity object
     */
    protected function setupFooter($entity)
    {
        $this->addFooterFieldSet()
            ->addButton($entity->id? 'save' : 'create')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'admin-blog-posts']);
    }
}