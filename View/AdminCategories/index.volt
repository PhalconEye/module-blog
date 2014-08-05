{#
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
#}

{% extends "Core/View/layouts/admin.volt" %}

{% block title %}{{ 'Categories'|i18n }}{% endblock %}

{% block head %}
    {{ helper('assets').addJs('assets/js/blog/admin/categories.js') }}
    {{ helper('assets').addJs('assets/js/core/widgets/modal.js') }}
    {{ helper('assets').addJs('assets/js/core/widgets/ckeditor.js') }}

    <script type="text/javascript">
        var categoriesData = {
            'parent_id': {{ parent_id ? parent_id : 'null' }},
            'link_create': '{{ url(['for':'admin-blog-categories-create'])}}',
            'link_edit': '{{ url(['for':'admin-blog-categories-edit'])}}',
            'link_delete': '{{ url(['for':'admin-blog-categories-delete'])}}',
            'link_order': '{{ url(['for':'admin-blog-categories-order'])}}'
        };
    </script>
{% endblock %}

{% block header %}
<div class="navbar navbar-header">
<div class="navbar-inner">
    {{ navigation.render() }}
</div>
</div>
{% endblock %}

{% block content %}
<div class="span12">
<div class="category_manage_header">
    <h3>
        <a href="{{ url("admin/module/blog/categories/browse") }}">{{ "Categories"|i18n }}</a> >
        {% for ancestor in ancestors %}
        <a href="{{ url("admin/module/blog/categories/browse/" ~ ancestor.id ) }}">{{ ancestor.title }}</a> >
        {% endfor %}
    </h3>
    <button id="add-new-category" class="btn btn-primary">{{ 'Add new Category'|i18n }}</button>
    <div id="label-saved" class="label label-success" style="display: none">{{ 'Saved...'|i18n }}</div>
</div>
<div class="menu_manage_body">
    <ul id="categories">
        {% for category in categories %}
        <li data-category-id="{{ category.id }}">
            <div class="item_title">
                <i class="glyphicon glyphicon-move"></i>
                {{ category.title }}
                | {{ 'Sub-categories: '|i18n }}{{ category.getSubCategories() ? category.getSubCategories().count() : 0 }}
            </div>
            <div class="item_options">
                <a class="btn btn-success category-manage" href="javascript:;">{{ 'Manage'|i18n }}</a>
                <a class="btn btn-success category-edit" href="javascript:;">{{ 'Edit'|i18n }}</a>
                <a class="btn btn-success category-delete" href="javascript:;">{{ 'Remove'|i18n }}</a>
            </div>
        </li>
        {% endfor %}
    </ul>
</div>
</div>

{#ITEM TEMPLATE#}
<div id="default_category" style="display:none;">
<li data-category-id="element-id">
    <div class="category_title"><i class="glyphicon glyphicon-move"></i>element-label</div>
    <div class="category_options">
        <a class="btn btn-success category-manage" href="javascript:;">{{ 'Manage'|i18n }}</a>
        <a class="btn btn-success category-edit" href="javascript:;">{{ 'Edit'|i18n }}</a>
        <a class="btn btn-success category-delete" href="javascript:;">{{ 'Remove'|i18n }}</a>
    </div>
</li>
</div>
{# END OF ITEM TEMPLATE#}

{% endblock %}
