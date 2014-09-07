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

{% extends "Core/View/layouts/main.volt" %}

{% block title %}{{ "Blog" |i18n }} - {{ blogTag.label }}{% endblock %}

{% block content %}
<div id="module-blog" class="list category-list">

<h2 class="breadcrumb">
  <a href="{{ url(['for': 'blog']) }}">{{ "Blog" |i18n }}</a>
  <span class="category-separator"> &gt; </span>
  <a href="{{ url(['for': 'blog-tag', 'tag': blogTag.label]) }}">{{ blogTag.label }}</a>
</h2>

{% for post in posts %}
  {{ partial("Blog/View/partials/post", post) }}
{% endfor %}

</div>
{% endblock %}
