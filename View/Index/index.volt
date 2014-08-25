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

    <div class="post-details">
        <span>{{ post.creation_date }}</span>
        <span>{{ post.category.title }}</span>
    </div>

    <div class="post-description">
        {{ post.description }}
        <a class="post-read-more" rel="contents" href="{{ url(['for': 'blog-post', 'slug': post.slug]) }}">
            {{ "Read more" |i18n }}
        </a>
    </div>

    {% if post.tags %}
    <div class="post-tags">
        {% for item in post.tags %}
        <a class="btn">{{ item.label }}</a>
        {% endfor %}
    </div>
    {% endif %}

</article>

{% endfor %}

</div>
{% endblock %}
