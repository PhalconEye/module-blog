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
 * Handler for Tags Form element
 *
 * @category  PhalconEye
 * @package   Blog\Assets
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */

(function (window, $, root, undefined) {

    root.ns('Blog.tags', {

        /**
         * Scope
         */
        scope: null,

        /**
         * Element name
         */
        name: null,

        /**
         * Initialization function
         *
         * @param scope Will scope the underlying Tag elements
         */
        init: function(scope) {

            var _this = this, autocomplete, tags;

            this.scope = scope;

            if (tags = $('input[type=text]', scope)) {

                // Get name of the element
                this.name = tags[0]['name'];

                // Get autocomplete facade
                autocomplete = root.ns('PhalconEye.widget.autocomplete');

                // Assign the custom callback to autocomplete
                if (typeof autocomplete.onSelectCallback === 'undefined') {
                    // This is a polyfill for PhalconEye < 0.5.0
                    // Todo: remove
                    tags.on('autocompleteselect', function(event, ui) {
                        _this.addNewTag(ui.item.value);

                        // Clear typed-in tag
                        ui.item.value = '';
                    });
                } else {
                    autocomplete.onSelectCallback = function(event, ui) {
                        _this.addNewTag(ui.item.value);

                        // Clear typed-in tag
                        ui.item.value = '';
                    };
                }

                autocomplete.init(tags);

                // Will add focus to input area
                $(scope).on('click', function() {
                    tags.focus();
                    return true;
                });

                // Special handlers for [Enter] and [Backspace]
                tags.on('keypress', function(event) {
                    if (event.key === 'Enter') {
                        _this.onTagAdded(event.target);
                        return false;
                    } else if (event.key === 'Backspace' && event.target.value === '') {
                        _this.removeTag($(_this.scope).find('.addedTag:last').get(0));
                        return false;
                    }
                });

                // Will assign event listener for removing tags
                $('.tagRemove', scope).on('click', function(event) {
                    _this.removeTag(event.target.parentElement);
                })
            }
        },

        /**
         * Will be fired once a new Tag is added
         *
         * @param target HTMLElement instance
         */
        onTagAdded: function(target) {
            this.addNewTag(target.value);

            // Clear typed-in tag
            target.value = '';
        },

        /**
         * Create new List element
         *
         * @param value Tag value
         */
        addNewTag: function(value) {
            var _this = this, tag;

            if (value == '') {
                return;
            }

            tag = $('<li></li>').addClass('addedTag').text(value).insertBefore($('.tagAdd', this.scope));

            $('<span></span>').addClass('tagRemove').text('x').appendTo(tag).on('click', function(event) {
                _this.removeTag(event.target.parentElement);
            });

            $('<input>', {type: 'hidden', name: this.name}).val(value).appendTo(tag);
        },

        /**
         * Removes given Tag from our list
         *
         * @param element addedTag HTMLElement instance
         */
        removeTag: function (element) {
            if (element && element.className === 'addedTag') {
                element.parentNode.removeChild(element);
            }
        }
    });

    // Will wait for document load
    $(function () {

        // Init tags
        $('.blog-tags').each(function() {
            root.ns('Blog.tags').init(this);
        });
    });

}(window, jQuery, PhalconEye));
