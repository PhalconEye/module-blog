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

use Core\Form\CoreForm;
use Core\Model\Language;
use Blog\Model\Category;
use Engine\Db\AbstractModel;

/**
 * Create Category
 *
 * @category  PhalconEye
 * @package   Blog\Form
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class CategoryForm extends CoreForm
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
            $entity = new Category();
        }

        $this->addEntity($entity);
    }

    /**
     * Initialize form
     */
    public function initialize()
    {
        $content = $this->addContentFieldSet()
            ->addText('title')
            ->addText('slug')
            ->addCkEditor(
                'description',
                "Description",
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
            ->addCheckbox('is_enabled', 'Is enabled', null, 1, true, false)
            ->addHidden('parent_id')
            ->addHidden('item_order');

        $content->setRequired('title');
    }
}