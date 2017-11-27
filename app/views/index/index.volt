{% extends "base.volt" %}

{% block title %}testphp{% endblock %}

{% block content %}

{% set numbers = ['one': 1, 'two': 2, 'three': 3] %}

{% for name, value in numbers %}
    {% if loop.first %} 123-----{% endif %}
    Name: {{ name }} Value: {{ value }}
    
{% endfor %}

{% endblock %}