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

use Blog\Model\Post;
use Blog\Form\Element\Tags as TagsField;
use Blog\Form\Element\Categories as CategoriesField;
use Core\Form\FileForm;
use Core\Model\Language;
use Core\Model\Settings;
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
class PostForm extends FileForm
{
    /**
     * Constructor
     *
     * @param Post $entity Instance
     */
    public function __construct(Post $entity = null)
    {
        parent::__construct();

        if (!$entity) {
            $entity = new Post();
        }

        // We need to call these in given order since initialize() has already been run
        $this->addEntity($entity);
        $this->setupTags($entity);
        $this->setupUpload($entity);
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
            ->add((new CategoriesField('category_id'))->setOption('label', 'Category'))
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
     * @param Post $entity Instance
     */
    protected function setupTags(Post $entity)
    {
        $url = $this->getDI()->get('url');

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
     * Setup upload field and preview thumbnail
     *
     * @param Post $entity Instance
     */
    protected function setupUpload(Post $entity)
    {
        $this->addFile(
            'image',
            'Image',
            null,
            true,
            $entity->image
        )
        ->addFile(
            'thumbnail',
            'Thumbnail',
            null,
            true,
            $entity->thumbnail
        );

        $hasGd = extension_loaded('gd');
        $hasImagick = extension_loaded('imagick');

        if ($hasGd || $hasImagick) {
            $this->setImageTransformation('image', [
                'adapter' => $hasImagick ? 'Imagick' : 'GD',
                'resize' =>  [
                    Settings::getValue('blog', 'image_width', ConfigForm::DEFAULT_IMG_WIDTH),
                    Settings::getValue('blog', 'image_height', ConfigForm::DEFAULT_IMG_HEIGHT)
                ],
                'crop' => [
                    Settings::getValue('blog', 'image_width', ConfigForm::DEFAULT_IMG_WIDTH),
                    Settings::getValue('blog', 'image_height', ConfigForm::DEFAULT_IMG_HEIGHT)
                ]
            ])
            ->setImageTransformation('thumbnail', [
                'adapter' => $hasImagick ? 'Imagick' : 'GD' ,
                'resize' =>  [
                    Settings::getValue('blog', 'thumbnail_width', ConfigForm::DEFAULT_THUMBNAIL_WIDTH),
                    Settings::getValue('blog', 'thumbnail_height', ConfigForm::DEFAULT_THUMBNAIL_HEIGHT)
                ],
                'crop' => [
                    Settings::getValue('blog', 'thumbnail_width', ConfigForm::DEFAULT_THUMBNAIL_WIDTH),
                    Settings::getValue('blog', 'thumbnail_width', ConfigForm::DEFAULT_THUMBNAIL_WIDTH)
                ]
            ]);
        }
    }

    /**
     * Setup form Footer
     *
     * @param Post $entity Instance
     */
    protected function setupFooter(Post $entity)
    {
        $this->addFooterFieldSet()
            ->addButton($entity->id? 'save' : 'create')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'admin-blog-posts']);
    }
}
