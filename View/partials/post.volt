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

<article {% if !post.is_enabled %}class="disabled"{% endif %}>

<a href="{{ url(['for': 'blog-post', 'slug': post.slug]) }}" rel="contents">
  <h3 class="title">{{ post.title }}</h3>
</a>

<div class="wrapper">

  {% if post.thumbnail %}
    <div class="thumbnail">
  <a href="{{ url(['for': 'blog-post', 'slug': post.slug]) }}" rel="contents">
    <img src="{{ url(post.thumbnail) }}" alt="{{ post.title }}" />
  </a>
</div>
  {% endif %}

  <div class="post">
    {% if helper('setting', 'blog').get('list_show_header', 1) %}
      <header>
        <small class="creation-data">{{ post.creation_date }}</small>
        <a href="{{ url(['for': 'blog-category', 'slug': post.category.slug]) }}">
          <small class="category-title">{{ post.category.title }}</small>
        </a>
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
            <a class="tag" href="{{ url(['for': 'blog-tag', 'tag': item.label]) }}">{{ item.label }}</a>
          {% endfor %}
        </div>
      {% endif %}
    </div>
  </div>
</div>
</article>
