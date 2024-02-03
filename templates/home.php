<body>
    <div class='home'>
        <div class='header'>
            <h1><?php echo SITE_TITLE ?></h1>
        </div>
        <div class='description'>
            <?php echo SITE_DESC_255 ?>
            <br><br>
            <form style='float:right;' method='get'>
                search: <input type='text' name='q' required>
                <input type='submit' value='Search'><a href='?i=r'> rules</a> | <a href='?i=h'>support</a> | <a href='?i=s'>&#9881;</a>
            </form>
            <br>
        </div>
        <div class='content'>
            <div class='linkcontainer'>
                <?php foreach($this->boards as $board): ?>
                    <div class='link'>
                        /<a href='?b=<?=$board->getLink()?>'><?=$board->getlink()?></a>/ - <?=$board->getTitle()?>
                        <br><i><?=$board->getDescription()?></i>
                    </div>
                        <?php endforeach ?>
            </div>
        </div>
    </div>