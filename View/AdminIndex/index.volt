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

{% block title %}{{ 'Blog'|i18n }}{% endblock %}

{% block header %}
<div class="navbar navbar-header">
<div class="navbar-inner">
    {{ navigation.render() }}
</div>
</div>
{% endblock %}

{% block content %}
<div class="span12">
<div class="row-fluid">
    <div class="row-fluid">
        <h2>{{ 'Blog'|i18n }}</h2>
        <p>No options yet...</p>
    </div>
</div>
</div>
{% endblock %}
