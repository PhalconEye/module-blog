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

{% block title %}{{ "Blog" |i18n }}{% endblock %}

{% block content %}
<div id="module-blog" class="list">

<h2>{{ "Blog" |i18n }}</h2>

{% for post in posts %}
<article>

  <a href="{{ url(['for': 'blog-post', 'slug': post.slug]) }}" rel="contents">
    <h3>{{ post.title }}</h3>
  </a>

  {% if helper('setting', 'blog').get('list_show_header', 1) %}
  <header>
    <small class="creation-data">{{ post.creation_date }}</small>
    <small class="category-title">{{ post.category.title }}</small>
  </header>
  {% endif %}

  <div class="post-description">
    {{ post.description }}
    {% if helper('setting', 'blog').get('list_show_read_more', 1) %}
      <a class="post-read-more" rel="contents" href="{{ url(['for': 'blog-post', 'slug': post.slug]) }}">
        {{ "Read more" |i18n }}
      </a>
    {% endif %}
  </div>

  <div class="footer">
    {% if post.tags and helper('setting', 'blog').get('list_show_tags', 1) %}
      <div class="post-tags">
        {% for item in post.tags %}
          <a class="btn">{{ item.label }}</a>
        {% endfor %}
      </div>
    {% endif %}
  </div>

</article>
{% endfor %}

</div>
{% endblock %}
