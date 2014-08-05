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
<div id="module-blog">

<h2>{{ "Blog" |i18n }}</h2>

<table style="width: 100%">
  <tbody>
    {% for post in posts %}
    <tr>
      <td style="width: 20%; border: 1px solid #000000">{{ post.creation_date }}</td>
      <td style="border: 1px solid #000000">
          <h3>{{ post.title }}</h3>
          <p>{{ post.description }}</p>
      </td>
    </tr>
    {% endfor %}
  </tbody>
</table>

</div>
{% endblock %}
