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

/**
 * Admin categories management javascript
 *
 * @category  PhalconEye
 * @package   Blog\Assets
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
var categoriesData = categoriesData || [];
(function (window, $, root, data, undefined) {
    $(function () {
        var container = $("#categories");

        var defaultItem = function () {
            return $('#default_category').html();
        };

        var editAction = function () {
            var id = '';
            if ($(this).parents('li').length && $(this).parents('li').data('category-id')) {
                id = $(this).parents('li').data('category-id');
            }
            root.widget.modal.open(data.link_edit + id, data);
        };

        var addAction = function () {
            if (data.parent_id) {
                data.link_create += '' + data.parent_id;
            }
            root.widget.modal.open(data.link_create, data);
        };

        var deleteAction = function () {
            var id = $(this).parents('li').data('category-id');
            if (confirm(root.i18n._('Do you really want to delete this category?'))) {
                if (data.parent_id) {
                    window.location.href = data.link_delete + id + '/' + data.parent_id;
                }
                else {
                    window.location.href = data.link_delete + id;
                }
            }
        };

        var manageAction = function () {
            window.location.href = window.location.pathname + '/' + $(this).parents('li').data('category-id');
        };

        container.sortable({
            update: function (event, ui) {
                var order = [];
                var index = 0;
                ui.item.parent().children().each(function () {
                    order[index++] = $(this).data('category-id');
                });

                $.ajax({
                    type: "POST",
                    url: data.link_order,
                    data: {
                        'order': order
                    },
                    dataType: 'json',
                    success: function () {
                        $('#label-saved').show();
                        $('#label-saved').fadeOut(1000);
                    }
                });
            }
        });
        container.disableSelection();

        $('#add-new-category').click(addAction);
        container.on('click', '.category-edit', editAction);
        container.on('click', '.category-delete', deleteAction);
        container.on('click', '.category-manage', manageAction);

        window.addItem = function (id, label) {
            container.append(defaultItem().replace(/element-id/gi, id).replace('element-label', label));
        };
    });
}(window, jQuery, PhalconEye, categoriesData));

