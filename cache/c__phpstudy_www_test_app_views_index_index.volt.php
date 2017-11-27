<!DOCTYPE html>
<html>
    <head>
        <title>testphp - My Webpage</title>
        <link rel="stylesheet" href="/static/css/bootstrap.css">
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo $this->url->get('/img/favicon.ico')?>"/>
        <script src="/static/js/jquery-3.2.1.min.js"></script>
    </head>

    <body>
        <div class="container">

<?php $numbers = ['one' => 1, 'two' => 2, 'three' => 3]; ?>

<?php $v42900026021iterator = $numbers; $v42900026021incr = 0; $v42900026021loop = new stdClass(); $v42900026021loop->self = &$v42900026021loop; $v42900026021loop->length = count($v42900026021iterator); $v42900026021loop->index = 1; $v42900026021loop->index0 = 1; $v42900026021loop->revindex = $v42900026021loop->length; $v42900026021loop->revindex0 = $v42900026021loop->length - 1; ?><?php foreach ($v42900026021iterator as $name => $value) { ?><?php $v42900026021loop->first = ($v42900026021incr == 0); $v42900026021loop->index = $v42900026021incr + 1; $v42900026021loop->index0 = $v42900026021incr; $v42900026021loop->revindex = $v42900026021loop->length - $v42900026021incr; $v42900026021loop->revindex0 = $v42900026021loop->length - ($v42900026021incr + 1); $v42900026021loop->last = ($v42900026021incr == ($v42900026021loop->length - 1)); ?>
    <?php if ($v42900026021loop->first) { ?> 123-----<?php } ?>
    Name: <?= $name ?> Value: <?= $value ?>
    
<?php $v42900026021incr++; } ?>

</div>

        <div class="footer">
            
        </div>
        <script src="/static/js/bootstrap.min.js"></script>
    </body>
</html>
 
