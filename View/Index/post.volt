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

{% block title %} Post - Title {% endblock %}

{% block content %}
<div id="module-blog" class="post">

<h2 class="breadcrumb">
    <a href="{{ url(['for': 'blog']) }}">{{ "Blog" |i18n }}</a>
    <span class="breadcrumb-separator"> &gt; </span>
    {{ post.title }}
</h2>

<article {% if !post.is_enabled %}class="disabled"{% endif %}>

  <header>
    {% if helper('setting', 'blog').get('post_show_date') == 1 %}
      <small class="creation-data">{{ post.creation_date }}</small>
    {% endif %}
    {% if helper('setting', 'blog').get('post_show_category_link') == 1 %}
      <a href="{{ url(['for': 'blog-category', 'slug': post.category.slug]) }}">
          <small class="category-title">{{ post.category.title }}</small>
      </a>
    {% endif %}
    {% if post.tags and helper('setting', 'blog').get('post_show_tags') == 1 %}
      <div class="post-tags">
        {% for item in post.tags %}
          <a class="btn" href="{{ url(['for': 'blog-tag', 'tag': item.label]) }}">{{ item.label }}</a>
        {% endfor %}
      </div>
    {% endif %}
  </header>

  {% if helper('setting', 'blog').get('post_show_description', 1) %}
  <div class="post-description">
    {{ post.description }}
  </div>
  {% endif %}

  {% if post.image %}
  <div class="post-image">
      <img src="{{ url(post.image) }}" alt="{{ post.title }}" />
  </div>
  {% endif %}

  <div class="post-text">
    {{ post.text }}
  </div>

  <div class="footer">
    {% if helper('setting', 'blog').get('post_show_date') == 2 %}
      <small class="creation-data">{{ post.creation_date }}</small>
    {% endif %}
    {% if helper('setting', 'blog').get('post_show_category_link') == 2 %}
      <small class="category-title">{{ post.category.title }}</small>
    {% endif %}
    {% if post.tags and helper('setting', 'blog').get('post_show_tags') == 2 %}
      <div class="post-tags">
        {% for item in post.tags %}
          <a class="tag" href="{{ url(['for': 'blog-tag', 'tag': item.label]) }}">{{ item.label }}</a>
        {% endfor %}
      </div>
    {% endif %}
  </div>

 </article>
</div>
{% endblock %}
