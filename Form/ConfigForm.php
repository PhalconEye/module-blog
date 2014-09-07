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

/**
 * Blog config form
 *
 * @category  PhalconEye
 * @package   Blog\Form
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class ConfigForm extends CoreForm
{
    const
        // Default width for re-sized image
        DEFAULT_IMG_WIDTH = 1024,

        // Default height for re-sized image
        DEFAULT_IMG_HEIGHT = 768,

        // Default width for re-sized thumbnail
        DEFAULT_THUMBNAIL_WIDTH = 200,

        // Default height for re-sized thumbnail
        DEFAULT_THUMBNAIL_HEIGHT = 150;

    /**
     * Initialize form
     */
    public function initialize()
    {
        $this->addContentFieldSet('Image options')

            ->addText(
                'image_width',
                'Image width (px)',
                null,
                self::DEFAULT_IMG_WIDTH
            )
            ->addText(
                'image_height',
                'Image height (px)',
                null,
                self::DEFAULT_IMG_HEIGHT
            )
            ->addText(
                'thumbnail_width',
                'Thumbnail width (px)',
                null,
                self::DEFAULT_THUMBNAIL_WIDTH
            )
            ->addText(
                'thumbnail_height',
                'Thumbnail height (px)',
                null,
                self::DEFAULT_THUMBNAIL_HEIGHT
            );

        $common = [0 => 'No', 1 => 'Above post', 2 => 'Under post'];

        $this->addContentFieldSet('Post View')

            ->addSelect(
                'post_hits',
                'Collect post hits',
                null,
                [0 => 'No', 1 => 'Yes'],
                0
            )
            ->addSelect(
                'post_show_date',
                'Show creation date',
                null,
                $common,
                1
            )
            ->addSelect(
                'post_show_category_link',
                'Show category link',
                null,
                $common,
                1
            )
            ->addSelect(
                'post_show_tags',
                'Show tags',
                null,
                $common,
                2
            )
            ->addSelect(
                'post_show_description',
                'Show short description',
                null,
                [0 => 'No', 1 => 'Yes'],
                1
            );

        $this->addContentFieldSet('List View')
            ->addSelect(
                'list_show_header',
                'Show short header',
                null,
                [0 => 'No', 1 => 'Yes'],
                1
            )
            ->addSelect(
                'list_show_tags',
                'Show tags',
                null,
                [0 => 'No', 1 => 'Yes'],
                1
            )
            ->addSelect(
                'list_show_read_more',
                'Show read more',
                null,
                [0 => 'No', 1 => 'Yes'],
                1
            );

        $this->addFooterFieldSet()
            ->addButton('save');
    }
}
