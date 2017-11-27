<!DOCTYPE html>
<html>
    <head>
        <title>{% block title %}site{% endblock %} - My Webpage</title>
        <link rel="stylesheet" href="/static/css/bootstrap.css">
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo $this->url->get('/img/favicon.ico')?>"/>
        <script src="/static/js/jquery-3.2.1.min.js"></script>
    </head>

    <body>
        <div class="container">{% block content %}{% endblock %}</div>

        <div class="footer">
            {% block footer %}{% endblock %}
        </div>
        <script src="/static/js/bootstrap.min.js"></script>
    </body>
</html>
 {% block js %}{% endblock %}
